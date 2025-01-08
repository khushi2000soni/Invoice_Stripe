<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;


class RoleUserTableSeeder extends Seeder
{
    public function run()
    {
        User::findOrFail(1)->assignRole(1);
        User::findOrFail(2)->assignRole(2);
        User::findOrFail(3)->assignRole(3);
        User::findOrFail(4)->assignRole(4);
    }
}
