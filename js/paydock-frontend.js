(function($) {
    $(function() {
        $("body").on("click", ".gravity-forms-paydock-tabs-head a", function(e) {
        	e.preventDefault();
            var tab_id = $(this).attr('class')
            $('.gravity-forms-paydock-tabs-head a').removeClass('current');
            $(this).addClass('current');

            $('.tabs_container').hide('fast', function() {
                $('#' + tab_id).show('fast');
            });
        })
    })
})(jQuery)


if (window.addEventListener) {
    window.addEventListener("message", paydocklistener);
} else {
    // IE8
    window.attachEvent("onmessage", paydocklistener);
}

function paydocklistener(event) {
    var PaydockEvent = JSON.parse(event.data);
    if (PaydockEvent.event == 'finish' && PaydockEvent.purpose == 'payment_source') {
        if (PaydockEvent.ref_id != '') {
            jQuery('#paydock_ref_id').val(PaydockEvent.ref_id);
            jQuery('#paydock_ref_id').closest('form').submit();
        }
    }
}