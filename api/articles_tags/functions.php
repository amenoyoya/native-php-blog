<?php
/**
 * 記事－タグ関連API内で使われる関数
 */

/**
 * タグの存在確認
 * 
 * @internal: registerTags関数内で呼び出される
 *
 * @param PDO $pdo: PDOオブジェクト
 * @param int $user_id: ユーザーID
 * @param int $tag_id: 記事ID
 * @param array &$response: エラーが発生した場合にレスポンスデータが渡される
 * 
 * @return bool: タグが存在するか
 */
function isTagExists($pdo, $user_id, $tag_id, &$response){
  $state = $pdo->prepare('select * from tags where user_id=? and id=?');
  if(!$state->bindValue(1, $user_id, PDO::PARAM_INT)
      || !$state->bindValue(2, $tag_id, PDO::PARAM_INT)
      || !$state->execute())
  {
    $response = [
      'status' => 500, 'message' => 'タグ確認中にエラーが発生しました',
    ];
    return false;
  }
  if(!$state->fetch()){
    $response = [
      'status' => 400, 'message' => '無効なタグIDが指定されています',
    ];
    return false;
  }
  return true;
}