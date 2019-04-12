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
 * 
 * @return bool: true=入力値妥当, false=入力値不正
 */
function isValid($name, &$response){
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
  return true;
}