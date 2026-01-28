<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'buyer@example.com'],
            [
                'name' => '購入者ユーザー',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'postcode' => '123-4567',
                'address' => '東京都渋谷区1-2-3',
                'building' => 'テストビル101',
            ]
        );

        User::updateOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => '出品者ユーザー',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'postcode' => '987-6543',
                'address' => '東京都新宿区4-5-6',
                'building' => 'サンプルマンション202',
            ]
        );
    }
}
