<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;

test('user seeder creates the trade admin only once', function () {
    config()->set('hashing.bcrypt.rounds', 12);

    $this->seed(UserSeeder::class);
    $this->seed(UserSeeder::class);

    $admin = User::query()->where('email', 'admin@sevaratrade.com')->sole();

    expect($admin->name)->toBe('Sevara Trade Admin')
        ->and($admin->role)->toBe(UserRole::Admin)
        ->and(Hash::isHashed($admin->password))->toBeTrue();
});
