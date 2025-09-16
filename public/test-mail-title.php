<?php
/**
 * メールタイトルのテスト用スクリプト
 */

// 文字エンコーディングの設定
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_regex_encoding('UTF-8');

// テスト用のタイトル
$titles = [
    '【お問い合わせ】サービスについて',
    'お問い合わせありがとうございます【Goals21】',
    'テスト件名：日本語のメールタイトル'
];

echo "<h2>メールタイトルのエンコーディングテスト</h2>";

foreach ($titles as $title) {
    echo "<h3>元のタイトル:</h3>";
    echo "<p>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</p>";
    
    echo "<h4>mb_encode_mimeheader() でエンコード:</h4>";
    $encoded = mb_encode_mimeheader($title, 'UTF-8', 'B');
    echo "<p>" . htmlspecialchars($encoded, ENT_QUOTES, 'UTF-8') . "</p>";
    
    echo "<h4>そのままUTF-8:</h4>";
    echo "<p>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</p>";
    
    echo "<hr>";
}

echo "<h3>文字エンコーディング情報:</h3>";
echo "<ul>";
echo "<li>mb_internal_encoding(): " . mb_internal_encoding() . "</li>";
echo "<li>default_charset: " . ini_get('default_charset') . "</li>";
echo "<li>mb_http_output(): " . mb_http_output() . "</li>";
echo "</ul>";
?>