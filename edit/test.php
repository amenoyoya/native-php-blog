<?php
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://' . $_SERVER['SERVER_NAME'] . '/api/articles/');
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 証明書の検証を行わない
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // curl_execの結果を文字列で返す
/*curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query([
    'blog-id' => 9
])); // 送信するデータを渡す
*/
$r = curl_exec($curl);
var_dump($r);