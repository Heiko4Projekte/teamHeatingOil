$(document).ready(function () {

    var cboxOptions = {
        width: '90%',
        maxWidth: 1100,
    };

    var spinnerStartTime,
        spinnerMinTime = 1750;

    /**
     *
     */
    var init = function () {


        var $modalLinks = $('.modal a');
        var $error = $('.modalError');


        $modalLinks.on('click', function (e) {
            e.preventDefault();
            var url = this.href;
            callAjax(url);
        });


        // generated Error Object in mod_review Template
        if ($error.length > 0) {
            fillModalContent(JSON.stringify(ErrorObject));
            var $warning = $('.modal a');
            $warning.on('click', function (e) {
                e.preventDefault();
                var url = this.href;
                callAjax(url);
            });
        }

        initResizeListener();
    };


    var initResizeListener = function() {
        $(window).resize(function() {
            $.colorbox.resize({
                width: window.innerWidth > cboxOptions.maxWidth ? cboxOptions.maxWidth : cboxOptions.width
            });
        });
    };


    /**
     *
     */
    var handleModalboxForm = function () {

        $.fn.matchHeight._apply($('[data-mh]'));

        var $form = $('#colorbox form');
        var toggleCheckbox = $form.find('input[name="shippingAddressEqualsBillingAddress"].checkbox');

        if (toggleCheckbox.length) {
            BillingToggler.init();
        }

        if ($form.length > 0) {
            var url = $form[0].action;
            $form.submit(function (e) {
                e.preventDefault();
                var formdata;
                formdata = $form.serialize();
                callAjax(url, formdata);
            })
        }

    };


    /**
     *
     * @param url
     */
    var callAjax = function (url, formdata) {

        $.ajax({
            url: url,
            data: formdata,
            processData: false,
            type: 'POST',
            beforeSend: function (xhr) {
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
            }
        })
            .done(function (data) {
                fillModalContent(data);
            });
    }

    /**
     *
     * url
     */
    var ajaxReload = function () {
        showSpinner();

        $.ajax({
            url: window.location.href,
            beforeSend: function (xhr) {
                // xhr.overrideMimeType("text/plain; charset=x-user-defined");
            }
        })
        .done(function (data) {
            $('#container').html($(data).find('#main'));

            init();
            hideSpinner();
        });
    }

    /**
     *
     * @param data
     */
    var fillModalContent = function (data) {

        var obj = jQuery.parseJSON(data);

        if (obj.modal) {
            var $modalboxContent = $('#cboxLoadedContent');

            if ($modalboxContent.is(":visible")) {
                $($modalboxContent).html(obj.content);
                $.colorbox.resize();
            } else {
                $.colorbox({
                    html: obj.content,
                    escKey: false,
                    overlayClose: false,
                    transition: 'elastic',
                    speed: 200,
                    width: cboxOptions.width,
                    maxWidth: cboxOptions.maxWidth,
                    close: '<span class="label">Schließen</span> <svg class="svg-icon"><use xlink:href="files/theme/team-ag-rechner/img/icons/icon-sprite.svg#close"></use></svg>',
                    onClosed: function () {
                        ajaxReload();
                    },
                });
            }

            handleModalboxForm();
        }
        else {
            $.colorbox.close();
        }
    };

    var showSpinner = function() {
        spinnerStartTime = Date.now();
        $('body').addClass('show-loading-spinner');
        $('.loading-spinner').fadeIn(250);
    };

    var hideSpinner = function() {
        var now = Date.now();
        var delay = now - spinnerStartTime;

        // Make sure that the spinner is hidden after a minimum time frame.
        if (delay < spinnerMinTime) {
            delay = spinnerMinTime - delay;
        } else {
            delay = 0;
        }

        setTimeout(function() {
            $('body').removeClass('show-loading-spinner');
            $('.loading-spinner').fadeOut(250);
        }, delay);
    };


    var BillingToggler = (function () {
        var form,
            toggleCheckbox,
            toggleGroup;

        var init = function () {
            form = $('#colorbox form');
            toggleCheckbox = form.find('input[name="shippingAddressEqualsBillingAddress"].checkbox');
            toggleGroup = $('.form-group-shippingAddress');

            if (toggleCheckbox.length) {
                initCheckboxListener();
            }

            handleCheckboxToggle();
        };

        var initCheckboxListener = function () {
            toggleCheckbox.on('change', function () {
                handleCheckboxToggle();
            });
        };

        var handleCheckboxToggle = function () {
            if (toggleCheckbox.prop('checked')) {
                hideToggleGroup();
            } else {
                showToggleGroup();
            }
        };

        var showToggleGroup = function () {
            toggleGroup.show();
        };

        var hideToggleGroup = function () {
            toggleGroup.hide();
        };

        return {
            init: init
        };

    })();


    init();

});