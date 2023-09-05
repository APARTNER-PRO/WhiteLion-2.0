<html lang="<?= LANGUAGE ?? 'uk' ?>" prefix="og: http://ogp.me/ns#">

<head>
    <title><?= $this->page->title ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="title" content="<?= $this->page->title ?>">
    <meta name="description" content="<?= $this->page->description ?>">
    <meta name="keywords" content="<?= $this->page->keywords ?>">
    <meta name="author" content="webspirit.com.ua">

    <meta property="og:locale" content="<?= LANGUAGE ?? 'uk' ?>" />
    <meta property="og:title" content="<?= $this->page->title ?>" />
    <meta property="og:description" content="<?= $this->page->description ?>" />
    <?php if (!empty($this->page->image)) { ?>
        <meta property="og:image" content="<?= $this->page->image ?>" />
    <?php } ?>

    <?= $this->page->meta ?>

    <link rel="icon" sizes="192x192" href="<?= SITE_URL ?>favicon.png">
    <link rel="shortcut icon" href="<?= SITE_URL ?>favicon.png" type="image/png" />
    <link rel="apple-touch-icon" href="<?= SITE_URL ?>favicon.png" type="image/png" />

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="<?= ASSETS_PATH ?>color_admin_v5.0/vendor.min.css" rel="stylesheet" />
    <link href="<?= ASSETS_PATH ?>color_admin_v5.0/app.min.css" rel="stylesheet" />
</head>

<body class='pace-top'>
    <!-- BEGIN #loader -->
    <div id="loader" class="app-loader">
        <span class="spinner"></span>
    </div>
    <!-- END #loader -->

    <!-- BEGIN #app -->
    <div id="app" class="app">
        <!-- BEGIN login -->
        <div class="login login-with-news-feed">
            <!-- BEGIN news-feed -->
            <div class="news-feed">
                <div class="news-image" style="background-image: url(<?= ASSETS_PATH ?>images/login-bg-4.jpg)"></div>
                <div class="news-caption">
                    <h4 class="caption-title"><b><?= SITE_NAME ?></b> Password reset</h4>
                </div>
            </div>
            <!-- END news-feed -->

            <!-- BEGIN login-container -->
            <div class="login-container">

                <!-- BEGIN login-header -->
                <div class="login-header mb-30px">
                    <div class="brand">
                        <div class="d-flex align-items-center">
                            <span class="logo"></span>

                            <b class="me-10px">Password</b> recovery
                        </div>
                        <small><?= SITE_NAME ?></small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-sign-in-alt"></i>
                    </div>
                </div>
                <!-- END login-header -->

                <!-- BEGIN login-content -->
                <div class="login-content">
                    <?php require APP_PATH . 'views/admin/@commons/notify.php'; ?>

                    <form action="<?= SITE_URL ?>reset/setnewpassword" method="POST" class="fs-13px">
                        <input type="hidden" name="id" value="<?= $user->id ?>">
                        <input type="hidden" name="reset_key" value="<?= $user->reset_key ?>">

                        <p>
                            User ID: <strong><?= $user->id ?></strong> <br>
                            Name: <strong><?= $user->name ?></strong> <br>
                            E-mail: <strong><?= $user->email ?></strong> <br>
                            Recovery key is valid until: <strong><?= date("Y.n.d H:i:s", $user->reset_expires) ?></strong> <br>
                        </p>

                        <h5 class="mb-15px"><?= $this->text('The password must contain the letters a-zA-Z and 0-9. Field length from 5 to 20 characters') ?></h5>

                        <div class="form-floating mb-15px">
                            <input type="password" name="password" class="form-control h-45px fs-13px" placeholder="New password" required id="password" />
                            <label for="password" class="d-flex align-items-center text-gray-600 fs-13px">New password</label>
                        </div>

                        <div class="form-floating mb-15px">
                            <input type="password" name="re-password" class="form-control h-45px fs-13px" placeholder="New password / repeat" required id="re-password" />
                            <label for="re-password" class="d-flex align-items-center text-gray-600 fs-13px">New password / repeat</label>
                        </div>

                        <div class="mb-15px">
                            <button type="submit" class="btn btn-success d-block w-100 h-45px btn-lg"><i class="far fa-check"></i> Set new password</button>
                        </div>
                        <hr class="bg-gray-600 opacity-2" />
                        <div class="text-gray-600 text-center text-gray-500-darker mb-0">
                            &copy; <?= date('Y') ?> by <?= SITE_NAME ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- BEGIN scroll-top-btn -->
        <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top" data-toggle="scroll-to-top"><i class="fa fa-angle-up"></i></a>
        <!-- END scroll-top-btn -->
    </div>
    <!-- END #app -->

    <!-- ================== BEGIN core-js ================== -->
    <script src="<?= ASSETS_PATH ?>color_admin_v5.0/vendor.min.js"></script>
    <script src="<?= ASSETS_PATH ?>color_admin_v5.0/app.min.js"></script>
    <script src="<?= ASSETS_PATH ?>color_admin_v5.0/default.min.js"></script>
    <!-- ================== END core-js ================== -->
</body>

</html>