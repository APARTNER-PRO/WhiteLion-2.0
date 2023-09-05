<link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>white-lion/profile/login.css">

<main class="container <?= ($this->alias->alias == 'reset') ? 'right-panel-active' : '' ?>" id="login-container">
    <div class="form-container sign-up-container">

        <?php require_once APP_PATH . 'views/@commons/notify.php'; ?>

        <form action="<?= SITE_URL ?>reset" method="POST" id="resetLogin">
            <h1><?= $this->text('Відновлення паролю', 4) ?></h1>
            <input type="text" name="email" value="<?= $this->data->re_post('email') ?>" placeholder="Email" required />
            <?php $this->recaptcha->button($this->text('Продовжити'), 'resetLogin', 'mt-15') ?>
        </form>

    </div>
    <div class="form-container sign-in-container">

        <?php require '@commons/login.php'; ?>

    </div>
    <div class="overlay-container m-hide">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1><?= $this->text('Вже зареєстровані?', 4) ?></h1>
                <p><?= $this->text('Увійти за допомогою email/телефону та паролю', 4) ?></p>
                <button class="ghost hexa" id="signIn"><?= $this->text('Увійти', 4) ?></button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1><?= $this->text('Забув пароль?', 5) ?></h1>
                <button class="ghost hexa" id="signUp"><?= $this->text('Відновити', 5) ?></button>
            </div>
        </div>
    </div>
</main>

<script type="text/javascript">
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('login-container');

    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });
</script>

<?php /* if ($_SESSION['option']->userSignUp && ($_SESSION['option']->facebook_initialise || $this->googlesignin->clientId)) {
    if ($this->googlesignin->clientId)
        echo '<script src="https://apis.google.com/js/platform.js" async defer></script>';
    $this->load->js('assets/white-lion/profile/login.js');
} */