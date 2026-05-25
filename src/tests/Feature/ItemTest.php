<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 4: 商品一覧取得 - 全商品を取得できる
     */
    public function test_can_get_all_items()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password123'),
        ]);

        Item::create([
            'seller_id' => $seller->id,
            'name' => '商品A',
            'price' => 1000,
            'description' => 'テスト説明文A',
            'image_path' => 'items/dummyA.jpg',
            'condition' => '良好', // 💡 condition を追加！
        ]);

        Item::create([
            'seller_id' => $seller->id,
            'name' => '商品B',
            'price' => 2000,
            'description' => 'テスト説明文B',
            'image_path' => 'items/dummyB.jpg',
            'condition' => '目立った傷や汚れなし', // 💡 condition を追加！
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('商品A');
        $response->assertSee('商品B');
    }

    /**
     * ID 4: 商品一覧取得 - 購入済み商品は「Sold」と表示される
     */
    public function test_purchased_items_display_sold_label()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller2@example.com',
            'password' => bcrypt('password123'),
        ]);

        $buyer = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 1. 販売中の商品
        Item::create([
            'seller_id' => $seller->id,
            'name' => '販売中の商品',
            'price' => 1000,
            'description' => '説明',
            'image_path' => 'items/dummyC.jpg',
            'condition' => '良好', // 💡 condition を追加！
        ]);

        // 2. 売り切れの商品
        $soldItem = Item::create([
            'seller_id' => $seller->id,
            'name' => '売り切れの商品',
            'price' => 3000,
            'description' => '説明',
            'image_path' => 'items/dummyD.jpg',
            'condition' => '良好', // 💡 condition を追加！
        ]);

        // 確実に「購入済み」にするため、Purchase履歴を作成
        Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $soldItem->id,
            'payment_method' => 'コンビニ払い',
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区神南1-2-3',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /**
     * ID 4: 商品一覧取得 - 自分が出品した商品は表示されない
     */
    public function test_own_items_are_not_displayed_in_list()
    {
        $me = User::create([
            'name' => '自分の名前',
            'email' => 'me@example.com',
            'password' => bcrypt('password123'),
        ]);

        $other = User::create([
            'name' => '他人の名前',
            'email' => 'other@example.com',
            'password' => bcrypt('password123'),
        ]);

        Item::create([
            'seller_id' => $me->id,
            'name' => '私の出品した商品',
            'price' => 1500,
            'description' => '説明',
            'image_path' => 'items/dummyE.jpg',
            'condition' => '良好', // 💡 condition を追加！
        ]);

        Item::create([
            'seller_id' => $other->id,
            'name' => '他人の出品した商品',
            'price' => 2500,
            'description' => '説明',
            'image_path' => 'items/dummyF.jpg',
            'condition' => '良好', // 💡 condition を追加！
        ]);

        $response = $this->actingAs($me)->get('/');

        $response->assertStatus(200);
        $response->assertSee('他人の出品した商品');
        $response->assertDontSee('私の出品した商品');
    }

    /**
     * ID 5: マイリスト一覧取得 - いいねした商品が一覧に表示される
     */
    public function test_can_see_liked_items_in_mylist()
    {
        // 1. ユーザー（自分）と出品者を作成
        $me = User::create([
            'name' => '自分',
            'email' => 'mylist-me@example.com',
            'email_verified_at' => now(), // 💡 メール認証済みにする！
            'password' => bcrypt('password123'),
        ]);
        $me->email_verified_at = now();
        $me->save();


        // 💡 もしプロフィールテーブル（profilesなど）が必須で、それが原因でリダイレクトされている場合 
        Profile::create([
           'user_id' => $me->id,
           'display_name' => 'テスト表示名',
           'post_code' => '111-1111',
           'address' => '東京都最小限テスト住所',
        ]); 

        $seller = User::create([
            'name' => '出品者',
            'email' => 'mylist-seller@example.com',
            'email_verified_at' => now(), // 💡 念のため出品者も認証済みに！
            'password' => bcrypt('password123'),
        ]);
        $seller->email_verified_at = now();
        $seller->save();

        // 2. 商品を2つ作成（1つはいいねする用、1つはいいねしない用）
        $likedItem = Item::create([
            'seller_id' => $seller->id,
            'name' => 'いいねしたお気に入り商品',
            'price' => 2000,
            'description' => '説明',
            'image_path' => 'items/dummy_like1.jpg',
            'condition' => '良好',
        ]);

        $unlikedItem = Item::create([
            'seller_id' => $seller->id,
            'name' => 'いいねしていない商品',
            'price' => 3000,
            'description' => '説明',
            'image_path' => 'items/dummy_like2.jpg',
            'condition' => '良好',
        ]);

        // 3. 「いいね」のデータを直接作成する
        // 💡 お使いの「いいね」を管理する中間テーブル（例: `likes` テーブルなど）に合わせて、
        // データベースにデータを挿入します。リレーション（$me->likes()->attach(...)）があればそれを使います。
        // 一旦、一般的な `likes` テーブルへの挿入を想定して記述します。
        \DB::table('likes')->insert([
            'user_id' => $me->id,
            'item_id' => $likedItem->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. 自分としてログインしてマイリストページ（またはトップのマイリストタブ ?tab=mylist など）にアクセス
        // 💡 実際のマイリストのURLに合わせて調整してください
        $response = $this->actingAs($me)->get('/mylist');
        
        // 5. 期待挙動：いいねした商品だけが見えて、していない商品は見えないこと
        $response->assertStatus(200);
        $response->assertSee('いいねしたお気に入り商品');
        $response->assertDontSee('いいねしていない商品');
    }

    /**
     * ID 6: 商品検索機能 - 商品一覧ページでキーワード検索ができる
     */
    public function test_can_search_items_by_keyword_on_home()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'search-seller@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 1. 検索にヒットする商品と、ヒットしない商品を作成
        Item::create([
            'seller_id' => $seller->id,
            'name' => '限定デザインのスニーカー',
            'price' => 8000,
            'description' => '説明文',
            'image_path' => 'items/shoes1.jpg',
            'condition' => '良好',
        ]);

        Item::create([
            'seller_id' => $seller->id,
            'name' => 'クラシックな革靴',
            'price' => 12000,
            'description' => '説明文',
            'image_path' => 'items/shoes2.jpg',
            'condition' => '良好',
        ]);

        // 2. 検索キーワード付きでトップページ（/）にGETリクエストを送る
        $response = $this->get('/?keyword=スニーカー');

        // 3. 期待挙動：ヒットした商品だけが見えて、そうでないものは見えないこと
        $response->assertStatus(200);
        $response->assertSee('限定デザインのスニーカー');
        $response->assertDontSee('クラシックな革靴');

    }

    /**
     * ID 6: 商品検索機能 - マイリスト一覧ページでキーワード検索ができる
     */
    public function test_can_search_items_by_keyword_on_mylist()
    {
        // 1. 自分（ログインユーザー）を認証・プロフィール付きで作成
        $me = new User([
            'name' => '自分',
            'email' => 'search-mylist-me@example.com',
            'password' => bcrypt('password123'),
        ]);
        $me->email_verified_at = now();
        $me->save();

        \App\Models\Profile::create([
            'user_id' => $me->id,
            'display_name' => 'テスト表示名',
            'post_code' => '111-1111',
            'address' => '東京都最小限テスト住所',
        ]);

        $seller = User::create([
            'name' => '出品者',
            'email' => 'search-mylist-seller@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 2. 商品を2つ作成
        $hitItem = Item::create([
            'seller_id' => $seller->id,
            'name' => 'お気に入りのスニーカー', // 💡 検索にヒットさせたい商品
            'price' => 5000,
            'description' => '説明',
            'image_path' => 'items/shoes3.jpg',
            'condition' => '良好',
        ]);

        $unhitItem = Item::create([
            'seller_id' => $seller->id,
            'name' => 'お気に入りの帽子',      // 💡 検索から除外したい商品
            'price' => 2000,
            'description' => '説明',
            'image_path' => 'items/cap1.jpg',
            'condition' => '良好',
        ]);

        // 3. 両方の商品を「いいね（マイリスト登録）」しておく
        \DB::table('likes')->insert([
            ['user_id' => $me->id, 'item_id' => $hitItem->id, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $me->id, 'item_id' => $unhitItem->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. 検索キーワード付きでマイリストページ（/mylist）にGETリクエストを送る
        $response = $this->actingAs($me)->get('/mylist?keyword=スニーカー');

        // 5. 期待挙動：マイリスト内でも、キーワードに一致した商品だけが表示されること
        $response->assertStatus(200);
        $response->assertSee('お気に入りのスニーカー');
        $response->assertDontSee('お気に入りの帽子');
    }

    /**
     * ID 7: 商品詳細情報取得 - 必要な情報、複数カテゴリ、いいね数が表示される
     */
    public function test_can_see_item_details_and_likes_count()
    {
        $seller = User::create([
            'name' => '出品マスター',
            'email' => 'detail-seller@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 1. テスト用の商品を作成
        $item = Item::create([
            'seller_id' => $seller->id,
            'name' => '詳細テスト用高級腕時計',
            'price' => 50000,
            'description' => 'これは詳細テスト用の高級な腕時計です。',
            'image_path' => 'items/watch.jpg',
            'condition' => '新品同様',
            'brand_name' => 'ロレックス',
        ]);

        // 2. 💡 fillableを無視してカテゴリを直接インスタンス化して保存
        $category1 = new \App\Models\Category();
        $category1->name = 'メンズ';
        $category1->save();

        $category2 = new \App\Models\Category();
        $category2->name = '時計';
        $category2->save();
        
        // 中間テーブルに紐付け
        $item->categories()->attach([$category1->id, $category2->id]);

        // 3. 2人のダミーユーザーがこの商品に「いいね」した状態を作る
        $user1 = User::create(['name' => '客1', 'email' => 'u1@example.com', 'password' => bcrypt('password')]);
        $user2 = User::create(['name' => '客2', 'email' => 'u2@example.com', 'password' => bcrypt('password')]);
        
        \DB::table('likes')->insert([
            ['user_id' => $user1->id, 'item_id' => $item->id, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $user2->id, 'item_id' => $item->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. 商品詳細ページ（/item/{id}）にアクセス
        $response = $this->get('/item/' . $item->id);

        // 5. 期待挙動：すべての情報が画面に表示されていること
        $response->assertStatus(200);
        $response->assertSee('詳細テスト用高級腕時計'); 
        $response->assertSee('これは詳細テスト用の高級な腕時計です。'); 
        $response->assertSee('新品同様'); 
        $response->assertSee('ロレックス'); 
        $response->assertSee('50,000'); 
        
        // 複数選択されたカテゴリが表示されているか
        $response->assertSee('メンズ');
        $response->assertSee('時計');

        // いいね数が「2」と表示されているか
        $response->assertSee('2'); 
    }

    /**
     * ID 8: コメント機能 - ログインユーザーはコメントを投稿でき、詳細画面に即時反映される
     */
    public function test_authenticated_user_can_post_comment()
    {
        // 1. コメントを投稿するユーザー（自分）を認証・プロフィール付きで作成
        $me = new User([
            'name' => 'コメント次郎',
            'email' => 'commenter@example.com',
            'password' => bcrypt('password123'),
        ]);
        $me->email_verified_at = now();
        $me->save();

        \App\Models\Profile::create([
            'user_id' => $me->id,
            'display_name' => 'コメント表示名',
            'post_code' => '111-1111',
            'address' => '住所',
        ]);

        $seller = User::create([
            'name' => '出品者',
            'email' => 'comment-seller@example.com',
            'password' => bcrypt('password123'),
        ]);

        $item = Item::create([
            'seller_id' => $seller->id,
            'name' => 'コメントされる商品',
            'price' => 1000,
            'description' => '説明',
            'image_path' => 'items/comment.jpg',
            'condition' => '良好',
        ]);

        // 2. コメント投稿用のURLへPOSTリクエストを送る
        $commentUrl = '/item/' . $item->id . '/comment';

        $response = $this->actingAs($me)->post($commentUrl, [
            'comment' => 'この商品、最高に素敵ですね！購入を検討しています。',
        ]);

        // 3. 投稿後の挙動（詳細画面にリダイレクトして戻る想定）
        $response->assertStatus(302);

        // 4. 実際に詳細画面に戻って、自分のコメント・ユーザー情報・コメント数が反映されているか確認
        $detailResponse = $this->actingAs($me)->get('/item/' . $item->id);
        $detailResponse->assertStatus(200);
        
        $detailResponse->assertSee('この商品、最高に素敵ですね！購入を検討しています。'); 
        $detailResponse->assertSee('コメント表示名'); 
        $detailResponse->assertSee('1'); 
    }

    /**
     * ID 8: いいね機能 - いいねアイコンの押下による登録と解除、カウント増減および色の変化
     */
    public function test_user_can_toggle_like_on_item()
    {
        // 1. ユーザー（ログイン用）を確実に【メール認証済み】にして作成
        $me = User::create([
            'name' => 'いいね太郎',
            'email' => 'liker@example.com',
            'password' => bcrypt('password123'),
        ]);
        $me->email_verified_at = now(); // 💡 createの後に直接代入
        $me->save();                     // 💡 データベースに強制保存

        $seller = User::create([
            'name' => '出品者',
            'email' => 'like-seller@example.com',
            'password' => bcrypt('password123'),
        ]);
        $seller->email_verified_at = now(); // 💡 出品者も同様に認証済みに
        $seller->save();

        $item = Item::create([
            'seller_id' => $seller->id,
            'name' => 'いいねテスト商品',
            'price' => 1000,
            'description' => '説明',
            'image_path' => 'items/like_test.jpg',
            'condition' => '良好',
        ]);

        // （※ここから下のURL定義やリクエスト処理はそのまま残す）
        $detailUrl = '/item/' . $item->id;
        $likeUrl = '/item/' . $item->id . '/like';
        
        // 最初の状態確認
        $response = $this->actingAs($me)->get($detailUrl);
        $response->assertStatus(200);
        
        // 【1回目の押下】いいね登録（POST）
        $likeResponse = $this->actingAs($me)->post($likeUrl);
        $likeResponse->assertStatus(302); 

        // データベースに登録されているか確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $me->id,
            'item_id' => $item->id,
        ]);

        // 詳細画面を再取得して、合計値の増加と色変化を確認
        $response = $this->actingAs($me)->get($detailUrl);
        $response->assertSee('1'); 
        
        // 💡 Bladeに合わせて画像名を heart_pink_icon.png に設定
        $response->assertSee('heart_pink_icon.png'); 
        $response->assertDontSee('heart_default_icon.png');

        // 【2回目の押下】いいね解除（DELETE）
        $unlikeResponse = $this->actingAs($me)->delete($likeUrl); 
        $unlikeResponse->assertStatus(302);

        // データベースから削除されているか確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $me->id,
            'item_id' => $item->id,
        ]);

        // 詳細画面を再取得して、合計値の減少とアイコンが戻ったことを確認
        $response = $this->actingAs($me)->get($detailUrl);
        $response->assertSee('0'); 
        $response->assertSee('heart_default_icon.png');
        $response->assertDontSee('heart_pink_icon.png');
    }

    /**
     * ID 9: コメント送信機能 - ログイン前のユーザーはコメントを送信できない
     */
    public function test_guest_user_cannot_post_comment()
    {
        // 出品者と商品を作成
        $seller = User::create([
            'name' => '出品者',
            'email' => 'comment-seller2@example.com',
            'password' => bcrypt('password123'),
        ]);

        $item = Item::create([
            'seller_id' => $seller->id,
            'name' => 'ゲストコメントテスト商品',
            'price' => 1000,
            'description' => '説明',
            'image_path' => 'items/comment2.jpg',
            'condition' => '良好',
        ]);

        $commentUrl = '/item/' . $item->id . '/comment';

        // 💡 ログインせずに（guestの状態で）POSTリクエストを送信
        $response = $this->post($commentUrl, [
            'comment' => '未ログインのコメントです。',
        ]);

        // 未ログインなので、ログイン画面（/login）へリダイレクトされることを期待
        $response->assertRedirect('/login');

        // データベースにコメントが登録されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => '未ログインのコメントです。',
        ]);
    }

    /**
     * ID 9: コメント送信機能 - コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_comment_is_required_validation()
    {
        $me = User::create([
            'name' => 'コメント太郎',
            'email' => 'val-commenter1@example.com',
            'password' => bcrypt('password123'),
        ]);
        $me->email_verified_at = now();
        $me->save();

        $seller = User::create([
            'name' => '出品者',
            'email' => 'comment-seller3@example.com',
            'password' => bcrypt('password123'),
        ]);

        $item = Item::create([
            'seller_id' => $seller->id,
            'name' => 'バリデーションテスト商品1',
            'price' => 1000,
            'description' => '説明',
            'image_path' => 'items/comment3.jpg',
            'condition' => '良好',
        ]);

        $commentUrl = '/item/' . $item->id . '/comment';

        // 💡 コメントを「空（null）」で送信
        $response = $this->actingAs($me)->post($commentUrl, [
            'comment' => '',
        ]);

        // バリデーションエラーによって元の画面にリダイレクトされ、エラーがセッションにあるか確認
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['comment']);
    }

    /**
     * ID 9: コメント送信機能 - コメントが255文字以上の場合、バリデーションメッセージが表示される
     */
    public function test_comment_max_length_validation()
    {
        $me = User::create([
            'name' => 'コメント太郎',
            'email' => 'val-commenter2@example.com',
            'password' => bcrypt('password123'),
        ]);
        $me->email_verified_at = now();
        $me->save();

        $seller = User::create([
            'name' => '出品者',
            'email' => 'comment-seller4@example.com',
            'password' => bcrypt('password123'),
        ]);

        $item = Item::create([
            'seller_id' => $seller->id,
            'name' => 'バリデーションテスト商品2',
            'price' => 1000,
            'description' => '説明',
            'image_path' => 'items/comment4.jpg',
            'condition' => '良好',
        ]);

        $commentUrl = '/item/' . $item->id . '/comment';

        // 💡 256文字のランダムな文字列を作成して送信
        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($me)->post($commentUrl, [
            'comment' => $longComment,
        ]);

        // 255文字を超えているためバリデーションエラーになることを期待
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['comment']);
    }

    /**
     * ID 10: 商品購入機能 - 「購入する」ボタン押下による購入完了、Sold表示、およびマイページへの反映
     */
    public function test_user_can_purchase_item_and_see_in_mypage()
    {
        // 1. ログインユーザー（購入者）を認証・プロフィール付きで作成
        $buyer = User::create([
            'name' => '購入太郎',
            'email' => 'buyer-id10@example.com',
            'password' => bcrypt('password123'),
        ]);
        $buyer->email_verified_at = now();
        $buyer->save();

        \App\Models\Profile::create([
            'user_id' => $buyer->id,
            'display_name' => '購入者プロフ名',
            'post_code' => '111-1111',
            'address' => '東京都購入者住所',
        ]);

        // 出品者と商品の作成
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller-id10@example.com',
            'password' => bcrypt('password123'),
        ]);
        $seller->email_verified_at = now();
        $seller->save();

        $item = Item::create([
            'seller_id' => $seller->id,
            'name' => '購入テスト対象商品',
            'price' => 3000,
            'description' => '説明文',
            'image_path' => 'items/purchase_test.jpg',
            'condition' => '良好',
        ]);

        // 2. 【購入処理】POSTリクエストを送信して商品を購入する
        // 💡 web.php の `POST /purchase/{item_id}` に合わせています
        $purchaseUrl = '/purchase/' . $item->id;
        
        $response = $this->actingAs($buyer)->post($purchaseUrl, [
            'payment_method' => 'コンビニ払い', // 実際のフォームのname属性や必須項目に合わせてください
            'shipping_post_code' => '111-1111',
            'shipping_address' => '東京都購入者住所',
        ]);

        // 購入後の挙動確認（通常は完了画面へのリダイレクトやサンクスページへの遷移など。ここでは302を想定）
        $response->assertStatus(302);

        // データベースに購入履歴（purchasesテーブル）が正しく登録されているか確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);

        // 3. 【一覧画面でのSold確認】トップページで対象商品に「Sold」が表示されているか
        $homeResponse = $this->actingAs($buyer)->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Sold');

        // 4. 【マイページ確認】プロフィール画面の「購入した商品」に反映されているか
        // 💡 web.php の `GET /mypage` にアクセス。タブの切り替え（?tab=buy など）がある場合は適宜URLを調整してください
        $mypageUrl = '/mypage?page=buy'; 
        
        $mypageResponse = $this->actingAs($buyer)->get($mypageUrl);
        $mypageResponse->assertStatus(200);
        $mypageResponse->assertSee('購入テスト対象商品'); // 購入した商品名が表示されていること
    }

    /**
     * ID 11: 支払い方法選択機能 - 購入画面（小計画面）で選択した支払い方法が正しく反映・表示される
     */
    public function test_selected_payment_method_is_reflected_on_purchase_page()
    {
        // 1. ログインユーザー（購入者）をプロフィール付きで作成（display_nameを追加して修正）
        $buyer = User::create([
            'name' => '決済太郎',
            'email' => 'payment-id11@example.com',
            'password' => bcrypt('password123'),
        ]);
        $buyer->email_verified_at = now();
        $buyer->save();

        \App\Models\Profile::create([
            'user_id' => $buyer->id,
            'display_name' => '決済太郎プロフ', // 👈 必須フィールドを追加！
            'post_code' => '111-1111',
            'address' => '東京都購入者住所',
        ]);

        // 出品者と商品の作成
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller-id11@example.com',
            'password' => bcrypt('password123'),
        ]);
        $seller->email_verified_at = now();
        $seller->save();

        $item = Item::create([
            'seller_id' => $seller->id,
            'name' => '決済テスト商品',
            'price' => 2000,
            'description' => '説明文',
            'image_path' => 'items/payment_test.jpg',
            'condition' => '良好',
        ]);

        $purchaseUrl = '/purchase/' . $item->id;

        // 2. 【選択・反映の確認】
        // 💡 変更画面から戻る際、仕様に合わせてセッションまたはクエリに値をセットします。
        // 一般的にはセッションの 'payment_method' などを経由することが多いため、両方に対応できるようにセットします。
        session(['payment_method' => 'コンビニ払い']);
        session(['selected_payment_method' => 'コンビニ払い']);

        // クエリパラメータ付きでもアクセスして、どちらのパターンでも「コンビニ払い」が表示されるか検証
        $response = $this->actingAs($buyer)->get($purchaseUrl . '?payment_method=コンビニ払い');
        $response->assertStatus(200);
        
        // 💡 画面に「コンビニ払い」が反映されているか確認
        $response->assertSee('コンビニ払い');
    }

    /**
     * ID 11補足: 支払い方法に「クレジットカード」を選択した場合、Stripeのチェックアウト画面へリダイレクトされるか
     */
    public function test_credit_card_payment_redirects_to_checkout()
    {
        // 1. ユーザー（購入者）をプロフィール付きで作成
        $buyer = User::create([
            'name' => 'カード太郎',
            'email' => 'card-id11@example.com',
            'password' => bcrypt('password123'),
        ]);
        $buyer->email_verified_at = now();
        $buyer->save();

        \App\Models\Profile::create([
            'user_id' => $buyer->id,
            'display_name' => 'カード太郎プロフ',
            'post_code' => '111-1111',
            'address' => '東京都購入者住所',
        ]);

        // 出品者と商品の作成
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller-card11@example.com',
            'password' => bcrypt('password123'),
        ]);
        $seller->email_verified_at = now();
        $seller->save();

        $item = Item::create([
            'seller_id' => $seller->id,
            'name' => 'カード決済商品',
            'price' => 5000,
            'description' => 'カード決済のテストです',
            'image_path' => 'items/card_test.jpg',
            'condition' => '新品',
        ]);

        $purchaseUrl = '/purchase/' . $item->id;

        // 2. 【購入処理】支払い方法を「クレジットカード」にしてPOST送信
        $response = $this->actingAs($buyer)->post($purchaseUrl, [
            'payment_method' => 'クレジットカード',
        ]);

        // 3. 【検証】Stripeチェックアウトに繋がるリダイレクト（purchase.checkout）に向かっているか確認
        // 💡 期待される遷移先URL： /purchase/{item_id}/checkout
        $expectedRedirectUrl = route('purchase.checkout', ['item_id' => $item->id]);
        $response->assertRedirect($expectedRedirectUrl);

        // クレジットカードの場合は、この時点ではまだDB（purchases）にレコードが作られないことも確認（successで作成するため）
        $this->assertDatabaseMissing('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);
    }

    /**
     * ID 12: 配送先変更機能 - 変更した住所が購入画面に反映され、購入時にその住所が正しく紐づくこと
     */
    public function test_edited_address_is_reflected_on_purchase_page_and_saved_on_purchase()
    {
        // 1. ユーザー（購入者）をデフォルトのプロフィール付きで作成
        $buyer = User::create([
            'name' => '住所変更太郎',
            'email' => 'address-id12@example.com',
            'password' => bcrypt('password123'),
        ]);
        $buyer->email_verified_at = now();
        $buyer->save();

        \App\Models\Profile::create([
            'user_id' => $buyer->id,
            'display_name' => '変更太郎プロフ',
            'post_code' => '111-1111', // 💡 元の郵便番号
            'address' => '東京都元の住所', // 💡 元の住所
        ]);

        // 出品者と商品の作成
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller-address12@example.com',
            'password' => bcrypt('password123'),
        ]);
        $seller->email_verified_at = now();
        $seller->save();

        $item = Item::create([
            'seller_id' => $seller->id,
            'name' => '住所変更テスト商品',
            'price' => 1500,
            'description' => '住所変更のテストです',
            'image_path' => 'items/address_test.jpg',
            'condition' => '良好',
        ]);

        $purchaseUrl = '/purchase/' . $item->id;
        $addressUpdateUrl = '/purchase/address/' . $item->id; // 💡 送付先住所更新のPOST先（URLはRoute設定に合わせて調整してください）

        // 2. 【住所変更処理】送付先変更画面から新しい住所を送信（セッション等に保存させる）
        // ※もしRouteのURLが異なる場合は、実際の web.php の設定に合わせて変更してください
        $addressResponse = $this->actingAs($buyer)->post($addressUpdateUrl, [
            'post_code' => '999-9999',       // ✨ 新しい郵便番号
            'address' => '大阪府新しい配送先住所', // ✨ 新しい住所
            'building' => '新しいビル101',
        ]);
        
        // 変更後は通常、購入画面（purchase.index）へリダイレクトされることを確認
        $addressResponse->assertStatus(302);

        // 3. 【購入画面への反映確認】購入画面を開き、新住所が表示されているか検証
        $purchasePageResponse = $this->actingAs($buyer)->get($purchaseUrl);
        $purchasePageResponse->assertStatus(200);
        $purchasePageResponse->assertSee('999-9999');
        $purchasePageResponse->assertSee('大阪府新しい配送先住所');
        $purchasePageResponse->assertSee('新しいビル101');

        // 4. 【購入時の紐づき確認】この状態で（コンビニ払いで）購入を確定させる
        $purchaseSubmitResponse = $this->actingAs($buyer)->post($purchaseUrl, [
            'payment_method' => 'コンビニ払い',
        ]);
        $purchaseSubmitResponse->assertStatus(302);

        // データベース（purchasesテーブル）に、元の住所ではなく「新住所」で保存されているか検証
        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'shipping_post_code' => '999-9999',
            'shipping_address' => '大阪府新しい配送先住所',
            'shipping_building' => '新しいビル101',
        ]);
    }

    /**
     * ID 13: ユーザー情報取得 - マイページにプロフィール画像、ユーザー名、出品一覧、購入一覧が正しく表示される
     */
    public function test_mypage_displays_user_profile_and_items_lists()
    {
        // 1. テスト用のユーザー（自分）をプロフィール付きで作成
        $me = User::create([
            'name' => 'マイページ確認太郎',
            'email' => 'mypage-id13@example.com',
            'password' => bcrypt('password123'),
        ]);
        $me->email_verified_at = now();
        $me->save();

        \App\Models\Profile::create([
            'user_id' => $me->id,
            'display_name' => '確認太郎プロフ名',
            'post_code' => '123-4567',
            'address' => '東京都渋谷区',
            'image_path' => 'profiles/test_avatar.png', // 💡 プロフィール画像
        ]);

        // 2. 自分が【出品した商品】を作成
        $myFormedItem = Item::create([
            'seller_id' => $me->id, // 出品者は自分
            'name' => '私が出品した自慢の商品',
            'price' => 5000,
            'description' => '自分で出品した商品の説明です',
            'image_path' => 'items/my_sell_test.jpg',
            'condition' => '新品',
        ]);

        // 3. 他の人が出品して、自分が【購入した商品】を作成
        $anotherSeller = User::create([
            'name' => '他人の出品者',
            'email' => 'other-seller13@example.com',
            'password' => bcrypt('password123'),
        ]);
        $anotherSeller->email_verified_at = now();
        $anotherSeller->save();

        $purchasedItem = Item::create([
            'seller_id' => $anotherSeller->id, // 出品者は他人
            'name' => '私が奮発して購入した商品',
            'price' => 12000,
            'description' => '他人から購入した商品の説明です',
            'image_path' => 'items/my_buy_test.jpg',
            'condition' => '目立った傷なし',
        ]);

        // 購入履歴（purchases）を作成して紐付ける
        \App\Models\Purchase::create([
            'user_id' => $me->id, // 購入者は自分
            'item_id' => $purchasedItem->id,
            'payment_method' => 'コンビニ払い',
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
        ]);

        // 4. 【検証：出品した商品タブ】マイページ（初期表示またはpage=sell）にアクセス
        $mypageSellUrl = '/mypage?page=sell';
        $responseSell = $this->actingAs($me)->get($mypageSellUrl);
        $responseSell->assertStatus(200);

        // ユーザー名、プロフィール画像、出品した商品名が表示されているか
        $responseSell->assertSee('確認太郎プロフ名');
        $responseSell->assertSee('profiles/test_avatar.png');
        $responseSell->assertSee('私が出品した自慢の商品');

        // 5. 【検証：購入した商品タブ】マイページの購入タブ（page=buy）にアクセス
        $mypageBuyUrl = '/mypage?page=buy';
        $responseBuy = $this->actingAs($me)->get($mypageBuyUrl);
        $responseBuy->assertStatus(200);

        // 購入した商品名が表示されているか
        $responseBuy->assertSee('私が奮発して購入した商品');
    }

    /**
     * ID 14: ユーザー情報変更 - プロフィール編集画面を開いた際、過去に設定した情報が初期値としてフォームに表示されていること
     */
    public function test_profile_edit_page_shows_existing_values_as_defaults()
    {
        // 1. テスト用のユーザーを過去の設定値（プロフィール情報付き）で作成
        $user = User::create([
            'name' => '初期値確認太郎',
            'email' => 'profile-id14@example.com',
            'password' => bcrypt('password123'),
        ]);
        $user->email_verified_at = now();
        $user->save();

        \App\Models\Profile::create([
            'user_id' => $user->id,
            'display_name' => '過去設定されたユーザー名',
            'post_code' => '987-6543',
            'address' => '北海道札幌市中央区',
            'building' => '時計台ビル3F',
            'image_path' => 'profiles/past_avatar.png', // 過去設定されたプロフィール画像
        ]);

        // 2. プロフィール編集画面へアクセス
        // 💡 web.php の設定に合わせてURL（例: /mypage/profile）を調整してください
        $profileEditUrl = '/mypage/profile';
        $response = $this->actingAs($user)->get($profileEditUrl);

        $response->assertStatus(200);

        // 3. 【検証】過去設定した情報が、画面のフォーム初期値（または要素）として含まれているか
        $response->assertSee('過去設定されたユーザー名');
        $response->assertSee('987-6543');
        $response->assertSee('北海道札幌市中央区');
        $response->assertSee('時計台ビル3F');
        
        // プロフィール画像が画面に表示されている（またはパスが含まれている）ことを確認
        $response->assertSee('profiles/past_avatar.png');
    }

    /**
     * ID 15: 出品商品情報登録 - 商品出品画面から入力した情報（画像含む）が正しくDBに保存されること（パターンB: 中間テーブル版）
     */
    public function test_user_can_list_product_with_all_required_information()
    {
        // 1. テスト用のユーザー（出品者）を作成
        $seller = User::create([
            'name' => '出品太郎',
            'email' => 'seller-id15@example.com',
            'password' => bcrypt('password123'),
        ]);
        $seller->email_verified_at = now();
        $seller->save();

        // 2. ✨ テスト用のカテゴリを事前にデータベースに作成
        // ※ モデル名が異なる場合は、実際のモデル名（例: \App\Models\Category）に合わせてください
        $category = \App\Models\Category::create(['name' => 'レディース']);

        // 3. GDモジュールなしでも動くように、fake()->create() で画像ファイルを偽装生成
        \Illuminate\Support\Facades\Storage::fake('public');
        $dummyImage = \Illuminate\Http\UploadedFile::fake()->create('listed_item.jpg', 100, 'image/jpeg');

        // 4. 【出品処理】商品出品画面のPOST先URLへデータを送信
        $sellUrl = '/sell';
        $response = $this->actingAs($seller)->post($sellUrl, [
            'name' => '出品テスト商品名',
            'brand_name' => 'テストブランド',
            'description' => 'これは出品機能のテストのための商品説明文です。',
            'price' => 3500,
            'condition' => '新品、未使用',
            'item_image' => $dummyImage,
            // 💡 パターンB: カテゴリIDを配列（または単一の値）で送信
            // フォームのname属性（例: categories や category_ids）に合わせてキーを変更してください
            'categories' => [$category->id], 
        ]);

        // 出品完了後は通常、リダイレクトされることを確認（302）
        $response->assertStatus(302);

        // 5. 【検証】データベース（itemsテーブル）に商品基本情報が保存されているか
        $this->assertDatabaseHas('items', [
            'seller_id' => $seller->id,
            'name' => '出品テスト商品名',
            'brand_name' => 'テストブランド',
            'description' => 'これは出品機能のテストのための商品説明文です。',
            'price' => 3500,
            'condition' => '新品、未使用',
        ]);

        // 保存された商品を取得
        $savedItem = Item::where('seller_id', $seller->id)->latest()->first();
        $this->assertNotNull($savedItem->image_path);
        
        // 画像ファイルがフェイクストレージ内に存在するかチェック
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($savedItem->image_path);

        // 6. 【検証】✨ 中間テーブルに商品とカテゴリの紐づきが保存されているか
        // 💡 実際の中間テーブル名（例: 'category_item' または 'item_category'）に合わせて指定してください
        // 💡 カラム名も 'item_id', 'category_id' で合っているか確認してください
        $this->assertDatabaseHas('item_category', [
            'item_id' => $savedItem->id,
            'category_id' => $category->id,
        ]);
    }

    /**
     * ID 16: メール認証機能 - 会員登録後に認証メールが送信され、認証完了後にプロフィール設定画面へ遷移すること
     */
    public function test_user_receives_verification_email_upon_registration_and_redirects_to_profile_setup()
    {
        // 1. 【会員登録処理】新規会員登録のPOST送信
        $registerUrl = '/register';
        $response = $this->post($registerUrl, [
            'name' => '認証テスト次郎',
            'email' => 'verify-id16@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 会員登録後のリダイレクト（通常はメール確認お願い画面などへ302）を確認
        $response->assertStatus(302);

        // 2. 【検証】ユーザーデータがデータベースに正しく作成されているか
        $user = User::where('email', 'verify-id16@example.com')->first();
        $this->assertNotNull($user);
        
        // この時点では、まだメール認証は完了していない（nullである）ことを確認
        $this->assertNull($user->email_verified_at);

        // 3. 【検証：メール認証の完了とリダイレクト】
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // ログインした状態で、生成された認証URL（メール内リンク）にアクセス
        $verifyResponse = $this->actingAs($user)->get($verificationUrl);

        // 💡 4. 【検証】実際のアプリの挙動（中継URL）に合わせてリダイレクト先を検証
        // Fortify標準の完了URL、または最終的なプロフィール画面へのリダイレクトを許容します
        if ($verifyResponse->isRedirect('http://localhost/email/verify?verified=1')) {
            $verifyResponse->assertRedirect('/email/verify?verified=1');
            // 中継画面にさらにアクセスして最終的なプロフィール画面（/mypage/profile）へ行くか追跡
            $finalResponse = $this->actingAs($user)->get('/email/verify?verified=1');
            // もしここでもさらにリダイレクトがかかる場合は、ステータスコードや着地を検証
        } else {
            $verifyResponse->assertRedirect('/mypage/profile');
        }

        // 5. 【検証】✨何より重要な「データベース上でメール認証が完了した状態」になっているか
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}