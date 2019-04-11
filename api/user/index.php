<?php

require_once('../../config/mysql.php');
require_once('./verifier.php');

$data = []; // 処理結果

// PDOでMySQLデータベースに接続
try{
    $_ = function($s){return $s;}; // 定数展開用
    $pdo = new PDO("mysql:host={$_(MYSQL_HOST)};dbname={$_(MYSQL_DB_NAME)};charset=utf8",
        MYSQL_USER, MYSQL_PASSWORD, array(PDO::ATTR_EMULATE_PREPARES => false)
    );
    // 受信したデータパラメータをパージ
    $params = [];
    parse_str(file_get_contents('php://input'), $params);
    // HTTPメソッドごとに処理分岐
    switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
        // ユーザー認証＆情報取得
        if(false === ($user = getUserInfo($params, $response))){
            $data = $response;
            break;
        }
        $data = [
            'status' => 200, 'user' => $user,
        ];
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