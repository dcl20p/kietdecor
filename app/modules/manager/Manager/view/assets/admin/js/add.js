(function () {
    const firstName = document.querySelector('#first_name'),
        lastName = document.querySelector('#last_name'),
        email = document.querySelector('#email'),
        confirmEmail = document.querySelector('#confirm_email'),
        address = document.querySelector('#address'),
        phone = document.querySelector('#phone'),
        phoneCode = document.querySelector('#phone_code'),
        selectedPhoneCode = phoneCode.getAttribute('data-value'),
        passInput = document.querySelector('#new_password'),
        confirmPassInput = document.querySelector('#confirm_new_password'),
        toggleShowHide = document.querySelectorAll('.show-pass'),
        yearSelect = document.getElementById('birthday_year'),
        monthSelect = document.getElementById('birthday_month'),
        daySelect = document.getElementById('birthday_day'),
        adminForm = document.getElementById('adminForm'),
        updateBgTimeline = document.querySelector('#update-bg-timeline'),
        bgTimelineInput = document.querySelector('#bg-timeline-input'),
        bgTimelineBlock = document.querySelector('#block-bg-timeline'),
        editForm = document.querySelector('#type-form').value;

    /** ================Init select choices================*/
    let choices = document.querySelectorAll('.choices');
    choices && choices.forEach((el) => {
        new Choices(el, {
            shouldSort: false,
            searchEnabled: false,
        });
    });

    /** ================Birthday================ */
    let choicesInstance = null;

    /**
     * event update day
     */
    const updateDays = () => {
        let year = yearSelect.value;
        let month = monthSelect.value;
        let daysInMonth = new Date(year, month, 0).getDate();
        let selectedDay = daySelect.getAttribute('data-value');

        // Delete current options
        daySelect.innerHTML = '';

        // Create new options
        for (let i = 1; i <= daysInMonth; i++) {
            let option = document.createElement('option');
            option.text = i;
            option.value = i;
            if (i == selectedDay) {
                option.selected = true;
            }
            daySelect.appendChild(option);
        }

        if (choicesInstance) {
            let options = Array.from(daySelect.options).map((option) => {
                return { value: option.value, label: option.text };
            });
            // Clear the existing choices and update them with new options
            choicesInstance.clearChoices();
            choicesInstance.setChoices(options);
        } else {
            // Initialize a new Choices instance with the updated options
            choicesInstance = new Choices(daySelect, {
                shouldSort: false,
                searchEnabled: false,
            });
        }
    };

    daySelect && updateDays();
    yearSelect && yearSelect.addEventListener('change', updateDays);
    monthSelect && monthSelect.addEventListener('change', updateDays);

    common.getListPhoneCode(phoneCode, selectedPhoneCode);

    /** ================Upload avatar================ */
    const updateAvatarBtn = document.querySelector('#update-avatar-btn'),
        avatarInput = document.querySelector('#avatar-input'),
        avatarImg = document.querySelector('.avatar img');

    /**
     * Event browser image
     * @param {*} evt 
     * @param {*} elInput 
     */
    const browserImage = (evt, elInput) => {
        elInput.click();
    };

    /**
     * event change image
     * @param {*} evt 
     * @param {*} elButton 
     * @param {*} elImg 
     * @param {*} elIcon 
     * @param {*} urlUpload 
     * @returns 
     */
    const eventChangeImage = (evt, elButton, elImg, elIcon, urlUpload) => {
        elButton.querySelector(elIcon).style.display = 'none';
        let file = evt.target.files[0];
        if (!file.type.match('image.*')) {
            common.showMessage('__invalid_image_type__', 'danger');
            return;
        }

        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            if (urlUpload == '__url_upload_avatar__') {
                elImg.src = reader.result;
            } else {
                elImg.style.backgroundImage = "url('" + reader.result + "')";
            }
        };
        elButton.querySelector(elIcon).style.display = 'inline-block';
    };
    updateAvatarBtn && updateAvatarBtn.addEventListener('click', (evt) => browserImage(evt, avatarInput));
    avatarInput && avatarInput.addEventListener('change', (evt) => eventChangeImage(
        evt, updateAvatarBtn, avatarImg, '.icon-edit-avatar', '__url_upload_avatar__'
    ));

    /** ================Upload background time line================ */
    updateBgTimeline && updateBgTimeline.addEventListener('click', (evt) => browserImage(evt, bgTimelineInput));
    bgTimelineInput && bgTimelineInput.addEventListener('change', (evt) => eventChangeImage(
        evt, updateBgTimeline, bgTimelineBlock, '.icon-edit-bg-timeline', '__url_upload_bg_timeline__'
    ));

    /** ==================== Save information ====================*/
    /**
     * Show/hide password
     * @param {*} evt 
     */
    const showHidePassword = (evt) => {
        let groupInput = evt.currentTarget.closest('.input-group');
        let inputPass = groupInput.querySelector('input');
        if (inputPass.type === 'password') {
            inputPass.type = 'text';
            evt.currentTarget.querySelector('i.material-icons').textContent = 'visibility_off';
        } else {
            inputPass.type = 'password';
            evt.currentTarget.querySelector('i.material-icons').textContent = 'visibility';
        }
    };

    const eventSaveInfo = (evt) => {
        evt.preventDefault();
        if (checkValidForm()) {
            let _self = evt.currentTarget;
            adminForm.submit();
        }
    };

    const checkValidForm = () => {
        const fieldRequired = [firstName,lastName, email, confirmEmail, phone];
        if (!editForm) {
            fieldRequired.push(passInput, confirmPassInput);
        }
        const fieldLength = [
            [firstName, 1, 100],
            [lastName, 1, 100],
            [phone, 1, 14],
            [email, 2, 100],
            [confirmEmail, 2, 100],
            [address, 0, 100],
        ];

        if (!common.checkRequired(fieldRequired)
            || !common.checkLength(fieldLength)
            || !common.checkEmailValid([email, confirmEmail])
            || !common.checkMatch(email, confirmEmail)
            || !common.checkPhoneNumber(phone)
            || (!editForm &&
                (!common.checkPassWord([passInput])
                    || !common.checkPassWord([confirmPassInput])
                    || !common.checkMatch(passInput, confirmPassInput)
                )
            )
        ) {
            return false;
        }

        return true;
    };

    toggleShowHide && toggleShowHide.forEach((el) => {
        el.addEventListener('click', showHidePassword)
    });
    [passInput, confirmPassInput].forEach((el) => {
        el.addEventListener('input', (password) => {
            common.checkPassWord([password.currentTarget], false);
        });
    });
    const btnSave = document.querySelectorAll('.btn-save');
    btnSave && btnSave.forEach((el) => {
        el.addEventListener('click', eventSaveInfo);
    });

})();