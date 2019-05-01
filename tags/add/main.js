/* タグ登録ボタンの実装 */
$('#add-tag').click(function(){
    var token = $('#user-token').val(), name = $('#tag-name').val();
    // 結果欄をクリアしておく
    $('#result').html('');
    // データベース処理実行
    requestAjax('/api/tags/', 'POST', {
        'user-token': token, 'tag-name': name
    }, {
        201: function(data){ // 正常終了
            $('#result').html('<div class="alert alert-success">' + data['message'] + '</div>');
            // 登録済みタグ一覧更新
            $('#tags').html($('#tags').html() + '<li class="list-inline-item">'
              + data['tag-name'] + '</li>'
            );
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