<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'TECH ERA COMPUTER', 'group' => 'general'],
            ['key' => 'institute_name', 'value' => 'TECH ERA COMPUTER', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Building Digital Skills for the Future', 'group' => 'general'],
            ['key' => 'institute_tagline', 'value' => 'Building Digital Skills for the Future', 'group' => 'general'],
            ['key' => 'helpline_phone', 'value' => '9162384660', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '9162384660', 'group' => 'contact'],
            ['key' => 'whatsapp_number', 'value' => '9162384660', 'group' => 'contact'],
            ['key' => 'corporate_office', 'value' => 'Sasbahana, Sheikhpura, Bihar - 801105', 'group' => 'contact'],
            ['key' => 'corporate_address', 'value' => 'Sasbahana, Sheikhpura, Bihar - 801105', 'group' => 'contact'],
            ['key' => 'institute_logo', 'value' => 'images/tech-era-logo.png', 'group' => 'branding'],
            ['key' => 'receipt_prefix', 'value' => 'TEC-RCP', 'group' => 'financial'],
            ['key' => 'certificate_prefix', 'value' => 'TEC/CERT', 'group' => 'financial'],
            ['key' => 'marksheet_prefix', 'value' => 'TEC/MS', 'group' => 'financial'],
            ['key' => 'currency_symbol', 'value' => '₹', 'group' => 'financial'],
            ['key' => 'parent_foundation', 'value' => 'TECH ERA Educational & Skill Development Society', 'group' => 'legal'],
            ['key' => 'affiliation_text', 'value' => 'Premier Computer & Technical Education Centre', 'group' => 'legal'],
            ['key' => 'total_franchises_count', 'value' => '120', 'group' => 'stats'],
            ['key' => 'total_states_count', 'value' => '12', 'group' => 'stats'],
            ['key' => 'total_cities_count', 'value' => '45', 'group' => 'stats'],
            ['key' => 'total_courses_count', 'value' => '24', 'group' => 'stats'],
        ];

        foreach ($settings as $item) {
            Setting::updateOrCreate(['key' => $item['key']], $item);
        }
    }
}
