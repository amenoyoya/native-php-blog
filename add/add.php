<?php

header('Content-Type: application/json'); // 結果をJSON形式で返す

// POSTチェック
if(!isset($_POST['blog-title']) || !isset($_POST['blog-body'])){
    die(json_encode(array(
        'ok' => false,
        'message' => '無効なパラメータが指定されています',
    )));
}
// HTMLエスケープ
$title = htmlspecialchars($_POST['blog-title']);
$body = htmlspecialchars($_POST['blog-body']);

// PDOでMySQLデータベースに接続
try{
    $pdo = new PDO('mysql:host=localhost.localdomain;dbname=blog;charset=utf8',
        'root', 'Exir@SQL190401', array(PDO::ATTR_EMULATE_PREPARES => false)
    );
}catch(PDOException $e){
    die(json_encode(array(
        'ok' => false,
        'message' => 'データベース接続エラー：' . $e->getMessage(),
    )));
}

// blogデータベース/articlesテーブルに記事登録
$state = $pdo->prepare('insert into articles (title, body) values (?, ?)');
if(!$state->bindValue(1, $title, PDO::PARAM_STR)
    || !$state->bindValue(2, $body, PDO::PARAM_STR)
    || !$state->execute())
{
    die(json_encode(array(
        'ok' => false,
        'message' => '記事の登録処理中にエラーが発生しました',
    )));
}

echo json_encode(array(
    'ok' => true,
    'message' => 'ブログ記事「' . $title . '」が登録されました',
));

?>