(function(){

    const elDes      = document.getElementById('description'),
        choices      = document.querySelectorAll('.choices'),
        elMetaDes    = document.getElementById('meta_desc'),
        metaKeyword  = document.getElementById('meta_keyword'),
        elImage      = document.getElementById('jsonImage'),
        name         = document.querySelector('#name'),
        location     = document.querySelector('#location'),
        projectCate  = document.querySelector('#project_cate'),
        service      = document.querySelector('#service'),
        assignee     = document.querySelector('#assignee'),
        btnNextStep2 = document.querySelector('#btnNextStep2');    

    const checkValidForm = () => {
        const fieldRequired = [name, projectCate, service];
        const fieldLength = [
            [name, 1, 512],
            [location, 1, 255],
            [assignee, 1, 50],
        ];
        if (!common.checkRequired(fieldRequired)
            || !common.checkLength(fieldLength)
        ) return false;

        return true;
    };

    const handleNextStep2 = (evt) => {
        evt.preventDefault();
        if (!checkValidForm()) {
            evt.stopPropagation();
        }
    };

    choices && choices.forEach((el) => {
        new Choices(el, {
            shouldSort: false,
            searchEnabled: false,
        });
    });

    const quillDes    = elDes && common.initQuill(elDes);
    const metaDes     = elMetaDes && common.initQuill(elMetaDes);
    const dropzoneImg = elImage && common.initDropzone(elImage);
    btnNextStep2 && btnNextStep2.addEventListener('click', handleNextStep2);
    metaKeyword && common.initChoicesTag(metaKeyword);
 
})()