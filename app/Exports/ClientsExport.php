<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class ClientsExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Client::query();

        // Apply same filters as in the controller
        if ($this->request->filled('search')) {
            $query->search($this->request->search);
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('interest_level')) {
            $query->where('interest_level', $this->request->interest_level);
        }

        if ($this->request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $this->request->date_to);
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre Completo',
            'Tipo de Documento',
            'Número de Documento',
            'Correo Electrónico',
            'Teléfono Principal',
            'Teléfono Secundario',
            'Dirección',
            'Fecha de Nacimiento',
            'Ocupación',
            'Estado',
            'Nivel de Interés',
            'Método de Contacto Preferido',
            'Último Contacto',
            'Fecha de Registro',
            'Notas'
        ];
    }

    public function map($client): array
    {
        return [
            $client->id,
            $client->name,
            $this->getDocumentTypeLabel($client->document_type),
            $client->document_number,
            $client->email,
            $client->phone,
            $client->secondary_phone,
            $client->address,
            $client->birth_date ? $client->birth_date->format('d/m/Y') : '',
            $client->occupation,
            $this->getStatusLabel($client->status),
            $this->getInterestLevelLabel($client->interest_level),
            $this->getContactMethodLabel($client->preferred_contact_method),
            $client->last_contact_date ? $client->last_contact_date->format('d/m/Y H:i') : '',
            $client->created_at->format('d/m/Y H:i'),
            $client->notes,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Birth date
            'N' => NumberFormat::FORMAT_DATE_DATETIME, // Last contact
            'O' => NumberFormat::FORMAT_DATE_DATETIME, // Created at
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function getDocumentTypeLabel($type)
    {
        $types = [
            'cedula' => 'Cédula de Ciudadanía',
            'cedula_extranjeria' => 'Cédula de Extranjería',
            'pasaporte' => 'Pasaporte',
            'nit' => 'NIT',
            'tarjeta_identidad' => 'Tarjeta de Identidad',
        ];

        return $types[$type] ?? $type;
    }

    private function getStatusLabel($status)
    {
        $statuses = [
            'prospecto' => 'Prospecto',
            'activo' => 'Activo',
            'inactivo' => 'Inactivo',
        ];

        return $statuses[$status] ?? $status;
    }

    private function getInterestLevelLabel($level)
    {
        $levels = [
            'low' => 'Bajo',
            'medium' => 'Medio',
            'high' => 'Alto',
        ];

        return $levels[$level] ?? $level;
    }

    private function getContactMethodLabel($method)
    {
        $methods = [
            'phone' => 'Teléfono',
            'email' => 'Email',
            'whatsapp' => 'WhatsApp',
            'both' => 'Teléfono y Email',
        ];

        return $methods[$method] ?? $method;
    }
}