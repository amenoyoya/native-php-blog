<?php
/** API関連の共通関数 **/

require_once(dirname(__FILE__) . '/../config/mysql.php');

/**
 * APIリクエストの受け口
 * MySQLデータベース接続＆HTTPリクエスト処理
 * 
 * @param function $callback: リクエストを処理するコールバック関数
 *   - @param string $method: HTTPメソッド名
 *   - @param array $params: 受信パラメータ
 *   - @param PDO $pdo: PDOオブジェクト
 *   - @param array &$response: リクエストが処理されたとき返されるデータ [status: ステータス, ...]
 *   - @return bool: リクエストを処理したかどうか
 * 
 * @return void
 */
function procAPIRequest($callback){
  // PDOでMySQLデータベースに接続
  try{
      $_ = function($s){return $s;}; // 定数展開用
      $pdo = new PDO("mysql:host={$_(MYSQL_HOST)};dbname={$_(MYSQL_DB_NAME)};charset=utf8",
        MYSQL_USER, MYSQL_PASSWORD, [PDO::ATTR_EMULATE_PREPARES => false]
      );
      // 受信したデータパラメータをパージ
      parse_str(file_get_contents('php://input'), $params);
      // リクエスト処理実行
      if(!$callback($_SERVER['REQUEST_METHOD'], $params, $pdo, $response)){
        // サポートされていないHTTPメソッドがリクエストされた
        $response = [
          'status' => 400, 'message' => 'サポートされていないHTTPメソッドがリクエストされました'
        ];
      }
  }catch(PDOException $e){
      $response = [
        'status' => 500, 'message' => 'データベース接続エラー：' . $e->getMessage(),
      ];
  }
  // ヘッダとレスポンスコードを設定し、JSONデータを返す
  header('Content-Type: application/json'); // 結果をJSON形式で返す
  http_response_code($response['status']);
  echo json_encode($response);
}


/**
 * トークン発行関数
 * 
 * @param string $password: 暗号化・復号パスワード
 * @param int $expire: トークンの有効期間（秒）
 * @param array $json: 暗号化するJSONデータ（expireキーは設定しないこと）
 * 
 * @return string: トークン
 */
function encryptToken($password, $expire, $json){
  // JSONデータに有効期限を設定
  $json['expire'] = time() + $expire;
  
  // JSONデータを平文化
  $json_str = json_encode($json); 
  
  // 暗号化用のメソッド: AES-256-CBC
  // 利用可能な暗号メソッドの一覧を取得するには openssl_get_cipher_methods() を使用
  $method = 'AES-256-CBC';
  
  // 暗号初期化ベクトル (IV) の長さを取得
  $iv_size = openssl_cipher_iv_length($method);  

  // 暗号化・復元用のIVキーを作成
  // 暗号モードに対するIVの長さに合わせたキーを生成
  $iv = openssl_random_pseudo_bytes($iv_size);
  
  // 暗号化
  $options = OPENSSL_RAW_DATA;
  $encrypted = openssl_encrypt($json_str, $method, $password, $options, $iv);

  // 暗号化データと初期化ベクトルを含むJSONデータを認証用データとする
  $authdata = ['enc' => base64_encode($encrypted), 'iv' => base64_encode($iv)];
  $authdata_str = json_encode($authdata);

  // トークンは認証用データをBASE64エンコードしたものとする
  return base64_encode($authdata_str);
}

/**
* トークン認証＆JSONデータ復号
* 
* @param string $password: 暗号化・復号パスワード
* @param string $token: 発行されたトークン
*
* @return array|bool: 成功時=[JSONデータ], 失敗時=false
*/
function decryptToken($password, $token){
  // トークンを認証用JSONデータに変換
  $authdata = json_decode(base64_decode($token), true);
  if(!$authdata) return false;
  if(!isset($authdata['enc']) || !isset($authdata['iv'])) return false;

  // 復号に必要な暗号文と初期化ベクトルを取得
  $enc = base64_decode($authdata['enc']);
  $iv = base64_decode($authdata['iv']);
  
  // 復号処理
  $method = 'AES-256-CBC';
  $options = OPENSSL_RAW_DATA;
  $result = openssl_decrypt($enc, $method, $password, $options, $iv);
  if(!$result) return false;
  
  // JSONデータに戻す
  $json = json_decode($result, true);

  // トークンの有効期限内か確認
  if(!isset($json['expire'])) return false;
  if($json['expire'] - time() < 0) return false; // 期限切れ

  // expireキーを削除してJSONデータを返す
  unset($json['expire']);
  return $json;
}


/**
 * ユーザー認証状態チェック関数
 * 
 * @param array $params: 受信パラメータ
 * @param array &$response: 認証エラーが発生した場合にレスポンスデータが渡される
 * 
 * @return array|bool: ユーザー認証済みなら[id: ユーザーID, name: ユーザー名], ユーザー未認証ならfalse
 */
function checkUserState($params, &$response){
  // パラメータチェック
  if(!isset($params['user-token'])){
    $response = [
      'status' => 400, 'message' => 'パラメータが不正です',
    ];
    return false;
  }
  // トークン認証
  // 暗号化パスワードはMySQLのパスワードを流用
  $user = decryptToken(MYSQL_PASSWORD, $params['user-token']);
  if(!$user){
      $response = [
        'status' => 401, 'message' => 'ユーザーが認証されていません',
      ];
      return false;
  }
  return $user;
}