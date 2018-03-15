(function ($) {

    $.payoneDirectDebit = $.payoneDirectDebit || {};
    $.payoneDirectDebit.iframe = null;
    $.payoneDirectDebit.setCheckoutDisabled = function (isDisabled) {
        $('#orderPlace').prop('disabled', isDisabled);
    };

    /**
     * @param form
     */
    $.payoneDirectDebit.storeAccountData = function (form) {
        return $.ajax({
            type: 'POST',
            url: '/payment/payone/checkout/storeAccountData',
            data: form.serialize(),
            dataType: 'json',
            async: true
        })
            .done(function (data) {
                var errorClasses = 'has-error error has-feedback';
                form.find('input, select').parent().removeClass(errorClasses);
            }).fail(function (data) {
                var data = data.responseJSON;
                if (data.errors && data.errors.message) {
                    $.payonePayment.showErrorMessage(data.errors.message);
                }
                console.log(data);
                }
            );

    };

    $.payoneDirectDebit.showSepaMandate = function () {
        return $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/payment/payone/checkout/getSepaMandateStep'
        })
            .done(function (data) {
                $(data.data.html).insertAfter('#createSepamandate');
                $('#sepaMandateConfirmation').show();

            }).fail(function (data) {
                    var data = data.responseJSON;
                    if (data.errors && data.errors.message) {
                        $.payonePayment.showErrorMessage(data.errors.message);
                    }
                    console.log(data);
                }
            );
    };

    $.payoneDirectDebit.hideAccountForm = function () {
        $('#createSepamandate').hide();
    };

    window.cancelPayone = function() {
        console.log('test1');
        $('button.btn.btn-success.btn-block').prop('disabled', false);
        $('button.btn.btn-success.btn-block i').addClass('fa-arrow-right').removeClass('fa-circle-o-notch fa-spin');
    };

    window.sepaForm = function(event) {
        console.log('test');
        console.log('submit button clicked');
        event.preventDefault();

        $('#sepaContinue').prop('disabled', true);

        var form = $('#createSepamandateForm');
        console.log('storing account data');

        $.when($.payoneDirectDebit.storeAccountData(form)).done(function () {
            console.log('submitting orderPlaceForm');

            $.payoneDirectDebit.hideAccountForm();
            $.payoneDirectDebit.showSepaMandate(form);

        }).fail(function (data, textStatus, jqXHR) {
            return false;
        });
        return false;
    }
}(window.jQuery, window, document));