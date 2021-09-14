<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Exception, Log;
use App\User;
use App\AppModel\Transaksi;
use App\AppModel\TempTransaksi;
use App\AppModel\Setting;
use App\AppModel\Mutasi;
use App\AppModel\SMSGateway;
use App\AppModel\SMSGatewaySetting;
use App\AppModel\Komisiref;
use App\AppModel\Tagihan;
use App\AppModel\Apiserver;

class SerpulWebhookController extends Controller
{
    public function listen(Request $request)
    {
        DB::beginTransaction();

        try{
            $apiserver = Apiserver::where('id',2)->first();
            $secret = $apiserver->api_secret;
            $incomingSecret = isset($_SERVER['HTTP_X_CALLBACK_SECRET']) ? $_SERVER['HTTP_X_CALLBACK_SECRET'] : '';
            
            if( !hash_equals($secret, $incomingSecret) ) {
                exit("Invalid secret");
            }

            $content = $request->getContent();
             

            $content = json_decode($content);

            $transaksi = Transaksi::where('order_id',$content->trxid)->where('status',0)->first();

            if(!$transaksi){
                throw new Exception('Transaction Not Found');
            }
            $tagihan = '';

            if(!empty($transaksi->tagihan_id)){
                $tagihan = Tagihan::where('id',$transaksi->tagihan_id)->first();
                
                if(!$tagihan){
                    throw new Exception('Tagihan Not Found');
                }
            }

            $user = User::where('id',$transaksi->user_id)->first();

            if(!$user){
                throw new Exception('User Not Found');
            }

            if($transaksi->status != 0){
                throw new Exception('Transaction Not Found');
            }

            if($content->status == 1){
                Temptransaksi::where('transaksi_id',$transaksi->id)->delete();

                $transaksi->token  = $content->token;
                $transaksi->note   = $content->note;
                $transaksi->status = 1;
                $transaksi->save();

                if(!empty($transaksi->tagihan_id)){
                    if($tagihan){
                        $tagihan->status  = 2;
                        $tagihan->expired = 0;
                        $tagihan->save();
                    }
                }

                if($user->referred_by != NULL)
                {
                    $dataKomisi_ref       = Setting::settingsBonus(2) ;
                    $ref_user             = $user->referred_by;
                    $getDataRef           = User::where('id',$ref_user)->first();
                    $sadlo_ref            = $getDataRef->saldo;
                
                    $komisi_ref           = $dataKomisi_ref->komisi;
                    $akumulasi_komisi_ref = $sadlo_ref  + $komisi_ref;
                    
                    $getDataRef->update([
                            'saldo'=>$akumulasi_komisi_ref
                        ]);
    
                        DB::table('mutasis_komisi')
                            ->insert([
                                'user_id'      => $getDataRef->id,
                                'from_reff_id' => $user->id,
                                'komisi'       => $komisi_ref,
                                'jenis_komisi' => 2,
                                'note'         => "Trx ".$content->note,
                                'created_at'   => date('Y-m-d H:i:s'),
                                'updated_at'   => date('Y-m-d H:i:s'),
                                ]);
    
                        $mutasiRewardReff = new Mutasi();
                        $mutasiRewardReff->user_id = $getDataRef->id;
                        $mutasiRewardReff->trxid = $transaksi->id;
                        $mutasiRewardReff->type = 'credit';
                        $mutasiRewardReff->nominal = $komisi_ref;
                        $mutasiRewardReff->saldo  = $getDataRef->saldo;
                        $mutasiRewardReff->note  = "BONUS TRANSAKSI REFERRAL (".$user->name.", #".$transaksi->id.")";
                        $mutasiRewardReff->save();
                }
                
                $enable_smsgateway = SMSGatewaySetting::where('name','enable')->first();
                $enable_smsbuyer   = SMSGatewaySetting::where('name','enable_sms_buyer')->first();

                if($enable_smsgateway->value == 1){
                    if($enable_smsbuyer->value == 1){
                        if(empty($tagihan)){
                            SMSGateway::send($transaksi->target, 'Pembelian '.$transaksi->produk.' ke '.$content->destination.' sukses. SN : '.$content->serial_number);
                        }else{
                            SMSGateway::send($tagihan->phone, 'BYR '.$tagihan->product_name.' '.$tagihan->no_pelanggan.' A/N '.$tagihan->nama.' Rp. '.$tagihan->jumlah_tagihan.' Adm Rp'.$tagihan->admin.' SUKSES Reff: '.$transaksi->token);
                        }
                    }
                }
                
                
            }elseif($content->status == 2){
                Temptransaksi::where('transaksi_id',$transaksi->id)->delete();

                if($transaksi->status == 0){

                    $user->refresh();
                    $sisaSaldo = $user->saldo + $transaksi->total;
                    $user->saldo = $sisaSaldo;
                    $user->save();

                    $transaksi->note            = $check->note;
                    $transaksi->saldo_after_trx = $transaksi->saldo_before_trx;
                    $transaksi->status          = 2;
                    $transaksi->save();
                    
                    $mutasi = new Mutasi();
                    $mutasi->user_id = $user->id;
                    $mutasi->trxid   = $transaksi->id;
                    $mutasi->type    = 'credit';
                    $mutasi->nominal = $transaksi->total;
                    $mutasi->saldo   = $sisaSaldo;
                    $mutasi->note    = $check->message;
                    $mutasi->save();  
                }
                
                $enable_smsgateway = SMSGatewaySetting::where('name','enable')->first();
                $enable_smsbuyer   = SMSGatewaySetting::where('name','enable_sms_buyer')->first();
                
                if($enable_smsgateway->value == 1){
                    if($enable_smsbuyer->value == 1){
                        if(empty($tagihan)){
                            SMSGateway::send($transaksi->target, 'Pembelian '.$transaksi->produk.' ke '.$content->target.' GAGAL. SN : '.$content->token);
                        }else{
                            SMSGateway::send($tagihan->phone, 'BYR '.$tagihan->product_name.' '.$tagihan->no_pelanggan.' A/N '.$tagihan->nama.' Rp. '.$tagihan->jumlah_tagihan.' Adm Rp'.$tagihan->admin.' GAGAL Reff: '.$transaksi->token);
                        }
                    }
                }
                
            }
            DB::commit();

        }catch(Exception $e){
            DB::rollback();
            \Log::error($e);
        }
    }
}
