$(document).ready(function () {
    // ------------------------------------------------------- //
    // Email RegExp
    // ------------------------------------------------------ //
    $.validator.methods.email = function (value, element) {
        return this.optional(element) || /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value);
    }
    // ------------------------------------------------------- //
    // Validation & Register User
    // ------------------------------------------------------ //
    $('#regForm').validate({
        errorElement: "div",
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        ignore: ':hidden:not(.summernote),.note-editable.card-block',
        errorPlacement: function (error, element) {
            error.addClass("invalid-feedback");
            if (element.prop("type") === "checkbox") {
                error.insertAfter(element.siblings("label"));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function () {
            // submit
            $.ajax({
                type: 'post',
                url: 'userRegister',
                dataType: 'json',
                data: $('#regForm').serialize(),
                success: function (res) {
                    $('.response').html(res);
                },
                beforeSend: function () {
                    $('.response').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.response').html(textStatus + ' ' + jqXHR.status + ' ' + errorThrown);
                }
            });
        }
    });
    // ------------------------------------------------------- //
    // Validation & Login User
    // ------------------------------------------------------ //
    $('#logForm').validate({
        errorElement: "div",
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        ignore: ':hidden:not(.summernote),.note-editable.card-block',
        errorPlacement: function (error, element) {
            error.addClass("invalid-feedback");
            if (element.prop("type") === "checkbox") {
                error.insertAfter(element.siblings("label"));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function () {
            // submit
            $.ajax({
                type: 'post',
                url: 'userLogin',
                dataType: 'json',
                data: $('#logForm').serialize(),
                success: function (res) {
                    if (res === 'auth') {
                        $.ajax({
                            type: 'post',
                            url: 'projects',
                            success: function (resView) {
                                var path = document.location.pathname;
                                var directory = path.substring(path.indexOf('/'), path.lastIndexOf('/'));
                                $('body').html(resView);
                                window.history.pushState('', '', directory + '/projects');
                                document.title = 'projects';
                            },
                            beforeSend: function () {
                                $('.response').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
                            },
                            error: function () {
                                $('.response').html('Error 500. Error en servidor');
                            }
                        });
                    } else {
                        $('.response').html(res);
                    }
                },
                beforeSend: function () {
                    $('.response').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.response').html(textStatus + ' ' + jqXHR.status + ' ' + errorThrown);
                }
            });
        }
    });
    // ------------------------------------------------------- //
    // Validation & Lost Password User
    // ------------------------------------------------------ //
    $('#lostPassForm').validate({
        errorElement: "div",
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        ignore: ':hidden:not(.summernote),.note-editable.card-block',
        errorPlacement: function (error, element) {
            error.addClass("invalid-feedback");
            if (element.prop("type") === "checkbox") {
                error.insertAfter(element.siblings("label"));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function () {
            // submit
            $.ajax({
                type: 'post',
                url: 'lostPassEmail',
                dataType: 'json',
                data: $('#lostPassForm').serialize(),
                success: function (res) {
                    $('.response').html(res);
                },
                beforeSend: function () {
                    $('.response').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.response').html(textStatus + ' ' + jqXHR.status + ' ' + errorThrown);
                }
            });
        }
    });

});
