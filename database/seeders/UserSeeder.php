<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@sevaratrade.com'],
            [
                'name' => 'Sevara Trade Admin',
                'password' => '$2y$12$Tu/7MSMN4geqOYBQPXNTF.W6q6ZviOjdGBubLERJgIBb.Y1IU7pgW',
                'role' => UserRole::Admin,
            ],
        );
    }
}
