<?php
/** ログイン処理とセッション保存の実装 **/

require_once('../functions.php');

session_start(); // セッション開始

// HTTPメソッドごとに処理分岐
switch($_SERVER['REQUEST_METHOD']){
case 'POST':
    // ユーザー認証＆トークン取得APIを呼び出す
    $data = callAPI('users', 'GET', $_POST)['json'];
    if($data['status'] != 200) break; // ログイン失敗
    // トークンをセッションに保存
    $_SESSION['user-token'] = $data['token'];
    break;
default:
    // サポートされていないHTTPメソッドがリクエストされた
    $data = [
        'status' => 400, 'message' => 'サポートされていないHTTPメソッドがリクエストされました'
    ];
    break;
}

// ヘッダとレスポンスコードを設定し、JSONデータを返す
header('Content-Type: application/json'); // 結果をJSON形式で返す
http_response_code($data['status']);
echo json_encode($data);