// 記事登録ボタンの実装
$('#add-article').click(function(){
    var title = $('#blog-title').val(), body = $('#blog-body').text();
    // 入力チェック
    if(title === ''){
        // タイトルが空の場合、警告を出す
        $('#result').html('<div class="alert alert-warning">ブログのタイトルを付けてください</div>');
    }else if(title.bytes > 200){
        // タイトルが200バイトを超える場合、警告を出す
        $('#result').html('<div class="alert alert-warning">ブログのタイトルが長すぎます</div>');
    }
});