<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryImage extends Model
{
    use HasFactory;

    protected $fillable = ['image'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            $image->deleteImage();
        });
    }

    public function deleteImage()
    {
        if (file_exists(public_path('storage/' . $this->image))) {
            unlink(public_path('storage/' . $this->image));
        }
    }
}
