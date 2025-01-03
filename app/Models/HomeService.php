<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class HomeService extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'about',
        'price',
        'category_id',
        'is_popular',
        'duration',
    ];

    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function benefits() {
        return $this->hasMany(ServiceBenefit::class);
    }

    public function testimonials() {
        return $this->hasMany(ServiceTestimonial::class);
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($homeService) {
            if ($homeService->thumbnail) {
                Storage::delete($homeService->thumbnail);
            }
            foreach ($homeService->testimonials as $testimonial) {
                if ($testimonial->photo) {
                    Storage::delete($testimonial->photo);
                }
            }
        });

        static::updating(function ($homeService) {
            if ($homeService->isDirty('thumbnail')) {
                Storage::delete($homeService->getOriginal('thumbnail'));
            }
        });
    }
}
