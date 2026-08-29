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
            <td style="background-color: #059669; padding: 24px 32px; text-align: center;">
                <h1 style="color: #ffffff; font-size: 22px; font-weight: bold; margin: 0; letter-spacing: -0.5px;">{{ $companyName }}</h1>
                <p style="color: #d1fae5; font-size: 13px; margin: 4px 0 0 0;">
                    {{ $locale === 'es' ? 'Factura Oficial' : 'Official Invoice' }} #{{ $purchaseRequest->number }}
                </p>
            </td>
        </tr>

        {{-- Body --}}
        <tr>
            <td style="padding: 32px 32px 24px 32px;">
                <h2 style="font-size: 17px; font-weight: 600; color: #111827; margin: 0 0 16px 0;">
                    {{ $locale === 'es'
                        ? '¡Hola ' . ($purchaseRequest->customer?->name ? e($purchaseRequest->customer->name) : '') . '! Tu factura ha sido emitida.'
                        : 'Hello ' . ($purchaseRequest->customer?->name ? e($purchaseRequest->customer->name) : '') . '! Your invoice has been issued.' }}
                </h2>

                <p style="font-size: 14px; line-height: 1.6; color: #4b5563; margin: 0 0 16px 0;">
                    {{ $locale === 'es'
                        ? 'Te informamos que se ha generado la factura correspondiente a tu pedido. En el archivo PDF adjunto a este correo encontrarás el desglose completo de productos, comisiones y tarifas aplicadas.'
                        : 'Please be advised that your invoice has been generated. In the PDF attached to this email, you will find the complete breakdown of items, commissions, and applied fees.' }}
                </p>

                {{-- Summary Table --}}
                <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 20px 0;">
                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 13px; color: #374151;">
                        <tr>
                            <td style="padding: 6px 0; color: #6b7280;">{{ $locale === 'es' ? 'Factura N°:' : 'Invoice #:' }}</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: bold; color: #111827;">{{ $purchaseRequest->number }}</td>
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
                            <td style="padding: 6px 0; color: #059669;">{{ $locale === 'es' ? 'Monto Pagado:' : 'Amount Paid:' }}</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: bold; color: #059669;">${{ number_format($paidAmount, 2) }}</td>
                        </tr>
                        <tr style="border-top: 1px dashed #d1d5db;">
                            <td style="padding: 8px 0 0 0; font-weight: bold; color: {{ $balance > 0 ? '#b45309' : '#059669' }};">
                                {{ $locale === 'es' ? 'Saldo por Pagar:' : 'Balance Due:' }}
                            </td>
                            <td style="padding: 8px 0 0 0; text-align: right; font-weight: bold; font-size: 14px; color: {{ $balance > 0 ? '#b45309' : '#059669' }};">
                                ${{ number_format($balance, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- PDF Attachment Reminder Box --}}
                <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 14px 16px; margin: 20px 0;">
                    <p style="font-size: 13px; color: #065f46; margin: 0; line-height: 1.5;">
                        <strong style="color: #047857;">📎 {{ $locale === 'es' ? 'Documento PDF Adjunto' : 'Attached PDF Document' }}:</strong><br>
                        {{ $locale === 'es'
                            ? 'Descarga el PDF adjunto para imprimir o guardar tu factura oficial con código QR.'
                            : 'Download the attached PDF to print or save your official invoice with QR code.' }}
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
