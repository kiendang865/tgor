<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachments extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'attachable_type', 'attachable_id', 'attachable_file_name', 'attachable_file_size', 'attachable_content_type', 'attachable_updated_at', 'attachable_name' 
    ];
    protected $appends = ['full_url'];
    public function attachable()
    {
        return $this->morphTo();
    }
    public function getFullUrlAttribute(){
        return url('attchments/').'/'.$this->attachable_name;
    }
}
