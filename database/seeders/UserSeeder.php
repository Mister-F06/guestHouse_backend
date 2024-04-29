<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(!User::where('email' , 'admin@mail.com')->first()){
            User::create([
                'firstname' => 'Admin',
                'lastname'  => 'GuestHouse',
                'telephone' => '44444444',
                'email'     => 'admin@mail.com',
                'password'  => bcrypt('password'),
                'role_id'   => Role::where('name' , 'admin')->first()->id
            ]);
        }
    }
}
