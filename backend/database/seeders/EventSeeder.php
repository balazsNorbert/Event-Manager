<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'john@example.com')->first();

        if ($user) {
            Event::factory()
                ->count(10)
                ->create([
                    'user_id' => $user->id,
                ]);
            $this->command->info('10 events created for john@example.com');
        } else {
            $this->command->error('User not found!');
        }
    }
}
