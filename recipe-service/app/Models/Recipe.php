<?php
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends BaseModel
{
    use SoftDeletes;

    protected $table = 'recipes';

    protected $fillable = [
        'name',
        'description',
        'prep_time',
        'difficulty',
        'vegetarian',
    ];

    protected $dates = ['deleted_at'];

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
