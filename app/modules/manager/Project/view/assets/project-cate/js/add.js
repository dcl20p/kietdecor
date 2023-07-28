(function(){

    const metaDesc   = document.getElementById('meta_desc'),
        metaKeyword  = document.getElementById('meta_keyword'),
        metaTitle    = document.getElementById('meta_title'),
        btnSubmit    = document.getElementById('btnSubmit'),
        elIcon       = document.getElementById('cateImg'),
        adminForm    = document.getElementById('adminForm'),
        name        = document.querySelector('#name'),
        btnNextStep2 = document.querySelector('#btnNextStep2');

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

    const initChoicesTag = (element) => {
        const tags = new Choices(element, {
            removeItemButton: false
        });
    }

    const initQuill = (element) => {
        const quill = new Quill(element, {
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    ['blockquote', 'code-block'],
                    [{ list: 'ordered' }, { list: 'bullet' }]
                ]
            },
            placeholder: 'Nhập thông tin mô tả về dịch vụ...',
            theme: 'snow' 
        });

        return quill;
    };
    const checkValidForm = () => {
        const fieldRequired = [name];
        const fieldLength = [
            [name, 1, 100]
        ];
        if (!common.checkRequired(fieldRequired)
            || !common.checkLength(fieldLength)
        ) return false;

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
        let descriptionValue = quillDes ? quillDes.root.innerHTML.trim() : '';
        let imgVale = projectCateDropzone ? projectCateDropzone.getQueuedFiles() : [];

        const inputDes = document.createElement('input');
        inputDes.type  = 'text';
        inputDes.name  = 'meta_desc';
        inputDes.value = descriptionValue;

        const inputImg = document.createElement('input');
        inputImg.type  = 'text';
        inputImg.name  = 'image';
        inputImg.value = imgVale;

        adminForm.appendChild(inputDes);
        adminForm.appendChild(inputImg);
        adminForm.submit();
    };
    
    Dropzone.autoDiscover = false;
    const projectCateDropzone = elIcon && initDropzone(elIcon);
    btnNextStep2 && btnNextStep2.addEventListener('click', handleNextStep1);
    btnSubmit && btnSubmit.addEventListener('click', handleSubmit);
    metaKeyword && initChoicesTag(metaKeyword);
    const quillDes = metaDesc && initQuill(metaDesc);


})();