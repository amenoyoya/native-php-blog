<?php

/** 記事新規作成・更新時の入力値チェック関数 **/

// @title: ブログタイトル
// @body: ブログ本文
// @data: 入力値エラーが発生した場合にレスポンスデータが渡される
// @return: true=入力値妥当, false=入力値不正
function isValid($title, $body, &$data){
    if($title === ''){
        $data = [
            'status' => 400, 'message' => 'タイトルは入力必須です',
        ];
        return false;
    }else if(strlen($title) > 200){
        $data = [
            'status' => 400, 'message' => 'タイトルは200バイト以内で指定してください',
        ];
        return false;
    }else if(mb_strlen($body) > 1000){
        $data = [
            'status' => 400, 'message' => '本文は100文字以内で指定してください',
        ];
        return false;
    }
    return true;
}