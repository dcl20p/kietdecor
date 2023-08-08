(function(){

    const elDes      = document.getElementById('description'),
        choices      = document.querySelectorAll('.choices'),
        elMetaDes    = document.getElementById('meta_desc'),
        metaKeyword  = document.getElementById('meta_keyword'),
        elImage      = document.getElementById('jsonImage'),
        thumbnail    = document.getElementById('thumbnail'),
        name         = document.querySelector('#name'),
        location     = document.querySelector('#location'),
        projectCate  = document.querySelector('#project_cate'),
        service      = document.querySelector('#service'),
        assignee     = document.querySelector('#assignee'),
        btnDefault   = document.querySelectorAll('.btnDefault'),
        btnNextStep2 = document.querySelector('#btnNextStep2');    

    const checkValidForm = () => {
        const fieldRequired = [name, projectCate, service];
        const fieldLength = [
            [name, 1, 512],
            [location, 0, 255],
            [assignee, 0, 50],
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

    btnDefault && btnDefault.forEach((el) => {
        el.addEventListener('click', (e) => {
            e.stopPropagation();
        })
    });

    choices && choices.forEach((el) => {
        new Choices(el, {
            shouldSort: false,
            searchEnabled: true,
        });
    });

    const quillDes          = elDes && common.initQuill(elDes);
    const metaDes           = elMetaDes && common.initQuill(elMetaDes);
    const dropzoneListImg   = elImage && common.initDropzone(elImage);
    const dropzoneThumbnail = elImage && common.initDropzone(thumbnail);
    btnNextStep2 && btnNextStep2.addEventListener('click', handleNextStep2);
    metaKeyword && common.initChoicesTags(metaKeyword);
 
})()