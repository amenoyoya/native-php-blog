<!doctype html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>研修用ブログ｜記事削除</title>
        <!-- stylesheets -->
        <link rel="stylesheet" href="../static/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <div class="row pt-4">
                <div class="col-md-12">
                    <h1>研修用ブログ｜記事削除</h1>
                    <?php
                        // GETチェック
                        if(!isset($_GET['id'])){
                            echo '<div class="alert alert-danger">削除対象の記事IDが指定されていません</div>';
                        }else{
                            // PDOでMySQLデータベースに接続
                            try{
                                $pdo = new PDO('mysql:host=localhost.localdomain;dbname=blog;charset=utf8',
                                    'root', 'Exir@SQL190401', array(PDO::ATTR_EMULATE_PREPARES => false)
                                );
                                // blogデータベース/articlesテーブルから記事削除
                                $state = $pdo->prepare('delete from articles where id = ?');
                                if(!$state->bindValue(1, $_GET['id'], PDO::PARAM_INT)
                                    || !$state->execute())
                                {
                                    echo '<div class="alert alert-danger">記事の削除処理中にエラーが発生しました</div>';
                                }else{
                                    echo '<div class="alert alert-success"><p>対象のブログ記事を削除しました</p><p>3秒後 トップページに戻ります</p></div>';
                                    echo '<script>setTimeout(function(){ location.href = "../"; }, 3000);</script>';
                                }
                            }catch(PDOException $e){
                                echo '<div class="alert alert-danger">データベース接続エラー：' . $e->getMessage() . '</div>';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
        
        <!-- javascripts -->
        <script src="../static/js/bootstrap.min.js"></script>
    </body>
</html>