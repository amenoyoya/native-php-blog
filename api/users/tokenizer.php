<?php
/** JSON簡易暗号化（トークン発行機能）関連 **/

/* トークン発行 */
// @password: 暗号化・復号パスワード
// @expire: トークンの有効期間（秒）
// @json: 暗号化するJSONデータ（expireキーは設定しないこと）
// @return: string トークン
function encryptToken($password, $expire, $json){
    // JSONデータに有効期限を設定
    $json['expire'] = time() + $expire;
    
    // JSONデータを平文化
    $json_str = json_encode($json); 
    
    // 暗号化用のメソッド: AES-256-CBC
    // 利用可能な暗号メソッドの一覧を取得するには openssl_get_cipher_methods() を使用
    $method = 'AES-256-CBC';
    
    // 暗号初期化ベクトル (IV) の長さを取得
    $iv_size = openssl_cipher_iv_length($method);  

    // 暗号化・復元用のIVキーを作成
    // 暗号モードに対するIVの長さに合わせたキーを生成
    $iv = openssl_random_pseudo_bytes($iv_size);
    
    // 暗号化
    $options = OPENSSL_RAW_DATA;
    $encrypted = openssl_encrypt($json_str, $method, $password, $options, $iv);

    // 暗号化データと初期化ベクトルを含むJSONデータを認証用データとする
    $authdata = ['enc' => base64_encode($encrypted), 'iv' => base64_encode($iv)];
    $authdata_str = json_encode($authdata);

    // トークンは認証用データをBASE64エンコードしたものとする
    return base64_encode($authdata_str);
}

/* トークン認証＆JSONデータ復号 */
// @password: 暗号化・復号パスワード
// @token: 発行されたトークン
// @return: 成功時=[JSONデータ], 失敗時=false
function decryptToken($password, $token){
    // トークンを認証用JSONデータに変換
    $authdata = json_decode(base64_decode($token), true);
    if(!$authdata) return false;
    if(!isset($authdata['enc']) || !isset($authdata['iv'])) return false;

    // 復号に必要な暗号文と初期化ベクトルを取得
    $enc = base64_decode($authdata['enc']);
    $iv = base64_decode($authdata['iv']);
    
    // 復号処理
    $method = 'AES-256-CBC';
    $options = OPENSSL_RAW_DATA;
    $result = openssl_decrypt($enc, $method, $password, $options, $iv);
    if(!$result) return false;
    
    // JSONデータに戻す
    $json = json_decode($result, true);

    // トークンの有効期限内か確認
    if(!isset($json['expire'])) return false;
    if($json['expire'] - time() < 0) return false; // 期限切れ

    // expireキーを削除してJSONデータを返す
    unset($json['expire']);
    return $json;
}