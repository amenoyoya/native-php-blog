<!doctype html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>研修用ブログ｜記事一覧</title>
        <!-- stylesheets -->
        <link rel="stylesheet" href="static/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <div class="row pt-4">
                <div class="col-md-12">
                    <h1>研修用ブログ｜記事一覧</h1>
                    <div class="card-deck">
                        <?php
                            // PDOでMySQLデータベースに接続
                            try{
                                $pdo = new PDO('mysql:host=localhost.localdomain;dbname=blog;charset=utf8',
                                    'root', 'Exir@SQL190401', array(PDO::ATTR_EMULATE_PREPARES => false)
                                );
                            }catch(PDOException $e){
                                exit('データベース接続エラー：' . $e->getMessage());
                            }
                            // 記事一覧取得：最新記事順（idの降順）
                            $query = $pdo->query('select * from articles order by id desc');
                            while($row = $query->fetch(PDO::FETCH_ASSOC)):
                                // 各パラメータは登録時点でHTMLエスケープされていると想定
                                $id = $row['id'];
                                $title = $row['title'];
                                $body = $row['body'];
                        ?>
                            <div class="card">
                                <div class="card-body">
                                    <!-- 記事タイトル -->
                                    <h2 class="card-title"><?php echo $title ?></div>
                                    <!-- 編集ボタン -->
                                    <a href="./edit/?id=<?php echo $id ?>" style="position: absolute; top: 5px; right: 60px; color: #ffffff" class="btn btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- 削除ボタン -->
                                    <a href="./delete/?id=<?php echo $id ?>" style="position: absolute; top: 5px; right: 5px; color: #ffffff" class="btn btn-danger">
                                        <i class="fas fa-backspace"></i>
                                    </a>
                                    <!-- 記事本文 -->
                                    <pre class="card-text pl-4 pb-4"><?php echo $body ?></pre>
                                </div>
                            </div>
                        <?php endwhile ?>
                    </div>
                    <div class="mt-4">
                        <a class="btn btn-primary btn-block btn-lg" href="./add/"><i class="fas fa-plus-circle"></i> 追加</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- javascripts -->
        <script src="static/js/bootstrap.min.js"></script>
    </body>
</html>