/* 文字列のバイト数表示 */
String.prototype.bytes = function () {
    return(encodeURIComponent(this).replace(/%../g,"x").length);
 }
 

/* Ajaxリクエスト関数 */
// @url: Ajaxで実行するURL
// @method: 'POST' | 'GET' | 'PUT' | 'DELETE'
// @data: Ajaxリクエストに付与するデータ
// @callbacks: ステータスコードごとのコールバック関数群
function requestAjax(url, method, data, callbacks){
    $.LoadingOverlay("show"); // ローディングオーバーレイ表示
    $.ajax({
        url: url,
        type: method,
        data: data,
        dataType: 'json',
    })
    .done(function(data, txtStatus, xhr){ // Ajaxリクエストが成功した時発動
        console.log('success', data);
        var func = callbacks[xhr.status]; // ステータスコードごとにコールバック実行
        if(typeof func === 'function') func(data);
    })
    .fail(function(xhr, txtStatus, err){ // Ajaxリクエストが失敗した時発動
        console.log('failed', xhr);
        var func = callbacks[xhr.status]; // ステータスコードごとにコールバック実行
        if(typeof func === 'function') func(xhr.responseJSON);
        else alert("APIの実行に失敗しました\n" + xhr.status + "\n" + txtStatus + "\n" + err.message); // 定義されていないエラーならalert
    })
    .always(function(data){ // Ajaxリクエストが成功・失敗どちらでも発動
        $.LoadingOverlay("hide"); // ローディングオーバーレイ非表示に
    });
}