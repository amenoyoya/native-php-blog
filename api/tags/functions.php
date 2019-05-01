<?php
/**
 * タグ関連API内で使われる関数
 */

/**
 * 記事タグ登録時の入力値チェック関数
 * 
 * @internal: registerTag関数内で呼び出される
 * 
 * @param string $name: タグ名
 * @param array &$response: 入力値エラーが発生した場合にレスポンスデータが渡される
 * @param PDO $pdo (optional): PDOオブジェクトを指定した場合はタグ名の重複がないかチェックする
 * 
 * @return bool: true=入力値妥当, false=入力値不正
 */
function isValid($name, &$response, $pdo=NULL){
  if($name === ''){
    $response = [
      'status' => 400, 'message' => 'タグ名は入力必須です',
    ];
    return false;
  }
  if(strlen($name) > 100){
    $response = [
      'status' => 400, 'message' => 'タグ名は100バイト以内で指定してください',
    ];
    return false;
  }
  if($pdo){ // タグ名の重複がないかチェック
    $state = $pdo->prepare('select * from tags where name=?');
    if(!$state->bindValue(1, $name, PDO::PARAM_STR) || !$state->execute()){
      $response = [
        'status' => 500, 'message' => 'タグ登録中にエラーが発生しました',
      ];
      return false;
    }
    if($state->fetch()){ // 登録されているタグ名の場合エラー
      $response = [
        'status' => 400, 'message' => 'すでに登録されているタグ名です',
      ];
      return false;
    }
  }
  return true;
}