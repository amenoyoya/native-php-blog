/* ユーザー登録ボタンの実装 */
$('#register-user').click(function(){
    var name = $('#user-name').val(), password = $('#user-password').val();
    // 結果欄をクリアしておく
    $('#result').html('');
    // データベース処理実行
    requestAjax('/register/register.php', 'POST', {
        'user-name': name, 'user-password': password
    }, {
        200: function(data){ // 正常終了
            $('#result').html(
                '<div class="alert alert-success">' +
                '<p>ユーザー「' + name + '」が登録されました</p>' +
                '<p>3秒後 トップページに戻ります</p>' +
                '</div>'
            );
            // 3秒後トップページ戻る
            setTimeout(function(){
                location.href = "/";
            }, 3000);
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