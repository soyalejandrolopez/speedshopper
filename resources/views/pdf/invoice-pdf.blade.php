<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $locale === 'es' ? 'Factura' : 'Invoice' }} #{{ $request->number }} - {{ $companyName }}</title>
    <style>
        @page {
            margin: 28px 32px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 0;
            font-size: 11.5px;
            line-height: 1.4;
        }

        /* Header */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #059669;
            padding-bottom: 14px;
            margin-bottom: 16px;
        }
        .logo-img {
            max-height: 46px;
            max-width: 180px;
        }
        .company-title {
            font-size: 20px;
            font-weight: bold;
            color: #047857;
            margin: 0;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            text-align: right;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .invoice-number {
            font-size: 13px;
            font-family: monospace;
            font-weight: bold;
            color: #059669;
            text-align: right;
            margin-top: 2px;
        }
        .invoice-date {
            font-size: 10px;
            color: #6b7280;
            text-align: right;
            margin-top: 2px;
        }

        /* Info boxes */
        .info-table {
            width: 100%;
            margin-bottom: 16px;
        }
        .info-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 12px;
        }
        .info-card-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #059669;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .items-table th {
            background-color: #ecfdf5;
            color: #065f46;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border-top: 1px solid #d1fae5;
            border-bottom: 1px solid #a7f3d0;
            text-align: left;
        }
        .items-table th.text-right {
            text-align: right;
        }
        .items-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 11px;
        }
        .items-table td.text-right {
            text-align: right;
        }
        .item-category-row {
            background-color: #f8fafc;
            font-weight: bold;
            font-size: 10px;
            color: #475569;
            text-transform: uppercase;
            padding: 5px 10px;
        }

        /* Totals section */
        .totals-table {
            width: 100%;
            margin-top: 10px;
        }
        .summary-box {
            width: 250px;
            float: right;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background-color: #fafafa;
            padding: 10px;
        }
        .summary-line {
            display: block;
            margin-bottom: 4px;
            font-size: 11px;
        }
        .summary-line strong {
            float: right;
        }
        .total-highlight {
            border-top: 2px solid #059669;
            padding-top: 6px;
            margin-top: 6px;
            font-size: 13px;
            font-weight: bold;
            color: #065f46;
        }
        .balance-highlight {
            border-top: 1px dashed #d1d5db;
            padding-top: 4px;
            margin-top: 4px;
            font-size: 12px;
            font-weight: bold;
            color: {{ $balance > 0 ? '#b45309' : '#047857' }};
        }

        /* Footer */
        .footer-table {
            width: 100%;
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
        }
        .qr-img {
            width: 60px;
            height: 60px;
        }
        .notice-box {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 9.5px;
            color: #92400e;
            margin-top: 14px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: middle;">
                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="{{ $companyName }}">
                @else
                    <h1 class="company-title">{{ $companyName }}</h1>
                    <div style="font-size: 10px; color: #059669; font-weight: bold; text-transform: uppercase;">
                        {{ $locale === 'es' ? 'Personal Shopper & Logística' : 'Personal Shopper & Logistics' }}
                    </div>
                @endif
            </td>
            <td style="width: 45%; vertical-align: middle;">
                <h2 class="invoice-title">{{ $locale === 'es' ? 'Factura Oficial' : 'Official Invoice' }}</h2>
                <div class="invoice-number">#{{ $request->number }}</div>
                <div class="invoice-date">
                    {{ $locale === 'es' ? 'Fecha de Emisión:' : 'Issue Date:' }} {{ $request->created_at->format('Y-m-d') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Info Cards (Company & Customer) -->
    <table class="info-table">
        <tr>
            <td style="width: 48%; vertical-align: top;">
                <div class="info-card">
                    <div class="info-card-title">{{ $locale === 'es' ? 'Emisor (Empresa)' : 'Issued By' }}</div>
                    <strong>{{ $companyName }}</strong><br>
                    {{ $warehouseAddress }}<br>
                    @if ($companyEmail)
                        {{ $companyEmail }}<br>
                    @endif
                    @if ($whatsappPhone)
                        WhatsApp: {{ $whatsappPhone }}
                    @endif
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; vertical-align: top;">
                <div class="info-card">
                    <div class="info-card-title">{{ $locale === 'es' ? 'Cliente (Facturar a)' : 'Billed To (Customer)' }}</div>
                    <strong>{{ $request->customer?->name ?? '—' }}</strong><br>
                    @if ($request->customer?->email)
                        {{ $request->customer->email }}<br>
                    @endif
                    @if ($request->customer?->phone || $request->customer?->whatsapp)
                        Tel / WA: {{ $request->customer->whatsapp ?: $request->customer->phone }}<br>
                    @endif
                    @if ($request->customer?->country)
                        {{ country_name($request->customer->country) }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Invoice Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 55%;">{{ $locale === 'es' ? 'Descripción del Artículo / Concepto' : 'Description / Item Concept' }}</th>
                <th style="width: 15%;">{{ $locale === 'es' ? 'Tipo' : 'Type' }}</th>
                <th class="text-right" style="width: 15%;">{{ $locale === 'es' ? 'Cantidad' : 'Qty' }}</th>
                <th class="text-right" style="width: 15%;">{{ $locale === 'es' ? 'Importe ($)' : 'Amount ($)' }}</th>
            </tr>
        </thead>
        <tbody>
            <!-- Product Line Items -->
            @php
                $productCosts = $request->costItems->where('type', \App\Enums\CostType::ProductCost);
                $additionalCosts = $request->costItems->where('type', '!=', \App\Enums\CostType::ProductCost);
            @endphp

            @if ($productCosts->isNotEmpty())
                <tr>
                    <td colspan="4" class="item-category-row">
                        {{ $locale === 'es' ? '1. Productos Comprados' : '1. Purchased Products' }}
                    </td>
                </tr>
                @foreach ($productCosts as $pCost)
                    <tr>
                        <td>
                            <strong>{{ $pCost->description }}</strong>
                            @if ($request->store)
                                <div style="font-size: 9.5px; color: #6b7280;">{{ $locale === 'es' ? 'Tienda:' : 'Store:' }} {{ $request->store }}</div>
                            @endif
                        </td>
                        <td style="color: #6b7280; font-size: 10px;">{{ $locale === 'es' ? 'Producto' : 'Product' }}</td>
                        <td class="text-right">{{ $request->quantity > 1 ? $request->quantity : 1 }}</td>
                        <td class="text-right" style="font-weight: 600;">${{ number_format($pCost->amount, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>
                        <strong>{{ $request->product_name }}</strong>
                        @if ($request->store)
                            <div style="font-size: 9.5px; color: #6b7280;">{{ $locale === 'es' ? 'Tienda:' : 'Store:' }} {{ $request->store }}</div>
                        @endif
                    </td>
                    <td style="color: #6b7280; font-size: 10px;">{{ $locale === 'es' ? 'Producto' : 'Product' }}</td>
                    <td class="text-right">{{ $request->quantity }}</td>
                    <td class="text-right" style="font-weight: 600;">${{ number_format((float) ($request->unit_price * $request->quantity), 2) }}</td>
                </tr>
            @endif

            <!-- Additional Fees & Logistics Costs -->
            @if ($additionalCosts->isNotEmpty())
                <tr>
                    <td colspan="4" class="item-category-row">
                        {{ $locale === 'es' ? '2. Tarifas y Servicios de Logística' : '2. Fees & Logistics Services' }}
                    </td>
                </tr>
                @foreach ($additionalCosts as $aCost)
                    <tr>
                        <td>{{ $aCost->description }}</td>
                        <td style="color: #6b7280; font-size: 10px;">{{ $aCost->type->label() }}</td>
                        <td class="text-right">1</td>
                        <td class="text-right" style="font-weight: 600;">${{ number_format($aCost->amount, 2) }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <!-- Totals & Balance Summary -->
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                @if ($request->notes)
                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; font-size: 10px;">
                        <strong style="color: #334155;">{{ $locale === 'es' ? 'Notas / Observaciones:' : 'Notes / Instructions:' }}</strong><br>
                        <span style="color: #64748b;">{{ $request->notes }}</span>
                    </div>
                @endif
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="summary-box">
                    <div class="summary-line">
                        <span>{{ $locale === 'es' ? 'Total Facturado:' : 'Total Invoiced:' }}</span>
                        <strong>${{ number_format($totalCost, 2) }}</strong>
                    </div>
                    <div class="summary-line" style="color: #047857;">
                        <span>{{ $locale === 'es' ? 'Monto Pagado:' : 'Amount Paid:' }}</span>
                        <strong>${{ number_format($paidAmount, 2) }}</strong>
                    </div>
                    <div class="balance-highlight">
                        <span>{{ $locale === 'es' ? 'Saldo por Pagar:' : 'Balance Due:' }}</span>
                        <strong style="float: right;">${{ number_format($balance, 2) }}</strong>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Notice -->
    <div class="notice-box">
        <strong>{{ $locale === 'es' ? 'Información Importante:' : 'Important Information:' }}</strong><br>
        {{ $locale === 'es'
            ? 'Gracias por tu preferencia. El envío internacional se calcula por caja según peso y dimensiones en el momento del despacho.'
            : 'Thank you for your business. International shipping is calculated per box based on weight and dimensions upon dispatch.' }}
    </div>

    <!-- Footer with QR Code -->
    <table class="footer-table">
        <tr>
            <td style="width: 75%; vertical-align: middle;">
                <div style="font-size: 10px; font-weight: bold; color: #047857;">{{ $companyName }}</div>
                <div style="font-size: 9px; color: #6b7280;">
                    {{ $warehouseAddress }} &bull; {{ $whatsappPhone }} &bull; {{ $companyEmail }}
                </div>
                <div style="font-size: 8.5px; color: #9ca3af; margin-top: 2px;">
                    {{ $locale === 'es' ? 'Documento generado automáticamente el' : 'Document automatically generated on' }} {{ $generatedAt->format('Y-m-d H:i:s') }}
                </div>
            </td>
            <td style="width: 25%; text-align: right; vertical-align: middle;">
                @if ($qrDataUri)
                    <img src="{{ $qrDataUri }}" class="qr-img" alt="QR Code"><br>
                    <span style="font-size: 8px; color: #9ca3af;">{{ $locale === 'es' ? 'Escanear para contacto' : 'Scan for assistance' }}</span>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>
