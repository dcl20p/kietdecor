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

    const uploadFiles = (objDropzone) => {
        return new Promise((resolve) => {
            objDropzone.processQueue();
            objDropzone.on('complete', function(file) {
                
                if (file.status === "success") {
                    const response = JSON.parse(file.xhr.response); 
                    resolve(response);
                } else {
                    common.showMessage('Tải lên thất bại', 'danger');
                    objDropzone.removeFile(file);
                }
            });
            objDropzone.on("error", (file, errorMessage) => {
                common.showMessage(errorMessage, 'danger');
                objDropzone.removeFile(file);
            });
        });
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
    }

    const handleNextStep2 = (evt) => {
        evt.preventDefault();
        // if (!checkValidForm()) {
        //     evt.stopPropagation();
        // }
    };

    const submitForm = (imgThumb, imgList) => {

    }

    const handleSubmit = async (evt) => {
        evt.preventDefault();
        if (checkValidUploadFile()) {
            uploadFiles(dropzoneThumbnail).then(response => {
                if (response.success) {
                    let imgThumb = response.data;
                    uploadFiles(dropzoneListImg).then(response => {
                        if (response.success) {
                            let imgList = response.data;
                            submitForm(imgThumb, imgList);
                        } else common.showMessage(response.msg, 'danger');
                    });
                } else common.showMessage(response.msg, 'danger');
            });
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

    const quillDes = elDes && common.initQuill(elDes);
    const metaDes  = elMetaDes && common.initQuill(elMetaDes);
    const dropzoneListImg   = elImage && common.initDropzone(elImage, {maxFiles: 50});
    const dropzoneThumbnail = elImage && common.initDropzone(thumbnail, {maxFiles: 1});

    // console.log("dropzoneThumbnail",dropzoneThumbnail);
    metaKeyword && common.initChoicesTags(metaKeyword);
    btnNextStep2 && btnNextStep2.addEventListener('click', handleNextStep2);
    btnSubmit && btnSubmit.addEventListener('click', handleSubmit);
})()