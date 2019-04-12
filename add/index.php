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
include('../template/add.php');