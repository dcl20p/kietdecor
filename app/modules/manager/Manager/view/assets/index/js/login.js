(function () {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const csrfToken = '__token__';

    /**
     * Check validate of password
     * @param {*} evt 
     * @returns boolean
     */
    const checkValidEmail = (evt) => {
        let email = evt.value;
        if (common.isValidEmail(email)
            && email.length >= 2
            && email.length <= 100
        ) {
            evt.closest('div.input-group').classList.add('is-valid');
            evt.closest('div.input-group').classList.remove('is-invalid');
            return true;
        } else {
            evt.closest('div.input-group').classList.remove('is-valid');
            evt.closest('div.input-group').classList.add('is-invalid');
            return false;
        }
    }
    
    /**
     * Check validate of password
     * @param {*} evt 
     * @returns boolean
     */
    const checkValidPassword = (evt) => {
        let pass = evt.value;
        if (common.isValidPassword(pass) && pass.length >= 6 && pass.length <= 50) {
            evt.closest('div.input-group').classList.add('is-valid');
            evt.closest('div.input-group').classList.remove('is-invalid');
            return true;
        } else {
            evt.closest('div.input-group').classList.remove('is-valid');
            evt.closest('div.input-group').classList.add('is-invalid');
            return false;
        }
    };

    /**
     * Set attribute for email & password while validate
     */
    const setAttributeEmailPassword = () => {
        const ulEmailElement = emailInput.closest('div.input-group').querySelector('ul');
        const ulPassElement = passwordInput.closest('div.input-group').querySelector('ul');

        if ("" == emailInput.value) {
            emailInput.removeAttribute('value');
        } else if (ulEmailElement) {
            emailInput.closest('div.input-group').classList.add('is-filled is-invalid');
        } else {
            if (checkValidEmail(emailInput)) {
                emailInput.closest('div.input-group').classList.add('is-valid');
            } else {
                emailInput.closest('div.input-group').classList.add('is-filled is-invalid');
            }
        }
    
        if ("" == passwordInput.value) {
            passwordInput.removeAttribute('value');
        } else if (ulPassElement) {
            passwordInput.closest('div.input-group').classList.add('is-filled is-invalid');
        } else {
            if (checkValidPassword(passwordInput)) {
                passwordInput.closest('div.input-group').classList.add('is-valid');
            } else {
                passwordInput.closest('div.input-group').classList.add('is-filled is-invalid');
            }
        }
    }

   /**
    * Show modal reset password
    * @param {*} evt 
    */
    const resetPassword = async (evt) => {
        let inputMixin = Swal.mixin({
            customClass: {
                confirmButton: "btn bg-gradient-success",
                cancelButton: "btn bg-gradient-danger",
            },
            buttonsStyling: false
        });
        const result = await inputMixin.fire({
            title: '__forgot_pass__',
            input: 'email',
            inputAttributes: { autocapitalize: "off" },
            inputPlaceholder: '__place_holder__',
            showCancelButton: true,
            confirmButtonText: '__send__',
            cancelButtonText: '__cancel__',
            showLoaderOnConfirm: true,
            preConfirm: async (email) => {
                try {
                    const response = await axios.post(`__api_reset_pass__`, 
                        {email:email}, {
                            headers: {
                                'Csrf-Token': csrfToken
                            }
                        }
                    );
                    if (!response.data) {
                        Swal.fire("Oops", "__error_info__", "error");
                    }
                    return response.data;
                } catch (error) {
                    Swal.fire("Oops", "__error_info__", "error");
                }
            },
            allowOutsideClick: () => !Swal.isLoading(),
        });
        if (result?.isConfirmed && result?.value) {
            if (result?.value.success) {
                Swal.fire(
                    "__success__", result?.value.msg ?? "__success_info__", "success"
                );
            } else {
                Swal.fire("Oops", result?.value.msg ?? '__error_info__', "error");
            }
        }
    }

    // show/hide password
    const showHidePassword = (evt) => {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            evt.currentTarget.querySelector('i.material-icons').textContent = 'visibility_off';
        } else {
            passwordInput.type = 'password';
            evt.currentTarget.querySelector('i.material-icons').textContent = 'visibility';
        }
    };

    /**
     * Event submit form
     * @param {*} evt 
     * @returns 
     */
    const eventSubmitForm = (evt) => {
        evt.preventDefault();
        if (!checkValidEmail(emailInput)) {
            common.showMessage('__input_invalid__', 'danger');
            emailInput.focus();
            return false;
        }
        if (!checkValidPassword(passwordInput)) {
            common.showMessage('__input_invalid__', 'danger');
            passwordInput.focus();
            return false;
        }
        evt.currentTarget.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Event input of email
        emailInput && emailInput.addEventListener('input', (evt) => {
            checkValidEmail(evt.currentTarget);
        });

        // Event input of password
        passwordInput && passwordInput.addEventListener('input', (evt) => {
            checkValidPassword(evt.currentTarget);
        });

        // Reset password
        const forgotPass = document.getElementById('forgot-pass');
        forgotPass && forgotPass.addEventListener('click', resetPassword);

        // Show/hide password
        const toggleShowHide = document.getElementById('show-pass');
        toggleShowHide && toggleShowHide.addEventListener('click', showHidePassword);

        // Submit form
        const formSubmit = document.getElementById('login-form');
        formSubmit && formSubmit.addEventListener('submit', eventSubmitForm);

        setAttributeEmailPassword();
    });
})();