<?php
/** PUTメソッド: ブログ記事の更新 **/

// 記事更新
// @params: 受信パラメータ
// @pdo: PDOオブジェクト
// @return: [status: ステータスコード, message: 処理結果のメッセージ]
//           ステータスコード: 200 OK（正常に更新完了）, 400 Bad Request（リクエストが不正）, 500 Internal Server Error（データベース処理エラー）
function updateArticle($params, $pdo){
    // パラメータチェック
    if(!isset($params['blog-id']) || !isset($params['blog-title']) || !isset($params['blog-body'])){
        return [
            'status' => 400, 'message' => 'パラメータが正しく指定されていません',
        ];
    }
    $id = $params['blog-id'];
    $title = $params['blog-title'];
    $body = $params['blog-body'];

    // 記事の存在確認
    $state = $pdo->prepare('select * from articles where id=?');
    if(!$state->bindValue(1, $id, PDO::PARAM_INT)
        || !$state->execute() || !$state->fetch())
    {
        return [
            'status' => 400, 'message' => '無効な記事IDが指定されています',
        ];
    }

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
        'message' => 'ブログ記事「' . htmlspecialchars($title) . '」が更新されました',
    ];
}