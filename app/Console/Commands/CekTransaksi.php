<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\AppModel\Transaksi;
use Pulsa;
use App\AppModel\Tagihan;
use App\AppModel\Mutasi;
use App\AppModel\Setting;
use App\AppModel\Temptransaksi;
use App\AppModel\Kurs;
use App\User;
use DB;
use Carbon\Carbon;
use App\AppModel\Bonus;

class CekTransaksi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaksi:cek';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek Status Transaksi';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        
        DB::beginTransaction();
        //Cek expired tagihan
        $time = Carbon::now()->subHours(24)->toDateTimeString();
        $tagihan = Tagihan::take(50)->get();
        foreach($tagihan as $item){
            if($item->created_at <= $time){
                $item->status =3;
                $item->expired =0;
                $item->save();
            }
        }
        try {
            $temp_transaksiCount = Temptransaksi::count();
            if ($temp_transaksiCount != null) {
                $temp_transaksi = Temptransaksi::all();
                foreach($temp_transaksi as $tmp){
                   
                    $transaksis = Transaksi::where('order_id', $tmp->transaksi->order_id)->first();
                    
                    //Cek transaksi kadaluarsa
                    if($transaksis->created_at <= $time){
                        $transaksis->update([
                                'status'=>2,
                                'note'=>'Transaksi Kadaluarsa'
                            ]);
                        if(!empty($transaksis->tagihan_id)){
                            $tagihan = Tagihan::where('tagihan_id',$transaksis->tagihan_id)->update([
                                'status'=>3,
                                'expired'=>0
                                ]);
                        }
                        
                        $tmp->delete;
                    }
                    
                    $check = Pulsa::history_trx_detail($tmp->transaksi->order_id);
                    
                    $user = User::where('id',$transaksis->user_id)->first();
                    $hargaproduk = $transaksis->total;

                    if($check->success == true){
                        $data = $check->data;
                        if($data->trxid == $tmp->transaksi->order_id) {
                            if( $data->status == 1 ) {
                                $tmp->delete();
                                if( $transaksis->status == 0 && $transaksis->jenis_transaksi == 'otomatis')
                                {
                                    $transaksis->token  = $data->token;
                                    $transaksis->note   = $data->note;
                                    $transaksis->status = 1;
                                
                                    DB::table('temptransaksis')
                                    ->where('transaksi_id', $transaksis->order_id)
                                    ->delete();
                                    
                                     //PROSES BONUS REFERREAL
                                    if($user->referred_by != NULL)
                                    {
                                        $dataKomisi_ref       = Setting::settingsBonus(2) ;
                                        $ref_user             = $user->referred_by;
                                        $getDataRef           = User::where('id',$ref_user)->first();
                                        $sadlo_ref            = $getDataRef->saldo;
                                        $komisi_ref           = $dataKomisi_ref->komisi;
                                        $akumulasi_komisi_ref = $sadlo_ref  + $komisi_ref;
                                        
                                        $user_ref = User::where('id',$ref_user)->first();
                                        $user_ref->update([
                                            'saldo'=>$akumulasi_komisi_ref,
                                            ]);
                                      
                                        
                                        DB::table('mutasis_komisi')
                                            ->insert([
                                                'user_id'      => $getDataRef->id,
                                                'from_reff_id' => $user->id,
                                                'komisi'       => $komisi_ref,
                                                'jenis_komisi' => 2,
                                                'note'         => "Trx ".$data->code,
                                                'created_at'   => date('Y-m-d H:i:s'),
                                                'updated_at'   => date('Y-m-d H:i:s'),
                                                ]);

                                        $mutasiRewardReff = new Mutasi();
                                        $mutasiRewardReff->user_id = $getDataRef->id;
                                        $mutasiRewardReff->trxid = $transaksis->order_id;
                                        $mutasiRewardReff->type = 'credit';
                                        $mutasiRewardReff->nominal = $komisi_ref;
                                        $mutasiRewardReff->saldo  = $user_ref->saldo;
                                        $mutasiRewardReff->note  = "BONUS TRANSAKSI REFERRAL (".$user->name.", #".$transaksis->id.")";
                                        $mutasiRewardReff->save();
                                    }
                                }
                            } elseif( ($data->status == '2') || ($data->status == '3') ) {
                                $tmp->delete();
                                if( $transaksis->status == 0 ) {
                                    $sisaSaldoIdr = $user->saldo + $transaksis->total;
                                    $user->saldo = $sisaSaldoIdr;
                                    $transaksis->note = $data->note;
                                    $transaksis->saldo_after_trx = $transaksis->saldo_before_trx;
                                    $transaksis->status = 2;
                                    
                                    if(!empty($transaksis->tagihan_id)){
                                        $tagihan = Tagihan::where('tagihan_id',$transaksis->tagihan_id)->update([
                                            'status'=>3,
                                            'expired'=>0
                                        ]);   
                                    }
                                    
                                    $mutasi = new Mutasi();
                                    $mutasi->user_id = $user->id;
                                    $mutasi->trxid = $transaksis->id;
                                    $mutasi->type = 'credit';
                                    $mutasi->nominal = $transaksis->total;
                                    $mutasi->saldo = $sisaSaldoIdr;
                                    $mutasi->note  = (($data->mtrpln != '-') ? 'TRANSAKSI '.$data->code.' '.$data->mtrpln.' GAGAL' : 'TRANSAKSI '.$data->code.' '.$data->target.' GAGAL');
                                    $mutasi->save();
                                }
                            }
                        }
                    }
                    
                    $user->save();
                    $transaksis->save();
                }
            }
            DB::commit();   
            $this->info('Succesfully Cek All Transaction');
        } catch (\Exception $e) {
         \Log::error($e);
          DB::rollback();
          dd($e);
        }    
    }
}