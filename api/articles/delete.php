<?php
/** DELETEメソッド: ブログ記事の削除 **/

require_once('./validation.php');
require_once('../user/verifier.php');

/**
 * 記事削除API
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
    if(false === ($user = getUserInfo($params, $response))) return $response;
    
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