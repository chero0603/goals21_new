<?php
/**
 * メール送信設定ファイル
 * レンタルサーバーに応じて適切な値に変更してください
 */

// 会社情報
define('COMPANY_NAME', '株式会社GOALs21');

// メール設定
// define('ADMIN_EMAIL', 'support@gold-t.tokyo');           // 管理者メールアドレス（要変更）
// define('SYSTEM_EMAIL', 'support@gold-t.tokyo');       // システム送信用メールアドレス（要変更）
define('ADMIN_EMAIL', 'support@gold-t.tokyo');           // 管理者メールアドレス（要変更）
define('SYSTEM_EMAIL', 'chero@grooow.com');       // システム送信用メールアドレス（要変更）

// セキュリティ設定
define('ALLOWED_DOMAINS', [
    'localhost',                    // 開発環境
    '127.0.0.1',                   // 開発環境
    'gold-t.tokyo',             // 本番ドメイン（要変更）
    'gold-t.tokyo'          // 本番ドメイン（要変更）
]);

// その他の設定
define('LOG_ENABLED', true);       // ログ記録の有効/無効
define('DEBUG_MODE', false);       // デバッグモード（本番では false）

/**
 * レンタルサーバー別の設定例
 */

// さくらインターネットの場合
// define('ADMIN_EMAIL', 'info@your-domain.sakura.ne.jp');
// define('SYSTEM_EMAIL', 'system@your-domain.sakura.ne.jp');

// ロリポップの場合
// define('ADMIN_EMAIL', 'info@your-domain.com');
// define('SYSTEM_EMAIL', 'system@your-domain.com');

// エックスサーバーの場合
// define('ADMIN_EMAIL', 'info@your-domain.com');
// define('SYSTEM_EMAIL', 'system@your-domain.com');

/**
 * 注意事項：
 * 1. 本番環境では必ず実際のドメイン名とメールアドレスに変更してください
 * 2. レンタルサーバーによってはSMTP認証が必要な場合があります
 * 3. 一部のレンタルサーバーではFromヘッダーに制限があります
 * 4. セキュリティのため、このファイルへの直接アクセスを制限してください
 */

// 直接アクセスを禁止
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    http_response_code(403);
    exit('Access denied');
}
?>