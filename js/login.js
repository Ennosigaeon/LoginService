(function($) {

    $.loginService = function(parameters) {
        var self = this;

        var defaults = {
            loginHandle: ".loginButton",
            logoutHandle: ".logoutButton",
            controller: 'php/login.php',
            loginTarget: 'intern',
            logoutTarget: 'index'
        };

        self.options = $.extend({}, defaults, parameters);

        $(self.options.loginHandle).click(function() {
            if ($.cookie('login') !== undefined) {
                $.ajax({
                    url: self.options.controller,
                    data: {cookie: '1', loginController: '1'},
                    statusCode: {
                        200: function() {
                            document.location.href = self.options.loginTarget;
                        },
                        403: function() {
                            self.showWindow();
                        }
                    }
                });
            }
            else {
                self.showWindow();
            }
        });

        $(self.options.logoutHandle).click(function() {
            $.post(self.options.controller, {logout: "1", loginController: '1'}, function() {
                document.location.href = self.options.logoutTarget;
            });
        });

        self.showWindow = function() {
            $("#loginWindow").show();
            $("#loginName").focus();
        };

        self.hideWindow = function() {
            $("#loginWindow").hide();
            $("#loginName").val('');
            $("#loginPw").val('');
        };

        self.submit = function() {
            $.ajax({
                url: self.options.controller,
                method: 'POST',
                data: {
                    name: $("#loginName").val(),
                    pw: $("#loginPw").val(),
                    loginController: '1'
                },
                statusCode: {
                    200: function() {
                        document.location.href = self.options.loginTarget;
                    },
                    403: function() {
                        $("#loginHint").text("Invalid Credentials").show();
                    }
                }
            });
        };

        self.registerListener = function() {
            $("#loginClose").click(function() {
                self.hideWindow();
            });
            $("#loginSubmit").click(function() {
                self.submit();
            });
            $('#rememberLogin').click(function() {
                if ($(this).prop('checked', true))
                    $.cookie('login', 1);
                else
                    $.removeCookie('login');
            });
            $(document).keyup(function(e) {
                if (e.keyCode === 27)
                    self.hideWindow();
            });
            $("#loginPw").keyup(function(e) {
                if (e.keyCode === 13)
                    self.submit();
            });
        };
        self.registerListener();
    }
})(jQuery);