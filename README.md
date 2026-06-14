# flea-market(COACHTECHフリマアプリ)

##ER図  

![ER図](./images/er-flea-market.png)


##環境構築

git clone git@github.com:mii4573/flea-market.git  
docker-compose exec php bash  
composer install  
cp .env.example .env  
php artisan key:generate  
php artisan migrate --seed  

##初期データについて  
動作に必要なユーザー、プロフィール、商品データが自動的にデータベースに投入されます
[全ての初期商品の出品者] 
テスト太郎(ID:1)の情報を入れています
[購入確認のための購入者]
テスト花子(ID:2)の情報をいれています


##使用技術

-php 8.1 -fpm  
-Laravel 8.x (Fortify)  
-MySQL 8.0.26  
-nginx 1.21.1  

##開発環境

商品一覧：http://localhost  
phpMyAdmin:http://localhost:8080  
メール認証(MailHog)URL：http://localhost:8025  





