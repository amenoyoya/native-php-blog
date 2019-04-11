<?php
/** GETメソッド: ユーザー認証とトークンの取得 **/

require_once('./tokenizer.php');

/* ユーザー認証＆トークン取得 */
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

    // バリデーション（ユーザー名の重複確認なし）
    if(!isValid($name, $password, $response)) return $response;
    
    // ユーザー認証
    $state = $pdo->prepare('select * from users where name=?');
    if(!$state->bindValue(1, $name, PDO::PARAM_STR) || !$state->execute()){
        return [
            'status' => 500, 'message' => 'ユーザー情報取得中にエラーが発生しました',
        ];
    }
    if(!($row = $state->fetch())){ // ユーザーが存在しない
        return [
            'status' => 400, 'message' => 'ユーザーは登録されていません',
        ];
    }
    
    // パスワードチェック
    if(!password_verify($password, $row['password'])){ // 認証失敗
        return [
            'status' => 400, 'message' => 'ユーザー名、もしくはパスワードが違います',
        ];
    }
    
    // ユーザー認証できたらトークンを発行
    // - 暗号化キーはMySQLのパスワードを流用
    // - 有効期間は1日間
    // - 含める情報はユーザーIDとユーザー名
    return [
        'status' => 200,
        'token' => encryptToken(MYSQL_PASSWORD, 60 * 60 * 24, [
            'id' => $row['id'], 'name' => $row['name'],
        ]),
    ];
}