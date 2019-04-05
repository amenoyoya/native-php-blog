<?php
/** GETメソッド: ブログ記事一覧の取得 **/

// ブログ記事一覧の取得
// @params: 受信パラメータ
// @pdo: PDOオブジェクト
// @return: [status: ステータスコード, articles: [記事オブジェクトの配列], article: 単一記事, message: エラーメッセージ]
//           ステータスコード: 200 OK（正常に取得完了）, 400 Bad Request（存在しない記事IDが指定された）
function getArticles($params, $pdo){
    // blog-idが指定されていれば、単一記事の取得
    if(isset($params['blog-id'])) return getArticle($pdo, $params['blog-id']);
    // 記事一覧取得：最新記事順（idの降順）
    $query = $pdo->query('select * from articles order by id desc');
    $articles = [];
    while($row = $query->fetch(PDO::FETCH_ASSOC)){
        // HTMLエスケープしながら記事データを取得
        $articles[] = [
            'id' => htmlspecialchars($row['id']),
            'title' => htmlspecialchars($row['title']),
            'body' => htmlspecialchars($row['body']),
        ];
    }
    return [
        'status' => 200,
        'articles' => $articles,
    ];
}

// 単一記事の取得: getArticles内で呼び出される
function getArticle($pdo, $id){
    $state = $pdo->prepare('select * from articles where id=?');
    if($state->bindValue(1, $id, PDO::PARAM_INT)
        && $state->execute() && $row = $state->fetch())
    {
        return [
            'status' => 200,
            'article' => [
                'id' => $row['id'], 'title' => $row['title'], 'body' => $row['body']
            ],
        ];
    }
    return [
        'status' => 400, 'message' => '無効な記事IDが指定されています',
    ];
}