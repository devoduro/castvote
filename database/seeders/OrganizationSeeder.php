<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        // Primary org: Ghana Music Awards
        $gma = Organization::create([
            'name'                   => 'Ghana Music Awards Ltd',
            'contact_email'          => 'admin@ghanamusicawards.com',
            'momo_settlement_number' => '0244000001',
            'data_protection_reg_no' => 'DPC-2024-0042',
        ]);

        Admin::create([
            'organization_id' => $gma->id,
            'name'            => 'Kwame Asante',
            'email'           => 'admin@castvote.test',
            'password'        => Hash::make('password'),
            'role'            => 'owner',
        ]);

        Admin::create([
            'organization_id' => $gma->id,
            'name'            => 'Ama Boateng',
            'email'           => 'manager@castvote.test',
            'password'        => Hash::make('password'),
            'role'            => 'manager',
        ]);

        Admin::create([
            'organization_id' => $gma->id,
            'name'            => 'Kofi Mensah',
            'email'           => 'viewer@castvote.test',
            'password'        => Hash::make('password'),
            'role'            => 'viewer',
        ]);

        // Second org: University of Ghana SRC
        $ug = Organization::create([
            'name'                   => 'University of Ghana SRC',
            'contact_email'          => 'src@ug.edu.gh',
            'momo_settlement_number' => '0201000002',
            'data_protection_reg_no' => 'DPC-2024-0078',
        ]);

        Admin::create([
            'organization_id' => $ug->id,
            'name'            => 'Abena Owusu',
            'email'           => 'src.admin@castvote.test',
            'password'        => Hash::make('password'),
            'role'            => 'owner',
        ]);
    }
}
