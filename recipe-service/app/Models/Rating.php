<?php
namespace App\Models;

class Rating extends BaseModel
{

    protected $table = 'ratings';
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'rating',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
