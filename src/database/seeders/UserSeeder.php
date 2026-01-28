<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedProfileImages();

        User::updateOrCreate(
            ['email' => 'buyer@example.com'],
            [
                'name' => '購入者ユーザー',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'profile_image' => 'profiles/buyer.jpg',
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
                'profile_image' => 'profiles/seller.jpg',
                'postcode' => '987-6543',
                'address' => '東京都新宿区4-5-6',
                'building' => 'サンプルマンション202',
            ]
        );
    }

    private function seedProfileImages(): void
    {
        $disk = Storage::disk('public');

        $disk->makeDirectory('profiles');

        $map = [
            'buyer.jpg' => base_path('database/seeders/images/buyer.jpg'),
            'seller.jpg' => base_path('database/seeders/images/seller.jpg'),
        ];

        foreach ($map as $filename => $from) {
            if (!file_exists($from)) {
                continue;
            }

            $disk->put('profiles/' . $filename, file_get_contents($from));
        }
    }
}
