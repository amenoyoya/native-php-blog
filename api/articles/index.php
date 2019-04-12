<?php

require_once('../functions.php');
require_once('./functions.php');

/**
 * ブログ記事一覧の取得API
 * 
 * @internal: メイン処理で呼び出される
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, articles: [記事オブジェクトの配列], article: 単一記事, message: エラーメッセージ]
 *           status: 200 OK（正常に取得完了）, 400 Bad Request, 401 Unauthorized, 500 Internal Server Error
 */
function getArticles($params, $pdo){
  // ユーザー認証チェック
  if(false === ($user = checkUserState($params, $response))) return $response;
  
  // article-idが指定されていれば、単一記事の取得
  if(isset($params['article-id'])) return getArticle($pdo, $user, $params['article-id']);
  
  // 認証済みユーザーの記事一覧取得：最新記事順（idの降順）
  $state = $pdo->prepare('select * from articles where user_id=? order by id desc');
  if(!$state->bindValue(1, $user['id'], PDO::PARAM_INT) || !$state->execute()){
      return [
          'status' => 500, 'message' => '記事取得中にエラーが発生しました',
      ];
  }
  $articles = [];
  while($row = $state->fetch(PDO::FETCH_ASSOC)){
      // HTMLエスケープしながら記事データを取得
      $articles[] = [
          'id' => htmlspecialchars($row['id']),
          'title' => htmlspecialchars($row['title']),
          'body' => htmlspecialchars($row['body']),
      ];
  }
  return [
      'status' => 200, 'user' => $user, 'articles' => $articles,
  ];
}


/**
 * 新規記事作成API
 * 
 * @internal: メイン処理で呼び出される
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, message: 処理結果のメッセージ]
 *           sttaus: 201 Created（正常に作成完了）, 400 Bad Request（リクエストが不正）,
 *                   401 Unauthorized（認証が必要）, 500 Internal Server Error（データベース処理エラー）
 */
function createArticle($params, $pdo){
  // ユーザー認証チェック
  if(false === ($user = checkUserState($params, $response))) return $response;

  // パラメータチェック
  if(!isset($params['article-title']) || !isset($params['article-body'])){
      return [
          'status' => 400, 'message' => 'パラメータが正しく指定されていません',
      ];
  }
  $title = $params['article-title'];
  $body = $params['article-body'];

  // バリデーションチェック
  if(!isValid($title, $body, $res)) return $res;
  

  // articlesテーブルにデータ挿入
  $state = $pdo->prepare('insert into articles (user_id, title, body) values (?, ?, ?)');
  if(!$state->bindValue(1, $user['id'], PDO::PARAM_INT)
      || !$state->bindValue(2, $title, PDO::PARAM_STR)
      || !$state->bindValue(3, $body, PDO::PARAM_STR)
      || !$state->execute())
  {
      return [
          'status' => 500, 'message' => '記事登録中にエラーが発生しました',
      ];
  }

  // 登録された記事のID（一番新しいID）を取得
  $state = $pdo->prepare('select max(id) as id from articles where user_id=?;');
  if(!$state->bindValue(1, $user['id'], PDO::PARAM_INT)
    || !$state->execute() || !($row = $state->fetch()))
  {
      return [
          'status' => 500, 'message' => '記事確認中にエラーが発生しました',
      ];
  }

  return [
      'status' => 201,
      'user' => $user, 'article-id' => htmlspecialchars($row['id']),
      'message' => 'ブログ記事「' . htmlspecialchars($title) . '」が登録されました',
  ];
}


/**
 * 記事更新API
 * 
 * @internal: メイン処理で呼び出される
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, message: 処理結果のメッセージ]
 *           status: 200 OK（正常に更新完了）, 400 Bad Request（リクエストが不正）,
 *                   401 Unauthorized（認証が必要）, 500 Internal Server Error（データベース処理エラー）
 */
function updateArticle($params, $pdo){
  // ユーザー認証チェック
  if(false === ($user = checkUserState($params, $response))) return $response;
  
  // パラメータチェック
  if(!isset($params['article-id']) || !isset($params['article-title']) || !isset($params['article-body'])){
      return [
          'status' => 400, 'message' => 'パラメータが正しく指定されていません',
      ];
  }
  $id = $params['article-id'];
  $title = $params['article-title'];
  $body = $params['article-body'];

  // 記事の存在確認
  if(!isArticleExists($pdo, $user['id'], $id, $response)) return $response;

  // バリデーションチェック
  if(!isValid($title, $body, $res)) return $res;

  // articlesテーブルを更新
  $state = $pdo->prepare('update articles set title=?, body=? where id=?');
  if(!$state->bindValue(1, $title, PDO::PARAM_STR)
      || !$state->bindValue(2, $body, PDO::PARAM_STR)
      || !$state->bindValue(3, $id, PDO::PARAM_INT)
      || !$state->execute())
  {
      return [
          'status' => 500, 'message' => '記事更新中にエラーが発生しました',
      ];
  }
  return [
      'status' => 200,
      'user' => $user,
      'message' => 'ブログ記事「' . htmlspecialchars($title) . '」が更新されました',
  ];
}


/**
 * 記事削除API
 * 
 * @internal: メイン処理で呼び出される
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, message: 処理結果のメッセージ]
 *           status: 200 OK（正常に更新完了）, 400 Bad Request（リクエストが不正）,
 *                   401 Unauthorized（認証が必要）, 500 Internal Server Error（データベース処理エラー）
 */
function deleteArticle($params, $pdo){
  // ユーザー認証チェック
  if(false === ($user = checkUserState($params, $response))) return $response;
  
  // パラメータチェック
  if(!isset($params['article-id'])){
      return [
          'status' => 400, 'message' => 'パラメータが正しく指定されていません',
      ];
  }
  $id = $params['article-id'];

  // 記事の存在確認
  if(!isArticleExists($pdo, $user['id'], $id, $response)) return $response;

  // articlesテーブルから削除
  $state = $pdo->prepare('delete from articles where id=?');
  if(!$state->bindValue(1, $id, PDO::PARAM_INT) || !$state->execute()){
      return [
          'status' => 500, 'message' => '記事削除中にエラーが発生しました',
      ];
  }
  return [
      'status' => 200,
      'message' => 'ID ' . htmlspecialchars($id) . ' の記事が削除されました',
  ];
}


/**
 * メイン処理
 * リクエストを処理してJSONデータを返す
 */
procAPIRequest(function($method, $params, $pdo, &$response){
  switch($method){
    case 'GET':
      // 記事一覧取得
      $response = getArticles($params, $pdo);
      return true;
  case 'POST':
      // 記事新規作成
      $response = createArticle($params, $pdo);
      return true;
  case 'PUT':
      // 記事更新
      $response = updateArticle($params, $pdo);
      return true;
  case 'DELETE':
      // 記事削除
      $response = deleteArticle($params, $pdo);
      return true;
  }
  return false;
});