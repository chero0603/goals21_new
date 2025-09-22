<?php
/**
 * メール送信スクリプト（レンタルサーバー用）
 * 管理者と問い合わせ者の両方にメールを送信
 */

// 文字エンコーディングの設定
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_regex_encoding('UTF-8');

// エラーレポートの設定（本番では非表示推奨）
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// 設定ファイルの読み込み
require_once 'config/mail-config.php';

// CSRF対策：Refererチェック
function validateReferer() {
    $allowedDomains = ALLOWED_DOMAINS;
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    
    if (empty($referer)) {
        return false;
    }
    
    $refererHost = parse_url($referer, PHP_URL_HOST);
    return in_array($refererHost, $allowedDomains);
}

// 入力値のサニタイズ（メール送信用とHTML出力用を分ける）
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// メール本文用のサニタイズ（HTMLエスケープなし）
function sanitizeForEmail($data) {
    if (is_array($data)) {
        return array_map('sanitizeForEmail', $data);
    }
    return trim($data);
}

// バリデーション関数
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    // 日本の電話番号形式をチェック
    $phone = preg_replace('/[\s\-]/', '', $phone);
    return preg_match('/^(0[1-9]\d{8,9}|050\d{8}|0[789]0\d{8})$/', $phone);
}

function validateContactForm($data) {
    $errors = [];

    // 必須フィールドのチェック
    if (empty($data['name']) || mb_strlen($data['name']) < 1) {
        $errors[] = 'お名前は必須です';
    } elseif (mb_strlen($data['name']) > 50) {
        $errors[] = 'お名前は50文字以内で入力してください';
    }

    if (empty($data['email'])) {
        $errors[] = 'メールアドレスは必須です';
    } elseif (!validateEmail($data['email'])) {
        $errors[] = '正しいメールアドレスを入力してください';
    }

    if (empty($data['phone'])) {
        $errors[] = '電話番号は必須です';
    } elseif (!validatePhone($data['phone'])) {
        $errors[] = '正しい電話番号を入力してください';
    }

    if (empty($data['inquiry_type'])) {
        $errors[] = 'お問い合わせ種別を選択してください';
    }

    if (empty($data['subject']) || mb_strlen($data['subject']) < 1) {
        $errors[] = '件名は必須です';
    } elseif (mb_strlen($data['subject']) > 100) {
        $errors[] = '件名は100文字以内で入力してください';
    }

    if (empty($data['message']) || mb_strlen($data['message']) < 10) {
        $errors[] = 'お問い合わせ内容は10文字以上で入力してください';
    } elseif (mb_strlen($data['message']) > 2000) {
        $errors[] = 'お問い合わせ内容は2000文字以内で入力してください';
    }

    return $errors;
}

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

// お客様向け自動返信メール本文作成
function createCustomerMailBody($data) {
    $inquiryType = getInquiryTypeLabel($data['inquiry_type']);
    
    $body = <<<EOT
{$data['name']} 様

この度は、弊社ウェブサイトよりお問い合わせをいただき、誠にありがとうございます。
以下の内容でお問い合わせを受け付けいたしました。

■ お問い合わせ内容
お問い合わせ種別: {$inquiryType}
件名: {$data['subject']}

内容:
{$data['message']}

※このメールは自動送信されています。
※心当たりのない方は、お手数ですが削除していただきますようお願いいたします。

────────────────────────
株式会社Goals21
〒103-0027 東京都中央区日本橋3-2-14 日本橋KNビル4F
TEL: 03-5201-3756 / FAX: 03-5201-3712
E-mail: supportlp@gold-t.tokyo
────────────────────────
EOT;

    return $body;
}

