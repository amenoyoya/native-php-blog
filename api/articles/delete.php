<?php
/** DELETEメソッド: ブログ記事の削除 **/

// 記事削除
// @params: 受信パラメータ
// @pdo: PDOオブジェクト
// @return: [status: ステータスコード, message: 処理結果のメッセージ]
//           ステータスコード: 200 OK（正常に更新完了）, 400 Bad Request（リクエストが不正）, 500 Internal Server Error（データベース処理エラー）
function deleteArticle($params, $pdo){
    // パラメータチェック
    if(!isset($params['blog-id'])){
        return [
            'status' => 400, 'message' => 'パラメータが正しく指定されていません',
        ];
    }
    $id = $params['blog-id'];

    // 記事の存在確認
    $state = $pdo->prepare('select * from articles where id=?');
    if(!$state->bindValue(1, $id, PDO::PARAM_INT)
        || !$state->execute() || !$state->fetch())
    {
        return [
            'status' => 400, 'message' => '無効な記事IDが指定されています',
        ];
    }

    // articlesテーブルから削除
    $state = $pdo->prepare('delete from articles where id=?');
    if(!$state->bindValue(1, $id, PDO::PARAM_INT)
        || !$state->execute())
    {
        return [
            'status' => 500, 'message' => '記事削除中にエラーが発生しました',
        ];
    }
    return [
        'status' => 200,
        'message' => 'ID ' . htmlspecialchars($id) . ' の記事が削除されました',
    ];
}