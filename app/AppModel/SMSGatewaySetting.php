<?php

namespace App\AppModel;

use stdClass, SimpleXMLElement;
use Illuminate\Database\Eloquent\Model;

class SMSGatewaySetting extends Model
{
    protected $table = 'smsgateway_settings';
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'name';
    public $incrementing = false;
}