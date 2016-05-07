$(document).ready(function () {
    $('.thumbnail').on('click', function() {
        var filename = $(this).attr('src');
        filename = filename.replace('thumbnails/', '');
        $('#mainPicture').attr('src', filename);
    });

    $('#mainPicture').on('click', function() {
        if ($(this).hasClass('fullsize')) {
            $(this).removeClass('fullsize');
            $('#pictureFrame').removeClass('fullsize');
        } else {
            $(this).addClass('fullsize');
            $('#pictureFrame').addClass('fullsize');
        }
    });
});