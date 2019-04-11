<!doctype html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>研修用ブログ｜新規ユーザー登録</title>
        <!-- stylesheets -->
        <link rel="stylesheet" href="../static/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <div class="row pt-4">
                <div class="col-md-12 mt-3 mb-3">
                    <h1><a href="../">研修用ブログ</a>｜新規ユーザー登録</h1>
                    <form>
                        <div class="form-group">
                            <label for="user-name">ユーザー名</label>
                            <input type="text" class="form-control" id="user-name">
                            <small class="text-muted">ユーザー名は32バイト以内で指定してください。</small>
                        </div>
                        <div class="form-group">
                            <label for="user-password">パスワード</label>
                            <input type="password" class="form-control" id="user-password">
                            <small class="text-muted">パスワードは8バイト以上32バイト以内で指定してください。</small>
                        </div>
                        <button type="button" class="btn btn-primary" id="register-user">登録</button>
                    </form>
                </div>

                <div class="col-md-12 mt-3 mb-3">
                    ユーザー登録済みの場合は、<a href="../login/">ログインページ</a>へ
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