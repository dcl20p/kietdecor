(function(){

    const metaDesc   = document.getElementById('meta_desc'),
        metaKeyword  = document.getElementById('meta_keyword'),
        btnSubmit    = document.getElementById('btnSubmit'),
        elIcon       = document.getElementById('cateImg'),
        adminForm    = document.getElementById('adminForm'),
        name         = document.querySelector('#name'),
        elAlias      = document.querySelector('#alias'),
        btnNextStep2 = document.querySelector('#btnNextStep2');

    const checkValidForm = () => {
        const fieldRequired = [name];
        const fieldLength = [
            [name, 1, 100]
        ];
        const fieldAlias = elAlias;
        if (!common.checkRequired(fieldRequired)
            || !common.checkLength(fieldLength)
            || !common.checkAlias(fieldAlias)
        ) return false;

        return true;
    };

    const handleNextStep2 = (evt) => {
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

    const createAlias = (evt) => {
        let _self     = evt.currentTarget,
            nameValue = _self.value || '';
        nameValue = common.renderAlias(nameValue);
        elAlias.value = nameValue;
    }
    
    const projectCateDropzone = elIcon && common.initDropzone(elIcon);
    btnNextStep2 && btnNextStep2.addEventListener('click', handleNextStep2);
    btnSubmit && btnSubmit.addEventListener('click', handleSubmit);
    metaKeyword && common.initChoicesTags(metaKeyword);
    const quillDes = metaDesc && common.initQuill(metaDesc);
    name && name.addEventListener('change', createAlias);


})();