(function(){

    const elDes      = document.getElementById('description'),
        choices      = document.querySelectorAll('.choices'),
        elMetaDes    = document.getElementById('meta_desc'),
        elImage      = document.getElementById('jsonImage');

    const quillDes    = elDes && common.initQuill(elDes);
    const metaDes     = elMetaDes && common.initQuill(elMetaDes);
    const dropzoneImg = elImage && common.initDropzone(elImage);
    choices && choices.forEach((el) => {
        new Choices(el, {
            shouldSort: false,
            searchEnabled: false,
        });
    });
})()