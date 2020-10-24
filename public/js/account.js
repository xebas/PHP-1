$(document).ready(function () {
    // ------------------------------------------------------- //
    // Validation & Edit Account (NICK)
    // ------------------------------------------------------ //
    $('#editNickForm').validate({
        rules: {
            editNick: {
                required: true
            },
        },
        messages: {
            editNick: {
                required: 'Por favor, modifica tu nick'
            }
        },
        submitHandler: function () {
            // submit
            $.ajax({
                type: 'post',
                url: 'userEditAccount',
                dataType: 'json',
                data: $('#editNickForm').serialize(),
                success: function (res) {
                    if (!Array.isArray(res)) {
                        $('.userNickPassAccRes').html(res);
                    } else {
                        $('.userNickPassAccRes').html(res[0]);
                        setTimeout(function () {
                            $('.userNickPassAccRes').empty();
                            $('.title .h5').html(res[1]);
                        }, 3000);
                    }
                },
                beforeSend: function () {
                    $('.userNickPassAccRes').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.userNickPassAccRes').html(textStatus + ' ' + jqXHR.status + ' ' + errorThrown);
                }
            });
        }
    });
    // ------------------------------------------------------- //
    // Validation & Edit Account (PASS)
    // ------------------------------------------------------ //
    $('#editPassForm').validate({
        rules: {
            editPass: {
                required: true,
                minlength: 6
            },
        },
        messages: {
            editPass: {
                required: 'Por favor, modifica tu contraseña',
                minlength: 'Introduce una contraseña de 6 caract. mínimo'
            }
        },
        submitHandler: function () {
            // submit
            $.ajax({
                type: 'post',
                url: 'userEditAccount',
                dataType: 'json',
                data: $('#editPassForm').serialize(),
                success: function (res) {
                    if (!Array.isArray(res)) {
                        $('.userNickPassAccRes').html(res);
                    } else {
                        $('.userNickPassAccRes').html(res[0]);
                        setTimeout(function () {
                            $('.userNickPassAccRes').empty();
                        }, 3000);
                    }
                },
                beforeSend: function () {
                    $('.userNickPassAccRes').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.userNickPassAccRes').html(textStatus + ' ' + jqXHR.status + ' ' + errorThrown);
                }
            });
        }
    });
    // ------------------------------------------------------- //
    // Validation & Upload Image
    // ------------------------------------------------------ //
    $('#imageAccForm').validate({
        rules: {
            imageAcc: {
                required: true,
                extension: 'jpg,jpeg,png',
                filesize: 5000000
            }
        },
        messages: {
            imageAcc: {
                required: 'Añade una imagen',
                extension: 'Sólo se admiten archivos con de este tipo: (jpg, jpeg, png)'
            }
        },
        errorPlacement: function (error, element) {
            error.appendTo('.userImageErrorRes');
        },
        submitHandler: function () {
            // submit
            var data = new FormData($('#imageAccForm')[0]);
            $.ajax({
                type: 'post',
                url: 'userEditAccount',
                dataType: 'json',
                data: data,
                contentType: false,
                processData: false,
                success: function (res) {
                    if (!Array.isArray(res)) {
                        $('.userImageErrorRes').html(res);
                    } else {
                        $('.userImageErrorRes').html(res[0]);
                        setTimeout(function () {
                            $('#imageAccForm').trigger('reset');
                            $('#preview').css('display', 'none');
                            $('#userImage').attr('src', res[1]);
                            $('.userImageErrorRes').empty();
                            $('.preview').empty();
                        }, 3000);
                    }
                },
                beforeSend: function () {
                    $('.userImageErrorRes').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.userImageErrorRes').html(textStatus + ' ' + jqXHR.status + ' ' + errorThrown);
                }
            });
        }

    });
    // ------------------------------------------------------- //
    // Delete Account
    // ------------------------------------------------------ //
    $('#submitDeleteAcc').click(function () {
        $.ajax({
            url: 'userDeleteAccount',
            dataType: 'json',
            success: function (res) {
                if (!Array.isArray(res)) {
                    $('.userDeleteAcc').html(res);
                } else {
                    $('.userDeleteAcc').html(res[0]);
                    setTimeout(function () {
                        location.href = 'close';
                    }, 3000);
                }
            },
            beforeSend: function () {
                $('.userDeleteAcc').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('.userDeleteAcc').html(textStatus + ' ' + jqXHR.status + ' ' + errorThrown);
            }
        });

    });

});
