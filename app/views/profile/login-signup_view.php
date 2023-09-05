<link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>white-lion/profile/login.css">

<main class="container <?= ($this->alias->alias == 'signup') ? 'right-panel-active' : '' ?>" id="login-container">
    <div class="form-container sign-up-container">

        <?php
        if ($this->alias->alias == 'signup')
            require_once APP_PATH . 'views/@commons/notify.php';

        require_once '@commons/signup.php';
        ?>

    </div>
    <div class="form-container sign-in-container">

        <?php
        if ($this->alias->alias == 'login')
            require_once APP_PATH . 'views/@commons/notify.php';

        require_once '@commons/login.php';
        ?>
        
    </div>
    <div class="overlay-container m-hide">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1><?= $this->text('Вже зареєстровані?', 4) ?></h1>
                <p><?= $this->text('Увійти за допомогою email та паролю', 4) ?></p>
                <button class="ghost hexa" id="signIn"><?= $this->text('Увійти', 4) ?></button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1><?= $this->text('Реєстрація', 5) ?></h1>
                <p><?= $this->text('Вкажіть свої персональні дані (ім\'я, емейл, телефон) та розпочнімо співпрацю з', 5) ?> <span class="GreatVibes"><?= SITE_NAME ?></span></p>
                <button class="ghost hexa" id="signUp"><?= $this->text('Зареєструватися', 5) ?></button>
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

    <?php // loaded in page_view and app.js
    /* $this->page->js_load = 'assets/plugins/jquery.mask/jquery.mask.min.js'; 
    if (!empty($_GET['redirect']) || $this->data->re_post('redirect')) {
        echo 'var redirect = "' . $this->data->re_post('redirect', $this->data->get('redirect')) . '";';
    } else echo "var redirect = false;"; ?>

    window.onload = function() {
        var mask_options = {
            onKeyPress: function(cep, e, field, options) {
                mask = '+00 (000) 000-00-00';
                if (cep == '+')
                    field.mask(mask, mask_options);
                else if (cep.length > 3) {
                    cep = cep.substr(0, 3);
                    if (cep == '+38')
                        $('input[name=phone]').mask('+38 (000) 000-00-00', mask_options);
                    else
                        field.mask(mask, mask_options);
                }
            }
        };
        $('input[name=phone]').mask('+38 (000) 000-00-00', mask_options);
    }; */ ?>
</script>
<?php /* if ($_SESSION['option']->userSignUp && ($_SESSION['option']->facebook_initialise || $this->googlesignin->clientId)) {
    if ($this->googlesignin->clientId)
        echo '<script src="https://apis.google.com/js/platform.js" async defer></script>';
    $this->load->js('assets/white-lion/profile/login.js');
} */