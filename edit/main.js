/* 記事更新ボタンの実装 */
$('#update-article').click(function(){
    var id = $('#article-id').val(), title = $('#article-title').val(), body = $('#article-body').val();
    
    $('#result').html(''); // 結果欄をクリアしておく
    // 入力チェック
    if(title === ''){
        // タイトルが空の場合、警告を出す
        $('#result').html('<div class="alert alert-warning">タイトルは入力必須です</div>');
        return false;
    }else if(title.bytes() > 200){
        // タイトルが200バイトを超える場合、警告を出す
        $('#result').html('<div class="alert alert-warning">タイトルは200バイト以内で指定してください</div>');
        return false;
    }else if(body.length > 100){
        // 本文が100文字を超える場合、警告を出す
        $('#result').html('<div class="alert alert-warning">本文は100文字以内で指定してください</div>');
        return false;
    }
    // データベース処理実行
    requestAjax('/api/articles/', 'PUT', {
        'article-id': id, 'article-title': title, 'article-body': body
    },  {
        200: function(data){ // 正常終了
            $('#result').html('<div class="alert alert-success">' + data['message'] + '</div>');
        },
        400: function(data){ // リクエストエラー
            $('#result').html('<div class="alert alert-warning">' + data['message'] + '</div>');
        },
        500: function(data){ // サーバーエラー
            $('#result').html('<div class="alert alert-danger">' + data['message'] + '</div>');
        }
    });
    return false;
});