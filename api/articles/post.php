<?php
/** POSTメソッド: 新規ブログ記事の作成 **/

require_once('./validation.php');

// 新規記事作成
// @params: 受信パラメータ
// @pdo: PDOオブジェクト
// @return: [status: ステータスコード, message: 処理結果のメッセージ]
//           ステータスコード: 201 Created（正常に作成完了）, 400 Bad Request（リクエストが不正）, 500 Internal Server Error（データベース処理エラー）
function createArticle($params, $pdo){
    // パラメータチェック
    if(!isset($params['blog-title']) || !isset($params['blog-body'])){
        return [
            'status' => 400, 'message' => 'パラメータが正しく指定されていません',
        ];
    }
    $title = $params['blog-title'];
    $body = $params['blog-body'];

    // バリデーションチェック
    if(!isValid($title, $body, $res)) return $res;
    

    // articlesテーブルにデータ挿入
    $state = $pdo->prepare('insert into articles (title, body) values (?, ?)');
    if(!$state->bindValue(1, $title, PDO::PARAM_STR)
        || !$state->bindValue(2, $body, PDO::PARAM_STR)
        || !$state->execute())
    {
        return [
            'status' => 500, 'message' => '記事登録中にエラーが発生しました',
        ];
    }
    return [
        'status' => 201,
        'message' => 'ブログ記事「' . htmlspecialchars($title) . '」が登録されました',
    ];
}