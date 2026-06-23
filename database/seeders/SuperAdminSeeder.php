<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::firstOrCreate(
            ['contact_email' => 'admin@castvote.com.gh'],
            ['name' => 'CastVote Ghana']
        );

        $email = env('SUPERADMIN_EMAIL', 'superadmin@castvote.com.gh');

        if (Admin::where('email', $email)->exists()) {
            $this->command->info("Superadmin [{$email}] already exists — skipping.");
            return;
        }

        Admin::create([
            'organization_id' => $org->id,
            'name'            => 'CastVote Superadmin',
            'email'           => $email,
            'password'        => Hash::make(env('SUPERADMIN_PASSWORD', 'changeme123')),
            'role'            => 'owner',
            'account_status'  => 'approved',
            'is_superadmin'   => true,
        ]);

        $this->command->info("Superadmin created: {$email}");
        $this->command->warn('Remember to change the default password!');
    }
}
