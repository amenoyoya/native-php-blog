// 記事更新ボタンの実装
$('#update-article').click(function(){
    var id = $('#blog-id').val(), title = $('#blog-title').val(), body = $('#blog-body').val();
    alert(body);
    $('#result').html(''); // 結果欄をクリアしておく
    // 入力チェック
    if(title === ''){
        // タイトルが空の場合、警告を出す
        $('#result').html('<div class="alert alert-warning">ブログのタイトルを付けてください</div>');
    }else if(title.bytes > 200){
        // タイトルが200バイトを超える場合、警告を出す
        $('#result').html('<div class="alert alert-warning">ブログのタイトルが長すぎます</div>');
    }
    // データベース処理実行
    ajaxDBctl('./update.php', 'POST', {
        'blog-id': id, 'blog-title': title, 'blog-body': body
    }, function(data){
        if(!data['ok']){ // PHP側でエラーが起こった際はアラート実行
            alert(data['message']);
        }else{ // 問題なければ、登録完了メッセージ
            $('#result').html('<div class="alert alert-success">' + data['message'] + '</div>');
        }
    });
    return false;
});