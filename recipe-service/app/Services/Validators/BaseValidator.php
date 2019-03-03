<?php
namespace App\Services\Validators;

use App\Exceptions\ValidationException;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Validator;

abstract class BaseValidator
{
    protected $validator;
    protected $mode;

    public function __construct(ValidationFactory $validator)
    {
        $this->validator = $validator;
        $this->registerNotStrictBoolean();
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function validate($data, $rules = [], $custom_errors = [])
    {
        if (empty($rules) && !empty($this->rules) && is_array($this->rules)) {
            //no rules passed to function, use the default rules defined in sub-class
            $rules = $this->rules;
        }

        $addRuleWhenRequiredFound = function ($rulesList) {
            if (in_array('required', $rulesList)) {
                array_unshift($rulesList, 'sometimes');
            }
            return $rulesList;
        };

        if ($this->mode == 'update') {
            $rules = array_map($addRuleWhenRequiredFound, $rules);
        }

        $validation = $this->validator->make($data, $rules, $custom_errors);

        if ($validation->fails()) {
            throw new ValidationException($validation->messages());
        }

        return true;
    }

    public function sanitize($data)
    {
        $boolean = function ($data) {
            $result = filter_var($data, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            return $result===null ? 'null' : $result;
        };
        $filters = [];
        if (!empty($this->filters)) {
            $filters = $this->filters;
        }
        foreach ($filters as $key => $value) {
            if (empty($data[$key])) {
                continue;
            }
            foreach ($value as $filter) {
                $data[$key] = ${$filter}($data[$key]);
            }
        }
        return $data;
    }

    protected function registerNotStrictBoolean()
    {
        $this->validator->extend('notStrictBoolean', function ($attribute, $value, $parameters, $validator) {
            $attribute = $attribute ?: null;
            $parameters = $parameters ?: null;
            $validator = $validator ?: null;
            return in_array($value, [true, false, 'true', 'false', 0, 1, '0', '1']);
        });
    }
}
