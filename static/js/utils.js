/* 文字列のバイト数表示 */
String.prototype.bytes = function () {
    return(encodeURIComponent(this).replace(/%../g,"x").length);
 }
 

/* データベース操作Ajaxリクエスト関数 */
// @url: Ajaxで実行するURL
// @method: 'POST' or 'GET'
// @data: Ajaxリクエストに付与するデータ
// @callback: Ajaxリクエストが成功したとき発動する関数 function(data)
function ajaxDBctl(url, method, data, callback){
    $.LoadingOverlay("show"); // ローディングオーバーレイ表示
    $.ajax({
        url: url,
        type: method,
        data: data
    })
    .done(function(data){ // Ajaxリクエストが成功した時発動
        callback(data);
    })
    .fail(function(data, text, err){ // Ajaxリクエストが失敗した時発動
        alert("データベースの更新に失敗しました\n" + data.status + "\n" + text + "\n" + err.message);
    })
    .always(function(data){ // Ajaxリクエストが成功・失敗どちらでも発動
        $.LoadingOverlay("hide"); // ローディングオーバーレイ非表示に
    });
}