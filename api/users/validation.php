<?php

/** 新規ユーザー登録時の入力値チェック関数 **/

// @name: ユーザー名
// @password: パスワード
// @response: 入力値エラーが発生した場合にレスポンスデータが渡される
// @return: true=入力値妥当, false=入力値不正
function isValid($name, $password, &$response){
    if($name === ''){
        $response = [
            'status' => 400, 'message' => 'ユーザー名は入力必須です',
        ];
        return false;
    }
    if($password === ''){
        $response = [
            'status' => 400, 'message' => 'パスワードは入力必須です',
        ];
        return false;
    }
    if(strlen($name) > 32){
        $data = [
            'status' => 400, 'message' => 'ユーザー名は32バイト以内で指定してください',
        ];
        return false;
    }
    if(strlen($password) > 32){
        $data = [
            'status' => 400, 'message' => 'パスワードは32バイト以内で指定してください',
        ];
        return false;
    }
    return true;
}