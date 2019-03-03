<?php
namespace App\Services\Validators;

class RecipeValidator extends BaseValidator
{
    public $rules = [
        'name'        => ['required', 'max:100'],
        'description' => ['required'],
        'prep_time'   => ['required', 'numeric', 'between:1,9999'],
        'difficulty'  => ['required', 'numeric', 'between:1,3'],
        'vegetarian'  => ['required', 'boolean'],
    ];

    public $filters = [
        'vegetarian'  => ['boolean'],
    ];
}
