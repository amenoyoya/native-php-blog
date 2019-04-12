<?php
/** 共通関数 **/

require_once(dirname(__FILE__) . '/config/api.php');

/**
 * REST API 呼び出し関数
 * 
 * @param string $api: 呼び出すAPI名
 * @param string $method: GET|POST|PUT|DELETE
 * @param array $data: 送信するデータ
 * @return array: [status: レスポンスコード, json: JSONデータ, response: レスポンステキスト]
 */
function callAPI($api, $method, $data=[]){
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, API_URL . $api . '/');
  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 証明書の検証を行わない
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // curl_execの結果を文字列で返す
  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // 送信するデータを渡す
  $r = curl_exec($curl);
  $res = [
    'status' => curl_getinfo($curl)['http_code'],
    'json' => json_decode($r, true),
    'response' => $r,
  ];
  curl_close($curl);
  return $res;
}