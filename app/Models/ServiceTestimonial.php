<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ServiceTestimonial extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'message',
        'photo',
        'home_service_id',
    ];

    public function homeService() {
        return $this->belongsTo(HomeService::class, 'home_service_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($testimonial) {
            if ($testimonial->photo) {
                Storage::delete($testimonial->photo);
            }
        });

        static::updating(function ($testimonial) {
            if ($testimonial->isDirty('photo')) {
                Storage::delete($testimonial->getOriginal('photo'));
            }
        });
    }
}
