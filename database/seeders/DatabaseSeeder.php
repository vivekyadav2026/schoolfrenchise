<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            GeographicSeeder::class,
            CourseSeeder::class,
            CentreSeeder::class,
            StudentAndCertificateSeeder::class,
            CmsSeeder::class,
            RolesAndPermissionsSeeder::class,
            SuperAdminSeeder::class,
            ManagementFoundationSeeder::class,
        ]);
    }
}
