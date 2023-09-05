<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<title><?= $this->page->title ?> | <?= SITE_NAME ?></title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="webspirit.com.ua" name="author" />

	<link rel="shortcut icon" href="<?= ASSETS_PATH ?>white-lion/white-lion-black.png">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
	<link href="<?= ASSETS_PATH ?>color_admin_v5.0/vendor.min.css" rel="stylesheet" />
	<link href="<?= ASSETS_PATH ?>color_admin_v5.0/app.min.css" rel="stylesheet" />
</head>

<body>
	<!-- BEGIN #loader -->
	<div id="loader" class="app-loader">
		<span class="spinner"></span>
	</div>
	<!-- END #loader -->

	<!-- BEGIN #app -->
	<div id="app" class="app app-header-fixed app-sidebar-fixed">
		<?php require_once '@commons/header.php'; ?>

		<!-- BEGIN #sidebar -->
		<div id="sidebar" class="app-sidebar">
			<!-- BEGIN scrollbar -->
			<div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
				<!-- BEGIN menu -->
				<div class="menu">
					<div class="menu-profile">
						<a href="javascript:;" class="menu-profile-link" data-toggle="app-sidebar-profile" data-target="#appSidebarProfileMenu">
							<div class="menu-profile-cover with-shadow"></div>
							<div class="menu-profile-image menu-profile-image-icon bg-gray-900 text-gray-600">
								<i class="fa fa-user"></i>
							</div>
							<div class="menu-profile-info">
								<div class="d-flex align-items-center">
									<div class="flex-grow-1"><?= $this->user->name ?></div>
									<div class="menu-caret ms-auto"></div>
								</div>
								<small><?= $this->user->type_title ?></small>
							</div>
						</a>
					</div>
					<div id="appSidebarProfileMenu" class="collapse">
						<div class="menu-item pt-5px">
							<a href="<?= SITE_URL ?>admin/wl_users/my" data-toggle="ajax" class="menu-link">
								<div class="menu-icon"><i class="fa fa-cog"></i></div>
								<div class="menu-text">Edit Profile</div>
							</a>
						</div>
						<div class="menu-item pb-5px">
							<a href="<?= SITE_URL ?>logout" class="menu-link">
								<div class="menu-icon"><i class="fas fa-sign-out-alt"></i></div>
								<div class="menu-text"> Logout</div>
							</a>
						</div>
						<div class="menu-divider m-0"></div>
					</div>
					<div class="menu-search mb-n3 d-none">
						<input type="text" class="form-control" placeholder="Sidebar menu filter..." data-sidebar-search="true" />
					</div>
					<div class="menu-header">Navigation</div>
					<div class="menu-item">
						<a href="<?= SITE_URL ?>admin" data-toggle="ajax" class="menu-link">
							<div class="menu-icon">
								<i class="fa fa-th-large"></i>
							</div>
							<div class="menu-text">Home</div>
						</a>
					</div>
					<?php require_once '@commons/__sidebar.php';
					if ($this->user->type_id < 3) { ?>
						<div class="menu-item">
							<a href="<?= SITE_URL ?>admin/wl_users" data-toggle="ajax" class="menu-link">
								<div class="menu-icon">
									<i class="fas fa-users"></i>
								</div>
								<div class="menu-text">Users</div>
							</a>
						</div>
					<?php } ?>
					<!-- <div class="menu-item">
						<a href="<?= SITE_URL ?>admin/wl_notifications" data-toggle="ajax" class="menu-link">
							<div class="menu-icon">
								<i class="fa fa-list-ol"></i>
							</div>
							<div class="menu-text">Notifications</div>
						</a>
					</div> -->
					<!-- BEGIN minify-button -->
					<div class="menu-item d-flex">
						<a href="javascript:;" class="app-sidebar-minify-btn ms-auto" data-toggle="app-sidebar-minify"><i class="fa fa-angle-double-left"></i></a>
					</div>
					<!-- END minify-button -->
				</div>
				<!-- END menu -->
			</div>
			<!-- END scrollbar -->
		</div>
		<div class="app-sidebar-bg"></div>
		<div class="app-sidebar-mobile-backdrop"><a href="#" data-dismiss="app-sidebar-mobile" class="stretched-link"></a></div>
		<!-- END #sidebar -->

		<div data-id="app-extra-elm"></div>

		<!-- begin #content -->
		<div id="content" class="app-content"></div>
		<!-- END #content -->

		<!-- BEGIN scroll-top-btn -->
		<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top" data-toggle="scroll-to-top"><i class="fa fa-angle-up"></i></a>
		<!-- END scroll-top-btn -->
	</div>
	<!-- END #app -->

	<!-- ================== BEGIN core-js ================== -->
	<link href="<?= ASSETS_PATH ?>plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />
	<script src="<?= ASSETS_PATH ?>color_admin_v5.0/vendor.min.js"></script>
	<script src="<?= ASSETS_PATH ?>color_admin_v5.0/app.min.js"></script>
	<script src="<?= ASSETS_PATH ?>color_admin_v5.0/default.min.js"></script>
	<script src="<?= ASSETS_PATH ?>plugins/sweetalert/sweetalert.min.js"></script>
	<script src="<?= ASSETS_PATH ?>plugins/gritter/js/jquery.gritter.js"></script>
    <script src="<?= ASSETS_PATH ?>white-lion/white-lion-admin.js"></script>
	<script type="text/javascript">
        const SERVER_URL = '<?= SERVER_URL ?>',
              SITE_URL = '<?= SITE_URL ?>',
              ADMIN_URL = '<?= SITE_URL ?>admin/';
		var cssLoaded = scriptsLoaded = [];

		App.settings({
			ajaxMode: true,
			ajaxDefaultUrl: SITE_URL + '<?= $this->data->url(true) ?>',
			ajaxType: 'GET',
			ajaxDataType: 'html'
		});
	</script>
	<!-- ================== END core-js ================== -->
</body>

</html>