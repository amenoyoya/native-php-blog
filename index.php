<?php

require_once('./functions.php');

session_start(); // セッション開始

// セッションが保存されていなければログインページにリダイレクト
if(!isset($_SESSION['user-token'])){
    header('Location: ./login/');
    exit;
}

// 記事一覧取得
$res = callAPI('articles', 'GET', ['user-token' => $_SESSION['user-token']])['json'];

// ユーザー認証エラーが発生した場合はログインページにリダイレクト
if($res['status'] === 401){
    header('Location: ./login/');
    exit;
}

// 正常に記事取得できた場合は、各種情報をセット
if($res['status'] === 200){
    $articles = isset($res['articles'])? $res['articles']: [];
    $user = $res['user'];
}
// エラー400, 500 が発生した場合はエラーメッセージをセット
else{
    $error_message = $res['json']['message'];
}

// テンプレートからView読み込み
include('./template/index.php');