
if (window.addEventListener) {
    window.addEventListener("message", paydocklistener);
} else {
    // IE8
    window.attachEvent("onmessage", paydocklistener);
}

function paydocklistener(event) {

	var PaydockEvent = JSON.parse(event.data);
	if(PaydockEvent.event == 'finish' && PaydockEvent.purpose == 'payment_source'){
		if(PaydockEvent.ref_id != ''){
			jQuery('#paydock_ref_id').val(PaydockEvent.ref_id);
			jQuery('#paydock_ref_id').closest('form').submit();
		}

	}


}