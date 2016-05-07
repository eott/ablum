$.extend({
    advanceSliderToActiveElement: function() {
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
    },

    updateMainPictureForThumbnail: function(element) {
        var filename = element.attr('src');
        filename = filename.replace('thumbnails/', 'main/');
        $('#mainPicture').attr('src', filename);
    }
});

$(document).ready(function () {
    $('.thumbnail').on('click', function() {
        $.updateMainPictureForThumbnail($(this));

        $('.thumbnail').removeClass('active');
        $(this).addClass('active');

        $.advanceSliderToActiveElement();
    });

    $('.navPrevious.thumbs, .navPrevious.main').on('click', function (e) {
        $('#thumbsSlider li:last-child').prependTo($('#thumbsSlider ul'));
    });

    $('.navNext.thumbs, .navNext.main').on('click', function (e) {
        $('#thumbsSlider li:first-child').appendTo($('#thumbsSlider ul'));
    });

    $('.navPrevious.main').on('click', function (e) {
        $.advanceSliderToActiveElement();
        $('#thumbsSlider li:last-child').prependTo($('#thumbsSlider ul'));
        $('.thumbnail').removeClass('active');
        $('#thumbsSlider li:first-child img').addClass('active');
        $.updateMainPictureForThumbnail($('.thumbnail.active'));
    });

    $('.navNext.main').on('click', function (e) {
        $.advanceSliderToActiveElement();
        $('#thumbsSlider li:first-child').appendTo($('#thumbsSlider ul'));
        $('.thumbnail').removeClass('active');
        $('#thumbsSlider li:first-child img').addClass('active');
        $.updateMainPictureForThumbnail($('.thumbnail.active'));
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