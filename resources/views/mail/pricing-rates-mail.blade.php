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
                    {{ $locale === 'es' ? 'Lista de Tarifas y Servicios' : 'Official Pricing & Services Guide' }}
                </p>
            </td>
        </tr>

        {{-- Body --}}
        <tr>
            <td style="padding: 32px 32px 24px 32px;">
                <h2 style="font-size: 17px; font-weight: 600; color: #111827; margin: 0 0 16px 0;">
                    {{ $locale === 'es' ? '¡Hola! Te compartimos nuestra lista de tarifas oficial.' : 'Hello! Here is our official rate sheet and services guide.' }}
                </h2>

                <p style="font-size: 14px; line-height: 1.6; color: #4b5563; margin: 0 0 16px 0;">
                    {{ $locale === 'es'
                        ? 'Adjunto a este correo encontrarás el documento en formato PDF con el desglose detallado de nuestras tarifas para compras personales (Personal Shopper), reempaques de cajas Heavy Duty, comisión de almacén y condiciones de almacenaje.'
                        : 'Attached to this email you will find the PDF document with the detailed breakdown of our rates for Personal Shopper purchases, Heavy Duty box repackaging, warehouse commission, and storage terms.' }}
                </p>

                @if (! empty($customMessage))
                    <div style="background-color: #f9fafb; border-left: 4px solid #059669; padding: 12px 16px; margin: 20px 0; border-radius: 4px;">
                        <p style="font-size: 13.5px; line-height: 1.5; color: #374151; margin: 0; font-style: italic;">
                            "{{ $customMessage }}"
                        </p>
                    </div>
                @endif

                {{-- Highlights box --}}
                <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 16px; margin: 24px 0;">
                    <h3 style="font-size: 14px; font-weight: bold; color: #065f46; margin: 0 0 8px 0;">
                        {{ $locale === 'es' ? 'Resumen de Servicios Incluidos en el PDF:' : 'Summary of Services in Attached PDF:' }}
                    </h3>
                    <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #047857; line-height: 1.5;">
                        <li>{{ $locale === 'es' ? 'Compras Personales por tramos (20% - 15%) con horas y tiendas asignadas' : 'Personal Shopper tiered commission (20% - 15%) with allocated stores & hours' }}</li>
                        <li>{{ $locale === 'es' ? 'Reempaque en Cajas Heavy Duty (Small $15, Medium $20, Large $25)' : 'Heavy Duty Box Repackaging (Small $15, Medium $20, Large $25)' }}</li>
                        <li>{{ $locale === 'es' ? 'Entrega y recepción en almacén' : 'Warehouse drop-off & receiving' }}</li>
                    </ul>
                </div>

                <p style="font-size: 13px; color: #6b7280; margin: 24px 0 0 0; line-height: 1.5;">
                    {{ $locale === 'es'
                        ? 'Si tienes alguna duda o deseas cotizar una compra especial, puedes responder a este correo o escribirnos por WhatsApp.'
                        : 'If you have any questions or would like a quote for a special order, feel free to reply to this email or reach us via WhatsApp.' }}
                </p>
            </td>
        </tr>

        {{-- Footer --}}
        <tr>
            <td style="background-color: #f9fafb; padding: 18px 32px; text-align: center; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af;">
                <strong>{{ $companyName }}</strong> &bull; {{ $locale === 'es' ? 'Todos los derechos reservados.' : 'All rights reserved.' }}
            </td>
        </tr>
    </table>
</body>
</html>
