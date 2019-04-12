<?php

require_once('../functions.php');
require_once('./functions.php');

/**
 * 記事に関連付けられたタグ一覧の取得API
 * 
 * @internal: メイン処理で呼び出される
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, user: ユーザー情報, article-id: 記事ID, tags: [タグ情報の配列], message: エラーメッセージ]
 *           status: 200 OK（正常に取得完了）, 400 Bad Request, 401 Unauthorized, 500 Internal Server Error
 */
function getTags($params, $pdo){
  // ユーザー認証チェック
  if(false === ($user = checkUserState($params, $response))) return $response;

  // パラメータチェック
  if(!isset($params['article-id'])){
    return [
      'status' => 400, 'message' => 'パラメータが不正です',
    ];
  }
  $article_id = $params['article-id'];
  
  // 記事に関連付けられたタグ一の覧取得
  $state = $pdo->prepare(
    'select tags.id as id, tags.name as name from tags '.
    'join articles_tags on tags.id = articles_tags.tag_id '.
    'where articles_tags.article_id = ?'
  );
  if(!$state->bindValue(1, $article_id, PDO::PARAM_INT) || !$state->execute()){
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
    'status' => 200, 'user' => $user, 'article-id' => $params['article-id'], 'tags' => $tags,
  ];
}


/**
 * 記事にタグ一覧を新規関連付けするAPI
 * 
 * @internal: メイン処理で呼び出される
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, user: ユーザー情報, article-id: 記事ID message: 処理結果のメッセージ]
 *           sttaus: 201 Created（正常に登録完了）, 400 Bad Request（リクエストが不正）,
 *                   401 Unauthorized（認証が必要）, 500 Internal Server Error（データベース処理エラー）
 */
function registerTags($params, $pdo){
  // ユーザー認証チェック
  if(false === ($user = checkUserState($params, $response))) return $response;

  // パラメータチェック
  // tagsは、タグIDの配列でなければならない
  if(!isset($params['article-id'])
    || !isset($params['tags']) || gettype($params['tags']) !== 'array')
  {
    return [
      'status' => 400, 'message' => 'パラメータが正しく指定されていません',
    ];
  }
  $article_id = $params['article-id'];
  $tags = $params['tags'];

  // 記事の存在確認
  if(!isArticleExists($pdo, $user['id'], $article_id, $res)) return $res;

  // articles_tagsテーブルから以前の関連付けを削除
  $state = $pdo->prepare('delete from articles_tags where article_id=?');
  if(!$state->bindValue(1, $article_id, PDO::PARAM_INT) || !$state->execute()){
    return [
      'status' => 500, 'message' => 'タグ関連付け中にエラーが発生しました',
    ];
  }
  
  // タグIDの配列を処理
  foreach($tags as $tag_id){
    // タグの存在確認
    if(!isTagExists($pdo, $user['id'], $tag_id, $res)) return $res;

    // articles_tagsテーブルにデータ挿入
    $state = $pdo->prepare('insert into articles_tags (article_id, tag_id) values (?, ?)');
    if(!$state->bindValue(1, $article_id, PDO::PARAM_INT)
      || !$state->bindValue(2, $tag_id, PDO::PARAM_INT)
      || !$state->execute())
    {
      return [
        'status' => 500, 'message' => 'タグ関連付け中にエラーが発生しました',
      ];
    }
  }
  return [
    'status' => 201,
    'user' => $user,
    'message' => '記事ID ' . htmlspecialchars($article_id) . ' に タグ ['
      . htmlspecialchars(implode(',', $tags)) . '] が関連付けられました',
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
    $response = registerTags($params, $pdo);
    return true;
  }
  return false;
});