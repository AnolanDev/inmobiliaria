<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Clientes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 10px;
        }
        
        .header h1 {
            color: #0066cc;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        
        .filters {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .filters h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 14px;
        }
        
        .filters p {
            margin: 3px 0;
            font-size: 11px;
        }
        
        .stats {
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats h3 {
            margin: 0;
            color: #1976d2;
            font-size: 16px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        
        table thead {
            background-color: #0066cc;
            color: white;
        }
        
        table th,
        table td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: left;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        table tbody tr:hover {
            background-color: #f5f5f5;
        }
        
        .status-activo {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .status-inactivo {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .status-prospecto {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .interest-high {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .interest-medium {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .interest-low {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .truncate {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Clientes</h1>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if($hasFilters)
    <div class="filters">
        <h3>Filtros Aplicados:</h3>
        @if($filters['search'])
            <p><strong>Búsqueda:</strong> {{ $filters['search'] }}</p>
        @endif
        @if($filters['status'])
            <p><strong>Estado:</strong> {{ $statusLabels[$filters['status']] ?? $filters['status'] }}</p>
        @endif
        @if($filters['interest_level'])
            <p><strong>Nivel de Interés:</strong> {{ $interestLabels[$filters['interest_level']] ?? $filters['interest_level'] }}</p>
        @endif
        @if($filters['date_from'] || $filters['date_to'])
            <p><strong>Período:</strong> 
                @if($filters['date_from']) Desde {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }} @endif
                @if($filters['date_to']) Hasta {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }} @endif
            </p>
        @endif
    </div>
    @endif

    <div class="stats">
        <h3>Total de Clientes: {{ $clients->count() }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 20%;">Nombre</th>
                <th style="width: 12%;">Documento</th>
                <th style="width: 18%;">Email</th>
                <th style="width: 12%;">Teléfono</th>
                <th style="width: 8%;">Estado</th>
                <th style="width: 8%;">Interés</th>
                <th style="width: 10%;">Contacto</th>
                <th style="width: 7%;">Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
            <tr>
                <td>{{ $client->id }}</td>
                <td>{{ $client->name }}</td>
                <td>
                    @if($client->document_number)
                        {{ $documentTypes[$client->document_type] ?? $client->document_type }}<br>
                        <small>{{ $client->document_number }}</small>
                    @else
                        -
                    @endif
                </td>
                <td class="truncate">{{ $client->email }}</td>
                <td>
                    {{ $client->phone }}
                    @if($client->secondary_phone)
                        <br><small>{{ $client->secondary_phone }}</small>
                    @endif
                </td>
                <td>
                    <span class="status-{{ $client->status }}">
                        {{ $statusLabels[$client->status] ?? $client->status }}
                    </span>
                </td>
                <td>
                    <span class="interest-{{ $client->interest_level }}">
                        {{ $interestLabels[$client->interest_level] ?? $client->interest_level }}
                    </span>
                </td>
                <td>{{ $contactMethods[$client->preferred_contact_method] ?? $client->preferred_contact_method }}</td>
                <td>{{ $client->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($clients->count() > 15)
        <div class="page-break"></div>
        
        <div class="header">
            <h1>Detalles Adicionales de Clientes</h1>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Nombre</th>
                    <th style="width: 20%;">Dirección</th>
                    <th style="width: 10%;">Nacimiento</th>
                    <th style="width: 15%;">Ocupación</th>
                    <th style="width: 12%;">Último Contacto</th>
                    <th style="width: 28%;">Notas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td class="truncate">{{ $client->address ?: '-' }}</td>
                    <td>{{ $client->birth_date ? $client->birth_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $client->occupation ?: '-' }}</td>
                    <td>{{ $client->last_contact_date ? $client->last_contact_date->format('d/m/Y') : '-' }}</td>
                    <td class="truncate">{{ Str::limit($client->notes, 100) ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Este reporte fue generado automáticamente desde el Sistema de Gestión Inmobiliaria</p>
        <p>{{ config('app.name') }} - {{ config('app.url') }}</p>
    </div>
</body>
</html>