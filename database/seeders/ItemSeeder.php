<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $sellerId = User::where('email', 'seller@example.com')->value('id');
        if (!$sellerId) {
            $this->command->warn('seller@example.com が見つかりません。先にUserSeederを実行してね。');
            return;
        }

        $seedDir = base_path('database/seeders/images');

        // 商品データ一覧
        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition_jp' => '良好',
                'image_file' => 'watch.jpg',
                'category' => 'アクセサリー',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'condition_jp' => '目立った傷や汚れなし',
                'image_file' => 'hdd.jpg',
                'category' => '家電',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'condition_jp' => 'やや傷や汚れあり',
                'image_file' => 'onion.jpg',
                'category' => 'キッチン',
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'condition_jp' => '状態が悪い',
                'image_file' => 'shoes.jpg',
                'category' => 'ファッション',
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'condition_jp' => '良好',
                'image_file' => 'laptop.jpg',
                'category' => '家電',
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => null,
                'description' => '高音質のレコーディング用マイク',
                'condition_jp' => '目立った傷や汚れなし',
                'image_file' => 'mic.jpg',
                'category' => '家電',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'condition_jp' => 'やや傷や汚れあり',
                'image_file' => 'bag.jpg',
                'category' => 'ファッション',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => null,
                'description' => '使いやすいタンブラー',
                'condition_jp' => '状態が悪い',
                'image_file' => 'tumbler.jpg',
                'category' => 'キッチン',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'condition_jp' => '良好',
                'image_file' => 'coffee_mill.jpg',
                'category' => 'キッチン',
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'condition_jp' => '目立った傷や汚れなし',
                'image_file' => 'makeup.jpg',
                'category' => 'コスメ',
            ],
        ];

        // 日本語コンディション → DB用キー
        $map = [
            '良好' => 'good',
            '目立った傷や汚れなし' => 'clean',
            'やや傷や汚れあり' => 'fair',
            '状態が悪い' => 'bad',
        ];

        foreach ($items as $data) {
            $srcPath = $seedDir . DIRECTORY_SEPARATOR . $data['image_file'];
            if (!file_exists($srcPath)) {
                $this->command->warn("画像が見つかりません: {$srcPath}");
                continue;
            }

            $condition = $map[$data['condition_jp']] ?? 'good';

            $ext = pathinfo($srcPath, PATHINFO_EXTENSION) ?: 'jpg';
            $dest = 'items/' . Str::uuid() . '.' . $ext;
            Storage::disk('public')->put($dest, file_get_contents($srcPath));

            $item = Item::create([
                'user_id' => $sellerId,
                'name' => $data['name'],
                'brand' => $data['brand'],
                'price' => $data['price'],
                'condition' => $condition,
                'description' => $data['description'],
                'image_path' => 'storage/' . $dest,
                'is_sold' => false,
            ]);

            $categoryId = Category::where('name', $data['category'])->value('id');
            if ($categoryId) {
                $item->categories()->sync([$categoryId]);
            }
        }
    }
}
