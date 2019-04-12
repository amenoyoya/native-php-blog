<!doctype html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>研修用ブログ｜新規タグ登録</title>
        <!-- stylesheets -->
        <link rel="stylesheet" href="../../static/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <div class="row pt-4">
                <div class="col-md-12 mt-3 mb-3">
                    <h1><a href="../../">研修用ブログ</a>｜新規タグ登録</h1>
                    <?php if(isset($error_message)): ?>
                        <?php echo $error_message ?>
                    <?php else: ?>
                        <p class="text-muted">筆者：<?php echo $user['name'] ?></p>
                        <form>
                            <input type="hidden" id="user-token" value="<?php echo $_SESSION['user-token'] ?>">
                            <div class="form-group">
                                <label for="tag-name">タグ名</label>
                                <input type="text" class="form-control" id="tag-name">
                                <small class="text-muted">タグ名は100バイト以内で指定してください。</small>
                            </div>
                            <button type="button" class="btn btn-primary" id="add-tag">追加</button>
                        </form>
                    <?php endif ?>
                </div>

                <div class="col-md-12 mt-3 mb-3">
                    <div id="result"></div>
                </div>

                <div class="col-md-12 mt-3 mb-3">
                  <h2>登録済みのタグ</h2>
                    <ul class="list-inline" id="tags">
                      <?php foreach($tags as $tag): ?>
                        <li class="list-inline-item"><?php echo $tag['name'] ?></li>
                      <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
        <!-- javascripts -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.6/dist/loadingoverlay.min.js"></script>
        <script src="../../static/js/bootstrap.min.js"></script>
        <script src="../../static/js/utils.js"></script>
        <script src="main.js"></script>
    </body>
</html>