<?php
require_once('./functions.php');

// 記事一覧取得
$res = callAPI('GET');
$articles = $res['json']['articles'];
// テンプレートからView読み込み
include('./template/index.php');