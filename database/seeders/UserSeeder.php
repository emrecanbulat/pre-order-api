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
    public function run(): void
    {
        User::factory(1)->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'phone' => '+905469339509',
            'role' => 'admin'
        ]);
        User::factory(1)->create([
            'name' => 'User',
            'email' => 'user@user.com',
            'phone' => '+15005550010',
            'role' => 'user'
        ]);
        User::factory(8)->create();
    }
}
