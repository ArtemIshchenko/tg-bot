$(document).on('change', '.custom-control-input', function(e) {
    var url = $(this).data('url');
    document.location.href = url;
});
