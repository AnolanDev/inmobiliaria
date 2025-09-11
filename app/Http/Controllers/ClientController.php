<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Property;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Exports\ClientsExport;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Client::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Interest level filter
        if ($request->filled('interest_level')) {
            $query->where('interest_level', $request->interest_level);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $clients = $query->latest()
                        ->paginate(12)
                        ->withQueryString();

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search', 'status', 'interest_level', 'date_from', 'date_to']),
            'statuses' => Client::STATUSES,
            'interestLevels' => Client::INTEREST_LEVELS,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Clients/Create', [
            'documentTypes' => Client::DOCUMENT_TYPES,
            'statuses' => Client::STATUSES,
            'interestLevels' => Client::INTEREST_LEVELS,
            'contactMethods' => Client::CONTACT_METHODS,
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('clients/profiles', 'public');
        }

        // Handle attachments upload
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('clients/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
            $data['attachments'] = $attachments;
        }

        $client = Client::create($data);

        return redirect()->route('clients.index')
                        ->with('success', 'Cliente creado exitosamente.');
    }

    public function show(Client $client): Response
    {
        $client->load(['visits.property', 'visits.agent', 'properties']);

        return Inertia::render('Clients/Show', [
            'client' => $client,
            'documentTypes' => Client::DOCUMENT_TYPES,
            'statuses' => Client::STATUSES,
            'interestLevels' => Client::INTEREST_LEVELS,
            'contactMethods' => Client::CONTACT_METHODS,
        ]);
    }

    public function edit(Client $client): Response
    {
        return Inertia::render('Clients/Edit', [
            'client' => $client,
            'documentTypes' => Client::DOCUMENT_TYPES,
            'statuses' => Client::STATUSES,
            'interestLevels' => Client::INTEREST_LEVELS,
            'contactMethods' => Client::CONTACT_METHODS,
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $data = $request->validated();

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($client->profile_image) {
                Storage::disk('public')->delete($client->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('clients/profiles', 'public');
        } else {
            // Remove profile_image from data if no new file uploaded to preserve existing value
            unset($data['profile_image']);
        }

        // Handle attachments upload
        if ($request->hasFile('attachments')) {
            $newAttachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('clients/attachments', 'public');
                $newAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
            // Merge with existing attachments
            $existingAttachments = $client->attachments ?? [];
            $data['attachments'] = array_merge($existingAttachments, $newAttachments);
        }

        // Handle removed attachments
        if ($request->has('remove_attachments')) {
            $existingAttachments = $client->attachments ?? [];
            $toRemove = $request->remove_attachments;
            
            foreach ($toRemove as $index) {
                if (isset($existingAttachments[$index])) {
                    Storage::disk('public')->delete($existingAttachments[$index]['path']);
                    unset($existingAttachments[$index]);
                }
            }
            $data['attachments'] = array_values($existingAttachments);
        }

        $client->update($data);

        return redirect()->route('clients.index')
                        ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        // Delete associated files
        if ($client->profile_image) {
            Storage::disk('public')->delete($client->profile_image);
        }

        if ($client->attachments) {
            foreach ($client->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $client->delete();

        return redirect()->route('clients.index')
                        ->with('success', 'Cliente eliminado exitosamente.');
    }

    /**
     * Quick create client from other modules
     */
    public function quickCreate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|max:20',
            'document_type' => 'required|in:' . implode(',', array_keys(Client::DOCUMENT_TYPES)),
            'document_number' => 'nullable|string|max:50',
        ]);

        $client = Client::create($request->only([
            'name', 'email', 'phone', 'document_type', 'document_number'
        ]));

        return response()->json([
            'client' => $client->only(['id', 'name', 'email', 'phone']),
            'message' => 'Cliente creado exitosamente'
        ]);
    }

    /**
     * Associate client with property
     */
    public function associateProperty(Request $request, Client $client)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'interest_type' => 'required|in:compra,arriendo,inversion',
            'notes' => 'nullable|string',
        ]);

        $client->properties()->attach($request->property_id, [
            'interest_type' => $request->interest_type,
            'notes' => $request->notes,
            'status' => 'interesado',
        ]);

        return response()->json([
            'message' => 'Cliente asociado a la propiedad exitosamente'
        ]);
    }

    /**
     * Get clients for select dropdown
     */
    public function getForSelect()
    {
        $clients = Client::select('id', 'name', 'email', 'phone')
                        ->where('status', '!=', 'inactivo')
                        ->orderBy('name')
                        ->get();

        return response()->json($clients);
    }

    /**
     * Export clients to Excel
     */
    public function exportExcel(Request $request)
    {
        $fileName = 'clientes_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new ClientsExport($request), $fileName);
    }

    /**
     * Export clients to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Client::query();

        // Apply same filters as in index method
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('interest_level')) {
            $query->where('interest_level', $request->interest_level);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $clients = $query->orderBy('name')->get();
        
        // Prepare data for the PDF view
        $data = [
            'clients' => $clients,
            'filters' => $request->only(['search', 'status', 'interest_level', 'date_from', 'date_to']),
            'hasFilters' => $request->hasAny(['search', 'status', 'interest_level', 'date_from', 'date_to']),
            'documentTypes' => Client::DOCUMENT_TYPES,
            'statusLabels' => Client::STATUSES,
            'interestLabels' => Client::INTEREST_LEVELS,
            'contactMethods' => Client::CONTACT_METHODS,
        ];
        
        $pdf = Pdf::loadView('exports.clients-pdf', $data);
        $pdf->setPaper('a4', 'landscape');
        
        $fileName = 'reporte_clientes_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($fileName);
    }
}