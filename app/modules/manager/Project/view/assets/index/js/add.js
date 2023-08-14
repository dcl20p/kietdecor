(function(){
    var isValidThumbnail = isValidListImage = true;

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
        btnSubmit    = document.getElementById('btnSubmit'),
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
        // if (!checkValidForm()) {
        //     evt.stopPropagation();
        // }
    };

    const handleSubmit = (evt) => {
        evt.preventDefault();
        dropzoneThumbnail.processQueue();
        dropzoneListImg.processQueue();
        // let descriptionValue = quillDes ? quillDes.root.innerHTML.trim() : '';
        // let imgVale = projectCateDropzone ? projectCateDropzone.getQueuedFiles() : [];

        // const inputDes = document.createElement('input');
        // inputDes.type  = 'text';
        // inputDes.name  = 'meta_desc';
        // inputDes.value = descriptionValue;

        // const inputImg = document.createElement('input');
        // inputImg.type  = 'text';
        // inputImg.name  = 'image';
        // inputImg.value = imgVale;

        // adminForm.appendChild(inputDes);
        // adminForm.appendChild(inputImg);
        // adminForm.submit();
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
    const dropzoneListImg   = elImage && common.initDropzone(elImage, {
        uploadMultiple: true,
        maxFiles: 2,
    });
    const dropzoneThumbnail = elImage && common.initDropzone(thumbnail, {
        maxFiles: 1,     
    });

    // console.log("dropzoneThumbnail",dropzoneThumbnail);
    metaKeyword && common.initChoicesTags(metaKeyword);
    btnNextStep2 && btnNextStep2.addEventListener('click', handleNextStep2);
    btnSubmit && btnSubmit.addEventListener('click', handleSubmit);
 
})()