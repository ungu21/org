<?php

namespace App\AppModel;

use Illuminate\Database\Eloquent\Model;

class VirtualAccountNumber extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }
    public function bank()
    {
        return $this->hasOne('App\AppModel\Bank','id','bank_id');
    }
    
}