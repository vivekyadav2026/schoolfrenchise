<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('SUPER_ADMIN_NAME', 'TECH ERA Super Admin');
        $email = env('SUPER_ADMIN_EMAIL', 'admin@drishticomputer.com');
        $password = env('SUPER_ADMIN_PASSWORD', 'Admin@12345');
        $mobile = env('SUPER_ADMIN_MOBILE', '9942434239');

        $superAdmin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'mobile' => $mobile,
                'password' => Hash::make($password),
                'role' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $superRole = Role::where('name', 'super_admin')->first();
        if ($superRole) {
            $superAdmin->roles()->syncWithoutDetaching([$superRole->id]);
        }

        // Record initialization in audit logs
        AuditLog::create([
            'user_id' => $superAdmin->id,
            'action' => 'super_admin_provisioned',
            'auditable_type' => User::class,
            'auditable_id' => $superAdmin->id,
            'new_values' => [
                'email' => $email,
                'role' => 'super_admin',
                'provisioned_via' => 'SuperAdminSeeder',
                'timestamp' => now()->toIso8601String(),
            ],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Artisan CLI / Seeder',
        ]);
    }
}
