<?php
namespace App\Models;

class Rating extends BaseModel
{

    protected $table = 'ratings';

    protected $fillable = [
        'rating',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
