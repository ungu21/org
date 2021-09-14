<?php

namespace App\AppModel;

use App\AppModel\SMSGateway;
use Illuminate\Database\Eloquent\Model;
use DB;

class SendSMS extends Model
{
    protected $table = 'smsgateway_outbox';
    
    public static function send($to, $message)
    {
        $to = SMSGateway::fixNumber($to);
        $message = str_replace(['http://', 'https://', 'www.'], '', $message);
        $message = str_replace("~", "\r\n", $message);
        
        if( $to !== false )
        {
            $id = DB::table('smsgateway_outbox')->insertGetId([
                        "sent_to"       => $to,
                        "message"       => $message,
                        "created_at"    => date("Y-m-d H:i:s"),
                        "updated_at"    => date("Y-m-d H:i:s")
                        ]);
            
            return $id;
        }
        
        return false;
    }
}