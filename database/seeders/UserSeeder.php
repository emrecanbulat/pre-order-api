<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory(1)->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'phone' => '05555555555',
            'role' => 'admin'
        ]);
        User::factory(9)->create();
    }
}
