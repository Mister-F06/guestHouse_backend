<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuestHouseRejected extends Model
{
    use HasFactory , SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Relationship methods
     */

    public function guestHouse()
    {
        return $this->belongsTo(GuestHouse::class);
    }
}
