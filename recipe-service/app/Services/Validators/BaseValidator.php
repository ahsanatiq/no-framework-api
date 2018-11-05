<?php
namespace App\Services\Validators;

use App\Exceptions\ValidationException;
use Illuminate\Validation\Factory as ValidationFactory;

abstract class BaseValidator
{
    protected $validator;
    protected $mode;

    public function __construct(ValidationFactory $validator) {
        $this->validator = $validator;
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function validate($data, $rules = [], $custom_errors = []) {
        if (empty($rules) && !empty($this->rules) && is_array($this->rules)) {
            //no rules passed to function, use the default rules defined in sub-class
            $rules = $this->rules;
        }

        if($this->mode == 'update')
        {
            $rules = array_map(function($rule) {
                $rule = array_filter($rule, function($rule_values) {
                    if(strtolower($rule_values)!='required') {
                        return true;
                    }
                });
                return $rule;
            }, $rules);
        }

        $validation = $this->validator->make($data, $rules, $custom_errors);

        if ( $validation->fails() ) {
            throw new ValidationException( $validation->messages() );
        }

        return true;
    }

}
