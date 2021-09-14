<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AppModel\Deposit;
use App\User;
use App\AppModel\Mutasi;
use App\AppModel\Provider;
use App\AppModel\SMSGateway;
use App\AppModel\Setting;
use App\AppModel\Bank;
use App\AppModel\Kurs;
use DB,Exception;


class PaymentTripayController extends Controller
{
    public function callbackPaymentTripay(Request $request)
    {
        $setting = Setting::first();
        $provider = Provider::where('id','1')->firstOrFail();
        $privateKey = $provider->private_key;
        \Log::info($privateKey);
        
        DB::beginTransaction();
        try
        {
            $json = $request->getContent();
            \Log::info($json);
          
            $callbackSignature = isset($_SERVER['HTTP_X_CALLBACK_SIGNATURE']) ? $_SERVER['HTTP_X_CALLBACK_SIGNATURE'] : '';
    
            $signature = hash_hmac('sha256',$json,$privateKey);
    
            if($callbackSignature !== $signature){
                exit("Invalid Signature");
            }
    
            $data = json_decode($json);
          
            $event = $_SERVER['HTTP_X_CALLBACK_EVENT'];
    
            if($event == 'payment_status')
            {   
                if($data->status == 'PAID')
                {
                    if($data->is_closed_payment == 1){
                        $deposit = Deposit::find($data->merchant_ref);
                    
                        if($deposit->status != 0){
                            throw new Exception("Status deposit: ".$deposit->status);
                        }
    
                        if(!$deposit){
                            throw new Exception('Deposit not found');
                        }
                        
                        $user = User::find($deposit->user_id);
                        if(!$user){
                            throw new Exception('User not found');
                        }
                       
                        $new_saldo = $user->saldo + $deposit->nominal;
                        $user->saldo = $new_saldo;
                        $user->save();

                        $note = 'Deposit sebesar Rp '.number_format($deposit->nominal, 0, '.', '.').' berhasil ditambahkan, saldo sekarang Rp '.number_format($user->saldo, 0, '.', '.').'.';
                        
                        $deposit->update([
                            'status'=>1,
                            'expire'=>0,
                            'note'=>$note
                        ]);
    
                        $mutasi = new Mutasi();
                        $mutasi->user_id = $user->id;
                        $mutasi->trxid = $deposit->id;
                        $mutasi->type = 'credit';
                        $mutasi->nominal = $deposit->nominal;
                        $mutasi->saldo = $new_saldo;
                        $mutasi->note = 'DEPOSIT/TOP-UP SALDO via '.$deposit->bank->nama_bank;
                        $mutasi->save();
    
                        DB::commit();
                        
                        $message = 'Yth. '.$user->name.', deposit Rp '.number_format($deposit->nominal, 0, '.', '.').' SUKSES via '.$deposit->bank->nama_bank.'. Saldo akhir: Rp '.number_format($user->saldo, 0, '.', '.').' ~ '.$setting->nama_sistem;
                        
                        SMSGateway::send($user->phone,$message);
                    }else{
                        $user = User::where('id',$data->merchant_ref)->first();
                        if(!$user){
                            throw new Exception('User Not Found');
                        }
                        $bank = Bank::where('code',$data->payment_method_code)->first();

                        $deposit = Deposit::create([
                            'bank_id'=>$bank->id,
                            'bank_kategori_id'=>$bank->bank_kategori_id,
                            'code_unik'=>0,
                            'nominal'=>$data->amount_received,
                            'nominal_trf'=>$data->total_amount,
                            'status'=>0,
                            'expire'=>1,
                            'user_id'=>$user->id,
                        ]);
                        
                        $nominal = floatval($deposit->nominal) - floatval($setting->deposit_fee);
    
                        $new_saldo = $user->saldo + $nominal;
                        $user->saldo = $new_saldo;
                        $user->save();

                        $note = 'Deposit sebesar Rp '.number_format($deposit->nominal, 0, '.', '.').' berhasil ditambahkan, saldo sekarang Rp '.number_format($user->saldo, 0, '.', '.').'.';

                        $deposit->update([
                            'status'=>1,
                            'expire'=>0,
                            'note'=>$note,
                        ]);

                        $mutasi = new Mutasi();
                        $mutasi->user_id = $user->id;
                        $mutasi->trxid = $deposit->id;
                        $mutasi->type = 'credit';
                        $mutasi->nominal = $deposit->nominal;
                        $mutasi->saldo = $user->saldo;
                        $mutasi->note = 'DEPOSIT/TOP-UP SALDO via '.$deposit->bank->nama_bank;
                        $mutasi->save();
                        
                        DB::commit();
                        
                        $message = 'Yth. '.$user->name.', deposit Rp '.number_format($deposit->nominal, 0, '.', '.').' SUKSES via '.$deposit->bank->nama_bank.'. Saldo akhir: Rp '.number_format($user->saldo, 0, '.', '.').' ~ '.$setting->nama_sistem;
                        
                        SMSGateway::send($user->phone,$message);
                    }
                }
                elseif($data->status == 'FAILED')
                {
                    if($data->is_closed_payment == 1){
                        $deposit = Deposit::find($data->merchant_ref);
                    
                        if(!$deposit){
                            throw new Exception('Deposit not found');
                        }
                        
                        $deposit->status = 2;
                        $deposit->expire = 0;
                        $deposit->note = $data->note;
                        $deposit->save();
                    }else{
                        $user = User::where('id',$data->merchant_ref)->first();
                        if(!$user){
                            throw new Exception('User Not Found');
                        }

                        Deposit::create([
                            'bank_id'=>$bank->id,
                            'bank_kategori_id'=>$bank->bank_kategori_id,
                            'code_unik'=>0,
                            'nominal'=>$data->amount_received,
                            'nominal_trf'=>$data->total_amount,
                            'status'=>2,
                            'expire'=>0,
                            'user_id'=>$user->id,
                            'note'=>$data->note
                        ]);
                    }
                    DB::commit();
                }
            }
           
            echo json_encode(['success'=>true]);
        }
        catch(\Exception $e)
        {
            \Log::error($e);
            DB::rollback();
            return $e->getMessage();
        }
      
    }
}
