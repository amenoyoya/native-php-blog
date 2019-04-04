<?php

header('Content-Type: application/json'); // 結果をJSON形式で返す

// POSTチェック
if(!isset($_POST['blog-id']) || !isset($_POST['blog-title']) || !isset($_POST['blog-body'])){
    die(json_encode(array(
        'ok' => false,
        'message' => '無効なパラメータが指定されています',
    )));
}
// HTMLエスケープ
$id = htmlspecialchars($_POST['blog-id']);
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
$state = $pdo->prepare('update articles set title=?, body=? where id=?');
if(!$state->bindValue(1, $title, PDO::PARAM_STR)
    || !$state->bindValue(2, $body, PDO::PARAM_STR)
    || !$state->bindValue(3, $id, PDO::PARAM_INT)
    || !$state->execute())
{
    die(json_encode(array(
        'ok' => false,
        'message' => '記事の更新処理中にエラーが発生しました',
    )));
}

echo json_encode(array(
    'ok' => true,
    'message' => 'ブログ記事「' . $title . '」が更新されました',
));

?>