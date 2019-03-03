<?php
namespace App\Exceptions;

class RecipeNotFoundException extends BaseException
{
    public function __construct()
    {
        parent::__construct("Recipe not found.", 404);
    }
}
