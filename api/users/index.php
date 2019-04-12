<?php

require_once('../functions.php');
require_once('./functions.php');

/**
 * ユーザー情報取得API
 * - ユーザートークンが渡されればユーザーIDとユーザー名取得
 * - ユーザー名とパスワードが渡されれば認証＆トークン取得
 * 
 * @internal: メイン処理で呼び出される
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, message: 処理結果のメッセージ]
 *           status: 201 Created（正常に登録完了）, 400 Bad Request（リクエストが不正）, 500 Internal Server Error（データベース処理エラー）
 */
function getUserInfo($params, $pdo){
  // ユーザートークンが渡されていないなら認証処理
  if(!isset($params['user-token'])) return getUserToken($params, $pdo);

  // トークンが渡されているならユーザー情報取得
  if(false === ($user = checkUserState($params, $response))) return $response;
  return [
    'status' => 200, 'user' => $user,
  ];
}


/**
 * 新規ユーザー登録API
 * 
 * @internal: メイン処理で呼び出される
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


/**
 * メイン処理
 * リクエストを処理してJSONデータを返す
 */
procAPIRequest(function($method, $params, $pdo, &$response){
  switch($method){
  case 'GET':
    // ユーザー情報 or トークン取得
    $response = getUserInfo($params, $pdo);
    return true;
  case 'POST':
    // 新規ユーザー登録
    $response = registerUser($params, $pdo);
    return true;
  }
  return false;
});