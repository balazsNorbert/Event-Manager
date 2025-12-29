<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'john@example.com')->exists()) {
            User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('jelszo123'),
                'role' => 'user'
            ]);
        }

        if (!User::where('email', 'agent@example.com')->exists()) {
            User::create([
                'name' => 'Agent Smith',
                'email' => 'agent@example.com',
                'password' => Hash::make('agent123'),
                'role' => 'agent'
            ]);
        }
    }
}
