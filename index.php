<?php
/*
require_once('./functions.php');

// 記事一覧取得
$res = callAPI('GET');
$articles = isset($res['json']['articles'])? $res['json']['articles']: [];
// テンプレートからView読み込み
include('./template/index.php');
*/

/*** 暗号化 ***/

//暗号化対象
$post_str = '東京都港区南青山２丁目';
 
//暗号化・復元用のソルトを指定します。これは漏れたらダメなやつです
$passphrase = 'HONDARA 271'; 
 
//暗号化用のメソッドを指定
$method = 'AES-256-CBC';
//利用可能な暗号メソッドの一覧を取得するには openssl_get_cipher_methods() を使用;
/*
$ciphers = openssl_get_cipher_methods();
var_dump($ciphers);
*/
 
//暗号初期化ベクトル (iv) の長さを取得
$iv_size = openssl_cipher_iv_length($method);  

//暗号化・復元用のIVキーを作成
//暗号モードに対するIVの長さに合わせたキーを生成します
$iv = openssl_random_pseudo_bytes($iv_size);
 
//暗号化を実施
$options = OPENSSL_RAW_DATA;
$encrypted = openssl_encrypt($post_str, $method, $passphrase, $options, $iv);

//echo $encrypted;
echo base64_encode($encrypted);


/*** 復号化 ***/

//暗号化したときのソルトを指定。これは漏れたらダメなやつです
$passphrase = 'HONDARA 271';
 
//暗号化用のメソッドを指定
$method = 'AES-256-CBC';
 
//復元を実施
$options = OPENSSL_RAW_DATA;
$decrypted = openssl_decrypt($encrypted, $method, $passphrase, $options, $iv);

echo '<br>';
echo $decrypted;