<?php

namespace App\AppModel;

use Illuminate\Database\Eloquent\Model;

class Apiserver extends Model
{
    protected $table='apiserver';
    protected $primaryKey='id';
    
    protected $fillable = ['*'];
    protected $hidden = ['endpoint','api_userid','api_key','api_secret','pin','saldo'];

}