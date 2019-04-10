<?php
require_once('../functions.php');

session_start(); // セッション開始

// セッションが保存されていなければログインページにリダイレクト
if(!isset($_SESSION['user-token'])){
    header('Location: ../login/');
    exit;
}

// GETチェック
if(isset($_GET['id'])){
    // 記事削除
    $res = callAPI('articles', 'DELETE', [
        'user-token' => $_SESSION['user-token'], 'article-id' => $_GET['id']
    ]);
    switch($res['status']){
    case 200:
        // 削除完了ページ
        include('../template/delete/index.php');
        break;
    case 400:
        // リクエストエラーページ
        $message = $res['json']['message'];
        include('../template/delete/error-bad-request.php');
        break;
    default:
        // サーバーエラーページ
        $message = $res['json']['message'];
        include('../template/delete/error.php');
        break;
    }
}else{
    // 記事IDが指定されていない場合のエラーページ
    include('../template/delete/error-no-id.php');
}