<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $locale === 'es' ? 'Lista de Tarifas y Servicios' : 'Price List & Services Guide' }} - {{ $companyName }}</title>
    <style>
        @page {
            margin: 28px 32px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.45;
        }

        /* Header */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #059669;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .logo-img {
            max-height: 48px;
            max-width: 180px;
        }
        .company-title {
            font-size: 22px;
            font-weight: bold;
            color: #047857;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .header-subtitle {
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            margin-top: 3px;
        }
        .header-contact {
            text-align: right;
            font-size: 10.5px;
            color: #6b7280;
            line-height: 1.35;
        }
        .header-contact strong {
            color: #111827;
        }

        /* Section titles */
        .section-header {
            background-color: #ecfdf5;
            border-left: 4px solid #059669;
            padding: 7px 12px;
            margin-top: 16px;
            margin-bottom: 10px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #065f46;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        /* Tables */
        .rate-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .rate-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 7px 10px;
            text-align: left;
            border-bottom: 1.5px solid #d1d5db;
        }
        .rate-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11.5px;
            color: #1f2937;
        }
        .rate-table tr:nth-child(even) td {
            background-color: #f9fafb;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .font-semibold { font-weight: 600; }
        .text-emerald { color: #059669; font-weight: bold; }
        .text-amber { color: #d97706; font-weight: bold; }
        .tag-badge {
            display: inline-block;
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }

        /* Callout Box / Cards */
        .info-card {
            background-color: #f0fdf4;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .info-card-warning {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 10px 14px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .info-title {
            font-size: 11.5px;
            font-weight: bold;
            color: #065f46;
            margin: 0 0 4px 0;
        }
        .info-title-warning {
            font-size: 11.5px;
            font-weight: bold;
            color: #92400e;
            margin: 0 0 4px 0;
        }
        .info-text {
            font-size: 10.5px;
            color: #374151;
            margin: 0;
            line-height: 1.4;
        }

        /* 2 column grid simulation with tables */
        .two-col-table {
            width: 100%;
            border-collapse: collapse;
        }
        .two-col-table td {
            vertical-align: top;
            padding: 0 6px;
        }

        /* Footer */
        .footer {
            margin-top: 24px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            text-align: center;
            font-size: 9.5px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 55%; vertical-align: middle;">
                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="{{ $companyName }}">
                @else
                    <h1 class="company-title">{{ $companyName }}</h1>
                @endif
                <div class="header-subtitle">
                    {{ $locale === 'es' ? 'Tarifario Oficial de Servicios y Compras' : 'Official Pricing & Services Rate Sheet' }}
                </div>
            </td>
            <td class="header-contact" style="width: 45%; vertical-align: middle;">
                <strong>{{ $companyName }}</strong><br>
                {{ $warehouseAddress }}<br>
                WhatsApp: {{ $whatsappPhone }}<br>
                <span style="color: #059669; font-weight: 600;">
                    {{ $locale === 'es' ? 'Válido para ' : 'Valid for ' }} {{ now()->translatedFormat('F Y') }}
                </span>
            </td>
        </tr>
    </table>

    {{-- SECTION 1: PERSONAL SHOPPER --}}
    <div class="section-header">
        <h2 class="section-title">
            {{ $locale === 'es' ? '1. Tarifas de Compras Personales (Personal Shopper)' : '1. Personal Shopper Rates & Store Visits' }}
        </h2>
    </div>

    <table class="rate-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 32%;">{{ $locale === 'es' ? 'Rango de Compra' : 'Purchase Range' }}</th>
                <th class="text-center" style="width: 22%;">{{ $locale === 'es' ? 'Comisión (% Compra)' : 'Shopper Fee (%)' }}</th>
                <th class="text-center" style="width: 23%;">{{ $locale === 'es' ? 'Tiendas Incluidas' : 'Included Stores' }}</th>
                <th class="text-center" style="width: 23%;">{{ $locale === 'es' ? 'Tiempo Incluido' : 'Included Time' }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rates['shopper_tiers'] as $tier)
                <tr>
                    <td class="font-semibold">
                        @if ($tier['max'])
                            ${{ number_format($tier['min']) }} - ${{ number_format($tier['max']) }} USD
                        @else
                            ${{ number_format($tier['min']) }} USD {{ $locale === 'es' ? 'y más' : 'and above' }}
                        @endif
                    </td>
                    <td class="text-center font-semibold text-emerald">
                        {{ $tier['percent'] }}% {{ $locale === 'es' ? 'compra total' : 'of total purchase' }}
                    </td>
                    <td class="text-center">
                        <span class="tag-badge">{{ $tier['stores'] }} {{ $locale === 'es' ? 'TIENDAS' : 'STORES' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="tag-badge">{{ $tier['hours'] }} {{ $locale === 'es' ? 'HORAS' : 'HOURS' }}</span>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" class="font-semibold" style="background-color: #f0fdf4;">
                    {{ $locale === 'es' ? 'Visitar una tienda adicional' : 'Additional store visit' }}
                </td>
                <td colspan="2" class="text-center font-semibold text-emerald" style="background-color: #f0fdf4;">
                    ${{ number_format($rates['extra_store_fee'], 2) }} USD
                </td>
            </tr>
        </tbody>
    </table>

    {{-- SECTION 2: REPACKAGING & WAREHOUSE --}}
    <div class="section-header" style="margin-top: 18px;">
        <h2 class="section-title">
            {{ $locale === 'es' ? '2. Tarifas de Reempaque y Almacén' : '2. Repackaging & Warehouse Services' }}
        </h2>
    </div>

    <table class="two-col-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 50%;">
                <table class="rate-table" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ $locale === 'es' ? 'Tipo de Caja (Heavy Duty)' : 'Box Type (Heavy Duty)' }}</th>
                            <th class="text-right">{{ $locale === 'es' ? 'Precio' : 'Price' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>1 {{ $locale === 'es' ? 'Caja Small' : 'Small Box' }}</strong> (Heavy Duty)</td>
                            <td class="text-right font-semibold text-emerald">${{ number_format($rates['box_small_heavy_duty'], 2) }} USD</td>
                        </tr>
                        <tr>
                            <td><strong>1 {{ $locale === 'es' ? 'Caja Mediana' : 'Medium Box' }}</strong> (Heavy Duty)</td>
                            <td class="text-right font-semibold text-emerald">${{ number_format($rates['box_medium_heavy_duty'], 2) }} USD</td>
                        </tr>
                        <tr>
                            <td><strong>1 {{ $locale === 'es' ? 'Caja Larga' : 'Large Box' }}</strong> (Heavy Duty)</td>
                            <td class="text-right font-semibold text-emerald">${{ number_format($rates['box_large_heavy_duty'], 2) }} USD</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 50%;">
                <table class="rate-table" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ $locale === 'es' ? 'Servicios de Logística' : 'Logistics Services' }}</th>
                            <th class="text-right">{{ $locale === 'es' ? 'Costo' : 'Cost' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $locale === 'es' ? 'Comisión Almacén' : 'Warehouse Fee' }}</td>
                            <td class="text-right font-semibold text-emerald">{{ $rates['warehouse_percent'] }}%</td>
                        </tr>
                        <tr>
                            <td>{{ $locale === 'es' ? 'Llevar caja al almacén' : 'Drop-off box at warehouse' }}</td>
                            <td class="text-right font-semibold text-emerald">${{ number_format($rates['warehouse_delivery_fee'], 2) }} USD</td>
                        </tr>
                        <tr>
                            <td>{{ $locale === 'es' ? 'Almacenaje mensual (después de 30 días)' : 'Monthly storage (after 30 days)' }}</td>
                            <td class="text-right font-semibold text-amber">${{ number_format($rates['monthly_storage_fee'], 2) }} USD/{{ $locale === 'es' ? 'mes' : 'mo' }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- Conditions and Notes --}}
    <div class="info-card">
        <p class="info-title">
            <span style="color: #059669;">●</span>
            {{ $locale === 'es' ? 'Compras Online y Recepción' : 'Online Purchases & Receiving' }}
        </p>
        <p class="info-text">
            {{ $locale === 'es' ? ($rates['notes_es']['repackage_notice'] ?? '') : ($rates['notes_en']['repackage_notice'] ?? '') }}
        </p>
    </div>

    <div class="info-card-warning">
        <p class="info-title-warning">
            <span style="color: #d97706;">●</span>
            {{ $locale === 'es' ? 'Política de Almacenaje' : 'Storage Policy' }}
        </p>
        <p class="info-text">
            {{ $locale === 'es' ? ($rates['notes_es']['storage_notice'] ?? '') : ($rates['notes_en']['storage_notice'] ?? '') }}
        </p>
    </div>

    {{-- Footer --}}
    <div class="footer">
        {{ $companyName }} &bull; {{ $warehouseAddress }} &bull; WhatsApp: {{ $whatsappPhone }} &bull;
        {{ $locale === 'es' ? 'Documento generado automáticamente el ' : 'Generated automatically on ' }} {{ $generatedAt->format('d/m/Y H:i') }}
    </div>

</body>
</html>
