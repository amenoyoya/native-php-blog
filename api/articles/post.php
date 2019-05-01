<?php
/** POSTメソッド: 新規ブログ記事の作成 **/

require_once('./validation.php');
require_once('../user/verifier.php');

// 新規記事作成
// @params: 受信パラメータ
// @pdo: PDOオブジェクト
// @return: [status: ステータスコード, message: 処理結果のメッセージ]
//           ステータスコード: 201 Created（正常に作成完了）, 400 Bad Request（リクエストが不正）,
//                            401 Unauthorized（認証が必要）, 500 Internal Server Error（データベース処理エラー）
function createArticle($params, $pdo){
    // ユーザー認証チェック
    if(false === ($user = getUserInfo($params, $response))) return $response;

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
    return [
        'status' => 201,
        'user' => $user,
        'message' => 'ブログ記事「' . htmlspecialchars($title) . '」が登録されました',
    ];
}