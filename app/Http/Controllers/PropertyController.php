<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Agent;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Properties/Index', [
            'properties' => Property::with('agent')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Properties/Create', [
            'agents' => Agent::where('is_active', true)->get(),
        ]);
    }

    public function store(StorePropertyRequest $request): RedirectResponse
    {
        Property::create($request->validated());

        return redirect()->route('properties.index')
            ->with('success', 'Propiedad creada exitosamente.');
    }

    public function show(Property $property): Response
    {
        return Inertia::render('Properties/Show', [
            'property' => $property->load('agent', 'visits.client'),
        ]);
    }

    public function edit(Property $property): Response
    {
        return Inertia::render('Properties/Edit', [
            'property' => $property,
            'agents' => Agent::where('is_active', true)->get(),
        ]);
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $property->update($request->validated());

        return redirect()->route('properties.index')
            ->with('success', 'Propiedad actualizada exitosamente.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $property->delete();

        return redirect()->route('properties.index')
            ->with('success', 'Propiedad eliminada exitosamente.');
    }
}
