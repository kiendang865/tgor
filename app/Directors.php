<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Directors extends Model
{
    use SoftDeletes;
    protected $table = 'companies';
    protected $fillable = [
        'company_name', 'bank_name', 'account_number', 'address', 'website', 'is_contractor', 'postal_code', 'uen_no', 'company_main_tel', 'remarks'
    ];
    public $timestamps = true;

}
