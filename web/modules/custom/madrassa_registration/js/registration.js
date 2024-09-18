(function ($, Drupal) {
    Drupal.behaviors.madrassa_registration = {
        attach: function (context, settings) {
            
$(document).ready(function() {
    $('.next').click(function() {
        var payment_method = $('#payment_method').val();
        var amount = $('#amount').val();
        var payment_date = $('#payment_date').val();
        var payment_receipt = $('#payment_receipt').val();
        var payment_note = $('#payment_note').val();

        if (payment_method == '') {
            alert('Veuillez selectionner un moyen de paiement');
            return false;
        } else if (payment_date == '') {
            alert('Veuillez renseigner la date de paiement');
            return false;
        } else {
            return true;
        }
    });
    var payment_method = $('#payment_method').val();
    if (payment_method == 0 || payment_method == '') {
        $('#payment_by_credit_card').hide();
        $('#payment_by_bank_transfert').hide();
        $('#payment_by_check').hide();
    }
    $('#payment_method').change(function() {
        var payment_method = $('#payment_method').val();
        if (payment_method == 'carte') {
            // $('#payment_by_credit_card').show();
            $('#modal-carte-payment').modal('show');
            $('#payment_by_bank_transfert').hide();
            $('#payment_by_check').hide();
        } else if (payment_method == 'virement') {
            $('#payment_by_bank_transfert').show();
            $('#payment_by_credit_card').hide();
            $('#payment_by_check').hide();
        } else if (payment_method == 'cheque') {
            $('#payment_by_check').show();
            $('#payment_by_credit_card').hide();
            $('#payment_by_bank_transfert').hide();
        } else {
            $('#payment_by_credit_card').hide();
            $('#payment_by_bank_transfert').hide();
            $('#payment_by_check').hide();
        }
    });
});
        }
    };
}(jQuery, Drupal));