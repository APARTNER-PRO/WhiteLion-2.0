<header class="container d-flex">
    <a href="<?=SITE_URL?>" class="d-flex v-center">
        <img src="<?=STYLE_PATH?>white-lion/white-lion-black.png" style="height:30px" title="White Lion CMS <?=WL_VERSION?>">
        <span>White Lion CMS <?=WL_VERSION?></span>
    </a>
    <nav>
        <a href="<?=SITE_URL?>"><?= $this->text('main', 0); ?></a>
        <?php if($this->user->auth()) { ?>
            <a href="<?=SITE_URL?>profile"><?= $this->text('profile', 0); ?></a></li>
            <?php if($this->user->can()) { ?>
            <a href="<?=SITE_URL?>admin">ADMIN</a></li>
            <?php } ?>
            <a href="<?=SITE_URL?>logout"><?= $this->text('logout', 0); ?></a></li>
        <?php } else { ?>
            <a href="<?=SITE_URL?>login"><?= $this->text('login', 0); ?></a></li>
        <?php } ?>
    </nav>
</header>