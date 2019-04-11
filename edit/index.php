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
    // 記事取得
    $res = callAPI('articles', 'GET', [
        'user-token' => $_SESSION['user-token'], 'article-id' => $_GET['id']
    ]);
    switch($res['status']){
    case 200:
        // 編集ページ
        $article = $res['json']['article'];
        $user = $res['json']['user'];
        include('../template/edit/index.php');
        break;
    case 400:
        // リクエストエラーページ
        $message = $res['json']['message'];
        include('../template/edit/error-bad-request.php');
        break;
    default:
        // サーバーエラーページ
        $message = $res['json']['message'];
        include('../template/edit/error.php');
        break;
    }
}else{
    // 記事IDが指定されていない場合のエラーページ
    include('../template/edit/error-no-id.php');
}