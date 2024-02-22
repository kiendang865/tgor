<?php

namespace App\Observers;

use App\User;

class UserObserver
{


    /**
     * Handle the user "creating" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function creating(User $user)
    {
        $user['display_name'] = $user['first_name'] ." ". $user['last_name'];
        $user['display_address'] =  $user['street_no'].' '. $user['street_name'].', '. $user['unit_no'].', '.$user['building_name'].' '.$user['postal_code'];
    }

    /**
     * Handle the user "created" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(User $user)
    {

    }

     /**
     * Handle the user "updating" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updating(User $user)
    {
        $user['display_name'] = $user['first_name'] ." ". $user['last_name'];
        $user['display_address'] =  $user['street_no'].' '. $user['street_name'].', '. $user['unit_no'].', '.$user['building_name'].' '.$user['postal_code'];
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
