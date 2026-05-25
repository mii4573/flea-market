<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;


class ProfileTest extends TestCase
{
   use RefreshDatabase;

   /**
     * ID 7: ユーザー情報取得 - マイページに必要な情報が表示される
     */
    public function test_can_see_user_profile_and_items_on_mypage()
    {
        // 1. メール認証・プロフィール済みのログインユーザーを作成
        $me = new User([
            'name' => 'マイページ太郎',
            'email' => 'mypage@example.com',
            'password' => bcrypt('password123'),
        ]);
        $me->email_verified_at = now();
        $me->save();

        Profile::create([
            'user_id' => $me->id,
            'display_name' => 'マイページ表示名',
            'post_code' => '111-1111',
            'address' => '東京都渋谷区',
            // 'img_path' => 'profiles/dummy.jpg', // 💡 プロフィール画像カラムがあれば解除
        ]);

        // 2. 「自分が出品した商品」を作成
        $mySoldItem = Item::create([
            'seller_id' => $me->id,
            'name' => '私が出品した自慢の服',
            'price' => 3000,
            'description' => '説明',
            'image_path' => 'items/my1.jpg',
            'condition' => '良好',
        ]);

        // 3. 「自分が購入した商品」を作成（出品者は別人）
        $seller = User::create([
            'name' => '出品者',
            'email' => 'other-seller@example.com',
            'password' => bcrypt('password123'),
        ]);
        $boughtItem = Item::create([
            'seller_id' => $seller->id,
            'name' => '私が奮発して買った靴',
            'price' => 15000,
            'description' => '説明',
            'image_path' => 'items/my2.jpg',
            'condition' => '良好',
        ]);
        // 購入履歴を作成
        Purchase::create([
            'user_id' => $me->id,
            'item_id' => $boughtItem->id,
            'payment_method' => 'カード払い',
            'shipping_post_code' => '111-1111',
            'shipping_address' => '東京都渋谷区',
        ]);

        // 4. マイページ（/mypage）にアクセス
        $response = $this->actingAs($me)->get('/mypage');

        // 5. 期待挙動：ユーザー情報、出品商品、購入商品がすべて見えていること
        $response->assertStatus(200);
        $response->assertSee('マイページ表示名');
        $response->assertSee('私が出品した自慢の服');
        $response->assertDontSee('私が奮発して買った靴'); // 初期状態では購入商品は見えない
        
       // 6. 次に「購入した商品」タブのURL（?page=buy）にアクセス
        // 💡 HTMLログの href="http://localhost/mypage?page=buy" に合わせます
        $buyTabResponse = $this->actingAs($me)->get('/mypage?page=buy');

        // 7. 期待挙動：購入した商品が見えていること
        $buyTabResponse->assertStatus(200);
        $buyTabResponse->assertSee('私が奮発して買った靴');
        $buyTabResponse->assertDontSee('私が出品した自慢の服'); // タブ切り替え後は出品商品は見えない（仕様に応じて変更可）
    }

    /**
     * ID 8: プロフィール設定変更 - 情報を更新すると正しく保存される
     */
    public function test_can_update_profile_successfully()
    {
        $me = new User([
            'name' => '初期太郎',
            'email' => 'update-profile@example.com',
            'password' => bcrypt('password123'),
        ]);
        $me->email_verified_at = now();
        $me->save();

        Profile::create([
            'user_id' => $me->id,
            'display_name' => '初期表示名',
            'post_code' => '000-0000',
            'address' => '初期住所',
        ]);

        // 💡 プロフィール編集の保存用URL（例: /mypage/profile/update など）
        // 実際のルーティング（POST先）に合わせて調整してください
        $updateUrl = '/mypage/profile'; 

        $response = $this->actingAs($me)->post($updateUrl, [
            'display_name' => '進化した表示名',
            'post_code' => '999-9999',
            'address' => '愛知県名古屋市',
        ]);

        // 更新後のリダイレクト先（通常はマイページ /mypage へ戻る想定）
        $response->assertStatus(302);

        // データベースが新しい情報に書き換わっているか確認
        $this->assertDatabaseHas('profiles', [
            'user_id' => $me->id,
            'display_name' => '進化した表示名',
            'post_code' => '999-9999',
            'address' => '愛知県名古屋市',
        ]);
    }
}
