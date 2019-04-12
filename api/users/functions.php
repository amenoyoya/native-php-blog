<?php
/**
 * ユーザー情報関連API内で使われる関数
 */

 /**
 * ユーザー認証＆トークン取得
 * 
 * @internal: getUserInfo関数内で呼び出される
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, token: トークン]
 *           status: 200 OK（正常に取得完了）, 400 Bad Request（ユーザー認証失敗）
 */
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


/**
 * ユーザー登録・認証時の入力値チェック関数
 * 
 * @internal: registerUser関数内で呼び出される
 * 
 * @param string $name: ユーザー名
 * @param string $password: パスワード
 * @param array &$response: 入力値エラーが発生した場合にレスポンスデータが渡される
 * @param PDO $pdo (optional): PDOオブジェクトを指定した場合はユーザー名の重複がないかチェックする
 * 
 * @return bool: true=入力値妥当, false=入力値不正
 */
function isValid($name, $password, &$response, $pdo=NULL){
  // ユーザー名入力チェック
  if($name === ''){
      $response = [
          'status' => 400, 'message' => 'ユーザー名は入力必須です',
      ];
      return false;
  }
  if(strlen($name) > 32){
      $data = [
          'status' => 400, 'message' => 'ユーザー名は32バイト以内で指定してください',
      ];
      return false;
  }
  
  if($pdo){ // ユーザー名の重複がないかチェック
      $state = $pdo->prepare('select * from users where name=?');
      if(!$state->bindValue(1, $name, PDO::PARAM_STR) || !$state->execute()){
          $response = [
              'status' => 500, 'message' => 'ユーザー登録中にエラーが発生しました',
          ];
          return false;
      }
      if($state->fetch()){ // 登録されているユーザー名の場合エラー
          $response = [
              'status' => 400, 'message' => 'すでに登録されているユーザー名です',
          ];
          return false;
      }
  }

  // パスワード入力チェック
  if(strlen($password) < 8){
      $response = [
          'status' => 400, 'message' => '8バイト以上の長さのパスワードを指定してください',
      ];
      return false;
  }
  if(strlen($password) > 32){
      $response = [
          'status' => 400, 'message' => 'パスワードは32バイト以内で指定してください',
      ];
      return false;
  }
  return true;
}