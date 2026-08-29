<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'company_name' => 'SpeedShopper',
            'warehouse_address' => '7835 Wood Hollow Dr, Baytown, TX 77521, USA',
            'whatsapp_phone' => '+13462333199',
            'countries_served' => 'VE,CO,EC,PE,CL,CR,PA,DO,SV,HN,MX',
            'shopper_fee' => '10.00',
            'shopper_fee_is_percent' => '0',
            'receiving_fee' => '2.50',
            'packing_fee' => '5.00',
            'currency' => 'USD',
            'notify_email' => '1',
            'notify_whatsapp' => '0',
            'whatsapp_api_url' => '',
            'whatsapp_api_token' => '',
            'logo_path' => '',
            'favicon_path' => '',
            'theme_color' => '#059669',
            'mail_enabled' => '0',
            'mail_host' => '',
            'mail_port' => '587',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'info@speedingshopper.com',
            'mail_from_name' => 'SpeedShopper',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
