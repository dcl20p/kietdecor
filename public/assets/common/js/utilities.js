const common = (function () {
    /**
     * Show message toast 's format
     * @param {string} message: message information
     * @param {string} action: denotes the type message by color
     *  - success: default
     *  - danger
     *  - warning
     *  - info
     *  - primary
     *  - secondary
     *  - light
     *  - dark
     * @param {string} element 
     */
    const showMessage = (message, action = "success", element = 'myToast') => {
        let toastElement = document.getElementById(element);
        if (toastElement) {
            // Remove all class name start with bg-gradient-
            toastElement.classList.forEach(className => {
                if (className.startsWith('bg-gradient-')) {
                    toastElement.classList.remove(className);
                }
            });
            // Add class & message
            toastElement.classList.add('bg-gradient-' + action);
            toastElement.querySelector('.toast-body').innerHTML = message;

            let toastInstance = toastElement && bootstrap.Toast.getInstance(toastElement);
            if (!toastInstance) {
                toastInstance = new bootstrap.Toast(toastElement);
            }
            toastInstance.show();
        }
    };

    /**
     * Check valid email
     * - ^ - Start of string
     * - (([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+")): The email username, which can be one of the following two options:
     * - [^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*: One or more characters that are not <>()[]\\.,;:\s@", 
     * optionally followed by one or more occurrences of a period followed by more characters that are not <>()[]\\.,;:\s@".
     * - ".+": One or more characters enclosed in double quotes.
     * - @: The at symbol.
     * - ((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,})): The email domain, which can be one of the following two options:
     * - \[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\]: An IP address enclosed in square brackets.
     * - ([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}: One or more sequences of letters, numbers, or hyphens followed by a period, followed by two or more letters.
     * - $: End of string.
     * 
     * @param {string} email 
     * @returns boolean
     */
    const isValidEmail = (email) => {
        return new RegExp(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/).test(email);
    };

    /**
     * Check valid password
     * - (?=.\d): Contains at least one digit.
     * - (?=.[a-z]): Contains at least one lowercase letter.
     * - (?=.[A-Z]): Contains at least one uppercase letter.
     * - (?=.[@$!%#?&]): Contains at least one special character.
     * - [a-zA-Z\d@$!%#?&]{6,50}: Consists of characters a-z, A-Z, digits, and special characters with a length between 6 and 50
     * 
     * @param {string} password 
     * @returns boolean
     */
    const isValidPassword = (password) => {
        return new RegExp(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*#?&,\-_+;])[a-zA-Z\d@$!%*#?&,\-_+;]{6,50}$/).test(password);
    };

    /**
     * Check valid phone number except for phone codes
     *
     * @param {string} phoneNumber
     * @returns {boolean}
     */
    const isValidPhoneNumber = (phoneNumber) => {
        return new RegExp(/^\d{1,14}$/).test(phoneNumber);
    };

    /**
     * Check user agent of Apple
     * @returns 
     */
    const isAppleUserAgent = () => {
        return /(iPhone|iPad|iPod|Macintosh).*AppleWebKit/i.test(navigator.userAgent);
    };

    /**
     * Show alert confirm
     * @param {*} e 
     * @param {function} callbackPreConfirm 
     * @param {function} callbackIsConfirm 
     */
    const alertConfirm = async (
        e, callbackIsConfirm, callbackPreConfirm = () => { }, callbackIsNotConfirm = () => { }
    ) => {
        let target = e;
        if (typeof e === 'object' && e.constructor === PointerEvent) {
            e.preventDefault();
            target = e.currentTarget;
        }
        let inputMixin = Swal.mixin({
            customClass: {
                confirmButton: "btn bg-gradient-success",
                cancelButton: "btn bg-gradient-danger",
            },
            buttonsStyling: false,
        });
        const { isConfirmed, value } = await inputMixin.fire({
            title: target.getAttribute('data-title') || window.jsonSystemLanguage['system_info'],
            text: target.getAttribute('data-confirm') || window.msg['confirm'],
            icon: target.getAttribute('data-type') || "warning",
            showCancelButton: true,
            confirmButtonText: window.jsonSystemLanguage['yes'],
            cancelButtonText: target.getAttribute('data-cancel-btn') || window.jsonSystemLanguage['no'],
            showLoaderOnConfirm: true,
            preConfirm: callbackPreConfirm,
            allowOutsideClick: () => !Swal.isLoading()
        });
        if (isConfirmed && value) {
            callbackIsConfirm(value);
        } else {
            callbackIsNotConfirm()
        }
    };

    /**
     * Check required fields
     * @param {array} fields 
     * @param {boolean} showToast 
     */
    const checkRequired = (fields, showToast = true) => {
        let stopLoop = false;
        fields.forEach((evt) => {
            if (stopLoop) return false;
            if ("" === evt.value.trim()) {
                showToast && showMessage(window.msg.not_empty, 'danger');
                if (evt.tagName == 'INPUT') {
                    evt.closest('div.input-group').classList.add('is-invalid', 'is-filled');
                    evt.focus();
                } else if (evt.tagName == 'SELECT') {
                    evt.click();
                }
                stopLoop = true;
            } else {
                if (evt.tagName == 'INPUT') {
                    evt.closest('div.input-group').classList.remove('is-invalid');
                    evt.closest('div.input-group').classList.add('is-valid', 'is-filled');
                }
            }
        });

        return !stopLoop;
    };

    /**
     * Check valid length of fields
     * @param {array} fields 
     * @param {boolean} showToast 
     */
    const checkLength = (fields, showToast = true) => {
        let stopLoop = false;
        let msgMin = window.msg.min_length;
        let msgMax = window.msg.max_length;
        fields.forEach((evt) => {
            if (stopLoop) return false;
            let el = evt[0],
                min = evt[1],
                max = evt[2];

            if (el instanceof HTMLElement
                && Number.isInteger(min)
                && Number.isInteger(max)
            ) {
                if (el.value.length < min) {
                    showToast && showMessage(msgMin.replace('{num}', min), 'danger');
                    el.closest('div.input-group').classList.add('is-invalid', 'is-filled');
                    el.focus();
                    stopLoop = true;
                } else if (el.value.length > max) {
                    showToast && showMessage(msgMax.replace('{num}', max), 'danger');
                    el.closest('div.input-group').classList.add('is-invalid', 'is-filled');
                    el.focus();
                    stopLoop = true;
                } else {
                    el.closest('div.input-group').classList.remove('is-invalid');
                    el.closest('div.input-group').classList.add('is-valid', 'is-filled');
                }
            }
        });

        return !stopLoop;
    };

    /**
     * Check valid format email
     * @param {array} fields 
     * @param {boolean} showToast 
     * @returns 
     */
    const checkEmailValid = (fields, showToast = true) => {
        let stopLoop = false;
        fields.forEach((evt) => {
            if (stopLoop) return false;
            if (!isValidEmail(evt.value.trim())) {
                showToast && showMessage(window.msg.email_invalid, 'danger');
                evt.closest('div.input-group').classList.add('is-invalid', 'is-filled');
                evt.focus();
                stopLoop = true;
            } else {
                evt.closest('div.input-group').classList.remove('is-invalid');
                evt.closest('div.input-group').classList.add('is-valid', 'is-filled');
            }
        });

        return !stopLoop;
    };

    /**
     * Check match 
     * @param {*} el 
     * @param {*} elConfirm 
     * @param {boolean} showToast 
     * @returns 
     */
    const checkMatch = (el, elConfirm, showToast = true) => {
        if (el.value !== elConfirm.value) {
            showToast && showMessage(window.msg.not_match, 'danger');
            elConfirm.closest('div.input-group').classList.add('is-invalid', 'is-filled');
            elConfirm.focus();
            return false;
        } else {
            el.closest('div.input-group').classList.remove('is-invalid');
            el.closest('div.input-group').classList.add('is-valid', 'is-filled');
            elConfirm.closest('div.input-group').classList.remove('is-invalid');
            elConfirm.closest('div.input-group').classList.add('is-valid', 'is-filled');
        }
        return true;
    }

    /**
     * Check valid phone number
     * @param {*} phone 
     * @returns 
     */
    const checkPhoneNumber = (phone, showToast = true) => {
        if (!isValidPhoneNumber(phone.value)) {
            showToast && showMessage(window.msg.phone_invalid, 'danger');
            phone.closest('div.input-group').classList.add('is-invalid', 'is-filled');
            phone.focus();
            return false;
        } else {
            phone.closest('div.input-group').classList.remove('is-invalid');
            phone.closest('div.input-group').classList.add('is-valid', 'is-filled');
        }

        return true;
    }

    /**
     * Check validate password 
     * @param {array} fields 
     * @param {boolean} showToast 
     */
    const checkPassWord = (fields, showToast = true) => {
        let stopLoop = false;
        fields.forEach((el) => {
            if (stopLoop) return false;
            let pass = el && el.value.trim();
            if (!isValidPassword(pass)
                || !(pass.length >= 6 && pass.length <= 50)
            ) {
                showToast && showMessage(window.msg.pw_invalid, 'danger');
                el.closest('div.input-group').classList.add('is-invalid', 'is-filled');
                el.focus();
                stopLoop = true;
            } else {
                el.closest('div.input-group').classList.remove('is-invalid');
                el.closest('div.input-group').classList.add('is-valid', 'is-filled');
            }
        });
        return !stopLoop;
    };

    /**
     * Get a list of phone codes for each country
     * @param {*} el: element need fill
     * @param {*} selectedCode: option selected
     */
    const getListPhoneCode = (el, selectedCode = '') => {
        let choicesInstancePhoneCode = null;
        axios({
            url: 'https://restcountries.com/v3.1/all',
            method: 'GET',
            timeout: 3000 // 3s
        })
        .then(response => {
            const countries = response.data;
            countries.forEach((country) => {
                const idd = country.idd;
                const countryName = country.name.common;

                if (Object.keys(idd).length !== 0) {
                    idd.suffixes.forEach((suffix) => {
                        const callingCode = `${idd.root}${suffix}`;

                        // Create option
                        let option = document.createElement('option'),
                            textOption = ' ' + callingCode + ' (' + countryName + ')';

                        option.value = callingCode;

                        // If user agent is device of Apple
                        if (isAppleUserAgent()) {
                            option.textContent = country.flag +  textOption;
                        } else {
                            let altFlag = country.flags.alt,
                                flag = flagToPNG(country.flags.png),
                                img  = document.createElement('img');

                            img.src   = flag;
                            img.alt   = altFlag;
                            img.style = "width:16px; height:12px";
                            option.appendChild(img);
                            
                            let span = document.createElement('span');
                            span.textContent = textOption;
                            option.appendChild(span);
                        }

                        if (selectedCode == callingCode) {
                            option.selected = true;
                        } else {
                            if (selectedCode == '') {
                                if (window.iLang == 'vi') {
                                    if (countryName == "Vietnam") {
                                        option.selected = true;
                                    }
                                } else if (window.iLang == 'en') {
                                    if (countryName == "United States" && suffix == '201') {
                                        option.selected = true;
                                    }
                                }
                            }
                        }
                        el.appendChild(option);
                    });
                }
            });
            if (choicesInstancePhoneCode) {
                let options = Array.from(el.options).map((option) => {
                    return { value: option.value, label: option.text };
                });
                // Clear the existing choices and update them with new options
                choicesInstancePhoneCode.clearChoices();
                choicesInstancePhoneCode.setChoices(options);
            } else {
                // Initialize a new Choices instance with the updated options
                choicesInstancePhoneCode = new Choices(el);
            }
        })
        .catch(error => {
            getListPhoneCodeManual(el, selectedCode);
        });
    };

    /**
     * Get a list of phone codes for each country
     * @param {*} el: element need fill
     * @param {*} selectedCode: option selected
     */
    const getListPhoneCodeManual = (el, selectedCode = '') => {
        let choicesInstancePhoneCode = null;
        loadScript('/assets/common/js/phoneCode.min.js', (countries) => {
            countries.forEach((country) => {
                const phoneCode = country.phoneCode;
                const countryName = country.countryName;

                // Create option
                let option = document.createElement('option'),
                    textOption = ' ' + phoneCode + ' (' + countryName + ')';
                option.value = phoneCode;

                if (isAppleUserAgent()) {
                    let flag = country.countryFlag;
                    option.text = flag + ' ' + textOption;
                } else {
                    let flag = flagToPNG(country.countryCode, '16x12', false),
                        altFlag = 'Flag of ' + countryName,
                        img = document.createElement('img');

                    img.src = flag;
                    img.alt = altFlag;
                    img.style = "width:16px; height:12px";
                    option.appendChild(img);

                    let span = document.createElement('span');
                    span.textContent = textOption;
                    option.appendChild(span);
                }

                if (selectedCode == phoneCode) {
                    option.selected = true;
                } else {
                    if (selectedCode == '') {
                        if (window.iLang == 'vi') {
                            if (phoneCode == "+84") {
                                option.selected = true;
                            }
                        } else if (window.iLang == 'en') {
                            if (phoneCode == "+1201") {
                                option.selected = true;
                            }
                        }
                    }
                }
                el.appendChild(option);
            });
            if (choicesInstancePhoneCode) {
                let options = Array.from(el.options).map((option) => {
                    return { value: option.value, label: option.text };
                });
                // Clear the existing choices and update them with new options
                choicesInstancePhoneCode.clearChoices();
                choicesInstancePhoneCode.setChoices(options);
            } else {
                // Initialize a new Choices instance with the updated options
                choicesInstancePhoneCode = new Choices(el);
            }
        });
    };

    /**
     * Load script
     * @param {*} src 
     * @param {*} callBack 
     */
    const loadScript = (src, callBack = () => { }, type = "text/javascript") => {
        const script = document.createElement('script');
        if (type == 'text/javascript') {
            script.src = src;
        } else if (type == 'module') {
            script.defer = true;
        }
        script.type = type;
        script.onload = () => {
            callBack(countryData);
        };
        document.head.appendChild(script);
    };

    /**
     * Convert flag emoji to png 
     * @param {*} phoneCode 
     */
    const flagToPNG = (phoneCode, size = '16x12', useCdn = true) => {
        // let phoneCode = phoneCode.toLowerCase();
        if (phoneCode != '') {
            if (useCdn) {
                return phoneCode;
            } else {
                phoneCode = phoneCode.toLowerCase();
                return `/assets/common/images/country-flag/${size}/${phoneCode}.png`;
            }
        }
    };

    /**
     * Init quill plugin
     * @param {*} element 
     * @param {*} toolbar 
     * @param {*} placeholder 
     * @param {*} theme 
     * @param {*} options: options of plugin
     * @returns 
     */
    const initQuill = (
        element, 
        toolbar = [
            ['bold', 'italic', 'underline'],
            ['blockquote', 'code-block'],
            [{ list: 'ordered' }, { list: 'bullet' }]
        ],
        placeholder = 'Nhập thông tin mô tả...',
        theme = 'snow',
        options = {}
    ) => {
        let params = {...{
            modules: {
                toolbar: toolbar
            },
            placeholder: placeholder,
            theme: theme
        }, ...options};

        const quill = new Quill(element, params);

        return quill;
    };


    /**
     * Init Dropzone
     * @param {*} element 
     * @param {*} maxFilesize 
     * @param {*} maxFiles 
     * @param {*} addRemoveLinks 
     * @param {*} callbackInit 
     * @returns 
     */
    const initDropzone = (element, options = {}) => {
        Dropzone.autoDiscover = false;
        let defaultOptions = {
            maxFilesize: 5,
            uploadMultiple: false,
            autoProcessQueue: false,
            acceptedFiles: ".png, .jpg, .jpeg, .gif",
            dictDefaultMessage: "Thả tệp vào đây hoặc nhấp để tải lên",
            dictRemoveFile: "Xóa tệp",
            addRemoveLinks: true,
            init: function() {
                this.on("error", (file, errorMessage) => {
                    showMessage(errorMessage, 'danger');
                    this.removeFile(file);
                });
            },
            accept: function(file, done) {
                let isDuplicate = this.files.some(function(existingFile) {
                    return existingFile.name === file.name && existingFile.size === file.size;
                });
                console.log("ACcept", this.files, 
                "===========================", file, isDuplicate);
                
                if (isDuplicate) {
                    showMessage('File đã tồn tại trong danh sách.', 'danger');
                    this.removeFile(file);
                } else {
                    done();
                }
            }
        };
        let params = {...defaultOptions, ...options};
        const img = new Dropzone(element, params
        // {
        //     maxFilesize: maxFilesize,
        //     maxFiles: maxFiles,
        //     acceptedFiles: ".png, .jpg, .jpeg, .gif",
        //     addRemoveLinks: addRemoveLinks,
        //     dictDefaultMessage: "Thả tệp vào đây hoặc nhấp để tải lên",
        //     dictRemoveFile: "Xóa tệp",
        //     init: function() {
        //         this.on("success", (file, response) => {
        //             if (typeof callbackSuccess === 'function') {
        //                 callbackSuccess(file, response);
        //             } else {
        //                 // console.log(file, response);
        //             }
        //         });
        //         this.on("error", (file, errorMessage) => {
        //             if (typeof callbackError === 'function') {
        //                 callbackError(file, errorMessage);
        //             } else {
        //                 console.log(errorMessage);
        //             }
        //         });
        //     },
        //     accept: function(file, done) {
        //         var isDuplicate = this.files.some(function(existingFile) {
        //             return existingFile.name === file.name && existingFile.size === file.size;
        //         });
        //         console.log(file, done, isDuplicate);
                
        //         // if (isDuplicate) {
        //         //     done("File đã tồn tại trong danh sách.");
        //         // } else {
        //         //     done();
        //         // }
        //     }
        // }
        );

        return img;
    };

    const initChoicesTags = (element, maxItemCount = 50, placeholder = 'Nhập tags keyword...') => {
        return new Choices(element, {
            removeItemButton: false,
            delimiter: ',',
            maxItemCount: maxItemCount,  
            placeholder: placeholder,
            duplicateItemsAllowed: false
        });
    };


    return {
        showMessage:        showMessage,
        isValidEmail:       isValidEmail,
        isValidPassword:    isValidPassword,
        alertConfirm:       alertConfirm,
        checkRequired:      checkRequired,
        checkLength:        checkLength,
        getListPhoneCode:   getListPhoneCode,
        checkEmailValid:    checkEmailValid,
        checkMatch:         checkMatch,
        isValidPhoneNumber: isValidPhoneNumber,
        checkPhoneNumber:   checkPhoneNumber,
        checkPassWord:      checkPassWord,
        initQuill:          initQuill,
        initDropzone:       initDropzone,
        initChoicesTags:    initChoicesTags,
    };
})();