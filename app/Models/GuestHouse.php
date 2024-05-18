<?php

namespace App\Models;

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

    /***
     * Define relationship
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
