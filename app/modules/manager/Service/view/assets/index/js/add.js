(function(){

    const elDes      = document.getElementById('description'),
        btnSubmit    = document.getElementById('btnSaveService'),
        elIcon       = document.getElementById('serviceImg'),
        adminForm    = document.getElementById('adminForm'),
        title        = document.querySelector('#title'),
        btnNextStep2 = document.querySelector('#btnNextStep2');

    const checkValidForm = () => {
        let descriptionValue = quillDes ? quillDes.root.innerHTML.trim() : '';
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
        let descriptionValue = quillDes ? quillDes.root.innerHTML.trim() : '';
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
    
    const quillDes = elDes && common.initQuill(elDes);
    const serviceDropzone = elIcon && common.initDropzone(elIcon);
    btnNextStep2 && btnNextStep2.addEventListener('click', handleNextStep1);
    btnSubmit && btnSubmit.addEventListener('click', handleSubmit);

})();