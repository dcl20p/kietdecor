<?php

namespace Manager\Form;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\Csrf;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\Hostname;
use Laminas\Validator\InArray;
use Laminas\Validator\NotEmpty as NotEmpty;
use Laminas\Validator\Regex;
use Laminas\Validator\StringLength;
use Laminas\I18n\Translator\TranslatorInterface;

/**
 * A login form with email, password, remember me, submit button, CSRF protection,
 * and dynamic translation support.
 */
class LoginForm extends Form
{
   /**
    * The translator instance to use for translations.
    *
    * @var TranslatorInterface
    */
    protected TranslatorInterface $translator;

    /**
     * Constructor.     
     */
    public function __construct(?string $name = null, array $options = [])
    {
        // Define form name
        parent::__construct($name, $options);

        $this->translator = $options['translator'];

        // Set POST method for this form
        $this->setAttributes([
            'method' => 'post',
            'class' => 'text-start',
            'role' => 'form',
            'action' => $options['route']
        ]);

        $this->addElements();
        $this->setInputFilter($this->createInputFilter());
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    private function addElements()
    {
        // Email element
        $this->add([
            'type' => Element\Email::class,
            'name' => 'email',
            'attributes' => [
                'autocomplete' => 'off',
                'id' => 'email',
                'class' => 'form-control w-100',
            ],
            'options' => [
                'label' => $this->translator->translate('Email'),
                'label_attributes' => ['class' => 'form-label'],
            ],
        ]);

        // Password element
        $this->add([
            'type' => Element\Password::class,
            'name' => 'password',
            'attributes' => [
                'autocomplete' => 'off',
                'id' => 'password',
                'class' => 'form-control w-100 position-relative',
            ],
            'options' => [
                'label' => $this->translator->translate('Password'),
                'label_attributes' => ['class' => 'form-label'],
            ],
        ]);

        // Remember me element
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'remember_me',
            'options' => [
                'checked_value' => '1',
                'unchecked_value' => '0',
                'label' => $this->translator->translate('Nhớ đăng nhập'),
                'label_attributes' => [
                    'class' => 'form-check-label mb-0 ms-3',
                    'for' => 'rememberMe'
                ],
            ],
            'attributes' => [
                'value' => '0',
                'class'=>'form-check-input',
                'id' => 'rememberMe'
            ]
        ]);

        // Redirect url element
        $this->add([
            'type' => 'hidden',
            'name' => 'redirect_url',
            'attributes' => [
                'autocomplete' => 'off',
            ],
        ]);

        // CSRF element
        $this->add([
            'type' => Element\Csrf::class,
            'name' => 'csrf',
            'attributes' => [
                'id' => 'csrf',
                'autocomplete' => 'off',
            ],
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ],
                'message' => [
                    Csrf::NOT_SAME => $this->translator->translate('Xác thực CSRF thất bại.')
                ]
            ],
        ]);

        // Submit button
        $this->add([
            'type' => Element\Button::class,
            'name' => 'submit',
            'attributes' => [
                'id' => 'btn-login',
                'class' => 'btn bg-gradient-primary w-100 my-4 mb-2',
                'type'  => 'submit'
            ],
            'options' => [
                'label' => $this->translator->translate('Đăng nhập')
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function createInputFilter()
    {
        $inputFilter = new InputFilter();

        // Add filters and validators for email field
        $inputEmail = [
            'name' => 'email',
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class]
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'message' => $this->translator->translate('Đầu vào không được để trống.')
                    ]
                ],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 100,
                        'message' => $this->translator->translate('Độ dài email phải từ 2 đến 100 ký tự.')
                    ]
                ],
                [
                    'name' => EmailAddress::class,
                    'options' => [
                        'allow' => Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                        'message' => $this->translator->translate('Đầu vào không phải là một địa chỉ email hợp lệ. Sử dụng định dạng cơ bản local-part@hostname.')
                    ]
                ],
                [
                    'name' => Regex::class,
                    'options' => [
                        'pattern' => "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/",
                        'message' => $this->translator->translate('Địa chỉ email không đúng định dạng.')
                    ]
                ]
            ]
        ];
        $inputFilter->add($inputEmail);

        // Add filters and validators for password field
        $inputPassword = [
            'name' => 'password',
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'message' => $this->translator->translate('Đầu vào không được để trống.')
                    ]
                ],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 6,
                        'max' => 50,
                        'message' => $this->translator->translate('Độ dài mật khẩu phải từ 6 đến 50 ký tự.')
                    ]
                ],
                [
                    'name' => Regex::class,
                    'options' => [   
                        'pattern' => '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*#?&,\-_+;])[a-zA-Z\d@$!%*#?&,\-_+;]*$/',
                        'message' => $this->translator->translate('Mật khẩu phải bao gồm chữ cái in hoa, chữ thường, số, và ký tự đặc biệt.')
                    ]
                ]
            ]
        ];
        $inputFilter->add($inputPassword);

        // Add validators for remember me field 
        $inputRememberMe = [
            'name' => 'remember_me',
            'validators' => [
                [
                    'name' => InArray::class,
                    'options' => [
                        'haystack' => [0, 1]
                    ]
                ]
            ]
        ];
        $inputFilter->add($inputRememberMe);

        // Add filters and validators for redirect url
        $inputRedirectUrl = [
            'name' => 'redirect_url',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class]
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 2048
                    ]
                ]
            ]
        ];
        $inputFilter->add($inputRedirectUrl);

        // Add filters and validators for csrf
        $inputCsrf = [
            'name' => 'csrf',
            'required' => false,
            'filters' => [
                ['name' => StringTrim::class]
            ],
            'validators' => [
                ['name' => Csrf::class]
            ]
        ];
        $inputFilter->add($inputCsrf);

        return $inputFilter;
    }
}