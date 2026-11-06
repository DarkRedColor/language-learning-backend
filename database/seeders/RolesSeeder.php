<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::updateOrCreate(['name' => 'admin']);
        Role::updateOrCreate(['name' => 'user']);
        Role::updateOrCreate(['name' => 'guest']);
        Role::updateOrCreate(['name' => 'project']);
        Role::updateOrCreate(['name' => 'qa']);
        Role::updateOrCreate(['name' => 'content']);
    }
}
