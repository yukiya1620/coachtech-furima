# COACHTECH フリマ（模擬案件1）

フリマアプリの模擬案件課題です。  
会員登録 / ログイン / メール認証 / 商品一覧・検索 / いいね（マイリスト）/ コメント / 購入 / マイページ などを実装しています。

---

## 機能一覧

- 認証（会員登録・ログイン・ログアウト）
- メール認証（MailHogで確認）
- 商品一覧（おすすめ / マイリスト）
- 商品検索（キーワード）
- 商品詳細
- いいね（追加/解除、マイリスト表示）
- コメント投稿（メール未認証ユーザーは認証誘導）
- 購入フロー
  - コンビニ払い：Stripeなしで即購入完了（購入レコード作成 + SOLD化）
  - クレカ払い：Stripe Checkoutへ遷移（決済完了後にsuccess画面へ）
- マイページ
  - プロフィール表示・編集
  - 出品した商品 / 購入した商品一覧
- SOLD表示（画像左上の斜めリボン）

---

## 使用技術

- PHP 8.x
- Laravel 8.x
- MySQL
- Docker / docker compose
- MailHog（ローカルメール確認）
- Stripe Checkout（クレカ決済遷移）

---

## 環境構築手順

### 1. リポジトリをクローン
```bash
git clone git@github.com:yukiya1620/coachtech-furima.git
cd coachtech-furima
```
### 2. コンテナ起動
```bash
docker compose up -d --build
```
### 3.依存インストール
```bash
docker compose exec app composer install
```
### 4.環境変数
```bash
cp .env.example .env
docker compose exec app php artisan key:generate
docker compose exec app php artisan optimize:clear
```
※.env は src/.env を使用します。
.env（src/.env）を以下の通り設定してください。

APP_URL=http://localhost:8080
DB_HOST=db
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel

### 5.マイグレーション / シーディング
```bash
docker compose exec app php artisan migrate --seed
```
※注意
docker compose down -v はDBのデータ（volume）を削除します。
DBを作り直す場合は php artisan migrate:fresh --seed を実行してください

### 6.ストレージ公開 (画像表示)
```bash
docker compose exec app php artisan storage:link
```
### 7.アプリ起動確認
 - アプリ　 : http://localhost:8080
 - MailHog : http://localhost:8025

### 8.トラブルシューティング
権限やキャッシュディレクトリ不足が起こった場合
```bash
docker compose exec app sh -lc "mkdir -p storage/logs storage/framework/{cache,data,sessions,testing,views} bootstrap/cache && chmod -R 775 storage bootstrap/cache"
docker compose exec app php artisan optimize:clear
```
---

### 使い方

