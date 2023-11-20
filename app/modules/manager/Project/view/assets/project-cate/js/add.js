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

    const submitForm = (spinner, imgThumb) => {
        let descriptionValue = quillDes ? quillDes.root.innerHTML.trim() : '';

        const inputDes = document.createElement('input');
        inputDes.type  = 'text';
        inputDes.name  = 'meta_desc';
        inputDes.value = descriptionValue;

        const inputThumb = document.createElement('input');
        inputThumb.type  = 'text';
        inputThumb.name  = 'image';
        inputThumb.value = imgThumb;

        adminForm.appendChild(inputDes);
        adminForm.appendChild(inputThumb);

        spinner.classList.add('d-none');
        adminForm.submit();
    };

    const handleSubmit =  async (evt) => {
        evt.preventDefault();
        let _self = evt.currentTarget,
            spinner = _self.querySelector('.spinner-border');

        spinner.classList.remove('d-none');
        if (projectCateDropzone.getQueuedFiles());
        common.uploadFiles(projectCateDropzone, false).then(response => {
            if (response.success) {
                let imgThumb = response.data;
                submitForm(spinner, imgThumb);
            } else {
                common.showMessage(response.msg, 'danger');
                spinner.classList.add('d-none');
            }
        });
    };

    const createAlias = (evt) => {
        let _self     = evt.currentTarget,
            nameValue = _self.value || '';
        nameValue = common.renderAlias(nameValue);
        elAlias.value = nameValue;
        elAlias.closest('div.input-group').classList.add('is-filled');
    };

    const getExistImage = (elImg) => {
        let path = elImg.dataset.path;
        let strName = elImg.dataset.name;
        return strName.split(',').filter(Boolean).map(name => ({
            name: name,
            url: `${path}/${name}`
        }));

    };

    const projectCateDropzone = elIcon && common.initDropzone(elIcon, {maxFiles: 1}, getExistImage(elIcon));
    btnNextStep2 && btnNextStep2.addEventListener('click', handleNextStep2);
    btnSubmit && btnSubmit.addEventListener('click', handleSubmit);
    metaKeyword && common.initChoicesTags(metaKeyword);
    const quillDes = metaDesc && common.initQuill(metaDesc);
    name && name.addEventListener('change', createAlias);


})();