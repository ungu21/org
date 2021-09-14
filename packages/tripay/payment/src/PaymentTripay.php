<?php
namespace Tripay\Payments;
use App\AppModel\Provider;

class PaymentTripay
{
    public function __construct()
    {
        $paymenttripay = Provider::where('name','PaymentTripay')->first();
        $this->apikey        = $paymenttripay->api_key;
        $this->private_key   = $paymenttripay->private_key;
        $this->merchant_code = $paymenttripay->merchant_code;
        $this->url_endpoint  = "https://tripay.co.id/api";  
    }
    
   
    public function curl($endpoint,$data)
    {
        $url = rtrim($this->url_endpoint,'/').'/'.ltrim($endpoint,'/');
       
        $header = [
            'Authorization: Bearer '.$this->apikey
        ];
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_FRESH_CONNECT,true);
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_HEADER,false);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
        curl_setopt($curl,CURLOPT_FAILONERROR,false);
        curl_setopt($curl,CURLOPT_POST,true);
        curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($data));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $errno  = curl_errno($curl);
        curl_close($curl);
        
        if($errno){
            return json_decode([
                'success'=>false,
                'message'=>$err,
                'connected'=>false,
            ]);
        }else{
            return $response;
        }
    }

    public function trx_close_payment($data = [])
    {
        $itemDetail = array([
            'name'=>'Invoice #'.$data['merchant_ref'].' '.$data['customer_name'],
            'price'=>$data['amount'],
            'quantity'=>1,
        ]);
        $data['order_items']  = $itemDetail;
        $data['expired_time'] = (time()+(01*60));
        $data['signature']    = hash_hmac('sha256',$this->merchant_code.$data['merchant_ref'].$data['amount'],$this->private_key);

        return $this->curl('/transaction/create',$data);
    }
    

    public function trx_open_payment($data =  [])
    {
        $data['signature'] = hash_hmac('sha256',$this->merchant_code.$data['method'].$data['merchant_ref'],$this->private_key);

        return $this->curl('/open-payment/create',$data);
    }

    public function instruction($data = [])
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_FRESH_CONNECT     => true,
        CURLOPT_URL               => "https://tripay.co.id/api/payment/instruction?".http_build_query($data),
        CURLOPT_RETURNTRANSFER    => true,
        CURLOPT_HEADER            => false,
        CURLOPT_HTTPHEADER        => array(
            "Authorization: Bearer ".$this->apikey
        ),
        CURLOPT_FAILONERROR       => false,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $errno  = curl_errno($curl);
        curl_close($curl);

        if($errno){
            return json_decode([
                'success'=>false,
                'message'=>$err,
                'connected'=>false,
            ]);
        }else{
            return $response;
        }
    }
}

?>