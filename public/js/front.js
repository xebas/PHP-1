$(document).ready(function () {
    // ------------------------------------------------------- //
    // GENERAL FUNCTIONS
    // ------------------------------------------------------ //
    // ---------------------------------------------------------------- //
    // Ajax Redirect (load views by Ajax)
    // Add to links to redirect -> class: 'link' & id: 'route'
    // ---------------------------------------------------------------- //
    $('.link').click(function (event) {

        var url = $(event.target).attr('id');
        var path = document.location.pathname;
        var directory = path.substring(path.indexOf('/'), path.lastIndexOf('/'));

        $.ajax({
            url: url,
            success: function (resView) {
                $('body').html(resView);
                window.history.pushState('', '', directory + '/' + url);
                document.title = url;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('body').html(textStatus + ' ' + jqXHR.status + ' ' + errorThrown);
            }
        });

    });
    // -------------------------------------------------------------------- //
    // Polygonizr: Library to draw vectors in registration and login forms
    // -------------------------------------------------------------------- //
    $('.formLeft').polygonizr({
        canvasHeight: $(this).height()
    });

    // ------------------------------------------------------- //
    // Tooltips init
    // ------------------------------------------------------ //
    $('[data-toggle="tooltip"]').tooltip();

    // ---------------------------------------------------------- //
    // Material Inputs: Effects on registration and login forms
    // ---------------------------------------------------------- //
    var materialInputs = $('input.input-material');
    // activate labels for prefilled values
    materialInputs.filter(function () {
        return $(this).val() !== "";
    }).siblings('.label-material').addClass('active');
    // move label on focus
    materialInputs.on('focus', function () {
        $(this).siblings('.label-material').addClass('active');
    });
    // remove/keep label on blur
    materialInputs.on('blur', function () {
        $(this).siblings('.label-material').removeClass('active');
        if ($(this).val() !== '') {
            $(this).siblings('.label-material').addClass('active');
        } else {
            $(this).siblings('.label-material').removeClass('active');
        }
    });

    // ------------------------------------------------------- //
    // Images load in inputs type file
    // ------------------------------------------------------ //
    bsCustomFileInput.init(); // input plugin 'bsCustomFile'
    // get file and preview image
    $('#inputGroupFile0, #inputGroupFile1, #inputGroupFile2').on('change', function () {
        var input = $(this)[0];
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#preview0, #preview1, #preview2').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    });
    // image size upload validate
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, 'El archivo pesa más de 5Mb');

    // ------------------------------------------------------- //
    // Reset forms - Close BS modals
    // ------------------------------------------------------ //
    $('.modal').on('hidden.bs.modal', function () {
        $('#editProjectForm, #addProjectForm').trigger('reset');
        $('.preview img').attr('src', '');
        $('.response').html('');
    });

    // ------------------------------------------------------- //
    // Footer
    // ------------------------------------------------------ //
    var pageContent = $('.page-content');
    $(document).on('sidebarChanged', function () {
        adjustFooter();
    });
    $(window).on('resize', function () {
        adjustFooter();
    })

    function adjustFooter() {
        var footerBlockHeight = $('.footer__block').outerHeight();
        pageContent.css('padding-bottom', footerBlockHeight + 'px');
    }

    // ------------------------------------------------------- //
    // Adding fade effect to dropdowns
    // ------------------------------------------------------ //
    $('.dropdown').on('show.bs.dropdown', function () {
        $(this).find('.dropdown-menu').first().stop(true, true).fadeIn(100).addClass('active');
    });
    $('.dropdown').on('hide.bs.dropdown', function () {
        $(this).find('.dropdown-menu').first().stop(true, true).fadeOut(100).removeClass('active');
    });

    // ------------------------------------------------------- //
    // Search Popup
    // ------------------------------------------------------ //
    $('.search-open').on('click', function (e) {
        e.preventDefault();
        $('.search-panel').fadeIn(100);
    })
    $('.search-panel .close-btn').on('click', function () {
        $('.search-panel').fadeOut(100);
    });

    // ------------------------------------------------------- //
    // Sidebar Functionality
    // ------------------------------------------------------ //
    $('.sidebar-toggle').on('click', function () {
        $(this).toggleClass('active');

        $('#sidebar').toggleClass('shrinked');
        $('.page-content').toggleClass('active');
        $(document).trigger('sidebarChanged');

        if ($('.sidebar-toggle').hasClass('active')) {
            $('.navbar-brand .brand-sm').addClass('visible');
            $('.navbar-brand .brand-big').removeClass('visible');
            $(this).find('i').attr('class', 'fa fa-long-arrow-right');
        } else {
            $('.navbar-brand .brand-sm').removeClass('visible');
            $('.navbar-brand .brand-big').addClass('visible');
            $(this).find('i').attr('class', 'fa fa-long-arrow-left');
        }
    });

});
