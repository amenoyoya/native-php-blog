<?php
/** PUTメソッド: ブログ記事の更新 **/

require_once('./validation.php');
require_once('../user/verifier.php');

/**
 * 記事更新API
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
    if(false === ($user = getUserInfo($params, $response))) return $response;
    
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