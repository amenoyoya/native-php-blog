<<<<<<< HEAD
<?php

require_once('../../config/mysql.php');
require_once('./post.php');
require_once('./get.php');

$data = []; // 処理結果

// PDOでMySQLデータベースに接続
try{
    $_ = function($s){return $s;}; // 定数展開用
    $pdo = new PDO("mysql:host={$_(MYSQL_HOST)};dbname={$_(MYSQL_DB_NAME)};charset=utf8",
        MYSQL_USER, MYSQL_PASSWORD, array(PDO::ATTR_EMULATE_PREPARES => false)
    );
    // 受信したデータパラメータをパージ
    $params = [];
    parse_str(file_get_contents('php://input'), $params);
    // HTTPメソッドごとに処理分岐
    switch($_SERVER['REQUEST_METHOD']){
    case 'POST':
        // 新規ユーザー登録
        $data = registerUser($params, $pdo);
        break;
    case 'GET':
        // ユーザー認証しトークンを発行
        $data = getUserToken($params, $pdo);
        break;
    default:
        // サポートされていないHTTPメソッドがリクエストされた
        $data = [
            'status' => 400, 'message' => 'サポートされていないHTTPメソッドがリクエストされました'
        ];
        break;
    }
}catch(PDOException $e){
    $data = [
        'status' => 500, 'message' => 'データベース接続エラー：' . $e->getMessage(),
    ];
}

// ヘッダとレスポンスコードを設定し、JSONデータを返す
header('Content-Type: application/json'); // 結果をJSON形式で返す
http_response_code($data['status']);
echo json_encode($data);
=======
<?php

require_once('../functions.php');
require_once('./functions.php');

/**
 * ユーザー情報取得API
 * - ユーザートークンが渡されればユーザーIDとユーザー名取得
 * - ユーザー名とパスワードが渡されれば認証＆トークン取得
 * 
 * @internal: メイン処理で呼び出される
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, message: 処理結果のメッセージ]
 *           status: 201 Created（正常に登録完了）, 400 Bad Request（リクエストが不正）, 500 Internal Server Error（データベース処理エラー）
 */
function getUserInfo($params, $pdo){
  // ユーザートークンが渡されていないなら認証処理
  if(!isset($params['user-token'])) return getUserToken($params, $pdo);

  // トークンが渡されているならユーザー情報取得
  if(false === ($user = checkUserState($params, $response))) return $response;
  return [
    'status' => 200, 'user' => $user,
  ];
}


/**
 * 新規ユーザー登録API
 * 
 * @internal: メイン処理で呼び出される
 * 
 * @param array $params: 受信パラメータ
 * @param PDO $pdo: PDOオブジェクト
 * 
 * @return array: [status: ステータスコード, message: 処理結果のメッセージ]
 *           status: 201 Created（正常に登録完了）, 400 Bad Request（リクエストが不正）, 500 Internal Server Error（データベース処理エラー）
 */
function registerUser($params, $pdo){
  // パラメータチェック
  if(!isset($params['user-name']) || !isset($params['user-password'])){
      return [
          'status' => 400, 'message' => 'パラメータが正しく指定されていません',
      ];
  }
  $name = $params['user-name'];
  $password = $params['user-password'];

  // バリデーションチェック
  if(!isValid($name, $password, $response, $pdo)) return $response;
  
  // パスワードはハッシュ化して保存する
  $hash = password_hash($password, PASSWORD_BCRYPT);

  // usersテーブルにデータ挿入
  $state = $pdo->prepare('insert into users (name, password) values (?, ?)');
  if(!$state->bindValue(1, $name, PDO::PARAM_STR)
      || !$state->bindValue(2, $hash, PDO::PARAM_STR)
      || !$state->execute())
  {
      return [
          'status' => 500, 'message' => 'ユーザー登録中にエラーが発生しました',
      ];
  }
  return [
      'status' => 201,
      'message' => '新規ユーザー「' . htmlspecialchars($name) . '」が登録されました',
  ];
}


/**
 * メイン処理
 * リクエストを処理してJSONデータを返す
 */
procAPIRequest(function($method, $params, $pdo, &$response){
  switch($method){
  case 'GET':
    // ユーザー情報 or トークン取得
    $response = getUserInfo($params, $pdo);
    return true;
  case 'POST':
    // 新規ユーザー登録
    $response = registerUser($params, $pdo);
    return true;
  }
  return false;
});
>>>>>>> develop
