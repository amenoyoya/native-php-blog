<<<<<<< HEAD
<?php

require_once('../functions.php');

session_start(); // セッション開始

// セッションが保存されていなければログインページにリダイレクト
if(!isset($_SESSION['user-token'])){
    header('Location: ../login/');
    exit;
}

// ユーザー情報取得
$res = callAPI('user', 'GET', ['user-token' => $_SESSION['user-token']])['json'];
if($res['status'] == 401){ // ユーザー未ログインならログインページにリダイレクト
    header('Location: ../login/');
    exit;
}

if($res['status'] == 200){ // ユーザーがログインしているならユーザー情報セット
    $user = $res['user'];
}else{ // エラー発生時はエラーメッセージをセット
    $error_message = $res['message'];
}

// テンプレートからView読み込み
=======
<?php

require_once('../functions.php');

session_start(); // セッション開始

// セッションが保存されていなければログインページにリダイレクト
if(!isset($_SESSION['user-token'])){
  header('Location: ../../login/');
  exit;
}

// 登録済みタグの取得
$res = callAPI('tags', 'GET', ['user-token' => $_SESSION['user-token']])['json'];

switch($res['status']){
case 200: // 取得完了
  $user = $res['user']; 
  $tags = isset($res['tags'])? $res['tags']: [];
  break;
case 401: // 認証エラー
  header('Location: ../../login/');
  exit;
default:
  $error_message = $res['message'];
  break;
}

// テンプレートからView読み込み
>>>>>>> develop
include('../template/add.php');