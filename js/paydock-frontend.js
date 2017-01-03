(function($) {
    $(function() {
        $(document).on('click', '.gravity-forms-paydock-tabs-head a', function(e) {
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

  // helper function to parse out the query string params
  function gf_paydock_gup(url, name) {
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?#&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( url );
    if( results == null )
      return "";
    else
      return results[1];
  }



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