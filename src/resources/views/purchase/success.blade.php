<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>購入完了</title>
    <style>
        body { text-align: center; padding: 50px; font-family: sans-serif; }
        .container { max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 30px; border-radius: 8px; }
        h1 { color: #28a745; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>ご購入ありがとうございました！</h1>
        <p>商品の購入手続きが正常に完了いたしました。</p>
                
        {{-- トップページや商品一覧に戻るリンク（ルート名はご自身の環境に合わせて調整してください） --}}
        <a href="/" class="btn">商品一覧へ戻る</a>
    </div>
</body>
</html>