// メール送信処理
function sendMail($to, $subject, $body, $headers, $envelopeFrom = SYSTEM_EMAIL) {
    // MB_Stringの設定
    mb_language('ja');
    mb_internal_encoding('UTF-8');
    
    // PHPのメール設定を一時的に変更
    $original_charset = ini_get('default_charset');
    ini_set('default_charset', 'UTF-8');
    
    // 件名をそのまま送信（UTF-8で）
    // レンタルサーバー対策としてエンベロープFromを指定
    $additionalParams = '-f' . $envelopeFrom;
    $result = mb_send_mail($to, $subject, $body, $headers, $additionalParams);
    
    // 設定を元に戻す
    ini_set('default_charset', $original_charset);
    
    return $result;
}

// メイン処理
try {
    // POSTリクエストのみ許可
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => false,
            'message' => '不正なアクセス方法です。お問い合わせフォームから送信してください。',
            'error_type' => 'method_not_allowed'
        ]);
        exit;
    }

    // Refererチェック（本番環境では有効化推奨）
    // if (!validateReferer()) {
    //     http_response_code(403);
    //     echo json_encode([
    //         'success' => false,
    //         'message' => 'セキュリティエラーが発生しました。正規のページからアクセスしてください。',
    //         'error_type' => 'invalid_referer'
    //     ]);
    //     exit;
    // }

    // フォームデータの取得とサニタイズ
    $formData = [
        'name' => sanitizeInput($_POST['name'] ?? ''),
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'phone' => sanitizeInput($_POST['phone'] ?? ''),
        'inquiry_type' => sanitizeInput($_POST['inquiry_type'] ?? ''),
        'subject' => sanitizeInput($_POST['subject'] ?? ''),
        'message' => sanitizeInput($_POST['message'] ?? ''),
        'timestamp' => $_POST['timestamp'] ?? date('c'),
        'client_ip' => $_POST['client_ip'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];

    // メール送信用のデータ（HTMLエスケープなし）
    $mailData = [
        'name' => sanitizeForEmail($_POST['name'] ?? ''),
        'email' => sanitizeForEmail($_POST['email'] ?? ''),
        'phone' => sanitizeForEmail($_POST['phone'] ?? ''),
        'inquiry_type' => sanitizeForEmail($_POST['inquiry_type'] ?? ''),
        'subject' => sanitizeForEmail($_POST['subject'] ?? ''),
        'message' => sanitizeForEmail($_POST['message'] ?? ''),
        'timestamp' => $_POST['timestamp'] ?? date('c'),
        'client_ip' => $_POST['client_ip'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];

    // バリデーション
    $errors = validateContactForm($formData);
    if (!empty($errors)) {
        http_response_code(400);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => false,
            'message' => '入力内容に不備があります。以下の項目をご確認ください。',
            'errors' => $errors,
            'error_type' => 'validation'
        ]);
        exit;
    }

    // メールヘッダーの設定
    // 送信者名に日本語が含まれている場合のみエンコード
    $fromName = COMPANY_NAME;
    $replyToName = $mailData['name'];
    
    // 日本語文字が含まれているかチェック
    if (mb_strlen($fromName) !== strlen($fromName)) {
        $fromName = mb_encode_mimeheader($fromName, 'UTF-8', 'B');
    }
    if (mb_strlen($replyToName) !== strlen($replyToName)) {
        $replyToName = mb_encode_mimeheader($replyToName, 'UTF-8', 'B');
    }
    
    // 制限緩和: ドメイン不一致時の自動調整
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $fallbackSender = $host ? ('noreply@' . preg_replace('/^www\./', '', $host)) : SYSTEM_EMAIL;
    $fromAddress = (defined('RELAX_FROM_POLICY') && RELAX_FROM_POLICY) ? $fallbackSender : SYSTEM_EMAIL;

    $adminHeaders = "From: " . $fromName . " <" . $fromAddress . ">\r\n";
    $adminHeaders .= "Reply-To: " . $replyToName . " <" . $mailData['email'] . ">\r\n";
    $adminHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $adminHeaders .= "Content-Transfer-Encoding: 8bit\r\n";
    $adminHeaders .= "MIME-Version: 1.0\r\n";
    $adminHeaders .= "Sender: " . SYSTEM_EMAIL . "\r\n";
    if (defined('DEBUG_BCC_EMAIL') && DEBUG_BCC_EMAIL) {
        $adminHeaders .= "Bcc: " . DEBUG_BCC_EMAIL . "\r\n";
    }
    $adminHeaders .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    $customerHeaders = "From: " . $fromName . " <" . $fromAddress . ">\r\n";
    $customerHeaders .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
    $customerHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $customerHeaders .= "Content-Transfer-Encoding: 8bit\r\n";
    $customerHeaders .= "MIME-Version: 1.0\r\n";
    $customerHeaders .= "Sender: " . SYSTEM_EMAIL . "\r\n";
    if (defined('DEBUG_BCC_EMAIL') && DEBUG_BCC_EMAIL) {
        $customerHeaders .= "Bcc: " . DEBUG_BCC_EMAIL . "\r\n";
    }
    $customerHeaders .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    // 管理者向けメール送信
    $adminSubject = '【お問い合わせ】' . $mailData['subject'];
    $adminBody = createAdminMailBody($mailData);
    $adminSent = sendMail(ADMIN_EMAIL, $adminSubject, $adminBody, $adminHeaders, $fromAddress);

    // お客様向け自動返信メール送信
    $customerSubject = 'お問い合わせありがとうございます【' . COMPANY_NAME . '】';
    $customerBody = createCustomerMailBody($mailData);
    $customerSent = sendMail($mailData['email'], $customerSubject, $customerBody, $customerHeaders, $fromAddress);

    // 結果の判定
    if ($adminSent) {
        // ログ記録（オプション）
        error_log("Contact form success: {$mailData['email']} - {$mailData['subject']}", 0);
        
        http_response_code(200);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => true,
            'message' => $customerSent
                ? 'お問い合わせを受け付けました！ご入力いただいたメールアドレスに確認メールをお送りしました。担当者より2営業日以内にご連絡いたします。'
                : 'お問い合わせを受け付けました。確認メールの送信に一時的に失敗しましたが、担当者よりご連絡いたします。',
            'details' => [
                'sent_to_admin' => true,
                'sent_to_customer' => (bool) $customerSent,
                'customer_email' => $mailData['email']
            ],
            'warnings' => $customerSent ? [] : ['customer_auto_reply_failed']
        ]);
    } else {
        // 詳細なエラー情報を提供
        $errorDetails = [];
        $userMessage = '';
        
        if (!$adminSent && !$customerSent) {
            $userMessage = 'メール送信に失敗しました。時間をおいて再度お試しいただくか、お電話にてお問い合わせください。';
            $errorDetails['issue'] = 'both_failed';
        } elseif (!$adminSent) {
            $userMessage = 'お問い合わせの受付メールの送信に失敗しました。お電話にてお問い合わせください。';
            $errorDetails['issue'] = 'admin_failed';
        } 
        
        // エラーログ記録
        error_log("Mail send failed - Admin: " . ($adminSent ? 'OK' : 'FAIL') . 
                 ", Customer: " . ($customerSent ? 'OK' : 'FAIL') . 
                 " - Email: {$mailData['email']}", 0);
        
        http_response_code(500);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => false,
            'message' => $userMessage,
            'error_type' => 'mail_send',
            'details' => [
                'admin_sent' => $adminSent,
                'customer_sent' => $customerSent,
                'contact_info' => '緊急時連絡先: 03-5201-3756'
            ]
        ]);
    }

} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage() . " - File: " . $e->getFile() . " - Line: " . $e->getLine(), 0);
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'success' => false,
        'message' => 'システムエラーが発生しました。恐れ入りますが、時間をおいて再度お試しいただくか、お電話にてお問い合わせください。',
        'error_type' => 'system_error',
        'details' => [
            'contact_info' => '電話: 03-5201-3756 (平日 9:00-18:00)',
            'email_info' => 'supportlp@gold-t.tokyo'
        ]
    ]);
}
?>