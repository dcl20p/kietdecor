(function () {
    let csrfToken = '__token__';
    const userId = document.getElementById('user-id').value;

    /** ================Init select choices================*/
    let choices = document.querySelectorAll('.choices');
    choices && choices.forEach((el) => {
        new Choices(el, {
            shouldSort: false,
            searchEnabled: false,
        });
    });

    /** ================Birthday================ */
    const yearSelect = document.getElementById('birthday_year'),
        monthSelect = document.getElementById('birthday_month'),
        daySelect = document.getElementById('birthday_day');

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

    /** ================Session================ */
    /**
     * show modal session
     * @param {*} evt 
     */
    const showModalSession = (evt) => {
        let _self = evt.target,
            ssId = _self.getAttribute('data-id'),
            name = _self.getAttribute('data-name'),
            spinnerDetail = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

        sessionModal.querySelector('.modal-header h6').innerHTML = name;
        sessionModal.querySelector('.modal-body').classList.add('text-center');
        sessionModal.querySelector('.modal-body').innerHTML = spinnerDetail;
        
        new bootstrap.Modal(sessionModal).show();
        axios.get('__url_view_detail_session__', {
            params: {
                ss_id: ssId,
            }
        })
        .then(response => {
            let rs = response.data;
            if (rs.success) {
                sessionModal.innerHTML = rs.html;
            }
        })
        .catch(err => {
            showMessage(window.msg.went_wrong, 'danger');
            new bootstrap.Modal(sessionModal).hide();
        });
    };
    const loadMoreSession = (evt) => {
        let _self = evt.currentTarget,
            page = parseInt(_self.getAttribute('data-page')) + 1,
            spinnerSeeMoreSession = _self.querySelector('.spinner-border'),
            textBtnSession = _self.querySelector('.txt-btn');

        spinnerSeeMoreSession.classList.remove('d-none');
        textBtnSession.classList.add('d-none');
        axios.get('__url_loadmore_session__', {
            params: {
                page: page,
            }
        })
        .then(response => {
            let rs = response.data;
            if (rs.success) {
                boxLoadMoreSession.insertAdjacentHTML('beforeend', rs.html);
                _self.setAttribute('data-page', page);
                if (rs.total < rs.limit) {
                    seeMoreSessionBtn.closest('div').remove();
                }
            }
            spinnerSeeMoreSession.classList.add('d-none');
            textBtnSession.classList.remove('d-none');
        })
        .catch(err => {
            showMessage(window.msg.went_wrong, 'danger');
            spinnerSeeMoreSession.classList.add('d-none');
            textBtnSession.classList.remove('d-none');
        });
    };

    const logOutSession = (_self) => {
        let ssId = _self.getAttribute('data-id'),
            spinner = _self.querySelector('.spinner-border'),
            textBtn = _self.querySelector('.txt-btn'),
            closeModalBtn = document.getElementById('btnCloseModalSS');
        spinner.classList.remove('d-none');
        textBtn.classList.add('d-none');

        common.alertConfirm(_self, (value) => {
            if (value?.token) {
                csrfToken = value.token;
            }
            if (value?.success) {
                common.showMessage(value.msg || window.msg.update_success, 'success');
                let elItemSession = document.querySelector('.item-session-' + ssId);
                elItemSession && elItemSession.remove();
            } else {
                common.showMessage(value.msg || window.msg.update_fail, 'danger');
            }
            spinner.classList.add('d-none');
            textBtn.classList.remove('d-none');
            closeModalBtn && closeModalBtn.click();
        }, async () => {
            try {
                const response = await axios.post('__url_logout_session__', 
                    {ss_id:ssId}, {
                        headers: {
                            'Csrf-Token': csrfToken
                        }
                    }
                );
                if (!response.data) {
                    common.showMessage(window.msg.went_wrong, 'danger');
                    spinner.classList.add('d-none');
                    textBtn.classList.remove('d-none');
                    closeModalBtn && closeModalBtn.click();
                }
                return response.data;
            } catch (error) {
                common.showMessage(window.msg.went_wrong, 'danger');
                spinner.classList.add('d-none');
                textBtn.classList.remove('d-none');
                closeModalBtn && closeModalBtn.click();
            }
        }, () => {
            spinner.classList.add('d-none');
            textBtn.classList.remove('d-none');
        });
    };
    const seeMoreSessionBtn = document.querySelector('#btnSeeMoreSession'),
        boxLoadMoreSession = document.querySelector('#box-loadmore-session'),
        sessionModal = document.getElementById('sessionModal');

        boxLoadMoreSession && boxLoadMoreSession.addEventListener('click', (evt) => {
        /** Show modal detail session */
        if (evt.target.tagName === 'A') {
            showModalSession(evt);
        }
    });
    /** Load more */
    seeMoreSessionBtn && seeMoreSessionBtn.addEventListener('click', loadMoreSession);

    sessionModal.addEventListener('click', (evt) => {
        /** Logout session */
        if (evt.target.className.includes('btn-delete-session')) {
            logOutSession(evt.target);
        } else if (evt.target.className === 'txt-btn') {
            logOutSession(evt.target.parentNode);
        }
    });

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
        common.alertConfirm(evt, (value) => {
            elInput.click();
        })
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
        // let userId = evt.target.getAttribute('data-id');
        if (!file.type.match('image.*')) {
            common.showMessage('__invalid_image_type__', 'danger');
            return;
        }

        let formData = new FormData();
        formData.append('file', file);
        formData.append('id', userId);
        axios.post(urlUpload, 
            formData, {
                headers: {
                    'Csrf-Token': csrfToken,
                }
        }).then(response => {
            if (response.data) csrfToken = response.data.token;
            if (response.data.success) {
                let reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => {
                    if (urlUpload == '__url_upload_avatar__') {
                        elImg.src = reader.result;
                    } else {
                        elImg.style.backgroundImage = "url('" + reader.result + "')";
                    }
                };
                common.showMessage(response.data.msg || window.msg.update_success, 'success');
            } else {
                common.showMessage(response.data.msg || window.msg.went_wrong, 'danger');
            }
            elButton.querySelector(elIcon).style.display = 'inline-block';
        }).catch(error => {
           common.showMessage(window.msg.went_wrong, 'danger');
           elButton.querySelector(elIcon).style.display = 'inline-block';
        });
    };
    updateAvatarBtn && updateAvatarBtn.addEventListener('click', (evt) => browserImage(evt, avatarInput));
    avatarInput && avatarInput.addEventListener('change', (evt) => eventChangeImage(
        evt, updateAvatarBtn, avatarImg, '.icon-edit-avatar', '__url_upload_avatar__'
    ));

    /** ================Upload background time line================ */
    const updateBgTimeline = document.querySelector('#update-bg-timeline'),
        bgTimelineInput = document.querySelector('#bg-timeline-input'),
        bgTimelineBlock = document.querySelector('#block-bg-timeline');

    updateBgTimeline && updateBgTimeline.addEventListener('click', (evt) => browserImage(evt, bgTimelineInput));
    bgTimelineInput && bgTimelineInput.addEventListener('change', (evt) => eventChangeImage(
        evt, updateBgTimeline, bgTimelineBlock, '.icon-edit-bg-timeline', '__url_upload_bg_timeline__'
    ));

    /** ================Update status================ */
    // const statusInput = document.querySelector('#status_user');
    /**
     * Change status of user
     * @param {*} evt 
     */
    /* const changStatusUser = async (evt) => {
        evt.preventDefault();
        let _self = evt.currentTarget;
        let status = _self.checked == true ? 1 : 0;

        common.alertConfirm(evt, (value) => {
            if (value?.token) {
                csrfToken = value.token;
            }
            if (value?.success) {
                common.showMessage(value.msg || window.msg.update_success, 'success');
            } else {
                common.showMessage(value.msg || window.msg.update_fail, 'danger');
            }
        }, async () => {
            try {
                const response = await axios.post('__url_change_status_user__', 
                    {status:status, id:userId}, {
                        headers: {
                            'Csrf-Token': csrfToken
                        }
                    }
                );
                if (!response.data) {
                    common.showMessage('__error_info__', 'danger');
                }
                return response.data;
            } catch (error) {
                common.showMessage('__error_info__', 'danger');
            }
        });
    };
    statusInput && statusInput.addEventListener('change', changStatusUser);
 */
    /** ================Update information basic of user================ */
    const updateUserBtn = document.querySelector('#btnUpdateInfo'),
        firstName = document.querySelector('#first_name'),
        lastName = document.querySelector('#last_name'),
        gender = document.querySelector('#gender'),
        birthdayYear = document.querySelector('#birthday_year'),
        birthdayMonth = document.querySelector('#birthday_month'),
        birthdayDay = document.querySelector('#birthday_day'),
        email = document.querySelector('#email'),
        confirmEmail = document.querySelector('#confirm_email'),
        address = document.querySelector('#address'),
        phone = document.querySelector('#phone'),
        phoneCode = document.querySelector('#phone_code'),
        selectedPhoneCode = phoneCode.getAttribute('data-value');
    
    /**
     * Check valid information of user
     * @returns boolean
     */
    const checkValidInfoUser = () => {
        const fieldRequired = [firstName,lastName, email, confirmEmail, phone];
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
        ) {
            return false;
        }

        return true;
    };

    /**
     * event update information of user
     * @param {*} evt 
     */
    const eventUpdateInfoUser = (evt) => {
        if (checkValidInfoUser()) {
            let _self = evt.currentTarget;
            let spinner = _self.querySelector('.spinner-border'),
                textBtn = _self.querySelector('.txt-btn');
            spinner.classList.remove('d-none');
            textBtn.classList.add('d-none');
            axios.post('__url_update_info_user__', 
                {
                    id: userId,
                    first_name: firstName.value,
                    last_name: lastName.value,
                    gender: gender.value,
                    birthday_year: birthdayYear.value,
                    birthday_month: birthdayMonth.value,
                    birthday_day: birthdayDay.value,
                    email: email.value,
                    confirm_email: confirmEmail.value,
                    address: address.value,
                    phone: phone.value,
                    phone_code: phoneCode.value,
                }, {
                    headers: {
                        'Csrf-Token': csrfToken,
                    }
            }).then(response => {
                if (response.data) csrfToken = response.data.token;
                if (response.data.success) {
                    common.showMessage(response.data.msg || window.msg.update_success, 'success');
                } else {
                    common.showMessage(response.data.msg || window.msg.went_wrong, 'danger');
                }
                spinner.classList.add('d-none');
                textBtn.classList.remove('d-none');
            }).catch(error => {
               common.showMessage(window.msg.went_wrong, 'danger');
               spinner.classList.add('d-none');
               textBtn.classList.remove('d-none');
            });
        }
    }; 
    common.getListPhoneCode(phoneCode, selectedPhoneCode);
    updateUserBtn && updateUserBtn.addEventListener('click', eventUpdateInfoUser);

    /** Change password */
    const changePassBtn = document.querySelector('#btnChangePass'),
        currentPassInput = document.querySelector('#old_password'),
        newPassInput = document.querySelector('#new_password'),
        confirmNewPassInput = document.querySelector('#confirm_new_password'),
        toggleShowHide = document.querySelectorAll('.show-pass');

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
    /**
     * Check valid form change password
     * @returns boolean
     */
    const checkValidFormChangePass = () => {
        if (
            !common.checkRequired([currentPassInput]) 
            || !common.checkPassWord([currentPassInput])
            || !common.checkRequired([newPassInput]) 
            || !common.checkPassWord([newPassInput])
            || !common.checkRequired([confirmNewPassInput]) 
            || !common.checkPassWord([confirmNewPassInput])
            || !common.checkMatch(newPassInput, confirmNewPassInput)
        ) {
            return false;
        } 

        return true;
    };

    /**
     * event handle change password
     * @param {*} evt 
     */
    const eventChangePass = (evt) => {
        evt.preventDefault();
        if (checkValidFormChangePass()) {
            let _self = evt.currentTarget,
                spinner = _self.querySelector('.spinner-border'),
                textBtn = _self.querySelector('.txt-btn');
            spinner.classList.remove('d-none');
            textBtn.classList.add('d-none');
            axios.post('__url_change_password__', 
                {
                    id: userId,
                    current_pass: currentPassInput.value,
                    new_pass: newPassInput.value,
                    confirm_new_pass: confirmNewPassInput.value,
                }, {
                    headers: {
                        'Csrf-Token': csrfToken,
                    }
            }).then(response => {
                if (response.data) csrfToken = response.data.token;
                if (response.data.success) {
                    common.showMessage(response.data.msg || window.msg.update_success, 'success');
                    window.location.href = '__url_logout__';
                } else {
                    common.showMessage(response.data.msg || window.msg.went_wrong, 'danger');
                }
                spinner.classList.add('d-none');
                textBtn.classList.remove('d-none');
            }).catch(error => {
               common.showMessage(window.msg.went_wrong, 'danger');
               spinner.classList.add('d-none');
               textBtn.classList.remove('d-none');
            });
        }
    };
    changePassBtn && changePassBtn.addEventListener('click', eventChangePass);
    toggleShowHide && toggleShowHide.forEach((el) => {
        el.addEventListener('click', showHidePassword)
    });

    [currentPassInput, newPassInput, confirmNewPassInput].forEach((el) => {
        el.addEventListener('input', (password) => {
            common.checkPassWord([password.currentTarget], false);
        });
    });

    /** ================Delete & Deactive account================ */
    /**
     * Deactive account
     * @param {*} evt 
     */
    const deActiveAccount = (evt) => {
        evt.preventDefault();
        let _self = evt.currentTarget;

        common.alertConfirm(_self, (value) => {
            if (value?.token) {
                csrfToken = value.token;
            }
            if (value?.success) {
                common.showMessage(value.msg || window.msg.update_success, 'success');
                window.location.href = '__url_logout__';
            } else {
                common.showMessage(value.msg || window.msg.update_fail, 'danger');
            }
        }, async () => {
            try {
                const response = await axios.post('__url_change_status_user__', 
                    {status: 0, id:userId}, {
                        headers: {
                            'Csrf-Token': csrfToken
                        }
                    }
                );
                if (!response.data) {
                    common.showMessage('__error_info__', 'danger');
                }
                return response.data;
            } catch (error) {
                common.showMessage('__error_info__', 'danger');
            }
        });
    };

    /**
     * Delete account
     * @param {*} evt 
     */
    const deleteAccount = (evt) => {
        evt.preventDefault();
        let _self = evt.currentTarget;

        common.alertConfirm(_self, (value) => {
            if (value?.token) {
                csrfToken = value.token;
            }

            if (value?.success) {
                common.showMessage(value.msg || window.msg.update_success, 'success');
                window.location.href = '__url_logout__';
            }else {
                common.showMessage(value.msg || window.msg.update_fail, 'danger');
            }
        }, async () => {
            try {
                const response = await axios.post('__url_delete_user__', 
                    {adm_id:userId}, {
                        headers: {
                            'Csrf-Token': csrfToken
                        }
                    }
                );
                if (!response.data) {
                    common.showMessage('__error_info__', 'danger');
                }
                return response.data;
            } catch (error) {
                common.showMessage('__error_info__', 'danger');
            }
        });
    };
    const deActiveBtn = document.querySelector('#btnDeactive'),
        deleteAccountBtn = document.querySelector('#btnDeleteAccount');

    deActiveBtn && deActiveBtn.addEventListener('click', deActiveAccount);
    deleteAccountBtn && deleteAccountBtn.addEventListener('click', deleteAccount);

})()