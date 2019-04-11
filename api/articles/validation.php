<?php

/** 入力値チェック関連 **/

/* 記事新規作成・更新時の入力値チェック関数 */
// @title: ブログタイトル
// @body: ブログ本文
// @response: 入力値エラーが発生した場合にレスポンスデータが渡される
// @return: true=入力値妥当, false=入力値不正
function isValid($title, $body, &$response){
    if($title === ''){
        $response = [
            'status' => 400, 'message' => 'タイトルは入力必須です',
        ];
        return false;
    }
    if(strlen($title) > 200){
        $data = [
            'status' => 400, 'message' => 'タイトルは200バイト以内で指定してください',
        ];
        return false;
    }
    if(mb_strlen($body) > 1000){
        $data = [
            'status' => 400, 'message' => '本文は100文字以内で指定してください',
        ];
        return false;
    }
    return true;
}


/* 記事の存在確認 */
// @pdo: PDOオブジェクト
// @user_id: ユーザーID
// @article_id: 記事ID
// @response: エラーが発生した場合にレスポンスデータが渡される
// @return: 記事が存在するか
function isArticleExists($pdo, $user_id, $article_id, &$response){
    $state = $pdo->prepare('select * from articles where user_id=? and id=?');
    if(!$state->bindValue(1, $user_id, PDO::PARAM_INT)
        || !$state->bindValue(2, $article_id, PDO::PARAM_INT)
        || !$state->execute())
    {
        $response = [
            'status' => 500, 'message' => '記事更新中にエラーが発生しました',
        ];
        return false;
    }
    if(!$state->fetch()){
        $response = [
            'status' => 400, 'message' => '無効な記事IDが指定されています',
        ];
        return false;
    }
    return true;
}