<?php

/** ユーザー登録・認証時の入力値チェック関数 **/

// @name: ユーザー名
// @password: パスワード
// @response: 入力値エラーが発生した場合にレスポンスデータが渡される
// @pdo[=NULL]: PDOオブジェクトを指定した場合はユーザー名の重複がないかチェックする
// @return: true=入力値妥当, false=入力値不正
function isValid($name, $password, &$response, $pdo=NULL){
    // ユーザー名入力チェック
    if($name === ''){
        $response = [
            'status' => 400, 'message' => 'ユーザー名は入力必須です',
        ];
        return false;
    }
    if(strlen($name) > 32){
        $data = [
            'status' => 400, 'message' => 'ユーザー名は32バイト以内で指定してください',
        ];
        return false;
    }
    
    if($pdo){ // ユーザー名の重複がないかチェック
        $state = $pdo->prepare('select * from users where name=?');
        if(!$state->bindValue(1, $name, PDO::PARAM_STR) || !$state->execute()){
            $response = [
                'status' => 500, 'message' => 'ユーザー登録中にエラーが発生しました',
            ];
            return false;
        }
        if($state->fetch()){ // 登録されているユーザー名の場合エラー
            $response = [
                'status' => 400, 'message' => 'すでに登録されているユーザー名です',
            ];
            return false;
        }
    }

    // パスワード入力チェック
    if(strlen($password) < 8){
        $response = [
            'status' => 400, 'message' => '8バイト以上の長さのパスワードを指定してください',
        ];
        return false;
    }
    if(strlen($password) > 32){
        $response = [
            'status' => 400, 'message' => 'パスワードは32バイト以内で指定してください',
        ];
        return false;
    }
    return true;
}