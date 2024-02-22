<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Contractor extends Model
{
    use SoftDeletes;
    protected $table = 'companies';
    protected $fillable = [
        'company_name', 'bank_name', 'account_number', 'address', 'website', 'is_contractor', 'service_id', 'uen_no', 'company_main_tel', 'remarks', 'postal_code'
    ];
    public $timestamps = true;
    
    public function services() {
        return $this->belongsToMany('App\Other', 'contractor_has_service', 'contractor_id', 'service_id');
    }

}
