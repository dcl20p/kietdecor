(function(){
    /** ================Init select choices================*/
    let choices = document.querySelectorAll('.choices');
    choices && choices.forEach((el) => {
        new Choices(el, {
            shouldSort: false,
            searchEnabled: false,
        });
    });
})()