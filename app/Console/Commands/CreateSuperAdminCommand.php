<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdminCommand extends Command
{
    protected $signature = 'app:create-super-admin 
                            {--name= : Name of the Super Administrator}
                            {--email= : Email address}
                            {--mobile= : Mobile number}
                            {--password= : Password (leave empty to be prompted securely)}';

    protected $description = 'Provision or update a Super Administrator account securely';

    public function handle(): int
    {
        $this->info('--- TECH ERA COMPUTER: Super Admin Provisioning ---');

        $name = $this->option('name') ?: $this->ask('Super Admin Full Name', env('SUPER_ADMIN_NAME', 'TECH ERA Super Admin'));
        $email = $this->option('email') ?: $this->ask('Super Admin Email', env('SUPER_ADMIN_EMAIL', 'admin@example.com'));
        $mobile = $this->option('mobile') ?: $this->ask('Super Admin Mobile Number', env('SUPER_ADMIN_MOBILE', '9942434239'));

        $password = $this->option('password');
        if (empty($password)) {
            $password = $this->secret('Enter Secure Password (min 8 characters)');
            $confirmPassword = $this->secret('Confirm Secure Password');

            if ($password !== $confirmPassword) {
                $this->error('Passwords do not match. Aborting.');
                return self::FAILURE;
            }
        }

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'mobile' => $mobile,
            'password' => $password,
        ], [
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'mobile' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        $user = User::updateOrCreate(
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

        $superRole = Role::firstOrCreate(['name' => 'super_admin'], [
            'display_name' => 'Super Administrator',
            'description' => 'Full administrative access',
        ]);
        $user->roles()->syncWithoutDetaching([$superRole->id]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'super_admin_cli_setup',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'new_values' => [
                'email' => $email,
                'name' => $name,
                'via' => 'CLI Command app:create-super-admin',
            ],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Artisan CLI',
        ]);

        $this->info("Super Admin account successfully configured for: {$email}");
        return self::SUCCESS;
    }
}
