var Auth = {
    _userLoggedIn: false,
    isUserLoggedIn: function () {
        return this._userLoggedIn;
    },
    setUserLoggedIn: function (userLoggedIn) {
        this._userLoggedIn = userLoggedIn;
    },
    getToken: function (token) {
        return $.get(Routing.generate('strokit_generate_token', {'tokenName' : token}));
    },
    showLoginFormAndGetToken: function (token, options) {
        var that = this;
        if (this.isUserLoggedIn()) {
            if (options.always) {
                options.always();
            }
            return true;
        }
        return this.showLoginForm({
            success: function() {
                if (options.success) {
                    that.getToken(token).done(options.success);
                }
            },
            always: function() {
                if (options.always) {
                    that.getToken(token).done(options.always);
                }
            }
        })
    },
    showLoginForm: function (options) {
        if (this.isUserLoggedIn()) {
            if (options.always) {
                options.always();
            }
            return true;
        }

        var that = this;
        var $loginFormModal = $('#loginFormModal');
        $loginFormModal.modal();
        $loginFormModal.find('form').off('submit');
        $loginFormModal.find('form').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                type: form.attr('method'),
                url: Routing.generate('fos_user_security_check'),
                data: form.serialize(),
                dataType: "json",
                error: function (data, status, object) {
                    console.log(data);
                    toastr.error(data.responseJSON.error);
                }
            }).done(function (data, status, object) {
                that._userLoggedIn = true;
                $loginFormModal.modal('hide');
                if (options.success) {
                    options.success(data, status, object);
                }
            }).always(function () {
                if (options.always) {
                    options.always();
                }
            });
        });
        return false;
    }
};