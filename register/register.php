<?php
/** 新規ユーザー登録＆ログイン処理 **/

require_once('../functions.php');

session_start(); // セッション開始

// HTTPメソッドごとに処理分岐
switch($_SERVER['REQUEST_METHOD']){
case 'POST':
    // 新規ユーザー登録APIを呼び出す
    $data = callAPI('users', 'POST', $_POST)['json'];
    if($data['status'] != 201) break; // 登録失敗
    // そのままログインする
    $data = callAPI('users', 'GET', [
        'user-name' => $_POST['user-name'], 'user-password' => $_POST['user-password']
    ])['json'];
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