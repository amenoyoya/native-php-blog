<?php

session_start(); // セッション開始

// セッションが保存されていなければログインページにリダイレクト
if(!isset($_SESSION['user-token'])){
    header('Location: ../login/');
    exit;
}

// テンプレートからView読み込み
include('../template/add.php');