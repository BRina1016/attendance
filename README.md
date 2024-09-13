# アプリケーション名

Atte（アット）
ある企業の勤怠管理システム
TOP 画像
<img width="1440" alt="Top" src="https://github.com/user-attachments/assets/36da4ec4-b6b9-411c-944a-4f3611792e5f">

## 作成した目的

人事評価のため

## URL

- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/

## 他のレポジトリ

https://github.com/BRina1016/attendance.git

## 機能一覧

・ログイン機能
・会員登録機能
・出退勤打刻機能
・休憩開始・終了打刻機能
・日付別勤怠情報取得
・ユーザー一覧ページ / ユーザー個別勤怠表

## 使用技術

-PHP 8.3.9
-Laravel8.83.27

## テーブル設計

![tabel](https://github.com/user-attachments/assets/e1288b26-90ba-42d8-aa79-df055802c3a6)

## ER 図

![atte_er](https://github.com/user-attachments/assets/e30aa0c0-2926-490c-b409-8f12393ca78b)

## 環境構築

Docker

1.  git clone https://github.com/BRina1016/attendance.git
2.  DockerDesktop アプリを立ち上げる
3.  docker-compose up -d --build

Laravel 環境構築

1.  docker-compose exec php bash
2.  composer install
3.  「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.env ファイルを作成
4.  .env に以下を追加
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=laravel_db
    DB_USERNAME=laravel_user
    DB_PASSWORD=laravel_pass

5.  アプリケーションキーの作成
    php artisan key:generate

6.  マイグレーションの実行
    php artisan migrate

7.  シーディングの実行
    php artisan db:seed
