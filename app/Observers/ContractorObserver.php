<?php

namespace App\Observers;

use App\Contractor;

class ContractorObserver
{
    /**
     * Handle the contractor "created" event.
     *
     * @param  \App\Contractor  $contractor
     * @return void
     */
    public function created(Contractor $contractor)
    {
        //
    }

    /**
     * Handle the contractor "creating" event.
     *
     * @param  \App\Contractor  $contractor
     * @return void
     */

    public function creating(Contractor $contractor)
    {
        // $contractor['display_name'] = $contractor['first_name'] ." ". $contractor['last_name'];
    }

    /**
     * Handle the contractor "updated" event.
     *
     * @param  \App\Contractor  $contractor
     * @return void
     */
    public function updated(Contractor $contractor)
    {
        //
    }

    /**
     * Handle the contractor "updating" event.
     *
     * @param  \App\Contractor  $contractor
     * @return void
     */
    public function updating(Contractor $contractor)
    {
        // $contractor['display_name'] = $contractor['first_name'] ." ". $contractor['last_name'];
    }

    /**
     * Handle the contractor "deleted" event.
     *
     * @param  \App\Contractor  $contractor
     * @return void
     */
    public function deleted(Contractor $contractor)
    {
        //
    }

    /**
     * Handle the contractor "restored" event.
     *
     * @param  \App\Contractor  $contractor
     * @return void
     */
    public function restored(Contractor $contractor)
    {
        //
    }

    /**
     * Handle the contractor "force deleted" event.
     *
     * @param  \App\Contractor  $contractor
     * @return void
     */
    public function forceDeleted(Contractor $contractor)
    {
        //
    }
}
