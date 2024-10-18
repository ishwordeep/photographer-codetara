<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active'
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Event to handle image deletion when a profile picture is updated
        static::updating(function ($user) {
            if ($user->isDirty('image')) {
                // Check if the old image exists and is not null
                $oldImage = $user->getOriginal('image');
                if ($oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });

        // Event to handle image deletion when the user is deleted
        static::deleting(function ($user) {
            // Delete the user's profile image when the user is deleted
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
        });
    }

}
