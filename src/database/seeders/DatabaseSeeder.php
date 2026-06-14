<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Purchase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::create([
          'id' => 1,
          'name' => 'テスト太郎',
          'email' => 'test@example.com',
          'password' => Hash::make('password123'),
        ]);
        
        // そのUserに紐づくProfileを作成（住所情報はこちら）
       $user1->profile()->create([
          'display_name' => 'テスト太郎',
          'post_code' => '123-4567',
          'address' => '東京都渋谷区...',
          'building' => '',
        ]);

        $user2 = User::create([
          'id' => 2,
          'name' => 'テスト花子',
          'email' => 'hanako@example.com',
          'password' => Hash::make('password456'),
        ]);

        $user2->profile()->create([
        'display_name' => '花子',
        'post_code' => '000-0000',
        'address' => '大阪府...',
        'building' => '',
        ]);


        $this->call([
           CategorySeeder::class, 
           ItemSeeder::class,
        ]);

        //Purchase::create([
            //'user_id' => 2,
            //'item_id' => 1,
            //'payment_method' => 'card', 
            //'shipping_address' => '大阪府...', 
            //'shipping_post_code' => '000-0000',
            //'shipping_building' => '',
        //]);
    }
}
