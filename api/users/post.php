<?php
/** POSTメソッド: 新規ユーザー登録 **/

require_once('./validation.php');

/**
 * 新規ユーザー登録
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, message: 処理結果のメッセージ]
 *           status: 201 Created（正常に登録完了）, 400 Bad Request（リクエストが不正）, 500 Internal Server Error（データベース処理エラー）
 */
function registerUser($params, $pdo){
    // パラメータチェック
    if(!isset($params['user-name']) || !isset($params['user-password'])){
        return [
            'status' => 400, 'message' => 'パラメータが正しく指定されていません',
        ];
    }
    $name = $params['user-name'];
    $password = $params['user-password'];

    // バリデーションチェック
    if(!isValid($name, $password, $response, $pdo)) return $response;
    
    // パスワードはハッシュ化して保存する
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // usersテーブルにデータ挿入
    $state = $pdo->prepare('insert into users (name, password) values (?, ?)');
    if(!$state->bindValue(1, $name, PDO::PARAM_STR)
        || !$state->bindValue(2, $hash, PDO::PARAM_STR)
        || !$state->execute())
    {
        return [
            'status' => 500, 'message' => 'ユーザー登録中にエラーが発生しました',
        ];
    }
    return [
        'status' => 201,
        'message' => '新規ユーザー「' . htmlspecialchars($name) . '」が登録されました',
    ];
}