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
        var success = false;

        $.ajax({
            type: 'POST',
            url: '/payone/checkout/storeAccountData',
            data: form.serialize(),
            dataType: 'json',
            async: false
        })
            .done(function (data) {
                var errorClasses = 'has-error error has-feedback';
                form.find('input, select').parent().removeClass(errorClasses);
                success = true;
                if (!data.success) {
                    $.payonePayment.showValidationErrors(form, data.errors, errorClasses);
                    if (data.errors.message) {
                        $.payonePayment.showErrorMessage(data.errors.message);
                    }
                    form.unbind('submit');
                    console.log(data);
                    success = false;
                }
            });

        return success;
    };

    $.payoneDirectDebit.showSepaMandate = function () {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/payone/checkout/getSepaMandateStep'
        })
            .done(function (data) {
                if (!data.success) {
                    if (data.errors.message) {
                        $.payonePayment.showErrorMessage(data.errors.message);
                    }
                    console.log(data);
                }
                $(data.data.html).insertAfter('#createSepamandate');
                $('#sepaMandateConfirmation').show();
            })
            .fail(function (data) {
                console.log(data);
            });
    };

    $.payoneDirectDebit.hideAccountForm = function () {
        $('#createSepamandate').hide();
    };

    $(function () {

        $('#createSepamandateForm').on("submit", function (event) {
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
                $.payonePayment.setCheckoutDisabled(false);
                return false;
            });
        });

        $(document).on('click', 'button.payone-cancel', function () {
            $('button.btn.btn-primary.btn-block').prop('disabled', false);
        });
    });
}(window.jQuery, window, document));