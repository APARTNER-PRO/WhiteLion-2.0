let wl = {
    // echo: false / swal / all
    // contentType: "application/x-www-form-urlencoded; charset=UTF-8" | "multipart/form-data" | "application/json; charset=utf-8"
    ajax: function (url, data = {}, echo = 'all', type = "POST", contentType = "application/x-www-form-urlencoded; charset=UTF-8") {
        return new Promise(function(resolve, reject) {
            if (url.substr(0, 4) != 'http') {
                url = SITE_URL + url;
            }
            var processData = true;
            if (contentType == 'multipart/form-data' || data instanceof FormData) {
                processData = false;
                contentType = false;
            } else if (contentType == 'application/json; charset=utf-8') {
                data = JSON.stringify(data);
            }
            $('#loader').removeClass('loaded').css('background', 'rgb(0 0 0 / 20%)');
            $.ajax({
                url: url,
                type: type,
                data: data,
                processData: processData,
                contentType: contentType,
                success: function (res) {
                    if (echo != false && echo != 'false') {
                        if (res.status === undefined || res.status === '')
                            res.status = 'error';
                        if (res.title === undefined || res.title === '')
                            res.title = res.status.charAt(0).toUpperCase() + res.status.slice(1) + '!';
                        if (res.text === undefined)
                            res.text = '';
                        if (res.icon === undefined || res.icon === '')
                            res.icon = res.status;
                        
                        if (echo == 'swal' || echo == 'all') {
                            swal({
                                title: res.title,
                                text: res.text,
                                icon: res.icon
                            });
                        }
                    }
                    resolve(res);
                },
                error: function () {
                    swal({
                        title: "Error!",
                        text: "Try Again!",
                        icon: "error"
                    });
                    reject("Error!");
                },
                timeout: function () {
                    swal({
                        title: "Timeout Error!",
                        text: "Try Again!",
                        icon: "error"
                    });
                    reject("Timeout Error!");
                },
                complete: function () {
                    setTimeout(function () {
                        $('#loader').removeClass(app.loader.fadingClass).addClass(app.loader.loadedClass);
                    }, app.loader.fadingTime);
                }
            });
        });
    },
    formSubmit: function (el) {
        let form = $(el),
            data = {},
            notify = 'all';
        
        if (form.data('notify') == 'false' || form.data('notify') == 'swal' || form.data('notify') == 'gritter') {
            notify = form.data('notify');
        }

        if (form.data('before')) {
            if (window[form.data('before')](el) === false)
                return false;
        }

        if (form.prop('enctype') == 'multipart/form-data')
            data = new FormData(el);
        else
            data = form.serialize();

        this.ajax(form.prop('action'), data, notify, form.prop('method'), form.prop('enctype'))
            .then((res) => {
                if (res.status == 'success') {
                    if (form.data('after')) {
                        window[form.data('after')](res);
                    }
                    form.find('input').val('');
                }
            });

        return false;
    },
    // doesn't supports in IE
    getGetParamsAsObject: function () {
        let objParams = {};
        let getParams = new URLSearchParams(window.location.search);

        for(const entry of getParams.entries()) {
            objParams[entry[0]] = entry[1];
        }

        return objParams;
    },
    setGetParamsAsObject: function (getObj) {
        let getParams = new URLSearchParams(window.location.search);
        for (const [key, value] of Object.entries(getObj)) {
            getParams.set(key, value);
        }

        return getParams;
    }
}