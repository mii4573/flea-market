<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['name' => '腕時計', 'price' => 15000, 'category_id' => 5, 'brand_name' => 'Rolax', 'description' => 'スタイリッシュなデザインのメンズ腕時計', 'image_path' => 'item-images/watch.jpg', 'condition' => '良好'], // メンズ
            ['name' => 'HDD', 'price' => 5000, 'category_id' => 2, 'brand_name' => '西芝', 'description' => '高速で信頼性の高いハードディスク', 'image_path' => 'item-images/hdd.jpg', 'condition' => '目立った傷や汚れなし'], // 家電・スマホ・カメラ
            ['name' => '玉ねぎ3束', 'price' => 300, 'category_id' => 11, 'brand_name' => '', 'description' => '新鮮な玉ねぎ3束のセット', 'image_path' => 'item-images/onion.jpg', 'condition' => 'やや傷や汚れあり'], // 食品
            ['name' => '革靴', 'price' => 4000, 'category_id' => 5, 'brand_name' => '', 'description' => 'クラシックなデザインの革靴', 'image_path' => 'item-images/leather-shoes.jpg', 'condition' => '状態が悪い'], // メンズ
            ['name' => 'ノートPC', 'price' => 45000, 'category_id' => 2, 'brand_name' => '', 'description' => '高性能なノートパソコン', 'image_path' => 'item-images/pc.jpg', 'condition' => '良好'], // 家電・スマホ・カメラ
            ['name' => 'マイク', 'price' => 8000, 'category_id' => 2, 'brand_name' => '', 'description' => '高音質のレコーディング用マイク', 'image_path' => 'item-images/mic.jpg', 'condition' => '目立った傷や汚れなし'], // 家電・スマホ・カメラ
            ['name' => 'ショルダーバッグ', 'price' => 3500, 'category_id' => 4, 'brand_name' => '', 'description' => 'おしゃれなショルダーバッグ', 'image_path' => 'item-images/bag.jpg', 'condition' => 'やや傷や汚れあり'], // レディース
            ['name' => 'タンブラー', 'price' => 500, 'category_id' => 8, 'brand_name' => '', 'description' => '使いやすいタンブラー', 'image_path' => 'item-images/tumbler.jpg', 'condition' => '状態が悪い'], // インテリア・住まい・小物
            ['name' => 'コーヒーミル', 'price' => 4000, 'category_id' => 8, 'brand_name' => 'Starbacks', 'description' => '手動のコーヒーミル', 'image_path' => 'item-images/mill.jpg', 'condition' => '良好'], // インテリア・住まい・小物
            ['name' => 'メイクセット', 'price' => 2500, 'category_id' => 6, 'brand_name' => '', 'description' => '便利なメイクアップセット', 'image_path' => 'item-images/makeup-set.jpg', 'condition' => '目立った傷や汚れなし'], // コスメ・香水・美容
        ];
        
        foreach ($items as $item) {
          Item::create(array_merge($item, ['seller_id' => 1]));
        }  
    }
}
