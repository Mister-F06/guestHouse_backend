<?php

namespace App\Observers;

use App\Models\GuestHouse;
use Illuminate\Support\Str;

class GuesthouseObserver
{
    /**
     * Handle the GuestHouse "created" event.
     */
    public function created(GuestHouse $guestHouse): void
    {
        $guestHouse->slug = Str::slug($guestHouse->name , '-');
        $guestHouse->saveQuietly();
    }

    /**
     * Handle the GuestHouse "updated" event.
     */
    public function updated(GuestHouse $guestHouse): void
    {
        $guestHouse->slug = Str::slug($guestHouse->name , '-');
        $guestHouse->saveQuietly();
    }

    /**
     * Handle the GuestHouse "deleted" event.
     */
    public function deleted(GuestHouse $guestHouse): void
    {
        //
    }

    /**
     * Handle the GuestHouse "restored" event.
     */
    public function restored(GuestHouse $guestHouse): void
    {
        //
    }

    /**
     * Handle the GuestHouse "force deleted" event.
     */
    public function forceDeleted(GuestHouse $guestHouse): void
    {
        //
    }
}
