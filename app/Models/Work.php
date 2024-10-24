<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'date',
        'is_active',
    ];

   

    public function images()
    {
        return $this->hasMany(WorkImage::class);
    }
}
