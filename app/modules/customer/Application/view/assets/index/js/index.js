(function ($) {
    "use strict";
    var owl = $('.featured-carousel');

    $('.featured-carousel').owlCarousel({
        animateOut: 'fadeOut',
        center: false,
        items: 1,
        loop: true,
        stagePadding: 0,
        margin: 0,
        smartSpeed: 1500,
        autoplay: true,
        dots: false,
        nav: false,
    });

    $('.thumbnail li').each(function (slide_index) {
        $(this).click(function (e) {
            owl.trigger('to.owl.carousel', [slide_index, 1500]);
            e.preventDefault();
        })
    })

    owl.on('changed.owl.carousel', function (event) {
        $('.thumbnail li').removeClass('active');
        $('.thumbnail li').eq(event.item.index - 2).addClass('active');
    });

    var swiper = new Swiper("#teams-swiper", {
        slidesPerView: 1,
        spaceBetween: 30,
        breakpoints: {
            480: {
              slidesPerView: 2,
              spaceBetween: 30
            },
            1024: {
              slidesPerView: 4,
              spaceBetween: 30
            },

        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

})(jQuery);