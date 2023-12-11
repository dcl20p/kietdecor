const lazyLoadInstance = (() => {
    const options = {
        classLazy: '.lazyload',
        threshold: 0.5, // Chỉ khi 50% phần tử hiển thị trong viewport, mới gọi callback.
    };

    const handleIntersection = (entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.src = entry.target.dataset.src;
                observer.unobserve(entry.target);
            }
        });
    };

    const initLazyLoad = () => {
        let imageElements = document.querySelectorAll(options.classLazy);
        const observer = new IntersectionObserver(handleIntersection, {
            threshold: options.threshold,
        });

        imageElements.forEach((img) => {
            observer.observe(img);
        });
    };

    const ready = (callback) => {
        if (document.readyState !== 'loading') {
            callback();
        } else if (document.addEventListener) {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            document.attachEvent('onreadystatechange', () => {
                if (document.readyState === 'complete') {
                    callback();
                }
            });
        }
    };

    return {
        init: initLazyLoad,
        ready: ready,
    };
})();

lazyLoadInstance.ready(() => {
    lazyLoadInstance.init();
});

window.addEventListener('scroll', () => {
    lazyLoadInstance.init();
});
