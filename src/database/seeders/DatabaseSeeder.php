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
        User::create([
          'id' => 1,
          'name' => 'テスト太郎',
          'email' => 'test@example.com',
          'password' => Hash::make('password123'),
        ]);

        $this->call([
           CategorySeeder::class, 
           ItemSeeder::class,
        ]);

        Purchase::create([
            'user_id' => 1,
            'item_id' => 1,
            'payment_method' => 'card', 
            'shipping_address' => '東京都渋谷区...', 
            'shipping_post_code' => '123-4567',
        ]);
    }
}
