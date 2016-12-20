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
});