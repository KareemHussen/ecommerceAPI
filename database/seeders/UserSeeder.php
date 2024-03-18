<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supderAdminRole = Role::create(['name' => 'supderAdmin']);
        $adminRole = Role::create(['name' => 'admin']);
        $clientRole = Role::create(['name' => 'client']);
        $vendorRole = Role::create(['name' => 'vendor']);

        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super-admin@super.con',
            'password' => bcrypt('12345678'),
        ]);

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345678'),
        ]);

        $client = User::create([
            'name' => 'Kimo',
            'email' => 'kareemhussen500@gmail.com',
            'password' => bcrypt('12345678'),
        ]);


        $superAdmin->assignRole($supderAdminRole);
        $admin->assignRole($adminRole);
        $client->assignRole($clientRole);


    }
}
