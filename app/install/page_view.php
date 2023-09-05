<!DOCTYPE html>
<!--[if IE 8]> <html lang="uk" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="uk">
<!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <title>Інсталяція White Lion CMS <?=WL_VERSION?></title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="WebSpirit Creation agency - webspirit.com.ua" name="author" />

    <!-- ================== BEGIN BASE CSS STYLE ================== -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="<?=SERVER_URL?>style/color-admin/app.min.css" rel="stylesheet" />
    <!-- ================== END BASE CSS STYLE ================== -->
    <link rel="shortcut icon" type="image/x-icon" href="<?=SERVER_URL?>style/white-lion/white-lion-black.png">
</head>
<body class="pace-top">
    <!-- begin #page-loader -->
    <div id="page-loader" class="fade show"><span class="spinner"></span></div>
    <!-- end #page-loader -->
    
    <!-- begin login-cover -->
    <div class="login-cover">
        <div class="login-cover-image" style="background-image: url(<?=SERVER_URL?>style/color-admin/login-bg/login-bg-12.jpg)" data-id="login-cover-image"></div>
        <div class="login-cover-bg"></div>
    </div>
    <!-- end login-cover -->
    
    <!-- begin #page-container -->
    <div id="page-container" class="fade">
        <!-- begin login -->
        <div class="login login-v2" data-pageload-addclass="animated fadeIn">
            <!-- begin brand -->
            <div class="login-header">
                <div class="brand">
                    <img src="<?=SERVER_URL?>style/white-lion/white-lion-white.png" style="width: 50px;"> White Lion CMS <?=WL_VERSION?>
                    <small>Інсталяція <?=SITE_URL?></small>
                </div>
            </div>
            <!-- end brand -->
            <!-- begin login-content -->
            <div class="login-content">
                <?php
                    if (isset($_SESSION['notify'])) require APP_PATH.'views'.DIRSEP.'admin'.DIRSEP.'notify_view.php';
                    if (isset($view_file) && $view_file != '') require_once($view_file.'.php');
                ?>
            </div>
            <!-- end login-content -->
        </div>
        <!-- end login -->

        <div id="divLoading"></div>
        
        <div id="login-copyright" class="d-none d-sm-block">White Lion CMS <?=WL_VERSION?>. Design by Color Admin</div>
        <!-- begin login-bg -->
        <ul class="login-bg-list clearfix">
            <li class="active"><a href="javascript:;" data-click="change-bg" data-img="<?=SERVER_URL?>style/color-admin/login-bg/login-bg-12.jpg" style="background-image: url(<?=SERVER_URL?>style/color-admin/login-bg/login-bg-12.jpg)"></a></li>
            <li><a href="javascript:;" data-click="change-bg" data-img="<?=SERVER_URL?>style/color-admin/login-bg/login-bg-16.jpg" style="background-image: url(<?=SERVER_URL?>style/color-admin/login-bg/login-bg-16.jpg)"></a></li>
            <li><a href="javascript:;" data-click="change-bg" data-img="<?=SERVER_URL?>style/color-admin/login-bg/login-bg-15.jpg" style="background-image: url(<?=SERVER_URL?>style/color-admin/login-bg/login-bg-15.jpg)"></a></li>
            <li><a href="javascript:;" data-click="change-bg" data-img="<?=SERVER_URL?>style/color-admin/login-bg/login-bg-14.jpg" style="background-image: url(<?=SERVER_URL?>style/color-admin/login-bg/login-bg-14.jpg)"></a></li>
            <li><a href="javascript:;" data-click="change-bg" data-img="<?=SERVER_URL?>style/color-admin/login-bg/login-bg-13.jpg" style="background-image: url(<?=SERVER_URL?>style/color-admin/login-bg/login-bg-13.jpg)"></a></li>
            <li><a href="javascript:;" data-click="change-bg" data-img="<?=SERVER_URL?>style/color-admin/login-bg/login-bg-17.jpg" style="background-image: url(<?=SERVER_URL?>style/color-admin/login-bg/login-bg-17.jpg)"></a></li>
        </ul>
        <!-- end login-bg -->
    </div>
    <!-- end page container -->
    
    <script src="<?=SERVER_URL?>assets/color-admin/app.min.js"></script>
    <script src="<?=SERVER_URL?>assets/color-admin/login-v2.min.js"></script>
</body>
</html>