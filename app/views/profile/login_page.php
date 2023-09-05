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
        <div class="login login-v2 fw-bold">
            <!-- BEGIN login-cover -->
            <div class="login-cover">
                <div class="login-cover-img" style="background-image: url(<?= ASSETS_PATH ?>images/login-bg-12.jpg)" data-id="login-cover-image"></div>
                <div class="login-cover-bg"></div>
            </div>
            <!-- END login-cover -->

            <!-- BEGIN login-container -->
            <div class="login-container">
                <!-- BEGIN login-header -->
                <div class="login-header">
                    <div class="brand">
                        <div class="d-flex align-items-center">
                            <span class="logo"></span> <b>Login</b>
                        </div>
                        <small><?= SITE_NAME ?></small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-lock"></i>
                    </div>
                </div>
                <!-- END login-header -->

                <!-- BEGIN login-content -->
                <div class="login-content">
                    <?php require APP_PATH . 'views/admin/@commons/notify.php'; ?>

                    <form action="<?= SITE_URL ?>login" method="POST" id="loginForm">
                        <div class="form-floating mb-20px">
                            <input type="text" class="form-control fs-13px h-45px border-0" placeholder="Email Address" id="emailAddress" name="email" value='<?= $this->data->re_post('email') ?>' required />
                            <label for="emailAddress" class="d-flex align-items-center text-gray-600 fs-13px">Email Address</label>
                        </div>
                        <div class="form-floating mb-20px">
                            <input type="password" class="form-control fs-13px h-45px border-0" placeholder="Password" name="password" required />
                            <label for="emailAddress" class="d-flex align-items-center text-gray-600 fs-13px">Password</label>
                        </div>
                        <?php if (isset($_GET['redirect']) || $this->data->re_post('redirect')) { ?>
                            <input type="hidden" name="redirect" value="<?= $this->data->re_post('redirect', $this->data->get('redirect')) ?>">
                        <?php } ?>
                        <div class="mb-20px">
                            <?php $this->recaptcha->button('Sign me in <i class="fas fa-sign-in-alt"></i>', 'loginForm', "btn btn-success d-block w-100 h-45px btn-lg") ?>
                        </div>
                        <div class="text-gray-500">
                            Back to <a href="<?= SITE_URL ?>" class="text-white">Main page</a> or <a href="<?= SITE_URL ?>reset" class="text-white">Reset password</a>.
                        </div>
                    </form>
                </div>
                <!-- END login-content -->
            </div>
            <!-- END login-container -->
        </div>

        <!-- BEGIN login-bg -->
        <div class="login-bg-list clearfix">
            <div class="login-bg-list-item active"><a href="javascript:;" class="login-bg-list-link" data-toggle="login-change-bg" data-img="<?= ASSETS_PATH ?>images/login-bg-12.jpg" style="background-image: url(<?= ASSETS_PATH ?>images/login-bg-12.jpg)"></a></div>
            <div class="login-bg-list-item"><a href="javascript:;" class="login-bg-list-link" data-toggle="login-change-bg" data-img="<?= ASSETS_PATH ?>images/login-bg-16.jpg" style="background-image: url(<?= ASSETS_PATH ?>images/login-bg-16.jpg)"></a></div>
            <div class="login-bg-list-item"><a href="javascript:;" class="login-bg-list-link" data-toggle="login-change-bg" data-img="<?= ASSETS_PATH ?>images/login-bg-15.jpg" style="background-image: url(<?= ASSETS_PATH ?>images/login-bg-15.jpg)"></a></div>
            <div class="login-bg-list-item"><a href="javascript:;" class="login-bg-list-link" data-toggle="login-change-bg" data-img="<?= ASSETS_PATH ?>images/login-bg-14.jpg" style="background-image: url(<?= ASSETS_PATH ?>images/login-bg-14.jpg)"></a></div>
            <div class="login-bg-list-item"><a href="javascript:;" class="login-bg-list-link" data-toggle="login-change-bg" data-img="<?= ASSETS_PATH ?>images/login-bg-13.jpg" style="background-image: url(<?= ASSETS_PATH ?>images/login-bg-13.jpg)"></a></div>
        </div>
        <!-- END login-bg -->

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
    <script type="text/javascript">
        var handleLoginPageChangeBackground = function() {
            "use strict";

            var toggleAttr = '[data-toggle="login-change-bg"]';
            var toggleImageAttr = '[data-id="login-cover-image"]';
            var toggleImageSrcAttr = 'data-img';
            var toggleItemClass = '.login-bg-list-item';
            var toggleActiveClass = 'active';

            $(document).on('click', toggleAttr, function(e) {
                e.preventDefault();

                $(toggleImageAttr).css('background-image', 'url(' + $(this).attr(toggleImageSrcAttr) + ')');
                $(toggleAttr).closest(toggleItemClass).removeClass(toggleActiveClass);
                $(this).closest(toggleItemClass).addClass(toggleActiveClass);
            });
        };

        $(document).ready(function() {
            handleLoginPageChangeBackground();
        });
    </script>
</body>

</html>