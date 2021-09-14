<?php

namespace App\AppModel;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $guarded = ['id'];

    public function bank(){
        return $this->hasMany('App\AppModel\Bank');
    }
}
