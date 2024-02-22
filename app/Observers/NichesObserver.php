<?php

namespace App\Observers;

use App\Niche;

class NichesObserver
{
    /**
     * Handle the niche "created" event.
     *
     * @param  \App\Niche  $niche
     * @return void
     */
    public function created(Niche $niche)
    {
        //
    }

    public function creating(Niche $niche)
    {
        $niche['full_location'] =  $niche->wing.' Wing'.' - Bay '.$niche->bay.' - Floor '.$niche->floor.' - Block '.$niche->block.' - Level '.$niche->level.', '.$niche->unit;
    }

    /**
     * Handle the niche "updated" event.
     *
     * @param  \App\Niche  $niche
     * @return void
     */
    public function updated(Niche $niche)
    {
        //
    }

    public function updating(Niche $niche)
    {
        $niche['full_location'] =  $niche->wing.' Wing'.' - Bay '.$niche->bay.' - Floor '.$niche->floor.' - Block '.$niche->block.', Niche Level '.$niche->level.', '.$niche->unit;
    }

    /**
     * Handle the niche "deleted" event.
     *
     * @param  \App\Niche  $niche
     * @return void
     */
    public function deleted(Niche $niche)
    {
        //
    }

    /**
     * Handle the niche "restored" event.
     *
     * @param  \App\Niche  $niche
     * @return void
     */
    public function restored(Niche $niche)
    {
        //
    }

    /**
     * Handle the niche "force deleted" event.
     *
     * @param  \App\Niche  $niche
     * @return void
     */
    public function forceDeleted(Niche $niche)
    {
        //
    }
}
