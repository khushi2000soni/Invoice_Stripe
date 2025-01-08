<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users[0] = [
            'name'           => 'Super Admin',
            'email'          => 'superadmin@invoice.com',
            'username'       => 'superadmin',
            'password'       => Hash::make('12345678'),
            'remember_token' => null,
            'auth_pin'        => null,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'created_by'     => 1,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
        $users[1] = [
            'name'           => 'Administrator',
            'email'          => 'admin@invoice.com',
            'username'       => 'admin',
            'password'       => Hash::make('12345678'),
            'remember_token' => null,
            'auth_pin'        => null,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'created_by'     => 1,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        $users[2] = [
            'name'           => 'Accountant',
            'email'          => 'accountant@invoice.com',
            'username'       => 'accountant',
            'password'       => Hash::make('12345678'),
            'remember_token' => null,
            'auth_pin'        => null,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'created_by'     => 1,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        $users[3] = [
            'name'           => 'Staff',
            'email'          => 'staff@invoice.com',
            'username'       => 'staff',
            'password'       => Hash::make('12345678'),
            'remember_token' => null,
            'auth_pin'        => null,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'created_by'     => 1,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        foreach($users as $key=>$user){
            $createdUser =  User::create($user);
        }
    }
}
