$(document).ready(function () {
    // -------------------------------------------------------------------- //
    // Semantic UI: Library component (search) for projects search
    // -------------------------------------------------------------------- //
    var projectsJSON = $('#projectsJSON').data('projects');

    $('.ui.search').search({
        source: projectsJSON,
        searchFields: ['title'],
        fullTextSearch: false,
        error: {
            noResults: 'No se ha encontrado ningún proyecto...'
        }
    });

    $('.result').each(function () {
        alert();

    });

    // -------------------------------------------------------------------- //
    // Masonry: Library for adapt images
    // -------------------------------------------------------------------- //
    var container_7_1 = document.querySelector('#psgal_7_1');
    var msnry;
    // initialize  after all images have loaded
    imagesLoaded(container_7_1, function () {
        // initialize Masonry after all images have loaded
        new Masonry(container_7_1, {
            // options...
            itemSelector: ".msnry_item"
            // percentPosition: true,
            // isFitWidth: true
        });
        container_7_1.className += " photoswipe_showme";
    });
    var totalWidth = 0;
    var totalHeight = 0;
    $(".my-gallery figure img").each(function (index) {
        totalWidth = $(this).clientWidth;
        totalWidth = $(this).clientHeight;
    });
    // -------------------------------------------------------------------- //
    // PhotoSwipe: Library for image gallery
    // -------------------------------------------------------------------- //
    var initPhotoSwipeFromDOM = function (gallerySelector) {
        // parse slide data (url, title, size ...) from DOM elements
        // (children of gallerySelector)
        var parseThumbnailElements = function (el) {
            var thumbElements = el.childNodes,
                numNodes = thumbElements.length,
                items = [],
                figureEl,
                linkEl,
                size,
                item;

            for (var i = 0; i < numNodes; i++) {
                figureEl = thumbElements[i]; // <figure> element
                // include only element nodes
                if (figureEl.nodeType !== 1) {
                    continue;
                }
                linkEl = figureEl.children[0]; // <a> element
                size = linkEl.getAttribute('data-size').split('x');
                // create slide object
                item = {
                    src: linkEl.getAttribute('href'),
                    w: parseInt(size[0], 10),
                    h: parseInt(size[1], 10)
                };
                if (figureEl.children.length > 1) {
                    // <figcaption> content
                    item.title = figureEl.children[1].innerHTML;
                }
                if (linkEl.children.length > 0) {
                    // <img> thumbnail element, retrieving thumbnail url
                    item.msrc = linkEl.children[0].getAttribute('src');
                }
                item.el = figureEl; // save link to element for getThumbBoundsFn
                items.push(item);
            }
            return items;
        };
        // find nearest parent element
        var closest = function closest(el, fn) {
            return el && (fn(el) ? el : closest(el.parentNode, fn));
        };
        // triggers when user clicks on thumbnail
        var onThumbnailsClick = function (e) {
            e = e || window.event;
            e.preventDefault ? e.preventDefault() : e.returnValue = false;
            var eTarget = e.target || e.srcElement;
            // find root element of slide
            var clickedListItem = closest(eTarget, function (el) {
                return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
            });
            if (!clickedListItem) {
                return;
            }
            // find index of clicked item by looping through all child nodes
            // alternatively, you may define index via data- attribute
            var clickedGallery = clickedListItem.parentNode,
                childNodes = clickedListItem.parentNode.childNodes,
                numChildNodes = childNodes.length,
                nodeIndex = 0,
                index;

            for (var i = 0; i < numChildNodes; i++) {
                if (childNodes[i].nodeType !== 1) {
                    continue;
                }
                if (childNodes[i] === clickedListItem) {
                    index = nodeIndex;
                    break;
                }
                nodeIndex++;
            }
            if (index >= 0) {
                // open PhotoSwipe if valid index found
                openPhotoSwipe(index, clickedGallery);
            }
            return false;
        };
        // parse picture index and gallery index from URL (#&pid=1&gid=2)
        var photoswipeParseHash = function () {
            var hash = window.location.hash.substring(1),
                params = {};
            if (hash.length < 5) {
                return params;
            }
            var vars = hash.split('&');
            for (var i = 0; i < vars.length; i++) {
                if (!vars[i]) {
                    continue;
                }
                var pair = vars[i].split('=');
                if (pair.length < 2) {
                    continue;
                }
                params[pair[0]] = pair[1];
            }
            if (params.gid) {
                params.gid = parseInt(params.gid, 10);
            }
            return params;
        };
        var openPhotoSwipe = function (index, galleryElement, disableAnimation, fromURL) {
            var pswpElement = document.querySelectorAll('.pswp')[0],
                gallery,
                options,
                items;
            items = parseThumbnailElements(galleryElement);
            // define options (if needed)
            options = {
                // define gallery index (for URL)
                galleryUID: galleryElement.getAttribute('data-pswp-uid'),
                getThumbBoundsFn: function (index) {
                    // See Options -> getThumbBoundsFn section of documentation for more info
                    var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
                        pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                        rect = thumbnail.getBoundingClientRect();
                    return {
                        x: rect.left,
                        y: rect.top + pageYScroll,
                        w: rect.width
                    };
                }
            };

            // PhotoSwipe opened from URL
            if (fromURL) {
                if (options.galleryPIDs) {
                    // parse real index when custom PIDs are used
                    // http://photoswipe.com/documentation/faq.html#custom-pid-in-url
                    for (var j = 0; j < items.length; j++) {
                        if (items[j].pid == index) {
                            options.index = j;
                            break;
                        }
                    }
                } else {
                    // in URL indexes start from 1
                    options.index = parseInt(index, 10) - 1;
                }
            } else {
                options.index = parseInt(index, 10);
            }
            // exit if index not found
            if (isNaN(options.index)) {
                return;
            }
            if (disableAnimation) {
                options.showAnimationDuration = 0;
            }
            // Pass data to PhotoSwipe and initialize it
            gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
            gallery.init();

        };
        // loop through all gallery elements and bind events
        var galleryElements = document.querySelectorAll(gallerySelector);
        for (var i = 0, l = galleryElements.length; i < l; i++) {
            galleryElements[i].setAttribute('data-pswp-uid', i + 1);
            galleryElements[i].onclick = onThumbnailsClick;
        }
        // Parse URL and open gallery if it contains #&pid=3&gid=1
        var hashData = photoswipeParseHash();
        if (hashData.pid && hashData.gid) {
            openPhotoSwipe(hashData.pid, galleryElements[hashData.gid - 1], true, true);
        }
    };
    // execute above function
    initPhotoSwipeFromDOM('.my-gallery');

    // ------------------------------------------------------- //
    // ADD PROJECT & Validation
    // ------------------------------------------------------ //
    var addProjectValidate = $('#addProjectForm').validate({
        rules: {
            titleProject: {
                required: true,
                maxlength: 20
            },
            descProject: {
                required: true,
                maxlength: 120
            },
            imageProject: {
                required: true,
                extension: 'jpg,jpeg,png',
                filesize: 5000000
            }
        },
        messages: {
            titleProject: {
                required: 'Por favor, añade un título de proyecto',
                maxlength: 'El título de proyecto sólo puede contener 20 caracteres'
            },
            descProject: {
                required: 'Por favor, añade una descripción de proyecto',
                maxlength: 'La descripción de proyecto sólo puede contener 120 caracteres'
            },
            imageProject: {
                required: 'Añade una imagen',
                extension: 'Sólo se admiten archivos con de este tipo: (jpg, jpeg, png)'
            }
        },
        submitHandler: function () {
            // submit
            var data = new FormData($('#addProjectForm')[0]);
            $.ajax({
                type: 'post',
                url: 'addProject',
                dataType: 'json',
                data: data,
                contentType: false,
                processData: false,
                success: function (res) {
                    if (!Array.isArray(res)) {
                        $('.addProjectRes').html(res);
                    } else {
                        $('.addProjectRes').html(res[0]);
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    }
                },
                beforeSend: function () {
                    $('.addProjectRes').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
                },
                error: function () {
                    $('.addProjectRes').html('Error 500. Error en servidor');
                }
            });
        }
    });
    // ------------------------------------------------------- //
    // EDIT PROJECT & Validation
    // ------------------------------------------------------ //
    var titleProject = ''; //global
    var descProject = ''; //global
    // pass title & description & id to modal & open
    $(document).on('click', '.edit-project', function () {
        titleProject = $(this).data('title');
        descProject = $(this).data('desc');
        var idProject = $(this).data('id');
        $('.etitleProject').val(titleProject);
        $('.eDescProject').val(descProject);
        $('.idProject').val(idProject);
        $('#editProjectModal').modal('show');
    });

    var editProjectValidate = $('#editProjectForm').validate({
        rules: {
            etitleProject: {
                required: true,
                maxlength: 20
            },
            eDescProject: {
                required: true,
                maxlength: 120
            },
        },
        messages: {
            etitleProject: {
                required: 'Por favor, añade un título de proyecto',
                maxlength: 'El título de proyecto sólo puede contener 20 caracteres'
            },
            eDescProject: {
                required: 'Por favor, añade una descripción de proyecto',
                maxlength: 'La descripción de proyecto sólo puede contener 120 caracteres'
            },
        },
        submitHandler: function () {
            // detect changes
            if ($('#etitleProject').val() === titleProject && $('#eDescProject').val() === descProject && $('#inputGroupFile2').val() === '') {
                $('.editProjectRes').html('No ha habido cambios en el formulario');
            } else {

                var data = new FormData($('#editProjectForm')[0]);
                $.ajax({
                    type: 'post',
                    url: 'userEditProject',
                    dataType: 'json',
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        if (!Array.isArray(res)) {
                            $('.editProjectRes').html(res);
                        } else {
                            $('.editProjectRes').html(res[0]);
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        }
                    },
                    beforeSend: function () {
                        $('.editProjectRes').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('.editProjectRes').html(textStatus + ' ' + jqXHR.status + ' ' + errorThrown);
                    }
                });

            }

        }

    });
    // ------------------------------------------------------- //
    // DELETE PROJECT
    // ------------------------------------------------------ //
    // pass title to modal & open
    $(document).on('click', '.delete-project', function () {
        var titleProject = $(this).data('title');
        $('.modal-body #dtitleProject').html(titleProject);
        $('#deleteProjectModal').modal('show');
    });
    // delete modal
    $('#submitDeleteProject').click(function () {
        var title = $('#dtitleProject').text();
        $.ajax({
            type: 'post',
            url: 'deleteProject',
            dataType: 'json',
            data: {
                title: title
            },
            success: function (res) {
                if (!Array.isArray(res)) {
                    $('.delProjectRes').html(res);
                } else {
                    $('.delProjectRes').html(res[0]);
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                }
            },
            beforeSend: function () {
                $('.delProjectRes').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('.delProjectRes').html(textStatus + ' ' + jqXHR.status + ' ' + errorThrown);
            }
        });

    });

});
