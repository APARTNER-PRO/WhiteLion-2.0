<form action="<?= SITE_URL ?>login" method="POST" id="formLogin">
    <h1><?= $this->text('Увійти', 4) ?></h1>
    <?php /* if ($_SESSION['option']->facebook_initialise || $this->googlesignin->clientId) { ?>
                <div class="social-container">
                    <?php if ($_SESSION['option']->facebook_initialise) { ?>
                        <a href="#" class="social facebook-login" title="<?= $this->text('Швидка реєстрація за допомогою facebook', 4) ?>"><i class="fab fa-facebook-f"></i></a>
                    <?php }
                    if ($this->googlesignin->clientId) { ?>
                        <a href="#" class="social google-login" title="<?= $this->text('Швидкий вхід за допомогою google', 4) ?>"><i class="fab fa-google"></i></a>
                    <?php } ?>
                </div>
                <span><?= $this->text('або за допомогою email та паролю', 4) ?></span>
            <?php } */ ?>
    <?php if (isset($_GET['redirect']) || $this->data->re_post('redirect')) { ?>
        <input type="hidden" name="redirect" value="<?= $this->data->re_post('redirect', $this->data->get('redirect')) ?>">
    <?php } ?>
    <input type="text" name="email_phone" value="<?= $this->data->re_post('email') ?>" placeholder="Email або телефон" required />
    <input type="password" name="password" placeholder="<?= $this->text('Пароль', 4) ?>" required />
    <a href="<?= SITE_URL ?>reset"><?= $this->text('Забули пароль?', 4) ?></a>
    <?php $this->recaptcha->button($this->text('Увійти', 'login'), 'formLogin', 'mt-15') ?>
</form>