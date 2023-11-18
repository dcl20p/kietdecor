(function($) {
	$.common = {
        callApi: (url, data, method = 'GET', callbacks = null, isForm = false, elReplace = null, dataType = 'json') => {
            let obj = {
                type: method,
                url: url,
                dataType: dataType,
                data: data
            }
            if (isForm) {
                obj = {...obj, ...{processData: false,contentType: false}};
            }
    
            $.ajax(obj)
            .done(function (rs, status) {
                if (typeof callbacks == 'function') {
                    callbacks(rs, status);
                } else {
                    if (elReplace !== null) elReplace.html(rs);
                }
            })
            .fail(function (rs) {
                if (rs && rs.responseText){
                    try {
                        let json = JSON.parse(rs.responseText);
                        console.log(json);
                    } catch (e) {
                    }
                }
            });
        }
	}
})(jQuery);