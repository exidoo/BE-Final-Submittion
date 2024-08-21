<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Ambil role admin dari tabel roles
       $rolesAdmin = Roles::where('name', 'admin')->first();
       // Ambil role owner dari tabel roles
       $rolesOwner = Roles::where('name', 'owner')->first();

       // Buat user admin
       User::create([
           'name' => 'admin',
           'email' => 'admin@gmail.com',
           'role_id' => $rolesAdmin->id,
           'password' => Hash::make('password'),
       ]);

       // Buat user owner
       User::create([
           'name' => 'Romi',
           'email' => 'owner@gmail.com',
           'role_id' => $rolesOwner->id,
           'password' => Hash::make('password'),
       ]);
    }
}
