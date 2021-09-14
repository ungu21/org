<?php

namespace ZerosDev\ZerosSMS;

use DB, Exception;
use Illuminate\Database\QueryException;

class ZerosSMS
{
    private $prefixs = [
        'indosat'   => ["+62856", "+62857", "+62855", "+62858", "+62814", "+62815", "+62816"],
        'telkomsel' => ["+62812", "+62853", "+62852", "+62822", "+62811", "+62813", "+62821", "+62823", "+62851"],
        'axis'      => ['+62831', '+62832', '+62838'],
        'smartfren' => ['+62881','+62882','+62883','+62884','+62885','+62886','+62887','+62888','+62889'],
        'tri'       => ['+62896','+62897','+62898','+62899','+62895'],
        'xl'        => ['+62817','+62818','+62819','+62859','+62877','+62878']
        ];
        
    private
        $to = false,
        $zenziva = [];
    
    public function __construct()
    {
        $this->zenziva = [
            'api_url'   => env('ZENZIVA_API_URL', ''), // Ganti Dengan Zenziva Api Url anda
            'userkey'   => env('ZENZIVA_USERKEY', ''), //Ganti dengan Zenziva UserKey Anda
            'passkey'  => env('ZENZIVA_PASSKEY', '') // Ganti Dengan Zenziva Passkey Anda
            ];
    }
    
    private function success($message = 'Success', $message_code = 0)
    {
        return json_encode([
                'success'       => true,
                'message'       => $message,
                'message_code'  => $message_code
            ]);
    }
    
    private function error($message = 'Error', $message_code = 0)
    {
        return json_encode([
                'success'       => false,
                'message'       => $message,
                'message_code'  => $message_code
            ]);
    }
    
    private function fixNumber($to)
    {
        # always use international phone code
        # here we use Indonesia (+62) as a valid number :)
        
        if( substr($to, 0, 3) == '+62' )
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
    
    public function to($to)
    {
        $this->to = $to;
        return $this;
    }
    
    public function local($text)
    {
        if( ($to = $this->fixNumber($this->to)) !== false )
        {
            $text = str_ireplace(['https://', 'http://', 'www.'], '', $text);
            
            try
            {
                DB::table('smsgateway_outbox')->insertGetId([
                            "sent_to"       => $to,
                            "message"       => $text,
                            "created_at"    => date("Y-m-d H:i:s"),
                            "updated_at"    => date("Y-m-d H:i:s")
                            ]);
                
                return $this->success();
            }
            catch(Exception $e)
            {
                if( $e instanceof QueryException ) {
                    report($e);
                }
                
                return $this->error($e->getMessage(), $e->getCode());
            }
        }
        
        return $this->error('Invalid Phone');
    }
    
    public function zenziva($text)
    {
        if( ($to = $this->fixNumber($this->to)) !== false )
        {
            $text = str_ireplace(['https://', 'http://', 'www.'], '', $text);
            
            try
            {
                $request = [
                    'userkey'       => $this->zenziva['userkey'],
                    'passkey'       => $this->zenziva['passkey'],
                    'nohp'          => $to,
                    'pesan'         => $text
                    ];
                    
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_FRESH_CONNECT       => true,
                    CURLOPT_URL                 => $this->zenziva['api_url'].'/smsapi.php?'.http_build_query($request),
                    CURLOPT_SSL_VERIFYHOST      => 0,
                    CURLOPT_SSL_VERIFYPEER      => false,
                    CURLOPT_RETURNTRANSFER      => true,
                    CURLOPT_HEADER              => false,
                    CURLOPT_CONNECTTIMEOUT      => 10,
                    CURLOPT_TIMEOUT             => (2*60),
                    CURLOPT_FAILONERROR         => true
                    ]);
                $response = curl_exec($ch);
                $errno = curl_errno($ch);
                $cerror = curl_error($ch);
                curl_close($ch);
                
                if( $errno ) {
                    throw new Exception($cerror, $errno);
                }
                
                $xmlObject = new \SimpleXMLElement($response);
                
                if( $xmlObject->message->status == '99' ) {
                    throw new Exception('Error', 99);
                }
                
                return $this->success();
            }
            catch(Exception $e)
            {
                if( $e instanceof QueryException ) {
                    report($e);
                }
                
                return $this->error($e->getMessage(), $e->getCode());
            }
        }
        
        return $this->error('Invalid Phone');
    }
}