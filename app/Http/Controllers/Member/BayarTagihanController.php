<?php

namespace App\Http\Controllers\Member;

use DB, Auth, Response, Validator,Log;
use App\AppModel\Pembayarankategori;
use App\AppModel\Pembayaranoperator;
use App\AppModel\Pembayaranproduk;
use App\AppModel\Antriantrx;
use App\AppModel\BlockPhone;
use App\AppModel\Transaksi;
use App\AppModel\Temptransaksi;
use App\AppModel\SMSGatewaySetting;
use App\AppModel\Mutasi;
use App\AppModel\Tagihan;
use App\AppModel\Setting;
use App\AppModel\SMSGateway;
use App\AppModel\Komisiref;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Exception;
use Pulsa;

class BayarTagihanController extends Controller
{
    //role user
    public $personal_role   = 1;
    public $admin_role      = 2;
    public $agen_role       = 3;
    public $enterprise_role = 4;
    
    public function __construct()
    {
        $this->settings = Setting::first();
    }
    public function bayartagihan(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'order_id'          => 'required',
        ]);
        
        if( $validate->fails() ) {
            return redirect()->back()->with('alert-error', 'Maaf terjadi kesalahan.');
        }

        if($this->settings->status == 0 && $this->settings->status_server == 0) {
            return redirect()->back()->with('alert-error', 'Sistem Sedang Maintenance, mohon kesabarannya menunggu.');
        }
        
        if( $this->settings->force_verification == 1 )
        {
            $verification = DB::table('users_validations')
                        ->select('*')
                        ->where('user_id', Auth::id())
                        ->first();
        
            if( !$verification )
            {
                return redirect()->back()->with('alert-error', 'Untuk melakukan transaksi ini, akun Anda harus terverifikasi, silahkan lakukan verifikasi <a href="/member/validasi-users" style="font-weight:bold;text-decoration:underline;">DISINI</a> .');
            }
            elseif( $verification->status != '1' )
            {
                return redirect()->back()->with('alert-error', 'Mohon maaf, verifikasi akun Anda masih dalam proses review. Anda belum dapat melakukan transaksi ini');
            }
        }
        
        $user = Auth::user();

        $tagihan = Tagihan::where('id', $request->order_id)->where('user_id', $user->id)->first();
        #jika tagihan ini bukan milik user
        if( !$tagihan ) {
            $message = 'Maaf ID Tagihan ini bukan milik Anda.';
            return redirect()->back()->with('alert-error', 'Maaf ID Tagihan ini bukan milik Anda.');
        }
 
        $userCek      = User::where('id', $user->id)->first();
        $cekTarget    = BlockPhone::where('phone', $tagihan->no_pelanggan)->first();
        $cekPhoneUser = BlockPhone::where('phone', $userCek->phone)->first();
        
        if($userCek->status == 0){
            return redirect()->back()->with('alert-error','Maaf akun anda dinonaktifkan');
        }
        if($userCek->status_saldo == 0){
            return redirect()->back()->with('alert-error','Maaf saldo anda dikunci oleh admin dan tidak bisa digunakan');
        }
        if( !is_null($cekTarget) || !is_null($cekPhoneUser) ) {
            return redirect()->back()->with('alert-error', 'No.Target termasuk nomor yang tercatat dalam daftar Blacklist Kami.');
        }

        if( $userCek->saldo <= $tagihan->jumlah_bayar ) { // jika saldo member tidak cukup
            return redirect()->back()->with('alert-error', 'Saldo Anda tidak mencukupi untuk melakukan transaksi ini, TOPUP saldo anda untuk dapat melakukan transaksi');
        }

        DB::beginTransaction();
          
        try
        {   
            $sisaSaldoIdr  = $user->saldo - $tagihan->jumlah_bayar;
            $userCek->saldo = $sisaSaldoIdr;
            $userCek->save();
            
            $bayarSukses                   = new Transaksi();
            $bayarSukses->apiserver_id     = $tagihan->apiserver_id;                
            $bayarSukses->order_id         = 0;
            $bayarSukses->tagihan_id       = $tagihan->tagihan_id;
            $bayarSukses->code             = "";
            $bayarSukses->produk           = "";
            $bayarSukses->harga_default    =  $tagihan->jumlah_tagihan;
            $bayarSukses->harga_markup     =  $tagihan->admin;
            $bayarSukses->total            =  $tagihan->jumlah_bayar;
            $bayarSukses->target           = "";
            $bayarSukses->mtrpln           = "";
            $bayarSukses->note             = "Initialize";
            $bayarSukses->pengirim         = $request->ip();
            $bayarSukses->status           = 0; // status proses
            $bayarSukses->user_id          = $userCek->id;
            $bayarSukses->via              = 'DIRECT';
            $bayarSukses->jenis_transaksi  = 'otomatis';
            $bayarSukses->saldo_before_trx = $userCek->saldo + $tagihan->jumlah_bayar;
            $bayarSukses->saldo_after_trx  = $userCek->saldo;
            $bayarSukses->save();
            
            $tagihan->status = 1; // status proses
            $tagihan->expired = 1;
            $tagihan->save();
            
            $mutasi          = new Mutasi();
            $mutasi->trxid   = $bayarSukses->id;
            $mutasi->user_id = $userCek->id;
            $mutasi->type    = 'debit';
            $mutasi->nominal = $tagihan->jumlah_bayar;
            $mutasi->saldo   = $userCek->saldo;
            $mutasi->note    = 'PEMBAYARAN TAGIHAN '.$tagihan->product_name.' '.$tagihan->no_pelanggan;
            $mutasi->save();
            
            $tagihan_id   = $tagihan->id;
            $transaksi_id = $bayarSukses->id;
            $mutasi_id    = $mutasi->id;
             //PROSES BONUS REFERREAL
             if($userCek->referred_by != NULL)
             {
                 $dataKomisi_ref       = Setting::settingsBonus(2) ;
                 $ref_user             = $userCek->referred_by;
                 $getDataRef           = User::where('id',$ref_user)->first();
                 $sadlo_ref            = $getDataRef->saldo;
             
                 $komisi_ref           = $dataKomisi_ref->komisi;
                 $akumulasi_komisi_ref = $sadlo_ref  + $komisi_ref;
           
                $user_ref =  DB::table('users')
                             ->where('id', $ref_user)
                             ->update([
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
                 $mutasiRewardReff->note  = "BONUS TRANSAKSI REFERRAL (".$user->name.", #".$bayarSukses->id.")";
                 $mutasiRewardReff->save();
             }
            
            $product = Pembayaranproduk::where('product_name', $tagihan->product_name)->first();
            
            $bayartagihan = Pulsa::trx_pembayaran($tagihan->tagihan_id);
            
            if( $bayartagihan->success != true ) {
                throw new \Exception($bayartagihan->message, 1);
            }
            
            $bayartagihan = $bayartagihan->data;
        
            $bayarSukses->order_id         = $bayartagihan->order_id;
            $bayarSukses->code             = $bayartagihan->code;
            $bayarSukses->produk           = $bayartagihan->produk;
            $bayarSukses->target           = $tagihan->phone;
            $bayarSukses->mtrpln           = $bayartagihan->mtrpln;
            $bayarSukses->note             = $bayartagihan->note;
            $bayarSukses->save();
        
            DB::commit();
            
            $request->session()->regenerateToken();
            
            return redirect()->to('/member/riwayat-transaksi/'.$bayarSukses->id);    
        }
        catch (\Exception $e)
        {
            Log::error($e);
            DB::rollback();
            
            if( $e->getCode() == '1' ) { // response error dari API
                return redirect()->back()->with('alert-error', $e->getMessage());
            }
            
            return redirect()->back()->with('alert-error', 'Please try again Error.[err-back]');
        }
    }
}
