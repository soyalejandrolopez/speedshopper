<?php

namespace Database\Seeders;

use App\Enums\CostType;
use App\Enums\PackageStatus;
use App\Enums\PaymentMethod;
use App\Enums\RequestStatus;
use App\Enums\ShipmentStatus;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@speedshopper.com'],
            [
                'name' => 'Dueña SpeedShopper',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        $mariaUser = User::firstOrCreate(
            ['email' => 'maria@example.com'],
            [
                'name' => 'María González',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $mariaUser->assignRole('client');

        $maria = Customer::create([
            'user_id' => $mariaUser->id,
            'name' => 'María González',
            'email' => 'maria@example.com',
            'whatsapp' => '+50255551234',
            'address' => 'Zona 10, Ciudad de Guatemala',
            'city' => 'Guatemala City',
            'country' => 'GT',
            'registered_at' => now()->subMonths(3),
        ]);

        $carlos = Customer::create([
            'name' => 'Carlos Pérez',
            'email' => 'carlos@example.com',
            'whatsapp' => '+52155556666',
            'address' => 'Av. Reforma 200, CDMX',
            'city' => 'Ciudad de México',
            'country' => 'MX',
            'registered_at' => now()->subMonths(1),
        ]);

        $request1 = PurchaseRequest::create([
            'customer_id' => $maria->id,
            'product_name' => 'Nike Air Max 270',
            'product_url' => 'https://www.nike.com/air-max-270',
            'store' => 'Nike.com',
            'description' => 'Talla 7.5 mujer, color blanco',
            'size_color' => '7.5 / Blanco',
            'quantity' => 1,
            'unit_price' => 69.99,
            'discount_found' => 15.00,
            'status' => RequestStatus::Received,
            'notes' => 'Cliente envió link por WhatsApp',
        ]);

        $request1->costItems()->createMany([
            ['type' => CostType::ProductCost, 'description' => 'Producto', 'amount' => 69.99],
            ['type' => CostType::SalesTax, 'description' => 'Sales tax 8.25%', 'amount' => 5.77],
            ['type' => CostType::UsShipping, 'description' => 'Envío dentro de USA', 'amount' => 0.00],
            ['type' => CostType::ShopperFee, 'description' => 'Fee personal shopper', 'amount' => 10.00],
        ]);

        $request2 = PurchaseRequest::create([
            'customer_id' => $maria->id,
            'product_name' => 'Victoria\'s Secret PINK Set',
            'product_url' => 'https://www.victoriassecret.com/pink-set',
            'store' => 'Victoria\'s Secret',
            'description' => 'Pijama set, talla M',
            'size_color' => 'M / Rosa',
            'quantity' => 1,
            'unit_price' => 42.50,
            'status' => RequestStatus::InTransit,
        ]);

        $package1 = Package::create([
            'customer_id' => $maria->id,
            'purchase_request_id' => $request1->id,
            'store' => 'Nike.com',
            'original_tracking' => '1Z999AA10123456784',
            'received_at' => now()->subDays(2),
            'weight_lb' => 3.2,
            'location' => 'Estante A-3',
            'status' => PackageStatus::Storing,
        ]);

        $package2 = Package::create([
            'customer_id' => $maria->id,
            'purchase_request_id' => $request2->id,
            'store' => 'Victoria\'s Secret',
            'original_tracking' => '94001118992231974284',
            'received_at' => now()->subDays(1),
            'weight_lb' => 1.8,
            'location' => 'Estante A-3',
            'status' => PackageStatus::Storing,
        ]);

        $package3 = Package::create([
            'customer_id' => $carlos->id,
            'store' => 'Amazon',
            'original_tracking' => 'TBA123456789000',
            'received_at' => now(),
            'weight_lb' => 5.4,
            'location' => 'Estante B-1',
            'status' => PackageStatus::Received,
        ]);

        $shipment = Shipment::create([
            'customer_id' => $maria->id,
            'carrier' => 'DHL Express',
            'destination_country' => 'GT',
            'final_weight_lb' => 5.0,
            'dimensions' => '12x10x8 in',
            'shipping_cost' => 68.00,
            'status' => ShipmentStatus::Ready,
            'notes' => 'Consolidación de 2 paquetes',
        ]);
        $shipment->packages()->attach([$package1->id, $package2->id]);

        $shipment->costItems()->createMany([
            ['type' => CostType::InternationalShipping, 'description' => 'Envío a Guatemala', 'amount' => 68.00],
            ['type' => CostType::PackingFee, 'description' => 'Empaque y protección', 'amount' => 5.00],
        ]);

        $payment = Payment::create([
            'customer_id' => $maria->id,
            'billable_type' => Shipment::class,
            'billable_id' => $shipment->id,
            'invoice_total' => 158.76,
            'amount_paid' => 66.26,
            'payment_method' => PaymentMethod::Zelle,
            'paid_at' => now()->subDay(),
            'notes' => 'Abono parcial por Zelle',
        ]);

        $payment2 = Payment::create([
            'customer_id' => $carlos->id,
            'invoice_total' => 120.00,
            'amount_paid' => 120.00,
            'payment_method' => PaymentMethod::Card,
            'paid_at' => now()->subDays(5),
        ]);
    }
}
