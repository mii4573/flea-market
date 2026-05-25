<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $categories = [
        ['name' => 'ファッション'],
        ['name' => '家電・スマホ・カメラ'],
        ['name' => 'スポーツ・レジャー'],
        ['name' => 'レディース'],
        ['name' => 'メンズ'],
        ['name' => 'コスメ・香水・美容'],
        ['name' => 'ベビー・キッズ'],
        ['name' => 'インテリア・住まい・小物'],
        ['name' => '本・音楽・ゲーム'],
        ['name' => 'ハンドメイド'],
        ['name' => '食品'],
    ];

    foreach ($categories as $category) {
        \App\Models\Category::create($category); 
    }
    }
}
