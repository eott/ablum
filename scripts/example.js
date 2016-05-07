$(document).ready(function () {
    $('.thumbnail').on('click', function() {
        var filename = $(this).attr('src');
        filename = filename.replace('thumbnails/', '');
        $('#mainPicture').attr('src', filename);

        $('.thumbnail').removeClass('active');
        $(this).addClass('active');

        var parent = $(this).parent;
        var isFirst = false;
        var safeGuard = 100;
        while (!isFirst && safeGuard-- > 0) {
            if ($('#thumbsSlider li:first-child img').hasClass('active')) {
                isFirst = true;
            }
            if (!isFirst) {
                $('#thumbsSlider li:first-child').appendTo($('#thumbsSlider ul'));
            }
        }
    });

    $('.navPrevious.thumbs, .navPrevious.main').on('click', function (e) {
        $('#thumbsSlider li:last-child').prependTo($('#thumbsSlider ul'));
    });
    $('.navNext.thumbs, .navNext.main').on('click', function (e) {
        $('#thumbsSlider li:first-child').appendTo($('#thumbsSlider ul'));
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