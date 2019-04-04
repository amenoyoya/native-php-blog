<?php

header('Content-Type: application/json'); // 結果をJSON形式で返す

// POSTチェック
if(!isset($_POST['blog-id']) || !isset($_POST['blog-title']) || !isset($_POST['blog-body'])){
    die(json_encode(array(
        'ok' => false,
        'message' => '無効なパラメータが指定されています',
    )));
}

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
if(!$state->bindValue(1, $_POST['blog-title'], PDO::PARAM_STR)
    || !$state->bindValue(2, $_POST['blog-body'], PDO::PARAM_STR)
    || !$state->bindValue(3, $_POST['blog-id'], PDO::PARAM_INT)
    || !$state->execute())
{
    die(json_encode(array(
        'ok' => false,
        'message' => '記事の更新処理中にエラーが発生しました',
    )));
}

echo json_encode(array(
    'ok' => true,
    'message' => 'ブログ記事「' . $_POST['blog-title'] . '」が更新されました',
));

?>