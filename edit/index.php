<?php
require_once('../functions.php');

// GETチェック
if(isset($_GET['id'])){
    // 記事取得
    $res = callAPI('GET', ['blog-id' => $_GET['id']]);
    switch($res['status']){
    case 200:
        // 編集ページ
        $article = $res['json']['article'];
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