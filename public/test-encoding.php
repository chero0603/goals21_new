<?php
/**
 * メール文字化けテスト用スクリプト
 */

// 文字エンコーディングの設定
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_regex_encoding('UTF-8');

// 設定ファイルの読み込み
require_once 'config/mail-config.php';

// テスト用データ
$testData = [
    'name' => 'テスト 太郎',
    'email' => 'test@example.com',
    'phone' => '090-1234-5678',
    'inquiry_type' => '一般的なお問い合わせ',
    'subject' => 'テスト件名',
    'message' => 'これはテストメッセージです。日本語の文字化けテストを行います。',
    'timestamp' => date('Y-m-d H:i:s'),
    'client_ip' => '127.0.0.1'
];

// お問い合わせ種別の日本語変換
function getInquiryTypeLabel($type) {
    $types = [
        'general' => '一般的なお問い合わせ',
        'account_closure' => '海外口座解約について',
        'procedures' => '手続きについて',
        'consultation' => '無料相談を希望'
    ];
    return $types[$type] ?? $type;
}

// 管理者向けメール本文作成
function createAdminMailBody($data) {
    $inquiryType = getInquiryTypeLabel($data['inquiry_type']);
    $timestamp = date('Y年m月d日 H:i:s');
    
    $body = <<<EOT
【ウェブサイトからのお問い合わせ】

受信日時: {$timestamp}

■ お客様情報
お名前: {$data['name']}
メールアドレス: {$data['email']}
電話番号: {$data['phone']}
お問い合わせ種別: {$inquiryType}

■ お問い合わせ内容
件名: {$data['subject']}

内容:
{$data['message']}

■ 送信情報
IPアドレス: {$data['client_ip']}
送信時刻: {$data['timestamp']}

---
このメールは自動送信されています。
EOT;

    return $body;
}

// メール本文を生成して表示
$mailBody = createAdminMailBody($testData);

// HTTPヘッダーを設定
header('Content-Type: text/plain; charset=UTF-8');

echo "=== メール文字化けテスト ===\n\n";
echo "現在の文字エンコーディング設定:\n";
echo "mb_internal_encoding: " . mb_internal_encoding() . "\n";
echo "default_charset: " . ini_get('default_charset') . "\n";
echo "mb_http_output: " . mb_http_output() . "\n\n";

echo "=== 生成されたメール本文 ===\n";
echo $mailBody;
echo "\n\n";

echo "=== MIMEヘッダーエンコードテスト ===\n";
echo "件名: " . mb_encode_mimeheader('【お問い合わせ】' . $testData['subject'], 'UTF-8', 'B') . "\n";
echo "差出人名: " . mb_encode_mimeheader('テスト会社', 'UTF-8', 'B') . "\n";
?>