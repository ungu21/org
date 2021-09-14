<?php

namespace App\AppModel;

use stdClass, SimpleXMLElement;
use Illuminate\Database\Eloquent\Model;
use App\AppModel\SMSGatewaySetting;
use App\AppModel\SMSGatewayOutbox;

class SMSGateway extends Model
{
    public static function fixNumber($to)
    {
        # always use international phone code
        # here we use Indonesia (+62) as a valid number :)
        
        if( substr($to, 0, 3) === '+62' )
        {
            # this is valid number
            # no further action is required
        }
        elseif( substr($to, 0, 3) == "628" )
        {
            $to = '+'.$to;
        }
        elseif( substr($to, 0, 2) == "08" )
        {
            $to = '+62'.substr($to, 1);
        }
        else
        {
            $to = false;
        }
        
        if( strlen($to) < 10 )
        {
            $to = false;
        }
        
        return $to;
    }
    
    public static function send($to, $message)
    {
        $to = self::fixNumber($to);
        $message = str_replace(['http://', 'https://', 'www.'], '', $message);
        $message = str_replace("~", "\r\n", $message);
        
        # default param
        $return = new stdClass();
        $return->success = false;
        $return->message = "Uninitialized";
        $return->balance = 0;
        $return->log_id = null;
        $dbStatus = 'pending';

        if( $to !== false )
        {
            $getSetting = SMSGatewaySetting::all();
            $setting = [];
            
            foreach($getSetting as $s)
            {
                $n = $s->name;
                $v = $s->value;
                
                $setting[$n] = $v;
            }
            
            if( @$setting['enable'] == '1' )
            {
                $userkey = isset($setting['zenziva_userkey']) ? $setting['zenziva_userkey'] : '';
                $passkey = isset($setting['zenziva_passkey']) ? $setting['zenziva_passkey'] : '';
                
                $ch = curl_init();
                
                curl_setopt($ch, CURLOPT_URL, 'https://console.zenziva.net/reguler/api/sendsms/');
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT,30);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                    'userkey' => $userkey,
                    'passkey' => $passkey,
                    'to' => $to,
                    'message' => $message
                ));
                $result = curl_exec($ch);
               
                $result = json_decode($result);
                $errno = curl_errno($ch);
                $error = curl_error($ch);
              
                curl_close($ch);
                
                if( $errno )
                {
                    $return->message = $error;
                }
                else
                {
                    $status = $result->status;
                    $text   = $result->text;
                    $balance = $result->cost;
                    if( $status == '1' )
                    {
                        $return->success = true;
                        $dbStatus = 'sent';
                    }
                    else
                    {
                        $dbStatus = 'failed';
                    }
                    
                    $return->message = (String) $text;
                    $return->balance = (int) $balance;
                    
                    if( @$setting['log_db'] == '1' )
                    {
                        $log = new SMSGatewayOutbox;
                        $log->sent_to = $to;
                        $log->message = $message;
                        $log->status = $dbStatus;
                        $log->note = $return->message;
                        $log->save();
                        
                        $return->log_id = $log->id;
                    }
                }
            }
            else
            {
                $return->message = 'SMS Gateway is disabled';
            }
        }
        else
        {
            $return->message = 'Invalid destination number';
        }
        
        return $return;
    }
    
    public static function setting()
    {
        $return = new \stdClass();
        
        foreach(SMSGatewaySetting::get() as $s)
        {
            $return->{$s->name} = $s->value;
        }
        
        return $return;
    }
}