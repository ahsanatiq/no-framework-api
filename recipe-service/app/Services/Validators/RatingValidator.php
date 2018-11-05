<?php
namespace App\Services\Validators;

class RatingValidator extends BaseValidator
{
    public $rules = [
        'rating'  => ['required', 'numeric', 'between:1,5'],
    ];

}
