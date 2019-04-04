<!doctype html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>研修用ブログ｜記事編集</title>
        <!-- stylesheets -->
        <link rel="stylesheet" href="../static/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <div class="row pt-4">
                <div class="col-md-12 mt-3 mb-3">
                    <h1><a href="../">研修用ブログ</a>｜記事編集</h1>
                    <?php
                        // GETチェック
                        if(isset($_GET['id'])):
                    ?>
                        <?php
                            // PDOでMySQLデータベースに接続
                            try{
                                $pdo = new PDO('mysql:host=localhost.localdomain;dbname=blog;charset=utf8',
                                    'root', 'Exir@SQL190401', array(PDO::ATTR_EMULATE_PREPARES => false)
                                );
                                // 記事取得
                                $state = $pdo->prepare('select * from articles where id = ?');
                                if($state->bindValue(1, $_GET['id'], PDO::PARAM_INT)
                                    && $state->execute()
                                    && $row = $state->fetch()):
                        ?>
                                    <form>
                                        <input type="hidden" id="blog-id" value="<?php echo $_GET['id'] ?>">
                                        <div class="form-group">
                                            <label for="blog-title">タイトル</label>
                                            <input type="text" class="form-control" id="blog-title" value="<?php echo $row['title'] ?>">
                                            <small class="text-muted">ブログタイトルは200バイト以内で指定してください。</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="blog-body">本文</label>
                                            <textarea class="form-control" id="blog-body"><?php echo $row['body'] ?></textarea>
                                        </div>
                                        <button type="button" class="btn btn-primary" id="update-article">更新</button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-warning">無効な記事IDが指定されています</div>
                                <?php endif ?>
                        <?php
                            }catch(PDOException $e){
                                echo('<div class="alert alert-danger">データベース接続エラー：' . $e->getMessage() . '</div>');
                            }
                        ?>
                    <?php else: ?>
                        <div class="alert alert-danger">編集対象の記事IDが指定されていません</div>
                    <?php endif ?>
                </div>

                <div class="col-md-12 mt-3 mb-3">
                    <div id="result"></div>
                </div>
            </div>
        </div>
        <!-- javascripts -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.6/dist/loadingoverlay.min.js"></script>
        <script src="../static/js/bootstrap.min.js"></script>
        <script src="../static/js/utils.js"></script>
        <script src="main.js"></script>
    </body>
</html>