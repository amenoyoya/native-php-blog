<!doctype html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>研修用ブログ｜記事一覧</title>
        <!-- stylesheets -->
        <link rel="stylesheet" href="./static/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <div class="row pt-4">
                <div class="col-md-12">
                    <h1>研修用ブログ｜記事一覧</h1>
                    <?php if(isset($error_message)): /* エラー400, 500 が発生した場合、エラーメッセージ表示 */ ?>
                        <?php echo $error_message ?>
                    <?php else: /*  記事が取得できた場合、一覧表示 */ ?>
                        <p class="text-muted">筆者：<?php echo $user['name'] ?></p>
                        <div class="card-deck">
                            <?php foreach($articles as $article): ?>
                                <div class="card">
                                    <div class="card-body">
                                        <!-- 記事タイトル -->
                                        <h2 class="card-title"><?php echo $article['title'] ?></div>
                                        <!-- 編集ボタン -->
                                        <a href="./edit/?id=<?php echo $article['id'] ?>" style="position: absolute; top: 5px; right: 60px; color: #ffffff" class="btn btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- 削除ボタン -->
                                        <a href="./delete/?id=<?php echo $article['id'] ?>" style="position: absolute; top: 5px; right: 5px; color: #ffffff" class="btn btn-danger">
                                            <i class="fas fa-backspace"></i>
                                        </a>
                                        <!-- 記事本文 -->
                                        <pre class="card-text pl-4 pb-4"><?php echo $article['body'] ?></pre>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>
                    <div class="mt-4">
                        <a class="btn btn-primary btn-block btn-lg" href="./add/"><i class="fas fa-plus-circle"></i> 追加</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>