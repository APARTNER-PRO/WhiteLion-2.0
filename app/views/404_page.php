<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$this->page->title?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="title" content="<?=$this->page->title?>">
    <meta name="description" content="<?=$this->page->description?>">
    <meta name="keywords" content="<?=$this->page->keywords?>">
    <meta name="author" content="webspirit.com.ua">

    <meta property="og:locale"             content="en" />
    <meta property="og:title"              content="<?=$this->page->title?>" />
    <meta property="og:description"        content="<?=$this->page->description?>" />
        <?php if(!empty($this->page->image)) { ?>
    <meta property="og:image"              content="<?=IMG_PATH.$this->page->image?>" />
        <?php } ?>

    <?=$this->page->meta?>

    <link rel="icon" sizes="192x192" href="<?=SITE_URL?>favicon.png">
    <link rel="shortcut icon" href="<?=SITE_URL?>favicon.png" type="image/png"/>
    <link rel="apple-touch-icon" href="<?=SITE_URL?>favicon.png" type="image/png"/>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="<?=ASSETS_PATH?>color_admin_v5.0/vendor.min.css" rel="stylesheet" />
    <link href="<?=ASSETS_PATH?>color_admin_v5.0/app.min.css" rel="stylesheet" />
</head>
<body class='pace-top'>
    <!-- BEGIN #loader -->
    <div id="loader" class="app-loader">
        <span class="spinner"></span>
    </div>
    <!-- END #loader -->

    <!-- BEGIN #app -->
    <div id="app" class="app">
        <!-- BEGIN error -->
        <div class="error">
            <div class="error-code">404</div>
            <div class="error-content">
                <div class="error-message">We couldn't find it...</div>
                <div class="error-desc mb-4">
                    The page you're looking for doesn't exist. <br />
                    Perhaps, there pages will help find what you're looking for.
                </div>
                <div>
                    <a href="<?=SITE_URL?>" class="btn btn-success px-3">Go Home</a>
                </div>
            </div>
        </div>
        <!-- END error -->
        
        <!-- BEGIN scroll-top-btn -->
        <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top" data-toggle="scroll-to-top"><i class="fa fa-angle-up"></i></a>
        <!-- END scroll-top-btn -->
    </div>
    <!-- END #app -->
    
    <!-- ================== BEGIN core-js ================== -->
    <script src="<?=ASSETS_PATH?>color_admin_v5.0/vendor.min.js"></script>
    <script src="<?=ASSETS_PATH?>color_admin_v5.0/app.min.js"></script>
    <script src="<?=ASSETS_PATH?>color_admin_v5.0/default.min.js"></script>
    <!-- ================== END core-js ================== -->
</body>
</html>