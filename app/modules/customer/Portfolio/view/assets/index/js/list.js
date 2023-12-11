(function ($) {
    $(document)
    .on('click tab', '#load_more', function() {
        const self = this;
        let page = this.getAttribute('data-page')||1;
        let limit = this.getAttribute('data-limit')||30;

        $.common.callApi(
            '__url_load_more__', {page:page, limit:limit}, 
            'GET', (rs, status) => {
                if (rs.success) {
                    $('#wrapper-items').append(rs.html);
                    lazyLoadInstance.init();
                    if (rs.total == limit) {
                        self.setAttribute('data-page', parseInt(page) + 1);
                    } else self.remove();
                }
            } 
        );
    });
})(jQuery);