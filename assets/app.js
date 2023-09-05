var App = {
    loader: function (action = 'show') {
        if (action === 'show' || action === true)
            $("#loader").addClass('show');
        else
            $("#loader").removeClass('show');
    },
    init__back_to_top: function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('#back_to_top').fadeIn();
            } else {
                $('#back_to_top').fadeOut();
            }
        });
        $("#back_to_top").click(function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return false;
        });
    },
    init__modals: function () {
        $('#modal-bg, .modal i.fa-times, .modal .close').click(function () {
            $('#modal-bg, .modal').fadeOut()
        });
        $('.to-modal').click(function (event) {
            event.preventDefault();
            let btn = $(this);

            if (btn.data('before')) {
                if (window[btn.data('before')](btn) === false)
                    return false;
            }

            var modal;
            if (btn.prop('tagName') == 'A')
                modal = btn.attr('href');
            else
                modal = btn.data('modal');
            if (modal) {
                $('#modal-bg, ' + modal).fadeIn(400, function () {
                    if (btn.data('after')) {
                        window[btn.data('after')](btn);
                    }
                 });
            }
        });
    },
    init__alert: function () {
        $('.alert .close').click(function () {
            $(this).closest('.alert').hide();
        });
    },
    init__phonemask: function () {
        var mask_options = {
            onKeyPress: function (cep, e, field, options) {
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
    },
    init__header: function () {

    },
    init__catalog: function (category_id) {
        
    },
    init__detail: function (product_id) {

    },
    init: function () {
        $('.select2').select2();

        this.loader('hide');
        this.init__modals();
        this.init__header();
        this.init__back_to_top();
        this.init__alert();
        this.init__phonemask();

        $('form.ajax').submit(wl.formSubmit);
    }
}