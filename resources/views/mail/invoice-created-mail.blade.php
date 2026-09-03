@php
    $isQuoted = $purchaseRequest->status === \App\Enums\RequestStatus::Quoted;
    $isPaid = ($totalCost > 0 && $balance <= 0 && ! $isQuoted) || $purchaseRequest->status === \App\Enums\RequestStatus::Purchased;
    $isPending = ! $isPaid && ! $isQuoted;
    $customerName = $purchaseRequest->customer?->name ? e($purchaseRequest->customer->name) : '';

    $themeRamp = theme_color_ramp(theme_color());

    if ($isQuoted) {
        $headerColor = '#0284c7';
        $headerTitle = $isUpdate
            ? ($locale === 'es' ? 'Cotización Actualizada' : 'Updated Quote')
            : ($locale === 'es' ? 'Presupuesto / Cotización' : 'Quotation / Estimate');
        $greeting = $isUpdate
            ? ($locale === 'es' ? "¡Hola {$customerName}! Tu cotización ha sido actualizada." : "Hello {$customerName}! Your quote has been updated.")
            : ($locale === 'es' ? "¡Hola {$customerName}! Hemos preparado tu cotización." : "Hello {$customerName}! We have prepared your quotation.");
        $messageBody = $isUpdate
            ? ($locale === 'es' ? 'Te informamos que se han actualizado los conceptos y costos de tu cotización. En el PDF adjunto encontrarás el presupuesto detallado.' : 'Please be advised that your quote has been updated. In the attached PDF, you will find the detailed estimate.')
            : ($locale === 'es' ? 'Te enviamos el presupuesto detallado correspondiente a tu solicitud. Puedes revisar el desglose en el PDF adjunto.' : 'We have prepared the quotation for your request. You can check the full breakdown in the attached PDF.');
    } elseif ($isPaid) {
        $headerColor = $themeRamp['600'];
        $headerTitle = $isUpdate
            ? ($locale === 'es' ? 'Factura Pagada (Actualizada)' : 'Paid Invoice (Updated)')
            : ($locale === 'es' ? 'Factura Oficial Pagada' : 'Official Paid Invoice');
        $greeting = $isUpdate
            ? ($locale === 'es' ? "¡Hola {$customerName}! Tu factura pagada ha sido actualizada." : "Hello {$customerName}! Your paid invoice has been updated.")
            : ($locale === 'es' ? "¡Hola {$customerName}! Pago confirmado y factura emitida." : "Hello {$customerName}! Payment confirmed and invoice issued.");
        $messageBody = $locale === 'es'
            ? 'Confirmamos que tu factura se encuentra pagada en su totalidad. Encontrarás el comprobante oficial en el PDF adjunto.'
            : 'We confirm that your invoice is registered as paid in full. You will find the official receipt in the attached PDF.';
    } else {
        $headerColor = '#d97706';
        $headerTitle = $isUpdate
            ? ($locale === 'es' ? 'Actualización de Factura Pendiente' : 'Pending Invoice Update')
            : ($locale === 'es' ? 'Factura Pendiente de Pago' : 'Pending Invoice');
        $greeting = $isUpdate
            ? ($locale === 'es' ? "¡Hola {$customerName}! Tu factura ha sido actualizada." : "Hello {$customerName}! Your invoice has been updated.")
            : ($locale === 'es' ? "¡Hola {$customerName}! Tu factura ha sido emitida." : "Hello {$customerName}! Your invoice has been issued.");
        $messageBody = $isUpdate
            ? ($locale === 'es' ? 'Te informamos que se ha actualizado la factura de tu pedido. Encontrarás los métodos de pago (Zelle y PayPal) y el desglose en el PDF adjunto.' : 'Please be advised that your invoice has been updated. You will find available payment methods (Zelle & PayPal) and breakdown in the attached PDF.')
            : ($locale === 'es' ? 'Te informamos que se ha generado la factura correspondiente a tu pedido. En el archivo PDF adjunto encontrarás el desglose completo.' : 'Please be advised that your invoice has been generated. In the attached PDF, you will find the complete breakdown.');
    }
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $companyName }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 24px; color: #1f2937;">
    <table align="center" width="100%" max-width="600" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
        {{-- Header banner --}}
        <tr>
            <td style="background-color: {{ $headerColor }}; padding: 24px 32px; text-align: center;">
                <h1 style="color: #ffffff; font-size: 22px; font-weight: bold; margin: 0; letter-spacing: -0.5px;">{{ $companyName }}</h1>
                <p style="color: #ffffff; opacity: 0.95; font-size: 13px; margin: 4px 0 0 0; font-weight: 500;">
                    {{ $headerTitle }} #{{ $purchaseRequest->number }}
                </p>
            </td>
        </tr>

        {{-- Body --}}
        <tr>
            <td style="padding: 32px 32px 24px 32px;">
                <h2 style="font-size: 17px; font-weight: 600; color: #111827; margin: 0 0 16px 0;">
                    {{ $greeting }}
                </h2>

                <p style="font-size: 14px; line-height: 1.6; color: #4b5563; margin: 0 0 16px 0;">
                    {{ $messageBody }}
                </p>

                {{-- Summary Table --}}
                <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 20px 0;">
                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 13px; color: #374151;">
                        <tr>
                            <td style="padding: 6px 0; color: #6b7280;">{{ $locale === 'es' ? 'Documento N°:' : 'Document #:' }}</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: bold; color: #111827;">{{ $purchaseRequest->number }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #6b7280;">{{ $locale === 'es' ? 'Tipo / Estado:' : 'Type / Status:' }}</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: bold;">
                                @if ($isQuoted)
                                    <span style="color: #0284c7;">{{ $locale === 'es' ? 'Cotización' : 'Quote' }}</span>
                                @elseif ($isPaid)
                                    <span style="color: {{ $themeRamp['600'] }};">{{ $locale === 'es' ? 'Pagado' : 'Paid' }}</span>
                                @else
                                    <span style="color: #d97706;">{{ $locale === 'es' ? 'Pendiente de Pago' : 'Pending Payment' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #6b7280;">{{ $locale === 'es' ? 'Producto(s):' : 'Product(s):' }}</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: 500;">{{ $purchaseRequest->product_name }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #6b7280;">{{ $locale === 'es' ? 'Total Facturado:' : 'Total Invoiced:' }}</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: bold; color: #111827;">${{ number_format($totalCost, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: {{ $themeRamp['600'] }};">{{ $locale === 'es' ? 'Monto Pagado:' : 'Amount Paid:' }}</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: bold; color: {{ $themeRamp['600'] }};">${{ number_format($paidAmount, 2) }}</td>
                        </tr>
                        <tr style="border-top: 1px dashed #d1d5db;">
                            <td style="padding: 8px 0 0 0; font-weight: bold; color: {{ $balance > 0 ? '#b45309' : $themeRamp['600'] }};">
                                {{ $locale === 'es' ? 'Balance Pendiente:' : 'Pending Balance:' }}
                            </td>
                            <td style="padding: 8px 0 0 0; text-align: right; font-weight: bold; font-size: 14px; color: {{ $balance > 0 ? '#b45309' : $themeRamp['600'] }};">
                                ${{ number_format($balance, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Payment Methods Box (Only for Pending or Quote) --}}
                @if ($balance > 0 || $isQuoted || $isPending)
                    <div style="background-color: #f0fdf4; border: 1.5px solid #86efac; border-radius: 8px; padding: 14px 16px; margin: 20px 0;">
                        <p style="font-size: 12px; font-weight: bold; text-transform: uppercase; color: #166534; margin: 0 0 8px 0; letter-spacing: 0.5px;">
                            💳 {{ $locale === 'es' ? 'Métodos de Pago Disponibles:' : 'Available Payment Methods:' }}
                        </p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 13px; color: #14532d;">
                            <tr>
                                <td style="padding: 3px 0; font-weight: bold; width: 80px;">Zelle:</td>
                                <td style="padding: 3px 0; font-family: monospace; font-weight: bold;">Gomez.Lilibeth1977@gmail.com</td>
                            </tr>
                            <tr>
                                <td style="padding: 3px 0; font-weight: bold; width: 80px;">PayPal:</td>
                                <td style="padding: 3px 0; font-family: monospace; font-weight: bold;">@speedingshopper</td>
                            </tr>
                        </table>
                        <p style="font-size: 11px; color: #166534; margin: 8px 0 0 0;">
                            {{ $locale === 'es'
                                ? '• Por favor enviar el comprobante de pago indicando el N° de factura #' . $purchaseRequest->number
                                : '• Please include invoice #' . $purchaseRequest->number . ' in your payment reference' }}
                        </p>
                    </div>
                @endif

                {{-- Warehouse Address Box (When Paid) --}}
                @if ($isPaid)
                    <div style="background-color: #ecfdf5; border: 1.5px solid #a7f3d0; border-radius: 8px; padding: 14px 16px; margin: 20px 0;">
                        <p style="font-size: 12px; font-weight: bold; text-transform: uppercase; color: #065f46; margin: 0 0 6px 0; letter-spacing: 0.5px;">
                            📍 {{ $locale === 'es' ? 'Dirección de Entrega y Almacén (USA):' : 'USA Delivery & Warehouse Address:' }}
                        </p>
                        <p style="font-size: 14px; font-weight: bold; color: #111827; margin: 0 0 4px 0;">
                            7835 Wood Hollow Dr Baytown Tx 77521
                        </p>
                        <p style="font-size: 12px; color: #065f46; margin: 0;">
                            {{ $locale === 'es'
                                ? 'Tus compras y paquetes son recibidos y procesados en nuestra dirección principal.'
                                : 'Your purchases and packages are received and processed at our main warehouse address.' }}
                        </p>
                    </div>
                @endif

                {{-- PDF Attachment Reminder Box --}}
                <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 14px 16px; margin: 20px 0;">
                    <p style="font-size: 13px; color: #065f46; margin: 0; line-height: 1.5;">
                        <strong style="color: #047857;">📎 {{ $locale === 'es' ? 'Documento PDF Adjunto' : 'Attached PDF Document' }}:</strong><br>
                        {{ $locale === 'es'
                            ? 'Descarga el PDF adjunto para imprimir o guardar tu documento oficial con código QR y detalles de pago.'
                            : 'Download the attached PDF to print or save your official document with QR code and payment details.' }}
                    </p>
                </div>

                <p style="font-size: 13px; color: #6b7280; margin: 24px 0 0 0; line-height: 1.5;">
                    {{ $locale === 'es'
                        ? 'Si tienes alguna pregunta sobre tu pedido, puedes responder directamente a este correo.'
                        : 'If you have any questions about your order, please reply directly to this email.' }}
                </p>
            </td>
        </tr>

        {{-- Footer --}}
        <tr>
            <td style="background-color: #f9fafb; padding: 20px 32px; text-align: center; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af;">
                <p style="margin: 0 0 4px 0; font-weight: 600; color: #6b7280;">{{ $companyName }}</p>
                <p style="margin: 0;">&copy; {{ date('Y') }} {{ $companyName }}. {{ $locale === 'es' ? 'Todos los derechos reservados.' : 'All rights reserved.' }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
