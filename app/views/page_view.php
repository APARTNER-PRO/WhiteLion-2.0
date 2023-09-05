<html lang="<?= LANGUAGE_LOCALE ?>" prefix="og: http://ogp.me/ns#">

<head>
	<title><?= $this->page->title ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta name="title" content="<?= $this->page->title ?>">
	<meta name="description" content="<?= $this->page->description ?>">
	<meta name="keywords" content="<?= $this->page->keywords ?>">
	<meta name="author" content="webspirit.com.ua">

	<meta property="og:locale" content="<?= LANGUAGE_LOCALE ?>" />
	<meta property="og:title" content="<?= $this->page->title ?>" />
	<meta property="og:description" content="<?= $this->page->description ?>" />
	<?php if (!empty($this->page->image)) { ?>
		<meta property="og:image" content="<?= $this->page->image ?>" />
	<?php } ?>

	<?= $this->page->meta ?>

	<link rel="icon" sizes="192x192" href="<?= STYLE_PATH ?>icons/favicon.png">
	<link rel="shortcut icon" href="<?= STYLE_PATH ?>icons/favicon.png" type="image/png" />
	<link rel="apple-touch-icon" href="<?= STYLE_PATH ?>icons/favicon.png" type="image/png" />

	<link href="<?= PLUGINS_PATH ?>font-awesome-5.15.1/css/all.min.css" rel="stylesheet" />
	<link href="<?= STYLE_PATH ?>wl.css" rel="stylesheet" />
	<link href="<?= STYLE_PATH ?>app.css" rel="stylesheet" />
</head>

<body class='pace-top'>
	<!-- BEGIN #loader -->
	<div id="loader" class="show"></div>
	<!-- END #loader -->

	<!-- BEGIN #app -->
	<?php
	include "@commons/header.php";

	echo ('<main id="app">');
	require_once '@commons/notify.php';
	if (isset($view_file)) require_once($view_file . '.php');
	echo ('</main>');

	include "@commons/footer.php";
	?>
	<!-- END #app -->

	<div id="modal-bg"></div>
	<div id="ajaxFormResult" class="hide">
		<img src="<?= IMAGES_PATH ?>logo.svg" alt="logo <?= SITE_NAME ?>">
		<p></p>
	</div>

	<?php
	require '@commons/modals.php';

	if (!empty($this->page->js_load)) {
		foreach ($this->page->js_load as $i => $js) {
			if (substr($js, 0, strlen(SERVER_URL)) == SERVER_URL) {
				$this->page->setArrayValue('js_load', $i, substr($js, strlen(SERVER_URL)));
			}
		}
	}
	?>

	<!-- ================== BEGIN core-js ================== -->
	<script src="<?= PLUGINS_PATH ?>jquery/jquery-3.5.1.min.js"></script>
	<script src="<?= PLUGINS_PATH ?>sweetalert/sweetalert.min.js"></script>
	<script src="<?= PLUGINS_PATH ?>jquery.mask/jquery.mask.min.js"></script>
	<script src="<?= ASSETS_PATH ?>wl.js"></script>
	<script src="<?= ASSETS_PATH ?>app.js"></script>
	<!-- ================== END core-js ================== -->
	<script type="text/javascript">
		var SERVER_URL = '<?= SERVER_URL ?>';
		var SITE_URL = '<?= SITE_URL ?>';
		var ALIAS_URL = '<?= $this->alias->url ?>/';
		var scriptsLoaded = cssLoaded = [];

		var ALIAS_URL = '<?= $this->alias->url ?>',
			ALIAS_API_URL = '<?= $this->alias->api_url ?>',
			ALIAS_ADMIN_URL = '<?= $this->alias->admin_url ?>';

		<?php if (count($this->page->css)) { ?>
			var cssFiles = ["<?= implode('", "', $this->page->css) ?>"];
			cssFiles.forEach(function(css, i, array) {
				if (cssLoaded.indexOf(css) < 0) {
					cssLoaded.push(css);
					$.ajax({
						url: SERVER_URL + css,
						dataType: "text/css",
						cache: true
					});
				}
			});
		<?php }
		if (count($this->page->js_load) && count($this->page->js_init)) { ?>
			var scripts = ["<?= implode('", "', $this->page->js_load) ?>"],
				urls = [];
			scripts.forEach(function(js, i, array) {
				if (scriptsLoaded.indexOf(js) < 0) {
					urls.push(js);
					scriptsLoaded.push(js);
				}
			});
			promises = $.map(urls, function(url) {
				return $.getScript(SERVER_URL + url);
			});
			$.when.apply(this, promises)
				.then(function() {
					<?= implode('; ' . PHP_EOL, $this->page->js_init); ?>
				});
		<?php } else if (!empty($this->page->js_load)) { ?>
			let scripts = ["<?= implode('", "', $this->page->js_load) ?>"];
			scripts.forEach(function(js, i, array) {
				if (scriptsLoaded.indexOf(js) < 0) {
					$.getScript(SERVER_URL + js);
					scriptsLoaded.push(js);
				}
			});
		<?php } else if (count($this->page->js_init)) { ?>
			<?= implode('; ' . PHP_EOL, $this->page->js_init); ?>
		<?php } ?>

		$(document).ready(function() {
			App.init();
		});
	</script>
</body>

</html>