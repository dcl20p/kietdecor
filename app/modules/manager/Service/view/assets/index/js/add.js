(function(){

    const elDes      = document.getElementById('description'),
        btnSubmit    = document.getElementById('btnSaveService'),
        elIcon       = document.getElementById('serviceImg'),
        adminForm    = document.getElementById('adminForm'),
        title        = document.querySelector('#title'),
        btnNextStep2 = document.querySelector('#btnNextStep2');

    const initQuill = (element) => {
        const quill = new Quill(element, {
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline',],
                    ['blockquote', 'code-block', 'image'],
                    [{ list: 'ordered' }, { list: 'bullet' }]
                ]
            },
            placeholder: 'Nhập thông tin mô tả về dịch vụ...',
            theme: 'snow' 
        });

        return quill;
    };

    const initDropzone = (element) => {
        const img = new Dropzone(element, {
            maxFilesize: 5,
            maxFiles: 1,
            acceptedFiles: ".png, .jpg, .jpeg, .gif",
            addRemoveLinks: true,
            dictDefaultMessage: "Thả tệp vào đây hoặc nhấp để tải lên",
            dictRemoveFile: "Xóa tệp",
            init: function() {
                this.on("success", function(file, response) {
                    console.log(file, response);
                });
                this.on("error", function(file, errorMessage) {
                    console.log(file, errorMessage);
                });
            }
        });
        return img;
    };

    const checkValidForm = () => {
        let descriptionValue = quillDes ? quillDes.getText().trim() : '';
        const fieldRequired = [title];
        const fieldLength = [
            [title, 1, 100]
        ];
        if (!common.checkRequired(fieldRequired)
            || !common.checkLength(fieldLength)
        ) return false;

        if (descriptionValue == '') {
            common.showMessage(window.msg.not_empty, 'danger');
            quillDes && quillDes.focus();
            return false;
        }
        return true;
    };

    const handleNextStep1 = (evt) => {
        evt.preventDefault();
        if (!checkValidForm()) {
            evt.stopPropagation();
        }
    };

    const handleSubmit = (evt) => {
        evt.preventDefault();
        let descriptionValue = quillDes ? quillDes.getText() : '';
        let imgVale = serviceDropzone ? serviceDropzone.getQueuedFiles() : [];

        const inputDes = document.createElement('input');
        inputDes.type  = 'text';
        inputDes.name  = 'description';
        inputDes.value = descriptionValue;

        const inputImg = document.createElement('input');
        inputImg.type  = 'text';
        inputImg.name  = 'icon';
        inputImg.value = imgVale;

        adminForm.appendChild(inputDes);
        adminForm.appendChild(inputImg);
        adminForm.submit();
    };
    
    Dropzone.autoDiscover = false;
    const quillDes = elDes && initQuill(elDes);
    const serviceDropzone = elIcon && initDropzone(elIcon);
    btnNextStep2 && btnNextStep2.addEventListener('click', handleNextStep1);
    btnSubmit && btnSubmit.addEventListener('click', handleSubmit);

})()