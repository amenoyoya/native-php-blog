<?php

require_once('../functions.php');
require_once('./functions.php');

/**
 * タグ一覧の取得API
 * 
 * @internal: メイン処理で呼び出される
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, user: ユーザー情報, tags: [タグ情報の配列], message: エラーメッセージ]
 *           status: 200 OK（正常に取得完了）, 400 Bad Request, 401 Unauthorized, 500 Internal Server Error
 */
function getTags($params, $pdo){
  // ユーザー認証チェック
  if(false === ($user = checkUserState($params, $response))) return $response;
  
  // 認証済みユーザーのタグ一覧取得
  $state = $pdo->prepare('select * from tags where user_id=?');
  if(!$state->bindValue(1, $user['id'], PDO::PARAM_INT) || !$state->execute()){
    return [
      'status' => 500, 'message' => 'タグ取得中にエラーが発生しました',
    ];
  }
  $tags = [];
  while($row = $state->fetch(PDO::FETCH_ASSOC)){
    // HTMLエスケープしながらタグ情報を取得
    $tags[] = [
      'id' => htmlspecialchars($row['id']),
      'name' => htmlspecialchars($row['name']),
    ];
  }
  return [
    'status' => 200, 'user' => $user, 'tags' => $tags,
  ];
}


/**
 * 新規タグ登録API
 * 
 * @internal: メイン処理で呼び出される
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, user: ユーザー情報, message: 処理結果のメッセージ]
 *           sttaus: 201 Created（正常に作成完了）, 400 Bad Request（リクエストが不正）,
 *                   401 Unauthorized（認証が必要）, 500 Internal Server Error（データベース処理エラー）
 */
function registerTag($params, $pdo){
  // ユーザー認証チェック
  if(false === ($user = checkUserState($params, $response))) return $response;

  // パラメータチェック
  if(!isset($params['tag-name'])){
    return [
      'status' => 400, 'message' => 'パラメータが正しく指定されていません',
    ];
  }
  $name = $params['tag-name'];

  // バリデーションチェック（＋タグ名の重複確認）
  if(!isValid($name, $res, $pdo)) return $res;
  
  // tagsテーブルにデータ挿入
  $state = $pdo->prepare('insert into tags (user_id, name) values (?, ?)');
  if(!$state->bindValue(1, $user['id'], PDO::PARAM_INT)
    || !$state->bindValue(2, $name, PDO::PARAM_STR)
    || !$state->execute())
  {
    return [
      'status' => 500, 'message' => 'タグ登録中にエラーが発生しました',
    ];
  }
  // タグ名をHTMLエスケープして結果を返す
  $name = htmlspecialchars($name);
  return [
    'status' => 201,
    'user' => $user, 'tag-name' => $name,
    'message' => 'タグ「' . $name . '」が登録されました',
  ];
}


/**
 * メイン処理
 * リクエストを処理してJSONデータを返す
 */
procAPIRequest(function($method, $params, $pdo, &$response){
  switch($method){
  case 'GET':
    // タグ一覧取得
    $response = getTags($params, $pdo);
    return true;
  case 'POST':
    // 新規タグ登録
    $response = registerTag($params, $pdo);
    return true;
  }
  return false;
});