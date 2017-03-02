jQuery(document).ready(function($) {
    $('.paydock-color-field').wpColorPicker({
        change: function(event, ui) {
            var color = ui.color.toString();
            SetFieldProperty(event.target.id, color);
        },
        clear: function() {
            SetFieldProperty(event.target.id, '');
        },
    });
    if (jQuery('#transaction_end').val() != '') {
        jQuery('#transaction_end').change();
    }
});

function showEndTransactionValueField(ele) {
    var label = jQuery('#' + ele.id + ' option:selected').text();
    var val = jQuery(ele).val();
    var loaded = jQuery('#gaddon-setting-row-transaction_end_value').hasClass('loaded')
    if (val != '') {
        jQuery('#gaddon-setting-row-transaction_end_value th').text(label)
        jQuery('#gaddon-setting-row-transaction_end_value').slideDown('slow').addClass('loaded');
    } else {
        jQuery('#gaddon-setting-row-transaction_end_value').slideUp('slow').addClass('loaded');
    }
    if (loaded) {
        jQuery('#transaction_end_value').val('')
    }
}