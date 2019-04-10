<?php
/** GETメソッド: ブログ記事一覧の取得 **/

require_once('./validation.php');

/* ブログ記事一覧の取得 */
// @params: 受信パラメータ
// @pdo: PDOオブジェクト
// @return: [status: ステータスコード, articles: [記事オブジェクトの配列], article: 単一記事, message: エラーメッセージ]
//           ステータスコード: 200 OK（正常に取得完了）, 400 Bad Request, 500 Internal Server Error
function getArticles($params, $pdo){
    // ユーザー認証チェック
    if(false === ($user_id = getUserID($params, $response))) return $response;
    
    // article-idが指定されていれば、単一記事の取得
    if(isset($params['article-id'])) return getArticle($pdo, $user_id, $params['article-id']);
    
    // 認証済みユーザーの記事一覧取得：最新記事順（idの降順）
    $state = $pdo->prepare('select * from articles where user_id=? order by id desc');
    if(!$state->bindValue(1, $user_id, PDO::PARAM_INT) || !$state->execute()){
        return [
            'status' => 500, 'message' => '記事取得中にエラーが発生しました',
        ];
    }
    $articles = [];
    while($row = $state->fetch(PDO::FETCH_ASSOC)){
        // HTMLエスケープしながら記事データを取得
        $articles[] = [
            'id' => htmlspecialchars($row['id']),
            'title' => htmlspecialchars($row['title']),
            'body' => htmlspecialchars($row['body']),
        ];
    }
    return [
        'status' => 200, 'articles' => $articles,
    ];
}


/* 単一記事の取得: getArticles内で呼び出される */
function getArticle($pdo, $user_id, $article_id){
    $state = $pdo->prepare('select * from articles where user_id=? and id=?');
    if(!$state->bindValue(1, $user_id, PDO::PARAM_INT)
        || !$state->bindValue(2, $article_id, PDO::PARAM_INT)
        || !$state->execute())
    {
        return [
            'status' => 500, 'message' => '記事取得中にエラーが発生しました',
        ];
    }
    if(!($row = $state->fetch())){
        return [
            'status' => 400, 'message' => '無効な記事IDが指定されています',
        ];
    }
    return [
        'status' => 200,
        'article' => [
            'id' => htmlspecialchars($row['id']),
            'title' => htmlspecialchars($row['title']),
            'body' => htmlspecialchars($row['body']),
        ],
    ];
}