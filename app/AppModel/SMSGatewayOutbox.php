<?php

namespace App\AppModel;

use Illuminate\Database\Eloquent\Model;

class SMSGatewayOutbox extends Model
{
    protected $table = "smsgateway_outbox";
}