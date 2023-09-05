<!-- BEGIN breadcrumb -->
<ol class="breadcrumb float-xl-end">

	<?php if (!empty($this->page->breadcrumbs)) {
		echo '<li class="breadcrumb-item"><a href="' . SITE_URL . 'admin" data-toggle="ajax">Home</a></li>';
		foreach ($this->page->breadcrumbs as $name => $link) {
			if ($link == '')
				echo ('<li class="breadcrumb-item active">' . $name . '</li>');
			else
				echo ('<li class="breadcrumb-item"><a href="' . SITE_URL . 'admin/' . $link . '" data-toggle="ajax">' . $name . '</a></li>');
		}
	} else
		echo '<li class="breadcrumb-item active"><a href="' . SITE_URL . 'admin" data-toggle="ajax">Home</a></li>'; ?>
</ol>
<!-- END breadcrumb -->

<!-- BEGIN page-header -->
<h1 class="page-header"><?= $this->page->name ?></h1>
<!-- END page-header -->

<?php
require_once '@commons/notify.php';
require_once($view_file . '.php');


if (!empty($this->page->js_load)) {
	foreach ($this->page->js_load as $i => $js) {
		if(substr($js, 0, strlen(SERVER_URL)) == SERVER_URL) {
			$this->page->setArrayValue('js_load', $i, substr($js, strlen(SERVER_URL)));
		}
	}
}
?>

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script>
	App.setPageTitle("<?= $this->page->title ?>");
	App.restartGlobalFunction();

	$('a[data-toggle="ajax"]').click(function() {
		$('.modal').modal('hide');
	});

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

	$('form.ajax').submit(function(event) {
		event.preventDefault();
		wl.formSubmit(this);
	});
</script>