<?php

require_once('./get.php');
require_once('./post.php');
require_once('./put.php');
require_once('./delete.php');

$data = []; // 処理結果

// PDOでMySQLデータベースに接続
try{
    $pdo = new PDO('mysql:host=localhost.localdomain;dbname=blog;charset=utf8',
        'root', 'Exir@SQL190401', array(PDO::ATTR_EMULATE_PREPARES => false)
    );
    // 受信したデータパラメータをパージ
    $params = [];
    parse_str(file_get_contents('php://input'), $params);
    // HTTPメソッドごとに処理分岐
    switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
        // 記事一覧取得
        $data = getArticles($params, $pdo);
        break;
    case 'POST':
        // 記事新規作成
        $data = createArticle($params, $pdo);
        break;
    case 'PUT':
        // 記事更新
        $data = updateArticle($params, $pdo);
        break;
    case 'DELETE':
        // 記事削除
        $data = deleteArticle($params, $pdo);
        break;
    default:
        // サポートされていないHTTPメソッドがリクエストされた
        $data = [
            'status' => 400, 'message' => 'サポートされていないHTTPメソッドがリクエストされました'
        ];
        break;
    }
}catch(PDOException $e){
    $data = [
        'status' => 500, 'message' => 'データベース接続エラー：' . $e->getMessage(),
    ];
}

// ヘッダとレスポンスコードを設定し、JSONデータを返す
header('Content-Type: application/json'); // 結果をJSON形式で返す
http_response_code($data['status']);
echo json_encode($data);