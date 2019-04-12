<?php
/**
 * ブログ記事関連API内で使われる関数
 */

 /**
 * 単一記事の取得
 * 
 * @internal: getArticles関数内で呼び出される
 * 
 * @param PDO $pdo: PDOオブジェクト
 * @param array $user: [id: ユーザーID, name: ユーザー名]
 * @param int $article_id: 記事ID
 * 
 * @return array: [status: ステータスコード, article: 単一記事, message: エラーメッセージ]
 *           status: 200 OK（正常に取得完了）, 400 Bad Request, 500 Internal Server Error
 */
function getArticle($pdo, $user, $article_id){
  $state = $pdo->prepare('select * from articles where user_id=? and id=?');
  if(!$state->bindValue(1, $user['id'], PDO::PARAM_INT)
      || !$state->bindValue(2, $article_id, PDO::PARAM_INT)
      || !$state->execute())
  {
      return [
          'status' => 500, 'message' => '記事取得中にエラーが発生しました',
      ];
  }
  if(!($row = $state->fetch())){
      return [
          'status' => 400, 'message' => '無効な記事IDが指定されています',
      ];
  }
  return [
      'status' => 200,
      'user' => $user,
      'article' => [
          'id' => htmlspecialchars($row['id']),
          'title' => htmlspecialchars($row['title']),
          'body' => htmlspecialchars($row['body']),
      ],
  ];
}


/**
 * 記事新規作成・更新時の入力値チェック関数
 * 
 * @internal
 * 
 * @param string $title: ブログタイトル
 * @param string $body: ブログ本文
 * @param array &$response: 入力値エラーが発生した場合にレスポンスデータが渡される
 * 
 * @return bool: true=入力値妥当, false=入力値不正
 */
function isValid($title, $body, &$response){
  if($title === ''){
      $response = [
          'status' => 400, 'message' => 'タイトルは入力必須です',
      ];
      return false;
  }
  if(strlen($title) > 200){
      $data = [
          'status' => 400, 'message' => 'タイトルは200バイト以内で指定してください',
      ];
      return false;
  }
  if(mb_strlen($body) > 1000){
      $data = [
          'status' => 400, 'message' => '本文は100文字以内で指定してください',
      ];
      return false;
  }
  return true;
}


/**
 * 記事の存在確認
 * 
 * @internal
 * 
 * @param PDO $pdo: PDOオブジェクト
 * @param int $user_id: ユーザーID
 * @param int $article_id: 記事ID
 * @param array &$response: エラーが発生した場合にレスポンスデータが渡される
 * 
 * @return bool: 記事が存在するか
 */
function isArticleExists($pdo, $user_id, $article_id, &$response){
  $state = $pdo->prepare('select * from articles where user_id=? and id=?');
  if(!$state->bindValue(1, $user_id, PDO::PARAM_INT)
      || !$state->bindValue(2, $article_id, PDO::PARAM_INT)
      || !$state->execute())
  {
      $response = [
          'status' => 500, 'message' => '記事更新中にエラーが発生しました',
      ];
      return false;
  }
  if(!$state->fetch()){
      $response = [
          'status' => 400, 'message' => '無効な記事IDが指定されています',
      ];
      return false;
  }
  return true;
}