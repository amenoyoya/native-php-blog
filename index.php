<!doctype html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>研修用ブログ｜記事一覧</title>
        <!-- stylesheets -->
        <link rel="stylesheet" href="static/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <div class="row pt-4">
                <div class="col-md-12">
                    <h1>研修用ブログ｜記事一覧</h1>
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
                    ?>
                        <div class="blog-title"><?php echo $row['title'] ?></div>
                        <div class="blog-body"><?php echo $row['body'] ?></div>
                    <?php endwhile ?>
                </div>
            </div>
        </div>
        <!-- javascripts -->
        <script src="static/js/bootstrap.min.js"></script>
    </body>
</html>