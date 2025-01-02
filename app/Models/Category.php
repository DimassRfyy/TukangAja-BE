<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'photo',
        'photo_white',
    ];

    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function homeServices() {
        return $this->hasMany(HomeService::class);
    }
    public function popularServices() {
        return $this->hasMany(HomeService::class)
                    ->where('is_popular',true);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            if ($category->photo) {
                Storage::delete($category->photo);
            }
            if ($category->photo_white) {
                Storage::delete($category->photo_white);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('photo')) {
                Storage::delete($category->getOriginal('photo'));
            }
            if ($category->isDirty('photo_white')) {
                Storage::delete($category->getOriginal('photo_white'));
            }
        });
    }
}
