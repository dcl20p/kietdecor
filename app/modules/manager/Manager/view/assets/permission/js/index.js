(function() {
    const checkAllModule = document.querySelectorAll('.check-all-module');
    const elModuleItem = document.querySelectorAll('.module-item');
    const checkAllController = document.querySelectorAll('.check-all-controller');
    const groupCode = document.querySelector('#group_code');
    /**
     * Event check all check box
     * @param {*} evt 
     * @param {*} elParent 
     */
    const checkAll = (evt, elParent = '.module-item') => {
        let prop = evt.checked;
        const parentItem = evt.closest(elParent),
            allCheckbox = parentItem.querySelectorAll('input[type="checkbox"]');

        allCheckbox.forEach((e) => {
            e.checked = prop;
        });
    };
    checkAllModule && checkAllModule.forEach((evt) => {
        evt.addEventListener('change', (el) => {
            checkAll(el.currentTarget, '.module-item');
        });
    });
    checkAllController && checkAllController.forEach((evt) => {
        evt.addEventListener('change', (el) => {
            checkAll(el.currentTarget, '.controller-item');
        });
    });

    // Event change group code
    groupCode && groupCode.addEventListener('change', (evt) => {
        window.location.href = window.location.pathname + '?gcode=' + (evt.target.value || '').trim();
    });

    // Check all event
    elModuleItem.forEach((el, i) => {
        let elControllerItem = el.querySelectorAll('.controller-item');
        let checkedAllController = true;
        elControllerItem.forEach((elCtrl, j) => {
            let elActionItem = elCtrl.querySelectorAll('.action-item');
            let elController = elCtrl.querySelector('.check-all-controller');
            let checkedAllAction = true;
            elActionItem.forEach((elAction, k) => {
                let checkbox = elAction.querySelector('.check-action');
                if (!checkbox.checked) {
                    checkedAllAction = false;
                    return;
                }
            });
            if (checkedAllAction) 
                elController.checked = true;
            if (!elController.checked) {
                checkedAllController = false;
                return;
            }
        });
        if (checkedAllController) 
            el.querySelector('.check-all-module').checked = true;
    });
})()