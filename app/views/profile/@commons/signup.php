<form action="<?= SITE_URL ?>signup" method="POST" id="formSignUp">
    <h1><?= $this->text('Реєстрація') ?></h1>
    <?php /* if($_SESSION['option']->facebook_initialise || $this->googlesignin->clientId) { ?>
        <div class="social-container">
            <?php if($_SESSION['option']->facebook_initialise) { ?>
                <a href="#" class="social facebook-login" title="<?=$this->text('Швидка реєстрація за допомогою facebook', 4)?>"><i class="fab fa-facebook-f"></i></a>
            <?php } if($this->googlesignin->clientId) { ?>
                <a href="#" class="social google-login" title="<?=$this->text('Швидкий вхід за допомогою google', 4)?>"><i class="fab fa-google"></i></a>
            <?php } ?>
        </div>
        <span>або за допомогою email та паролю</span>
    <?php } */ ?>
    <div class="flex wrap">
        <input name="first_name" type="text" value="<?= $this->data->re_post('first_name') ?>" placeholder="<?= $this->text('Ім\'я', 5) ?>" required />
        <input name="last_name" type="text" value="<?= $this->data->re_post('last_name') ?>" placeholder="<?= $this->text('Прізвище', 5) ?>" required />
    </div>
    <div class="flex wrap">
        <input name="email" type="email" value="<?= $this->data->re_post('email') ?>" placeholder="Email" required />
        <input name="phone" type="text" value="<?= $this->data->re_post('phone') ?>" placeholder="<?= $this->text('Контактний телефон', 5) ?>" required minlength="19" />
    </div>
    <div class="flex wrap">
        <input name="password" type="password" value="<?= $this->data->re_post('password') ?>" class="form-control" placeholder="<?= $this->text('Пароль', 4) ?>" required />
        <input name="re-password" type="password" class="form-control" placeholder="<?= $this->text('Повторіть пароль', 5) ?>" required />
    </div>
    <span><?= $this->text('*пороль має містити від 5 до 20 символів', 5) ?></span>

    <?php $this->recaptcha->button($this->text('Зареєструватися', 'signup'), 'formSignUp', 'mt-15') ?>
</form>