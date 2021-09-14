<?php

namespace App\Http\Controllers\Member;

use Auth, Response, Validator;
use App\User;
use App\AppModel\Mutasi;
use App\AppModel\Setting;
use App\AppModel\MenuSubmenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\AppModel\BlockPhone;
use DB;
use Log;

class TransferSaldoController extends Controller
{
    public function __construct()
    {
        $this->settings = Setting::first();
    }
    public function transferSaldo()
    {
        $URL_uri = request()->segment(1).'/'.request()->segment(2);
        $datasubmenu2 = MenuSubmenu::getSubMenuOneMemberURL($URL_uri)->first();
        $setting = $this->settings;
    
        if($datasubmenu2->status_sub != 0 )
        {
            return view('member.deposit.transfer-saldo.index',compact('setting'));
        }
        else
        {
            abort(404);
        }
    }
    
    public function cekNomor(Request $request)
    {
        $rules = array (
            'no_tujuan' => 'required',
        );
        
        $validator = Validator::make ($request->all(), $rules );
        
        if ($validator->fails ())
        {
            return Response::json ( array (
                    'errors' => $validator->getMessageBag ()->toArray () 
            ) );
        }
        else
        {
            $user = User::where('phone', $request->no_tujuan)->first();
            return Response::json($user);
        }
    }
    
    public function kirimSaldo(Request $request)
    {
        if( $this->settings->status == 0 ) {
            return redirect()->back()->with('alert-error', 'Sistem Sedang Maintenance, mohon kesabarannya menunggu.');
        }
        
        $this->validate($request, [
            'no_tujuan' => 'required',
            'nominal' => 'required',
            'password' => 'required|passcheck:' . Auth::user()->password,
        ],[
            'no_tujuan.required' => 'Nomor Handphone Tujuan Transfer tidak boleh kosong.',
            'nominal.required' => 'Nominal Transfer tidak boleh kosong.',
            'nominal.regex'=>'Nominal yang anda masukkan tidak valid',
            'password.required' => 'Kata Sandi tidak boleh kosong.',
            'password.passcheck' => 'Kata Sandi tidak cocok, periksa kembali kata sandi anda.',
        ]);
        
        if( $this->settings->force_verification == 1 )
        {
            $verification = DB::table('users_validations')
                        ->select('*')
                        ->where('user_id', auth()->user()->id)
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
        
        $nominal = str_replace('.','',$request->nominal);
        
        if( $request->no_tujuan != Auth::user()->phone )
        {
            $penerima = User::where('phone', $request->no_tujuan)->first();
            
            if( !empty($penerima) )
            {
                $saldo = Auth::user()->saldo;    
        
                $minim_saldo    = $this->settings->min_saldo_user;
                $minim_transfer = $this->settings->min_nominal_transfer;

                if($saldo >= $minim_saldo)
                {
                    if( $nominal >= $minim_transfer )
                    {
                        // Kurang Saldo Pengirim
                        $pengirim = Auth::user();
                       
                        // Tambah Saldo Penerima
                       
                        $sisaSaldoPengirim = $pengirim->saldo - $nominal;
                        $sisaSaldoPenerima = $penerima->saldo + $nominal;
                        $pengirim->saldo = $sisaSaldoPengirim;
                        $penerima->saldo = $sisaSaldoPenerima;
                        $pengirim->save();
                        $penerima->save();
                        
                        // Mutasi Saldo Pengirim
                        $mutasiPengirim = new Mutasi();
                        $mutasiPengirim->user_id = $pengirim->id;
                        $mutasiPengirim->type = 'debit';
                        $mutasiPengirim->nominal = $nominal;
                        $mutasiPengirim->saldo  = $sisaSaldoPengirim;
                        $mutasiPengirim->note  = 'TRANSFER SALDO KE '.$request->no_tujuan.' BERHASIL';
                        $mutasiPengirim->save();
                        
                        // Mutasi Saldo Penerima
                        $mutasiPenerima = new Mutasi();
                        $mutasiPenerima->user_id = $penerima->id;
                        $mutasiPenerima->type = 'credit';
                        $mutasiPenerima->nominal = $nominal;
                        $mutasiPenerima->saldo  = $sisaSaldoPenerima;
                        $mutasiPenerima->note  = 'SALDO TRANSFER DARI '.$pengirim->phone;
                        $mutasiPenerima->save();
  
                        return redirect()->back()->with('alert-success', 'Transfer Saldo Berhasil, Saldo penerima telah di tambahkan.');
                    }
                    else
                    {
                        return redirect()->back()->with('alert-error','Transfer Saldo Gagal, minimal nominal saldo yang anda dapat transfer adalah Rp. '.number_format($minim_transfer,0,'.','.'));
                    }
                }
                else
                {
                    return redirect()->back()->with('alert-error', 'Transfer Saldo Gagal, anda harus memiliki minimal saldo Rp. '.number_format($minim_saldo,0,'.','.').' untuk dapat melakukan transfer saldo.');    
                }
            }
            else
            {
                return redirect()->back()->with('alert-error', 'Nomor Handphone tujuan transfer tidak ditemukan, periksa kembali nomor handphone tujuan anda.');
            }
        }
        else
        {
            return redirect()->back()->with('alert-error', 'Anda tidak dapat melakukan transfer saldo ke akun anda sendiri.');
        }
    }
}
