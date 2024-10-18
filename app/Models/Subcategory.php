<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Subcategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'slug',
        'description',
        'image',
        'is_active'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Event to handle image deletion when a profile picture is updated
        static::updating(function ($subcategory) {
            if ($subcategory->isDirty('image')) {
                // Check if the old image exists and is not null
                $oldImage = $subcategory->getOriginal('image');
                if ($oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });

        // Event to handle image deletion when the user is deleted
        static::deleting(function ($subcategory) {
            // Delete the user's profile image when the user is deleted
            if ($subcategory->image) {
                Storage::disk('public')->delete($subcategory->image);
            }
        });
    }
}
