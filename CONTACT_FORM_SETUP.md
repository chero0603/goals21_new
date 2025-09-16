# 問い合わせフォーム メールシステム 実装ガイド

## 概要
このシステムは、Astro.jsベースのWebサイトに問い合わせフォーム機能を実装し、管理者と問い合わせ者の両方にメール通知を送信する機能を提供します。レンタルサーバーでの運用を前提としており、バリデーション機能も含まれています。

## 機能一覧

### フロントエンド機能
- ✅ リアルタイムバリデーション
- ✅ 文字数カウンター
- ✅ フォームデータのローカル保存（ページリロード対応）
- ✅ 送信中の状態表示
- ✅ 成功・エラー通知表示
- ✅ レスポンシブデザイン

### バックエンド機能
- ✅ 包括的なサーバーサイドバリデーション
- ✅ CSRF対策
- ✅ 管理者向けメール送信
- ✅ お客様向け自動返信メール
- ✅ セキュリティヘッダー設定
- ✅ エラーログ記録

## ファイル構成

```
src/
├── components/
│   └── Contact.astro              # 問い合わせフォームコンポーネント
├── pages/
│   └── api/
│       └── contact.ts             # Astro APIエンドポイント
└── scripts/
    └── form-validation.js         # クライアントサイドバリデーション

public/
├── mail.php                       # PHP メール送信スクリプト
├── config/
│   └── mail-config.php           # メール設定ファイル
└── .htaccess                     # セキュリティ設定
```

## セットアップ手順

### 1. 基本設定

#### 1.1 メール設定ファイルの編集
`public/config/mail-config.php` を編集して、実際の環境に合わせて設定を変更してください：

```php
// 会社情報
define('COMPANY_NAME', 'あなたの会社名');

// メール設定
define('ADMIN_EMAIL', 'admin@your-domain.com');      // 管理者メールアドレス
define('SYSTEM_EMAIL', 'system@your-domain.com');   // システム送信用メールアドレス

// セキュリティ設定
define('ALLOWED_DOMAINS', [
    'your-domain.com',             // あなたのドメイン
    'www.your-domain.com'          // www付きドメイン
]);
```

#### 1.2 レンタルサーバー固有の設定

**さくらインターネットの場合：**
```php
define('ADMIN_EMAIL', 'info@your-domain.sakura.ne.jp');
define('SYSTEM_EMAIL', 'system@your-domain.sakura.ne.jp');
```

**ロリポップ・エックスサーバーの場合：**
```php
define('ADMIN_EMAIL', 'info@your-domain.com');
define('SYSTEM_EMAIL', 'system@your-domain.com');
```

### 2. ファイルのアップロード

以下のファイルをレンタルサーバーにアップロードしてください：

1. `public/mail.php` → ドキュメントルート直下
2. `public/config/mail-config.php` → ドキュメントルート/config/
3. `public/.htaccess` → ドキュメントルート直下（既存の場合は追記）

### 3. パーミッション設定

```bash
# ディレクトリ
chmod 755 config/

# ファイル
chmod 644 mail.php
chmod 644 config/mail-config.php
chmod 644 .htaccess
```

### 4. 動作テスト

#### 4.1 基本テスト
1. フォームから実際にメール送信を試す
2. 管理者メールアドレスに通知が届くか確認
3. 問い合わせ者に自動返信メールが届くか確認

#### 4.2 バリデーションテスト
- 必須項目を空で送信
- 不正なメールアドレスで送信
- 文字数制限を超えて送信

## バリデーションルール

### 必須項目
- お名前（1-50文字）
- メールアドレス（有効な形式）
- 電話番号（日本の形式）
- お問い合わせ種別
- 件名（1-100文字）
- お問い合わせ内容（10-2000文字）

### 電話番号形式
- 固定電話：03-1234-5678
- 携帯電話：090-1234-5678, 080-1234-5678, 070-1234-5678
- IP電話：050-1234-5678

## セキュリティ機能

### CSRF対策
- Refererチェック
- Origin ヘッダーチェック
- 許可ドメインの制限

### 入力値のサニタイズ
- HTMLエスケープ処理
- SQLインジェクション対策

### アクセス制御
- 設定ファイルへの直接アクセス禁止
- PHPエラーの非表示
- 不要なヘッダー情報の削除

## トラブルシューティング

### メールが送信されない場合

#### 1. PHP設定の確認
```php
// mail.php に追加してテスト
if (function_exists('mail')) {
    echo "mail()関数は利用可能です\n";
} else {
    echo "mail()関数が無効になっています\n";
}
```

#### 2. レンタルサーバーのメール制限
- 送信可能なメールアドレス形式を確認
- SMTP認証が必要な場合は、PHPMailerの使用を検討

#### 3. ログの確認
```php
// エラーログの場所を確認
echo "Error log: " . ini_get('error_log');
```

### よくある問題と解決策

#### メールの文字化け
```php
// mail.php のヘッダー設定を確認
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
```

#### 送信者アドレスの制限
一部のレンタルサーバーでは、Fromヘッダーに特定のドメインのメールアドレスしか使用できません。その場合は設定ファイルのSYSTEM_EMAILを適切なアドレスに変更してください。

#### API通信エラー
- フロントエンドからPHPスクリプトへの通信が失敗する場合は、パスの設定を確認してください
- CORS設定が必要な場合があります

## カスタマイズ

### メールテンプレートの変更
`mail.php` の `createAdminMailBody()` と `createCustomerMailBody()` 関数を編集してください。

### バリデーションルールの追加
1. `src/scripts/form-validation.js` のvalidationRulesを編集
2. `src/pages/api/contact.ts` のvalidateContactForm関数を編集
3. `public/mail.php` のvalidateContactForm関数を編集

### 新しいフィールドの追加
1. `src/components/Contact.astro` にフォームフィールドを追加
2. 上記3つのバリデーション関数に対応するルールを追加

## 本番環境での注意事項

1. **設定ファイルの保護**: mail-config.php への直接アクセスを禁止
2. **エラー表示の無効化**: DEBUG_MODE を false に設定
3. **ログ監視**: エラーログを定期的にチェック
4. **メール送信制限**: レンタルサーバーの送信制限を確認
5. **SSL/TLS**: HTTPS通信の使用を推奨

## サポート

実装や設定でご不明な点がございましたら、以下の情報とともにお問い合わせください：

- 使用しているレンタルサーバー名
- エラーメッセージの詳細
- PHP バージョン
- 設定ファイルの内容（メールアドレス等は除く）