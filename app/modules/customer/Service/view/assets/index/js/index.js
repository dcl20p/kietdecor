(function ($) {
    var swiper = new Swiper("#service-swiper", {
        slidesPerView: 1,
        spaceBetween: 10,
        breakpoints: {
            480: {
              slidesPerView: 2,
              spaceBetween: 10
            },
            1024: {
              slidesPerView: 4,
              spaceBetween: 10
            },

        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
})(jQuery);