<?php /* 適切な記事IDが指定されているときの編集画面 */ ?>
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
                    <form>
                        <input type="hidden" id="blog-id" value="<?php echo $article['id'] ?>">
                        <div class="form-group">
                            <label for="blog-title">タイトル</label>
                            <input type="text" class="form-control" id="blog-title" value="<?php echo $article['title'] ?>">
                            <small class="text-muted">ブログタイトルは200バイト以内で指定してください。</small>
                        </div>
                        <div class="form-group">
                            <label for="blog-body">本文</label>
                            <textarea class="form-control" id="blog-body"><?php echo $article['body'] ?></textarea>
                        </div>
                        <button type="button" class="btn btn-primary" id="update-article">更新</button>
                    </form>
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