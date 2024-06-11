<?php

namespace App\Models;

use App\Models\Scopes\StatusScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class GuestHouse extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'media',
    ];


    /**
     * The attributes added for serialization
     *
     * @var array<int, string>
     */
    protected $appends = ['cover' , 'pictures' , 'videos'];
    /**/

    /**
     * Attributes methods
     */
    public function getCoverAttribute() {
        return $this->getFirstMediaUrl('Cover');
    }

    public function getPicturesAttribute()
    {
        return $this->getMedia('Pictures');
    }

    public function getVideosAttribute()
    {
        return $this->getMedia('Videos');
    }

    /**
     * scope a query to only include enabled houses
     */
    public function scopeEnabled(Builder $query) : void
    {
        $query->where('is_enabled' , true);
    }
    /***
     * Define relationship
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
