/* 記事登録ボタンの実装 */
$('#add-article').click(function(){
    var token = $('#user-token').val(), title = $('#article-title').val(), body = $('#article-body').val();
    // 結果欄をクリアしておく
    $('#result').html('');
    // データベース処理実行
    requestAjax('/api/articles/', 'POST', {
        'user-token': token, 'article-title': title, 'article-body': body
    }, {
        201: function(data){ // 正常終了
            $('#result').html('<div class="alert alert-success">' + data['message'] + '</div>');
        },
        401: function(data){ // 認証エラー
            $('#result').html('<div class="alert alert-info">' + data['message'] + '</div>');
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