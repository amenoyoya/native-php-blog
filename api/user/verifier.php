<?php
/** トークン認証＆ユーザー情報取得関連 **/

require_once('../users/tokenizer.php'); // トークン関連ライブラリ

/* ユーザー認証チェック */
// @params: 受信パラメータ
// @response: 認証エラーが発生した場合にレスポンスデータが渡される
// @return: ユーザー認証済みなら[id: ユーザーID, name: ユーザー名], ユーザー未認証ならfalse
function getUserInfo($params, &$response){
    // パラメータチェック
    if(!isset($params['user-token'])){
        $response = [
            'status' => 400, 'message' => 'パラメータが正しく指定されていません',
        ];
        return false;
    }

    // トークン認証
    // 暗号化パスワードはMySQLのパスワードを流用
    $user = decryptToken(MYSQL_PASSWORD, $params['user-token']);
    if(!$user){
        $response = [
            'status' => 401, 'message' => 'ユーザーが認証されていません',
        ];
        return false;
    }
    return $user;
}