# SpeedShopper — Client & Package Management System

Sistema de gestión para un negocio de **personal shopper y reenvío de paquetes en Baytown, TX** con envíos a Latinoamérica. Construido con **Laravel 12+** (PHP 8.3+), **Livewire 3**, **Alpine.js**, **Tailwind CSS 4**, **Flowbite** y **Lucide**.

## Funcionalidades

### Página pública (`/`)
- Landing con "Cómo funciona" (4 pasos), tarifas y países atendidos
- Formulario público **"Solicitar una compra"** (crea cliente + solicitud automáticamente)
- Contacto directo por WhatsApp (número configurable)
- Bilingüe **ES/EN** con selector de idioma

### Panel Admin (`/dashboard`)
- Dashboard con métricas: clientes, solicitudes abiertas, paquetes recibidos hoy, envíos en tránsito, balance por cobrar
- **Clientes**: número auto (CUST-0001), WhatsApp, dirección, país, notas, balance
- **Solicitudes de compra**: número auto (REQ-0001), link del producto, tienda, descuento encontrado, costos (producto, sales tax, shipping USA, fee shopper) y transición de estados
- **Paquetes**: número auto (PKG-0001), tracking original, peso, foto, ubicación en bodega
- **Envíos / Consolidación**: número auto (BOX-0001), selección de paquetes → caja, transportista, tracking internacional, costos
- **Pagos**: total facturado, monto pagado, balance, método de pago
- **Configuración**: fees (shopper, recepción, empaque), dirección de bodega, WhatsApp, países

### Portal del cliente (`/portal`)
- Resumen personalizado: balance pendiente, pedidos, cajas
- Mis solicitudes (crear nuevas), mis paquetes, mis envíos, mis pagos
- Cada cliente **solo ve sus propios registros** (enforzado por Policies)

### API REST (`/api/v1`, Sanctum)
- Auth por tokens (`POST /api/v1/login`, `GET /api/v1/me`)
- CRUD: `customers`, `requests`, `packages`, `shipments`, `payments`
- Los clientes solo reciben sus propios recursos; campos sensibles (notas) solo para admin

## Flujo de estados

```
Solicitud:  Solicitud recibida → Cotización enviada → Esperando pago → Comprado
            → En tránsito a Baytown → Recibido (→ Cancelado en cualquier punto)
Paquete:    Recibido → En bodega → Empacando → Listo para envío → Enviado → Entregado
Envío:      Borrador → Listo para envío → En tránsito → Entregado
```

Cada transición queda registrada en `statuses_history` (de → a, nota, usuario, fecha).

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12+, PHP 8.3+, SQLite (dev) |
| Auth | Laravel Breeze (Livewire stack) + Fortify-style flows |
| API Auth | Laravel Sanctum |
| Roles/Permisos | Spatie Laravel Permission (`admin`, `client`) |
| UI | Blade + Livewire 3 + Alpine.js + Tailwind CSS 4 + Flowbite + Lucide |
| Validación | Laravel Form Requests |
| Autorización | Policies + Gates |
| i18n | Español (default) / Inglés |
| Tests | Pest (63 tests) |

## Instalación

```sh
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

## Usuarios demo (seeder)

| Rol | Email | Password |
|---|---|---|
| Admin | `admin@speedshopper.com` | `password` |
| Cliente | `maria@example.com` | `password` |

## Estructura

```
app/
├── Concerns/            # GeneratesNumbers, TracksStatuses, HasCosts, ValidatesWithFormRequest
├── Enums/               # RequestStatus, PackageStatus, ShipmentStatus, CostType, PaymentMethod
├── Http/
│   ├── Controllers/Api/V1/
│   ├── Requests/        # Form Requests (admin + portal + API comparten reglas)
│   └── Resources/       # API Resources
├── Livewire/
│   ├── Admin/           # Dashboard + CRUDs
│   ├── Portal/          # Cuenta del cliente
│   └── PublicRequestForm.php
├── Models/              # Customer, PurchaseRequest, Package, Shipment, Payment, CostItem, StatusHistory, Setting
└── Policies/            # CustomerPolicy, PurchaseRequestPolicy, PackagePolicy, ShipmentPolicy, PaymentPolicy
```

## Tests

```sh
./vendor/bin/pest
```

Cobertura: policies (clientes aislados), flujo de estados, CRUDs Livewire, portal, API Sanctum (scoping por rol), generación de números y enums.

## Producción

- Cambiar `DB_CONNECTION` a `mysql`/`pgsql` en `.env`
- Configurar `MAIL_*` para verificación de email
- Ajustar fees, WhatsApp y países en **Configuración** (panel admin)
- Cambiar las contraseñas demo antes de salir a producción
