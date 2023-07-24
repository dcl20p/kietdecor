(function ($) {
    var swiper = new Swiper("#about-swiper", {
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