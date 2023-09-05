<!-- BEGIN #header -->
<div id="header" class="app-header">
  <!-- BEGIN navbar-header -->
  <div class="navbar-header">
    <button type="button" class="navbar-mobile-toggler d-none" data-toggle="app-sidebar-end-mobile">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a href="<?= SITE_URL ?>" class="navbar-brand ms-0"><span class="navbar-logo"></span> <?= SITE_NAME ?></a>
    <button type="button" class="navbar-mobile-toggler d-none" data-bs-toggle="collapse" data-bs-target="#top-navbar">
      <span class="fa-stack fa-lg">
        <i class="far fa-square fa-stack-2x"></i>
        <i class="fa fa-cog fa-stack-1x mt-1px"></i>
      </span>
    </button>
    <button type="button" class="navbar-mobile-toggler d-none" data-toggle="app-top-menu-mobile">
      <span class="fa-stack fa-lg">
        <i class="far fa-square fa-stack-2x"></i>
        <i class="fa fa-cog fa-stack-1x mt-1px"></i>
      </span>
    </button>
    <button type="button" class="navbar-mobile-toggler" data-toggle="app-sidebar-mobile">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
  </div>
  <!-- END navbar-header -->

  <!-- BEGIN header-nav -->
  <div class="navbar-nav">
    <?php /*<div class="navbar-item navbar-form">
      <form action="<?=SITE_URL?>admin/search" method="POST" name="search">
        <div class="form-group">
          <input type="text" name="by" class="form-control" placeholder="Enter keyword" />
          <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
        </div>
      </form>
    </div>*/ ?>
    <div class="navbar-item dropdown">
      <a href="#" data-bs-toggle="dropdown" class="navbar-link dropdown-toggle icon">
        <i class="fa fa-bell"></i>
        <span class="badge">0</span>
      </a>
      <div class="dropdown-menu media-list dropdown-menu-end">
        <div class="dropdown-header">NOTIFICATIONS (0)</div>
        <div class="text-center w-300px py-3">
          No notification found
        </div>
      </div>
    </div>

    <div class="navbar-item navbar-user dropdown">
      <a href="#" class="navbar-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
        <div class="image image-icon bg-gray-800 text-gray-600">
          <i class="fa fa-user"></i>
        </div>
        <span>
          <span class="d-none d-md-inline"><?= $this->user->name ?></span>
          <b class="caret"></b>
        </span>
      </a>
      <div class="dropdown-menu dropdown-menu-end me-1">
        <a href="<?= SITE_URL ?>admin/wl_users/my" data-toggle="ajax" class="dropdown-item"><i class="fa fa-cog"></i> Edit Profile</a>
        <div class="dropdown-divider"></div>
        <a href="<?= SITE_URL ?>logout" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Log Out</a>
      </div>
    </div>
  </div>
  <!-- END header-nav -->
</div>
<!-- END #header -->