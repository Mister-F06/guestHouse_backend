<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Role::where('name' , 'admin')->first()) {
            Role::create([
                'name'  => 'admin',
                'label'=> 'Administrateur'
            ]);
        }

        if (!Role::where('name' , 'manager')->first()) {
            Role::create([
                'name'  => 'manager',
                'label' => 'Gérant'
            ]);
        }
    }
}
