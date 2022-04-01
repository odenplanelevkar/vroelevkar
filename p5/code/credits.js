const tl = new TimelineMax();

$(document).ready(function () {

    $('html, body').scrollTop = 0

    $('html, body').animate({
        scrollTop: 0
    }, 1);

    $('html, body').animate({
        scrollTop: $('.creators').get(0).scrollHeight
    }, 70000, 'linear');
});
