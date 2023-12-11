(function(){
    var thumbnails    = __thumbnails__,
        listThumbnails= __listThumbnails__,
        isEdit        = __isEdit__;
    const elDes       = document.getElementById('description'),
        elChoices     = document.querySelectorAll('.choices'),
        elMetaDes     = document.getElementById('meta_desc'),
        elMetaKeyword = document.getElementById('meta_keyword'),
        elMetaTitle   = document.getElementById('meta_title'),
        elImage       = document.getElementById('jsonImage'),
        elThumbnail   = document.getElementById('thumbnail'),
        elName        = document.querySelector('#name'),
        elLocation    = document.querySelector('#location'),
        elProjectCate = document.querySelector('#prc_id'),
        elService     = document.querySelector('#sv_id'),
        elAssignee    = document.querySelector('#assignee'),
        elStatus      = document.getElementById('status'),
        btnDefault    = document.querySelectorAll('.btnDefault'),
        btnSubmit     = document.getElementById('btnSubmit'),
        btnNextStep2  = document.querySelector('#btnNextStep2'),
        adminForm     = document.getElementById('adminForm'),
        btnNextStep3  = document.querySelector('#btnNextStep3');    

    const checkValidForm = () => {
        const fieldRequired = [elName, elProjectCate, elService];
        const fieldLength = [
            [elName, 1, 512],
            [elLocation, 0, 255],
            [elAssignee, 0, 50],
        ];
        if (!common.checkRequired(fieldRequired)
            || !common.checkLength(fieldLength)
        ) return false;

        return true;
    };

    const checkValidUploadFile = () => {
        if (dropzoneListImg.getQueuedFiles().length === 0) {
            common.showMessage('Chưa chọn danh sách hình ảnh', 'danger');
            return false;
        }

        if (dropzoneThumbnail.getQueuedFiles().length === 0) {
            common.showMessage('Chưa chọn hình thumbnail', 'danger');
            return false;
        }
        return true;
    };

    const handleNextStep2 = (evt) => {
        evt.preventDefault();
        if (!checkValidForm()) {
            evt.stopPropagation();
        }
    };

    const handleNextStep3 = (evt) => {
        evt.preventDefault();
        if (!checkValidUploadFile()) {
            evt.stopPropagation();
        }
    };

    const submitForm = (spinner, imgThumb, imgList) => {
        let desc = quillDes ? quillDes.root.innerHTML.trim() : '';
        let metaDesc = metaDes ? metaDes.root.innerHTML.trim() : '';

        const inputDes = document.createElement('input');
        inputDes.type  = 'text';
        inputDes.name  = 'description';
        inputDes.value = desc;

        const inputMetaDes = document.createElement('input');
        inputMetaDes.type  = 'text';
        inputMetaDes.name  = 'meta_desc';
        inputMetaDes.value = metaDesc;

        const inputThumb = document.createElement('input');
        inputThumb.type  = 'text';
        inputThumb.name  = 'thumbnail';
        inputThumb.value = imgThumb;

        const inputListImg = document.createElement('input');
        inputListImg.type  = 'text';
        inputListImg.name  = 'json_image';
        inputListImg.value = imgList;

        adminForm.appendChild(inputDes);
        adminForm.appendChild(inputMetaDes);
        adminForm.appendChild(inputThumb);
        adminForm.appendChild(inputListImg);

        spinner.classList.add('d-none');
        adminForm.submit();
    };

    const handleSubmit = async (evt) => {
        evt.preventDefault();
        let _self = evt.currentTarget,
            spinner = _self.querySelector('.spinner-border');

        spinner.classList.remove('d-none');
        common.uploadFiles(dropzoneThumbnail).then(response => {
            if (response.success) {
                let imgThumb = response.data;
                common.uploadFiles(dropzoneListImg).then(response => {
                    if (response.success) {
                        let imgList = response.data;
                        submitForm(spinner, imgThumb, imgList);
                    } else {
                        common.showMessage(response.msg, 'danger');
                        spinner.classList.add('d-none');
                    }
                });
            } else {
                common.showMessage(response.msg, 'danger');
                spinner.classList.add('d-none');
            }
        });
    };

    btnDefault && btnDefault.forEach((el) => {
        el.addEventListener('click', (e) => {
            e.stopPropagation();
        })
    });

    elChoices && elChoices.forEach((el) => {
        new Choices(el, {
            shouldSort: false,
            searchEnabled: true,
        });
    });

    const quillDes  = elDes && common.initQuill(elDes);
    const metaDes   = elMetaDes && common.initQuill(elMetaDes);
    const dropzoneListImg   = elImage && common.initDropzone(elImage, {maxFiles: 50}, listThumbnails);
    const dropzoneThumbnail = elImage && common.initDropzone(elThumbnail, {maxFiles: 1}, thumbnails); 

    console.log(dropzoneThumbnail);
    
    elMetaKeyword && common.initChoicesTags(elMetaKeyword);
    btnNextStep2 && btnNextStep2.addEventListener('click', handleNextStep2);
    btnNextStep3 && btnNextStep3.addEventListener('click', handleNextStep3);
    btnSubmit && btnSubmit.addEventListener('click', handleSubmit);
})()