<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'salutation', 'first_name', 'last_name', 'email', 'password', 'phone', 'passport', 'nationality', 'street_no', 'street_name', 'building_name', 'postal_code', 'alternative_contact_no', 'church_attended', 'religion_id', 'preferred_contact_by_id', 'is_tgor', 'display_address', 'display_name', 'unit_no', 'status', 'type', 'company_id',
       'display_address_2', 'remarks'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    Protected $guard_name ='api';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function religion()
    {
        return $this->belongsTo('App\Reference', 'religion_id', 'id');
    }
    public function preferredContactBy()
    {
        return $this->belongsTo('App\Reference', 'preferred_contact_by_id', 'id');
    }
    public function isTgor()
    {
        return $this->belongsTo('App\Reference', 'is_tgor', 'id');
    }
    public function salutation()
    {
        return $this->belongsTo('App\Reference', 'salutation', 'id');
    }
    public function adminStatus()
    {
        return $this->belongsTo('App\Reference', 'status', 'id');
    }
    public function preferred_contact_by(){
        return $this->belongsTo('App\Reference', 'preferred_contact_by_id', 'id');
    }
    public function salutation_name()
    {
        return $this->belongsTo('App\Reference', 'salutation', 'id');
    }
}
