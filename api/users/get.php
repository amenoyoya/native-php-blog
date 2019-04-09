<?php
/** GETメソッド: ユーザー認証とトークンの取得 **/

require_once('./php-jwt/JWT.php');
use \Firebase\JWT\JWT;

/* 前準備: 署名用の鍵の生成
$ cd ~
$ mkdir keys
$ chmod 700 keys
$ cd keys

// パスフレーズなしで鍵生成
$ ssh-keygen -t rsa -b 4096 -f blog.key
$ openssl rsa -in blog.key -pubout -outform PEM -out blog.key.pub
writing RSA key

$ chmod 600 blog.key blog.key.pub
 */

// トークン取得
// @params: 受信パラメータ
// @pdo: PDOオブジェクト
// @return: [status: ステータスコード, token: トークン]
//           ステータスコード: 200 OK（正常に取得完了）, 400 Bad Request（ユーザー認証失敗）
function getUserToken($params, $pdo){
    // パラメータチェック
    if(!isset($params['user-name']) || !isset($params['user-password'])){
        return [
            'status' => 400, 'message' => 'パラメータが正しく指定されていません',
        ];
    }
    $name = $params['user-name'];
    $password = $params['user-password'];
    // ユーザー認証
    $state = $pdo->prepare('select id from users where name=? and password=?');
    if(!$state->bindValue(1, $name, PDO::PARAM_STR)
        || !$state->bindValue(2, $password, PDO::PARAM_STR)
        || !$state->execute())
    {
        return [
            'status' => 500, 'message' => 'ユーザー情報取得中にエラーが発生しました',
        ];
    }
    if($row = $state->fetch()){
        // ユーザー認証できたら、ユーザーIDを含む JSON Web Token を発行
        $current_time = time();
        $expiry = $current_time + (24 * 60 * 60); //有効期限として1日後を指定
        $claims = [
            'iat' => $current_time,
            'exp' => $expiry,
            'user_id' => $row['id'],
        ];
        // 秘密鍵の取得
        $private_key = file_get_contents('~/keys/blog.key');
        // エンコード
        $jwt = JWT::encode($claims, $private_key, 'RS256');
        return [
            'status' => 200, 'token' => $jwt,
        ];
    }
    // ユーザー認証失敗
    return [
        'status' => 400, 'message' => 'ユーザーは登録されていません',
    ];
}