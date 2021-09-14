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

class CekTagihanController extends Controller
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

    public function cektagihan(Request $request)
    {
        $produk = $request->input('produk');
       
        if($this->settings->status == 0 and $this->settings->status_server == 0) {
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
                return redirect()->back()->with('alert-error', 'Untuk melakukan transaksi, akun Anda harus terverifikasi, silahkan lakukan verifikasi <a href="/member/validasi-users" style="font-weight:bold;text-decoration:underline;">DISINI</a> .');
            }
            elseif( $verification->status != '1' )
            {
                return redirect()->back()->with('alert-error', 'Mohon maaf, verifikasi akun Anda masih dalam proses review. Anda belum dapat melakukan transaksi ini');
            }
        }
        
        $this->validate($request,[
            'produk'         => 'required',
            'nomor_rekening' => 'required',
            'target'         => 'required',
            'pin'            => 'required',
        ],[
            'produk.required'         => 'Produk tidak boleh kosong',
            'nomor_rekening.required' => 'Nomor Pelanggan tidak boleh kosong',
            'target.required'         => 'Nomor HP Pembeli tidak boleh kosong',
            'pin.required'            => 'PIN tidak boleh kosong',
        ]);

        $produk       = $request->produk;
        $no_pelanggan = $request->nomor_rekening;
        $phone        = $request->target;
        $pin          = $request->pin;

        $userCek      = User::where('id', Auth::user()->id)->first();
        $cekTarget    = BlockPhone::where('phone', $phone)->first();
        $cekPhoneUser = BlockPhone::where('phone', $userCek->phone)->first();
        
        if( !is_null($cekTarget) || !is_null($cekPhoneUser) )
        {
            return redirect()->back()->with('alert-error', 'No.Target termasuk nomor yang tercatat dalam daftar Blacklist Kami.');
        }
        elseif($userCek->status == 0)
        {
            return redirect()->back()->with('alert-error', 'Maaf, Akun anda di nonaktifkan!');
        }
        elseif( $userCek->pin != $request->pin )
        {
            return redirect()->back()->with('alert-error', 'Maaf, Pin anda salah!');
        }
        if($userCek->status_saldo == 0){
            return redirect()->back()->with('alert-error','Maaf saldo anda dikunci oleh admin dan tidak bisa digunakan');
        }
        
        DB::beginTransaction();
        
        try
        {
            $getPembayaranData = Pembayaranproduk::with('pembayarankategori')->where('code', $produk)->first();
            
            $cektagihanLokal = Tagihan::where(['no_pelanggan'=>$no_pelanggan,'code'=>$getPembayaranData->code,'product_name'=>$getPembayaranData->product_name])->where('status',0)->first();
            
            if($cektagihanLokal){
                return redirect()->back()->with('alert-error','Anda sudah melakukan pengecekan tagihan dengan No.pelanggan '.$no_pelanggan.' ('.ucwords($cektagihanLokal->nama).') ');
            }

            if( !$getPembayaranData ) {
                return redirect()->back()->with('alert-error', 'Maaf, produk sedang gangguan');
            }
            
            $tagihan = Tagihan::create([
                    'apiserver_id'  => $getPembayaranData->apiserver_id,
                    'code'          => $getPembayaranData->code,
                    'user_id'       => $userCek->id,
                    'phone'         => $phone,
                    'no_pelanggan'  => $no_pelanggan,
                    'via'           => 'DIRECT',
                    'product_name'   => $getPembayaranData->product_name,
                ]);
            
            $cekTagihan = Pulsa::cek_tagihan($request->produk,$request->target,$request->nomor_rekening);
            
            $cekSaldo = Pulsa::cek_saldo();
          
            $kategori = strtoupper($getPembayaranData->pembayarankategori->product_name);
            
            if( $cekTagihan->success == true )
            {
                $cekTagihan = $cekTagihan->data;
                
                if($cekSaldo->data >= $cekTagihan->jumlah_bayar)
                { 
                    $tagihan->update([
                       'tagihan_id'     => $cekTagihan->tagihan_id,
                       'no_pelanggan'   => $cekTagihan->no_pelanggan,
                       'nama'           => ucwords($cekTagihan->nama),
                       'periode'        => $cekTagihan->periode,
                       'jumlah_tagihan' => $cekTagihan->jumlah_bayar,
                       'admin'          => $getPembayaranData->price_markup,
                       'jumlah_bayar'   => ($cekTagihan->jumlah_bayar + $getPembayaranData->price_markup),
                    ]);   
                }
                else
                {
                    return redirect()->back()->with('alert-error','Sistem Pembayaran Error, mohon laporkan admin supaya bisa segera ditangani.Terima kasih');
                }
            }
            else
            {
                return redirect()->back()->with('alert-error', 'Cek Pembayaran gagal, silahkan coba kembali');
            }
            
            DB::commit();
            
            $request->session()->regenerateToken();
            
            return redirect()->to('/member/tagihan-pembayaran/'.$tagihan->id);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error($e);
            if( $e->getCode() == '1' ) {
                return redirect()->back()->with('alert-error', $e->getMessage());
            }
            
            return redirect()->back()->with('alert-error', 'Cek pembayaran gagal, silahkan coba kembali [err-back]');
        }
    }
    
    public function cektagihanhome(Request $request)
    {
        $produk = $request->input('produk');
       
        if($this->settings->status == 0 && $this->settings->status_server == 0){
            return Response::json([ 'success' => false, 'message' => 'Sistem Sedang Maintenance, mohon kesabarannya menunggu.']);
        }
        
        if( $this->settings->force_verification == 1 )
        {
            $verification = DB::table('users_validations')
                        ->select('*')
                        ->where('user_id', Auth::id())
                        ->first();
        
            if( !$verification )
            {
                return Response::json([ 'success' => false, 'message' => 'Untuk melakukan transaksi ini, akun Anda harus terverifikasi, silahkan lakukan verifikasi di menu Validasi User']);
            }
            elseif( $verification->status != '1' )
            {
                return Response::json([ 'success' => false, 'message' => 'Mohon maaf, verifikasi akun Anda masih dalam proses review. Anda belum dapat melakukan transaksi ini']);
            }
        }
        
        $this->validate($request,[
            'produk'         => 'required',
            'nomor_rekening' => 'required',
            'target'         => 'required',
            'pin'            => 'required',
        ],[
            'produk.required'         => 'Produk tidak boleh kosong',
            'nomor_rekening.required' => 'Nomor Pelanggan tidak boleh kosong',
            'target.required'         => 'Nomor HP Pembeli tidak boleh kosong',
            'pin.required'            => 'PIN tidak boleh kosong',
        ]);

        $produk       = $request->produk;
        $no_pelanggan = $request->nomor_rekening;
        $phone        = $request->target;
        $pin          = $request->pin;

        $userCek      = User::where('id',Auth::user()->id)->first();
        $cekTarget    = BlockPhone::where('phone', $phone)->first();
        $cekPhoneUser = BlockPhone::where('phone',$userCek->phone)->first();
        if( !is_null($cekTarget) || !is_null($cekPhoneUser) ) {
            return Response::json([ 'success' => false, 'message' => 'No.Target termasuk nomor yang tercatat dalam daftar Blacklist Kami.']);
        }

        if($userCek->status == 0)
        {
            return Response::json([ 'success' => false, 'message' => 'Maaf, Akun anda di nonaktifkan!']);
        }
        
        if($userCek->status_saldo == 0){
            return Response::json(['success'=>false,'message'=>'Maaf, saldo anda dikunci oleh admin dan tidak bisa digunakan']);
        }
        if( $userCek->pin != $request->pin )
        {
            return Response::json([ 'success' => false, 'message' => 'Maaf, Pin anda salah!']);
        }

        DB::beginTransaction();
        
        try
        {
            $getPembayaranData = Pembayaranproduk::with('pembayarankategori')->where('code', $produk)->first();
            
            if( !$getPembayaranData ) {
                return Response::json([ 'success' => false, 'message' => 'Maaf, produk sedang gangguan']);
            }

            $tagihan = Tagihan::create([
                    'apiserver_id'   => $getPembayaranData->apiserver_id,
                    'user_id'       =>$userCek->id,
                    'phone'         =>$phone,
                    'no_pelanggan'  =>$no_pelanggan,
                    'via'           =>'DIRECT',
                    'product_name'   => $getPembayaranData->product_name,
                ]);
            
            $cekTagihan = Pulsa::cek_tagihan($request->produk,$request->target,$request->nomor_rekening);
       
            $cekSaldo = Pulsa::cek_saldo();
         
            $kategori = strtoupper($getPembayaranData->pembayarankategori->product_name);
            
            if($cekTagihan->success == true)
            {
                $cekTagihan = $cekTagihan->data;
                if($cekSaldo->data >= $cekTagihan->jumlah_bayar){
                    $tagihan->update([
                       'tagihan_id'     => $cekTagihan->tagihan_id,
                       'no_pelanggan'   => $cekTagihan->no_pelanggan,
                       'nama'           => ucwords($cekTagihan->nama),
                       'periode'        => $cekTagihan->periode,
                       'jumlah_tagihan' => $cekTagihan->jumlah_bayar,
                       'admin'          => $getPembayaranData->price_markup,
                       'jumlah_bayar'   => ($cekTagihan->jumlah_bayar + $getPembayaranData->price_markup),
                    ]);
                }else{
                  return Response::json(['success'=>false, 'message'=>'Sistem Pembayaran Error, mohon laporkan admin supaya bisa segera ditangani.Terima kasih']);
                }
            }else{
                return Response::json(['success'=>false, 'message'=>'Sistem Pembayaran Error, mohon laporkan admin supaya bisa segera ditangani.Terima kasih']);
            }
            
            DB::commit();
            
            $tagihan->token = csrf_token();
            return Response::json($tagihan);
            
        }
        catch (\Exception $e)
        {
            DB::rollback();
            Log::error($e);
            return Response::json([ 'success' => false, 'message' => 'Cek pembayaran gagal, silahkan coba kembali.[err-back]']);
        }
    }
    
  
}
