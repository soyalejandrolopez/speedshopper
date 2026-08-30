<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Financial Report') }} — {{ $company }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }
        .header { border-bottom: 3px solid {{ $colors["600"] }}; padding-bottom: 12px; margin-bottom: 16px; }
        .header table { width: 100%; }
        .company { font-size: 20px; font-weight: bold; color: {{ $colors['800'] }}; }
        .address { font-size: 10px; color: #6b7280; margin-top: 2px; }
        .doc-title { text-align: right; }
        .doc-title .title { font-size: 16px; font-weight: bold; color: {{ $colors["600"] }}; text-transform: uppercase; }
        .doc-title .meta { font-size: 10px; color: #6b7280; margin-top: 2px; }
        .logo-img { height: 44px; width: auto; }
        .section-title {
            font-size: 13px; font-weight: bold; color: {{ $colors['800'] }};
            border-bottom: 1px solid #d1d5db; padding-bottom: 4px; margin: 18px 0 8px;
        }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th {
            background: {{ $colors['50'] }}; color: {{ $colors['800'] }}; text-align: left;
            padding: 6px 8px; font-size: 10px; text-transform: uppercase;
        }
        table.data td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; }
        .summary { margin-top: 8px; }
        .summary td { padding: 6px 8px; }
        .amount { text-align: right; font-variant-numeric: tabular-nums; }
        .strong { font-weight: bold; }
        .total-row { background: {{ $colors["600"] }}; color: #fff; font-weight: bold; }
        .total-row td { padding: 7px 8px; }
        .footer {
            margin-top: 24px; padding-top: 10px; border-top: 1px solid #e5e7eb;
            font-size: 9px; color: #9ca3af; text-align: center;
        }
        .badge { color: {{ $colors["600"] }}; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    @if ($logo)
                        <img src="{{ $logo }}" class="logo-img" alt="{{ $company }}">
                    @endif
                    <div class="company">{{ $company }}</div>
                    <div class="address">{{ $address }}</div>
                </td>
                <td class="doc-title">
                    <div class="title">{{ __('Reporte Financiero') }}</div>
                    <div class="meta">{{ $period['label'] }}</div>
                    <div class="meta">{{ __('Generated') }}: {{ $generatedAt }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data summary" cellspacing="0">
        <tr>
            <td class="strong">{{ __('Total Facturado') }}</td>
            <td class="amount strong">{{ money($period['invoiced']) }}</td>
            <td class="strong">{{ __('New Customers') }}</td>
            <td class="amount">{{ $period['newCustomers'] }}</td>
        </tr>
        <tr>
            <td class="strong" style="color: {{ $colors['800'] }};">{{ __('Ganancia Servicios') }}</td>
            <td class="amount strong" style="color: {{ $colors['800'] }};">{{ money($period['earnings']) }}</td>
            <td class="strong">{{ __('Requests') }}</td>
            <td class="amount">{{ $period['requests'] }}</td>
        </tr>
        <tr>
            <td class="strong">{{ __('Total Cobrado') }}</td>
            <td class="amount strong">{{ money($period['collected']) }}</td>
            <td class="strong">{{ __('Packages') }}</td>
            <td class="amount">{{ $period['packages'] }}</td>
        </tr>
        <tr>
            <td class="strong">{{ __('Saldo por Cobrar') }}</td>
            <td class="amount strong">{{ money($period['balance']) }}</td>
            <td class="strong">{{ __('Shipments') }}</td>
            <td class="amount">{{ $period['shipments'] }}</td>
        </tr>
    </table>

    @if ($revenue)
        <div class="section-title">{{ __('Ingresos por Período') }}</div>
        <table class="data" cellspacing="0">
            <thead>
                <tr>
                    <th>{{ __('Period') }}</th>
                    <th class="amount">{{ __('Cobrado') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($revenue as $row)
                    <tr>
                        <td>{{ $row['label'] }}</td>
                        <td class="amount">{{ money($row['total']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="section-title">{{ __('Payments') }} ({{ count($payments) }})</div>
    @if ($payments)
        <table class="data" cellspacing="0">
            <thead>
                <tr>
                    <th>{{ __('Number') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th>{{ __('Method') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th class="amount">{{ __('Total Facturado') }}</th>
                    <th class="amount">{{ __('Ganancia Servicios') }}</th>
                    <th class="amount">{{ __('Pagado') }}</th>
                    <th class="amount">{{ __('Saldo') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $p)
                    <tr>
                        <td>{{ $p['number'] }}</td>
                        <td>{{ $p['customer'] }}</td>
                        <td>{{ $p['method'] }}</td>
                        <td>{{ $p['date'] }}</td>
                        <td class="amount">{{ number_format($p['invoice_total'], 2) }}</td>
                        <td class="amount">{{ number_format($p['service_profit'], 2) }}</td>
                        <td class="amount">{{ number_format($p['amount_paid'], 2) }}</td>
                        <td class="amount">{{ number_format($p['balance'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4">{{ __('Total') }}</td>
                    <td class="amount">{{ number_format($period['invoiced'], 2) }}</td>
                    <td class="amount">{{ number_format($period['earnings'], 2) }}</td>
                    <td class="amount">{{ number_format($period['collected'], 2) }}</td>
                    <td class="amount">{{ number_format($period['balance'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <p>{{ __('No records found.') }}</p>
    @endif

    <div class="footer">
        {{ $company }} — {{ $address }} · {{ __('Generated') }}: {{ $generatedAt }}
    </div>
</body>
</html>
