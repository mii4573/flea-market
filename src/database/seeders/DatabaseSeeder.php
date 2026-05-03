<?php

namespace Database\Seeders;

use App\Models\User;
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
           ItemSeeder::class,
        ]);

        \App\Models\Purchase::create([
            'user_id' => 1,
            'item_id' => 1,
        ]);
    }
}
