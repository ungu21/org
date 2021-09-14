<?php

namespace App\Http\Controllers\Member;

use Response, Auth, Validator, Input, Notif,Log;
use App\Jobs\QueueTransaksi;
use App\Jobs\QueuePembelian;
use App\User;
use App\AppModel\Antriantrx;
use App\AppModel\Pembeliankategori;
use App\AppModel\Pembelianoperator;
use App\AppModel\Pembelianproduk;
use App\AppModel\V_pembelianproduk_personal as Personal;
use App\AppModel\V_pembelianproduk_agen as Agen;
use App\AppModel\V_pembelianproduk_enterprise as Enterprise;
use App\AppModel\Transaksi;
use App\AppModel\Temptransaksi;
use App\AppModel\Setting;
use App\AppModel\Mutasi;
use App\AppModel\Kurs;
use App\AppModel\MenuSubmenu;
use App\AppModel\SMSGateway;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AppModel\BlockPhone;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Uuid;

class OrderPrabayarController extends Controller
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
    public function orderproduct(Request $request)
    {
        sleep(1);
    
        if (!$request->isMethod('post')) {
            return redirect()->back()->with('alert-error', 'Transaksi tidak diproses. silahkan ulangi.[err01].');
        }

        if( (date("G") >= 23) && (intval(date("i")) >= 30) )
        {
            return redirect()->back()->with('alert-error', 'Transaksi tidak diproses. Sedang maintenance harian. Silahkan ulangi setelah pukul 00:00 WIB.');
        }

        if($this->settings->status == 0 || $this->settings->status_server == 0){
            return redirect()->back()->with('alert-error', 'Sistem Sedang Maintenance, mohon kesabarannya menunggu.');
        }
        
        if( $this->settings->force_verification == 1  )
        {
            $user = User::where('id',auth()->user()->id)->first();
            
            if(!$user){
                return redirect()->back()->with('alert-error','User tidak ditemukan');
            }
            $verification = DB::table('users_validations')
                        ->select('*')
                        ->where('user_id', auth()->user()->id)
                        ->first();
           
            if( !$verification)
            {
                return redirect()->back()->with('alert-error', 'Untuk melakukan transaksi ini, akun Anda harus terverifikasi, silahkan lakukan verifikasi <a href="/member/validasi-users" style="font-weight:bold;text-decoration:underline;">DISINI</a> .');
            }
            elseif( $verification->status != '1')
            {
                return redirect()->back()->with('alert-error', 'Mohon maaf, verifikasi akun Anda masih dalam proses review. Anda belum dapat melakukan transaksi ini');
            }   
            
        }

        $v = Validator::make($request->all(), [
            'produk' => 'required',
            'target' => 'required',
            'pin'    => 'required',
        ],[
            'produk.required' => 'Produk tidak boleh kosong',
            'target.required' => 'Nomor Handphone / Rekening Pengisian tidak boleh kosong',
            'pin.required'    => 'PIN tidak boleh kosong',
        ]);
        
        if( $v->fails() ) {
            return redirect()->back()->with('alert-error', $v->errors()->first());
        }
            
        $userCek        = User::where('id', Auth::user()->id)->first();
        $cekTarget      = BlockPhone::where('phone',trim(isset($request->target) ? $request->target:$request->phone))->first();
        $cekPhoneUser   = BlockPhone::where('phone',trim($userCek->phone))->first();
      
        
        if($userCek->status == 0) {
          return redirect()->back()->with('alert-error', 'Maaf, Akun anda di nonaktifkan!');
        }
        
        if($userCek->status_saldo == 0){
            return redirect()->back()->with('alert-error','Maaf Saldo Anda dikunci oleh admin dan tidak bisa digunakan');
        }

        if( !is_null($cekTarget) || !is_null($cekPhoneUser) ) {
            return redirect()->back()->with('alert-error', 'No.Target termasuk nomor yang tercatat dalam daftar Blacklist Kami.');
        }

        if( $userCek->pin != $request->pin ){
            return redirect()->back()->with('alert-error', 'Maaf, Pin anda salah!');
        }

        // Deklarasi Variable ====================================
        
        $nameTableTemp   = Uuid::generate(5, strtotime("now"), Uuid::NS_DNS);
        $createTemporary = Schema::create($nameTableTemp, function (Blueprint $table) {
          $table->engine = 'InnoDB';
          $table->increments('id');
          $table->string('apiserver_id')->nullable();
          $table->string('type')->nullable();
          $table->string('produk');
          $table->string('target');
          $table->string('no_meter_pln')->nullable();
          $table->timestamps();
          $table->temporary();
        });

        if (Schema::hasTable($nameTableTemp))
        {
            Schema::drop($nameTableTemp);
            return redirect()->back()->with('alert-error', 'Transaksi Tidak Diproses. Ulangi transaksi[err-temp01].');
        }
        
        #insert ke table temporary. jika paramnya member tidak ada maka memasukkan pararam dari API
        $cekcode = Pembelianproduk::where(['product_id'=>$request->produk])->first();
        $kategori_produk = Pembeliankategori::where('id',$cekcode->pembeliankategori_id)->first();

        if(in_array($kategori_produk->type,['REGULER','TRANSFER','INTERNET'])){
            $phone          = $this->fixphone($request->target);
        }else{
            $phone          = $request->target;
        }
         
        if(empty($cekcode)){
            return redirect()->back()->with('alert-error','Code Produk tidak ditemukan!');
        }
            
        DB::table($nameTableTemp)
                ->insert([
                  'apiserver_id'    => $cekcode->apiserver_id,
                  'type'            => $request->type,
                  'produk'          => $request->produk,
                  'target'          => $phone,
                  'no_meter_pln'    => (!isset($request->no_meter_pln) ? NULL : (($request->no_meter_pln != null && $request->no_meter_pln != '') ? $request->no_meter_pln : NULL)),
                ]);

        $check_temp = DB::table($nameTableTemp)
              ->where('id', '1')
              ->first();
       
        $apiserver_id   = $check_temp->apiserver_id;
        $type           = $check_temp->type;
        $product_id     = $check_temp->produk;
        $target         = $check_temp->target;
        $no_meter_pln   = $check_temp->no_meter_pln;
        $drob_temp      = Schema::drop($nameTableTemp);
        $produk         = produkMembershipAuth($product_id);
      
        if( !$produk ) {
            return redirect()->back()->with('alert-error', 'Produk tidak ditemukan!');
        }
        
         if($produk->status !=1){
            return redirect()->back()->with('alert-error','Produk sedang gangguan');
        }

        $user           = User::find(Auth::id());
        
        $ip_address     = $request->ip();
        
        $transaksi      = Transaksi::where('code', $product_id)->where('target', $target)->whereIn('status', [0, 1])->whereDate('created_at', '=', date('Y-m-d'))->first();
        $queue          = Antriantrx::where('code', $product_id)->where('target', $target)->where('status', 0)->whereDate('created_at', '=', date('Y-m-d'))->first();
        
        $currentBalance = $user->saldo;
        $currentBalance = is_numeric($currentBalance) ? $currentBalance : 0;

        if( $currentBalance < $produk->price )
        {
            return redirect()->back()->with('alert-error', 'Transaksi Tidak Diproses. Saldo tidak cukup untuk transaksi ini, silahkan isi saldo anda terlebih dahulu.');
        }
        elseif( !empty($queue) )
        {
            return redirect()->back()->with('alert-error', 'Transaksi '.$produk->product_name.' '.$target.' sudah pernah dilakukan pada '.strftime("%d %b %Y %H:%M", strtotime($queue->created_at)).' status DALAM ANTRIAN');
        }
        // elseif( !empty($transaksi) )
        // {
        //     return redirect()->back()->with('alert-error', 'Transaksi '.$produk->product_name.' '.$target.' sudah pernah dilakukan pada '.strftime("%d %b %Y %H:%M", strtotime($transaksi->created_at)).'. Mohon gunakan nominal atau nomor tujuan yang berbeda');
        // }
        else
        {
            // get last trx
            $ltx = Antriantrx::where('code', $product_id)->where('target', $target)->where('created_at', '>=', Carbon::now()->subMinutes(5)->toDateTimeString())->count();
            if( $ltx > 0 ) {
                return redirect()->back()->with('alert-error', 'Mohon tunggu 5 menit sebelum mengulai transaksi yang sama atau cek riwayat transaksi Anda');
            }
            
            DB::beginTransaction();
            
            try
            {
                $user = User::find($user->id);
               
                if( !$user ) {
                    throw new \Exception("Gagal");
                }
                
                $sisaSaldoIdr = $user->saldo - $produk->price; 
                $user->saldo = $sisaSaldoIdr;
                $user->save();
          
                // Proses Transaksi
                $antrian                  = new Antriantrx();
                $antrian->apiserver_id    = $apiserver_id;
                $antrian->code            = $product_id;
                $antrian->produk          = $produk->product_name;
                $antrian->harga_default   = $produk->price_default;
                $antrian->harga_markup    = $produk->price_markup;
                $antrian->target          = $target;
                $antrian->via             = 'DIRECT';
              
                if (!empty($no_meter_pln)) {
                    $antrian->mtrpln = $no_meter_pln;
                }

                $antrian->note    = "Transaksi dalam antrian.";
                $antrian->status  = 0;  // Status Proses
            
                $mutasi           = new Mutasi();
                $mutasi->user_id  = $user->id;
                $mutasi->type     = 'debit';
                $mutasi->nominal  = $produk->price;
                $mutasi->saldo    = $user->saldo;
                $mutasi->note     = !empty($no_meter_pln) ? 'TRANSAKSI '.$produk->product_name.' '.$no_meter_pln : 'TRANSAKSI '.$produk->product_name.' '.$target;
                $mutasi->save();

                $antrian->pengirim = $ip_address;
                $antrian->user_id = $user->id; 
                $antrian->save();
                
                DB::commit();
              
                $checkantrian = Antriantrx::findOrFail($antrian->id);
              
                if($checkantrian->status == 0) {

                    $antrian_id      = $antrian->id;
                    $mutasi_id       = $mutasi->id;
                    $code            = $produk->product_id;
                    $via             = 'DIRECT';
                    $jenis_transaksi = 'otomatis';
                  
                    dispatch_now(new QueuePembelian($apiserver_id,$produk, $type, $code, $target, $no_meter_pln, $user, $ip_address, $antrian_id, $mutasi_id, $via, $jenis_transaksi));
                }
                
                $request->session()->regenerateToken();
              
                return redirect()->back()->with('alert-success', 'Pembelian anda telah diantrikan. Silahkan Lihat Di <a href="'.url('/member/riwayat-transaksi').'" style="font-weight:bold;text-decoration:underline;">RIWAYAT TRANSAKSI</a> untuk melihat detail pembelian. Tuliskan pengalaman bertransaksi anda bersama kami <a href="'.url('/member/testimonial').'" style="font-weight:bold;text-decoration:underline;">KIRIM TESTIMONIAL</a>.');
            }
            catch (\Exception $e)
            {
                DB::rollback();
                Log::error($e);
                return redirect()->back()->with('alert-error', $e->getMessage());
            }
        }
    }
    
    public function orderproducthome(Request $request)
    {
            sleep(1);
            
            if (!$request->isMethod('post')) {
                return Response::json(['success' => false ,'message' => 'Transaksi tidak diproses. silahkan ulangi.[err01].']);
            }

            if( (date("G") >= 23) && (intval(date("i")) >= 30) )
            {
                return Response::json(['success' => false ,'message' => 'Transaksi tidak diproses. Sedang maintenance harian. Silahkan ulangi setelah pukul 00:00 WIB.']);
            }

            if($this->settings->status == 0 || $this->settings->status_server == 0){
                return Response::json(['success' => false ,'message' => 'Sistem Sedang Maintenance, mohon kesabarannya menunggu.']);
            }
            
            if( $this->settings->force_verification == 1 )
            {
                $user = User::where('id',auth()->user()->id)->first();
                
                if(!$user){
                    return redirect()->back()->with('alert-error','User tidak ditemukan');
                }
                
                $verification = DB::table('users_validations')
                            ->select('*')
                            ->where('user_id', auth()->user()->id)
                            ->first();
            
             
                if(!$verification)
                {
                    return Response::json([ 'success' => false, 'message' => 'Untuk melakukan transaksi ini, akun Anda harus terverifikasi, silahkan lakukan verifikasi di menu Validasi User']);
                }
                elseif( $verification->status != '1')
                {
                    return Response::json([ 'success' => false, 'message' => 'Mohon maaf, verifikasi akun Anda masih dalam proses review. Anda belum dapat melakukan transaksi ini']);
                }
                
            }

            $v = Validator::make($request->all(), [
                'produk' => 'required',
                'target' => 'required',
                'pin'    => 'required',
            ],[
                'produk.required' => 'Produk tidak boleh kosong',
                'target.required' => 'Nomor Handphone / Rekening Pengisian tidak boleh kosong',
                'pin.required'    => 'PIN tidak boleh kosong',
            ]);
            
            if( $v->fails() ) {
                return Response::json([ 'success' => false, 'message' => $v->errors()->first()]);
            }
            
            $userCek  = User::where('id',Auth::user()->id)->first();
          
            $cekTarget    = BlockPhone::where('phone',trim(isset($request->target)?$request->target:$request->phone))->first();
            $cekPhoneUser = BlockPhone::where('phone',trim($userCek->phone))->first();
            
            if($userCek->status == 0) {
              return Response::json(['success' => false ,'message' => 'Maaf, Akun anda di nonaktifkan!']);
            }
            
            if($userCek->status_saldo == 0){
                return Response::json(['success'=>false,'message'=>'Maaf, saldo dikunci oleh admin dan tidak bisa digunakan']);
            }

            if( !is_null($cekTarget) || !is_null($cekPhoneUser) ){
                return Response::json(['success' => false ,'message' => 'No.Target termasuk nomor yang tercatat dalam daftar Blacklist Kami.']);
            }


            if( $userCek->pin != $request->pin ){
                return Response::json(['success' => false ,'message' => 'Maaf, Pin anda salah!']);
            }

            // Deklarasi Variable ====================================
            
            $nameTableTemp   = Uuid::generate(5, strtotime("now"), Uuid::NS_DNS);
            $createTemporary = Schema::create($nameTableTemp, function (Blueprint $table) {
              $table->engine = 'InnoDB';
              $table->increments('id');
              $table->string('apiserver_id')->nullable();
              $table->string('type');
              $table->string('produk');
              $table->string('target');
              $table->string('no_meter_pln')->nullable();
              $table->timestamps();
              $table->temporary();
            });
          
            if (Schema::hasTable($nameTableTemp))
            {
                Schema::drop($nameTableTemp);
                return Response::json(['success' => false ,'message' => 'Transaksi Tidak Diproses. Ulangi transaksi[err-temp01].']);
            }

            #insert ke table temporary. jika paramnya member tidak ada maka memasukkan pararam dari API
            $cekcode = Pembelianproduk::where(['product_id'=>$request->produk])->first();
            $kategori_produk = Pembeliankategori::where('id',$cekcode->pembeliankategori_id)->first();

            if(in_array($kategori_produk->type,['REGULER','TRANSFER','INTERNET'])){
                $phone          = $this->fixphone($request->target);
            }else{
                $phone          = $request->target;
            }
            
            if(empty($cekcode)){
                return Response::json(['success'=>false,'message'=>'Code produk tidak ditemukan']);
            }
            
            DB::table($nameTableTemp)
                    ->insert([
                      'apiserver_id'    =>$cekcode->apiserver_id,
                      'type'            => $request->type,
                      'produk'          => $request->produk,
                      'target'          => $request->target,
                      'no_meter_pln'    => !empty($request->no_meter_pln) ? $request->no_meter_pln : NULL,
                    ]);

            $check_temp = DB::table($nameTableTemp)
                  ->select('*')
                  ->where('id', '1')
                  ->first();
            
            $apiserver_id = $check_temp->apiserver_id;
            $type         = $check_temp->type;
            $product_id   = $check_temp->produk;
            $target       = $phone;
            $no_meter_pln = $check_temp->no_meter_pln;


            $drob_temp = Schema::drop($nameTableTemp);

            $produk = produkMembershipAuth($product_id);
           

            $user         = User::find(Auth::id());
       
            $ip_address   = $request->ip();
           
            $transaksi    = Transaksi::where('code', $product_id)->where('target', $target)->whereIn('status', [0, 1])->whereDate('created_at', '=', date('Y-m-d'))->first();
          
            $queue        = Antriantrx::where('code', $product_id)->where('target', $target)->where('status', 0)->whereDate('created_at', '=', date('Y-m-d'))->first();
            
            $currentBalance = $user->saldo;
            $currentBalance = is_numeric($currentBalance) ? $currentBalance : 0;
            
            if( $currentBalance < $produk->price)
            {
                return Response::json(['success' => false ,'message' => 'Transaksi Tidak Diproses. Saldo tidak cukup untuk transaksi ini, silahkan isi saldo anda terlebih dahulu.']);
            }
            elseif( !empty($queue) )
            {
              return Response::json(['success'=>false,'message'=>'Transaksi '.$produk->product_name.' '.$target.' sudah pernah dilakukan pada '.strftime("%d %b %Y %H:%M", strtotime($queue->created_at)).' status DALAM ANTRIAN']);
            }
            elseif( !empty($transaksi) )
            {
                return Response::json(['success'=>false,'message'=>'Transaksi '.$produk->product_name.' '.$target.' sudah pernah dilakukan pada '.strftime("%d %b %Y %H:%M", strtotime($transaksi->created_at)).'. Mohon gunakan nominal atau nomor tujuan yang berbeda']);
            }
            else
            {
                // get last trx
                $ltx = Antriantrx::where('code', $product_id)->where('target', $target)->where('created_at', '>=', Carbon::now()->subMinutes(5)->toDateTimeString())->count();
                if( $ltx > 0 ) {
                    return Response::json(['success' => false, 'message' => 'Mohon tunggu 5 menit sebelum mengulai transaksi yang sama atau cek riwayat transaksi Anda']);
                }
            
                DB::beginTransaction();

                try
                {
                
                  $sisaSaldoIdr  =  $user->saldo - $produk->price;
                  $user->saldo = $sisaSaldoIdr;
                  $user->save();

                  $antrian = new Antriantrx();
                  $antrian->apiserver_id    = $apiserver_id;
                  $antrian->code            = $product_id;
                  $antrian->produk          = $produk->product_name;
                  $antrian->harga_default   = $produk->price_default;
                  $antrian->harga_markup    = $produk->price_markup;
                  $antrian->target          = $target;
                  $antrian->via             = 'DIRECT';
              
                  if (!empty($no_meter_pln)) {
                      $antrian->mtrpln = $no_meter_pln;
                  }
  
                  $antrian->note = "Transaksi dalam antrian.";
                  $antrian->status = 0;  // Status Proses
                
                  
                  $mutasi           = new Mutasi();
                  $mutasi->user_id  = $user->id;
                  $mutasi->type     = 'debit';
                  $mutasi->nominal  = $produk->price;
                  $mutasi->saldo    = $sisaSaldoIdr; 
                  $mutasi->note     = !empty($no_meter_pln) ? 'TRANSAKSI '.$produk->product_name.' '.$no_meter_pln : 'TRANSAKSI '.$produk->product_name.' '.$target;
                  $mutasi->save();
                  
                  $antrian->pengirim    = $ip_address;
                  $antrian->user_id     = $user->id; 
                  $antrian->save();

                  DB::commit();
                  
                  $checkantrian = Antriantrx::findOrFail($antrian->id);
                  
                  if($checkantrian->status == 0) {
                      
                      $antrian_id      = $antrian->id;
                      $mutasi_id       = $mutasi->id;
                      $code            = $produk->product_id;
                      $via             = 'DIRECT';
                      $jenis_transaksi = 'otomatis';
                      
                      dispatch_now(new QueuePembelian($apiserver_id,$produk, $type, $code, $target, $no_meter_pln, $user, $ip_address, $antrian_id, $mutasi_id, $via, $jenis_transaksi));
                  }
                
                  $request->session()->regenerateToken();
                  
                  return Response::json(['success' => true ,'message' => 'Pembelian anda telah diantrikan. Silahkan Lihat Di <a href="/member/riwayat-transaksi" style="font-weight:bold;text-decoration:underline;">RIWAYAT TRANSAKSI</a> untuk melihat detail pembelian. Tuliskan pengalaman bertransaksi anda bersama kami <a href="/member/testimonial" style="font-weight:bold;text-decoration:underline;">KIRIM TESTIMONIAL</a>.']);
                }
                catch (\Exception $e)
                {
                  DB::rollback();
                  return Response::json(['success' => false ,'message' =>$e->getMessage()]);
                }
          }
    }
    
    public function fixphone($phone){
        if( substr($phone,0,3) == '628')
        {
            $phone = '0'.substr($phone,2);
        }
        elseif(substr($phone,0,4) == '+628')
        {
            $phone = '0'.substr($phone,3);
        }
        elseif(substr($phone,0,5) == '+6208')
        {
            $phone = substr($phone,3);
        }
        elseif(substr($phone,0,4) == '6208'){
            $phone = substr($phone,2);
        }

        return $phone;
    }
}