### 会員登録 → メール認証 (MailHog)
1. 画面から会員登録を行う
2. メール認証誘導画面が表示される
3. MailHog (http://localhost:8025) を開き、届いたメールの認証リンクをクリック
4. 認証完了後、プロフィール設定/マイページ関連フローへ遷移
※MailHogはローカル開発向けのメール確認ツールのため、画面表示は英語になります。

### 検索機能
ヘッダーの検索欄にキーワードを入力し検索できます。
検索キーワードは画面遷移後も検索欄に保持されます。

### 購入フロー (支払い方法)

#### コンビニ払い (convenience)
 - Stripeを使用せず即購入完了
 - purchases に購入情報を保存
 - 対象の item.is_sold を true に更新 (SOLD表示)

#### クレジットカード払い (credit)
 - Stripe Checkout に遷移
 - 成功URL : purchase/{item}/success
 - キャンセルURL : purchase/{item}/cancel
   ※このアプリは config('services.stripe.secret') を参照します
   ※Stripeを利用する場合は .env に STRIPE_SECRET=sk_test_... を設定(config/services.php の stripe.secret に紐づく)
   ※Stripe未設定の場合はクレジットカード払いを選択して購入するとエラーメッセージを表示し、コンビニ払いを案内します。
   ※ローカルで購入完了まで確認する場合は、コンビニ払いを利用するとスムーズです。

### PHPUnit (自動テスト)

### 実行コマンド
```bash
docker compose exec app php artisan test
```
※初回のみ、テスト用DB laravel_test が必要です。作成する場合 :
```bash
docker compose exec db mysql -uroot -p -e "
CREATE DATABASE IF NOT EXISTS laravel_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON laravel_test.* TO 'laravel'@'%';
FLUSH PRIVILEGES;
"
```

### 実行結果
 - Feature / Unit テスト : 28 tests Passed

### 補足
 - 画像のフォールバック (no-image) を用意しています
 - メール未認証ユーザーは、コメント投稿など一部機能で認証誘導へリダイレクトされます

### 手動テスト
テストケース一覧（ID1〜16）に基づき、以下の画面/機能の手動確認を実施しました。

- 会員登録 / ログイン / ログアウト（ID1〜3）
  - 各フォームのバリデーションエラーメッセージ表示を確認
- 商品一覧（おすすめ）/ SOLD表示（ID4）
  - 一覧で商品が表示されること、SOLD表示（売り切れラベル）が表示されることを確認
- マイリスト（いいね一覧 / SOLD表示 / 未認証は表示なし）（ID5）
  - いいねした商品のみがマイリストに表示されること、SOLD表示が反映されることを確認
  - ゲスト（未ログイン/未認証）はマイリストに表示されない仕様であることを確認
- 商品検索（部分一致 / キーワード保持）（ID6）
  - キーワードの部分一致検索ができることを確認
  - タブ切り替え後も検索キーワードが保持されることを確認
- 商品詳細情報（カテゴリ等の表示）（ID7）
  - 商品の詳細情報（カテゴリ等）が表示されることを確認
- いいね（追加 / 解除 / アイコン状態）（ID8）
  - 追加/解除によりカウントが増減し、アイコン状態が切り替わることを確認
- コメント（ログイン時のみ投稿 / バリデーション）（ID9）
  - ログイン時のみ投稿できること、未ログイン時は投稿できないことを確認
  - 必須/文字数などのバリデーションが動作することを確認
  - 投稿後、投稿者アイコンとコメント本文が表示されることを確認
- 購入（購入完了 / SOLD表示 / マイページ購入履歴反映）（ID10）
  - コンビニ払いで購入完了できることを確認（購入情報保存 + SOLD化）
  - SOLD商品の購入不可を確認
  - 購入後、購入者のマイページに購入履歴が反映されることを確認
  - クレジットカード払いはStripe Checkoutへ遷移し、購入完了およびキャンセルが可能なことを確認
- 支払い方法変更が反映（ID11）
  - 購入画面で選択した支払い方法が表示に反映されることを確認
- 配送先変更が購入画面に反映・購入時に保存（ID12）
  - 配送先変更が購入画面に反映されることを確認
  - 購入時に配送先情報が保存されることを確認
- マイページ表示（ユーザー情報取得 / 変更）（ID13〜14）
  - マイページにユーザー情報が表示されることを確認
  - プロフィール（ユーザー情報/アイコン）の変更ができることを確認
- 出品（登録・保存）（ID15）
  - 出品登録ができること、登録内容が保存されることを確認
- （応用）メール認証（MailHog）（ID16）
  - 会員登録後にメール認証誘導へ遷移することを確認
  - MailHogから認証リンクを開き、認証完了後にプロフィール設定へ遷移することを確認

上記は期待挙動どおりに動作することを確認しました。

### ディレクトリ構成 (主な箇所)
 - app/Http/Controllers : 各種コントローラー
 - app/Http/Requests : バリデーション (例 : PurchaseRequest)
 - resources/views : Bladeテンプレート
 - resources/css : 画面スタイル
 - database/factories : Factory (テスト用)
 - tests/Feature : 機能テスト

### 補足
 - 画像のフォールバック (no-image) を用意しています
 - メール未認証ユーザーは、コメント投稿など一部機能で認証誘導へリダイレクトされます