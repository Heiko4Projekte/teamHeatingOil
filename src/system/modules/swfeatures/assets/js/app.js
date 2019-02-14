!(function($) {

    'use strict';

    function viewport() {
        var e = window, a = 'inner';
        if (!('innerWidth' in window )) {
            a = 'client';
            e = document.documentElement || document.body;
        }
        return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
    }

    var ProductListToggler = (function() {
        var module,
            products,
            productHeaders,
            breakpoint = 1024;

        var init = function() {
            module = $('[data-module="product-list"]');
            products = module.find('.product-list-product');
            productHeaders = products.find('.header-information');

            if (module.length) {
                resetProducts();
                initHeaderClickListener();
                initResizeListener();
            }
        };

        var resetProducts = function() {
            deactivateProduct('all');
            activateProduct(products.eq(1));
            setProductHeight();
        };

        var activateProduct = function(product) {
            product.addClass('active');
        };

        var deactivateProduct = function(product) {
            if (product === 'all') {
                products.removeClass('active');
            } else {
                product.removeClass('active');
            }
        };

        var initHeaderClickListener = function() {
            productHeaders.on('click', function() {

                if (viewport().width <= breakpoint) {
                    var product = $(this).parents('.product-list-product');
                    if (!product.hasClass('active')) {
                        deactivateProduct('all');
                        activateProduct(product)

                        setProductHeight();
                    }
                }

            });
        };

        var initResizeListener = function() {
            $(window).on('resize', function() {
                setProductHeight();
            });
        };

        var setProductHeight = function() {

            products.each(function(i, el) {
                if (!$(el).hasClass('active')) {
                    $(el).height('auto');
                } else {
                    var activeProduct = products.filter('.active');
                    var activeHeader = activeProduct.find('.header-information');
                    var activeBody = activeProduct.find('.body-information');

                    activeProduct.height(activeHeader.outerHeight(true) + activeBody.outerHeight(true));
                }
            });
        };

        return {
            init: init
        };

    })();

    var ProductConfigurator = (function() {
        var toggler,
            form,
            state = false;

        var init = function() {
            toggler = $('.mobile-product-configuration-toggler');
            form = $('.hasteform_productconfigurator_form');

            if (toggler.length && form.length) {
                initTogglerListener();
            }
        };

        var initTogglerListener = function() {
            toggler.on('click', function(e) {
                handleClick();
            });
        };

        var handleClick = function() {
            if (!state) {
                showOverlay();
            } else {
                hideOverlay();
            }
        };

        var showOverlay = function() {
            state = true;

            var content = '<div class="inner">' + $('.top-row').html() + '</div>';

            $.colorbox({
                html: content,
                className: 'product-configurator-overlay',
                escKey: false,
                overlayClose: false,
                maxWidth: '90%',
                transition: 'elastic',
                speed: 200,
                width: 1100,
                close: '<svg class="svg-icon label-less"><use xlink:href="files/theme/team-ag-rechner/img/icons/icon-sprite.svg#close"></use></svg>',
                onClosed: function () {
                    state = false
                },
            });
        };

        var hideOverlay = function() {
            state = false;
        };

        return {
            init: init
        };

    })();

    var MobileMenu = (function() {
        var toggler,
            menu;

        var init = function() {
            toggler = $('.main-navigation-toggler');
            menu = $('.main-navigation ul');

            if (toggler.length && menu.length) {
                initClickListener();
            }
        };

        var initClickListener = function() {
            toggler.on('click', function() {
                toggleMenu();
            });
        };

        var toggleMenu = function() {
            if ($('body').hasClass('mobile-menu-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        };

        var openMenu = function() {
            $('body').addClass('mobile-menu-open');
        };

        var closeMenu = function() {
            $('body').removeClass('mobile-menu-open');
        };

        return {
            init: init
        };

    })();

    var StickyErrorBox = (function() {
        var errorBoxes;

        var init = function() {
            errorBoxes = $('.form-error-box');

            if (errorBoxes.length) {
                initStickyErrorBoxes();
            }
        };

        var initStickyErrorBoxes = function() {
            errorBoxes.stick_in_parent();
        };

        return {
            init: init
        };

    })();

    $('document').ready(function() {
        MobileMenu.init();
        ProductListToggler.init();
        ProductConfigurator.init();
        // StickyErrorBox.init();
    });

})(jQuery);