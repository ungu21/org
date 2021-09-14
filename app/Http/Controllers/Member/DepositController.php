<?php

namespace App\Http\Controllers\Member;

use Auth, Response, Validator;
use App\User;
use App\AppModel\Bank;
use App\AppModel\Bank_kategori;
use App\AppModel\Provider;
use App\AppModel\Mutasi;
use App\AppModel\Deposit;
use App\AppModel\Transaksi;
use App\AppModel\Setting;
use App\AppModel\Kurs;
use App\AppModel\MenuSubmenu;
use App\AppModel\VirtualAccountNumber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\AppModel\SMSGateway;
use App\AppModel\BlockPhone;
use DB;
use Log;
use PaymentTripay;

class DepositController extends Controller
{
    public function __construct()
    {
        $this->settings = Setting::first();
    }
    
    public function index()
    {
        $URL_uri = request()->segment(1).'/'.request()->segment(2);
        $datasubmenu2 = MenuSubmenu::getSubMenuOneMemberURL($URL_uri)->first();

        if($datasubmenu2->status_sub != 0 )
        {   
            $bank = Bank_kategori::with(['bank'=>function($q){
                $q->where('status',1);
            }])->where('status',1)->orderby('urutan','asc')->get();
            
    	   return view('member.deposit.form',compact('bank'));
        }
        else
        {
            abort(404);
        }
    }

    public function bank_cek($id){
        $bank = Bank::where('id',$id)->first();
        if($bank){
            return response()->json(['success'=>true,'is_close'=>$bank->is_closed],200);
        }else{
            return response()->json(['success'=>false,'message'=>'Bank Not found!'],500);
        }
    }
    
    public function depositsaldo(Request $request)
    {
        $this->validate($request,[
            'bank_id'          => 'required',
            'id_category_bank' => 'required',
            'nominal'          => 'required|regex:/^[0-9\.]+$/i',
        ],[
            'bank_id.required'          => 'Bank boleh kosong',
            'id_category_bank.required' => 'Kategori Bank tidak boleh kosong',
            'nominal.required'          => 'Nominal tidak boleh kosong',
        ]);
        
        if( $this->settings->status == 0 ) {
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

        if(($request->input('id_category_bank') == '') || ($request->input('id_category_bank') != '2' && $request->input('bank_id') == '')){
            return redirect()->back()->with('alert-error', 'Pilih terlebih dahulu jenis pembayaran yang ingin anda gunakan.!');
        }
        
        $getbank = Bank::find($request->bank_id);
        if($getbank == null || empty($getbank)){
            return redirect()->back()->withErrors('alert-error','Data Bank tidak Ditemukan');
        }

        $getkategoribank = Bank_kategori::find($getbank->bank_kategori_id);
        if($getkategoribank == null || empty($getkategoribank)){
            return redirect()->back()->with('alert-error','Data Kategori Bank Tidak Ditemukan');
        }

        $provider = Provider::find($getbank->provider_id);
        if($provider == null || empty ($provider)){
            return redirect()->back()->with('alert-error','Data Kategori Bank tidak ditemukan');
        }

        $userCek  = User::where('id',Auth::user()->id)->first();
        if($userCek->status == 0){
            return redirect()->back()->with('alert-error','Maaf Akun anda dinonaktifkan');
        }
        $cekPhone = BlockPhone::getDataPhoneWhere($userCek->phone);
        $rolesId = $userCek->roles()->first()->id;
      
        if( !$cekPhone )
        {
          
            if( $this->settings->force_verification == 1 )
            {   
                $verification = DB::table('users_validations')
                            ->select('*')
                            ->where('user_id', auth()->user()->id)
                            ->first();
                if($userCek->roles[0]->id == 3){
                    if( !$verification)
                    {
                        return redirect()->back()->with('alert-error', 'Untuk melakukan transaksi ini, akun Anda harus terverifikasi, silahkan lakukan verifikasi <a href="/member/validasi-users" style="font-weight:bold;text-decoration:underline;">DISINI</a> .');
                    }
                    elseif( $verification->status != '1')
                    {
                        return redirect()->back()->with('alert-error', 'Mohon maaf, verifikasi akun Anda masih dalam proses review. Anda belum dapat melakukan transaksi ini');
                    }   
                }
            }
                        
            $nominal = str_replace(".", "", $request->nominal);
            
            $nominal_trf = str_replace(".", "", $request->nominal) + intval($this->settings->deposit_fee);
            
            $getData = Deposit::getMinDeposit();
     
            if($getbank->is_closed == 1){
                if( $nominal < $getData[0]->minimal_nominal )
                {
                    return redirect()->back()->with('alert-error', 'Minimal Deposit Rp. '.number_format($getData[0]->minimal_nominal, 0, '.', '.').'');
                }
                elseif( substr($nominal, -3) !== '000' )
                {
                    return redirect()->back()->with('alert-error', 'Deposit harus dengan nominal kelipatan 1000, misal : 50000, 51000, 100000, dst');
                }
                else
                {
                    if(Auth::user()->roles()->first()->id == 1){
                        if( $this->settings->max_daily_deposit_personal > 0 )
                        {
                            $dailyDepositRequest = (int) Deposit::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->whereIn('status', [0,1])->count();
                            
                            if( $dailyDepositRequest >= $this->settings->max_daily_deposit_personal )
                            {
                                return redirect()->back()->with('alert-error', 'Anda sudah mencapai batas maksimum request deposit harian!');
                            }
                        }
                    }else if(Auth::user()->roles()->first()->id == 3){
                        if( $this->settings->max_daily_deposit_agen > 0 )
                        {
                            $dailyDepositRequest = (int) Deposit::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->whereIn('status', [0,1])->count();
                            
                            if( $dailyDepositRequest >= $this->settings->max_daily_deposit_agen )
                            {
                                return redirect()->back()->with('alert-error', 'Anda sudah mencapai batas maksimum request deposit harian!');
                            }
                        }
                    }else if(Auth::user()->roles()->first()->id == 4){
                        if( $this->settings->max_daily_deposit_enterprise > 0 )
                        {
                            $dailyDepositRequest = (int) Deposit::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->whereIn('status', [0,1])->count();
                            
                            if( $dailyDepositRequest >= $this->settings->max_daily_deposit_enterprise )
                            {
                                return redirect()->back()->with('alert-error', 'Anda sudah mencapai batas maksimum request deposit harian!');
                            }
                        }
                    }
                  
                    if($provider->name == 'CekMutasi')
                    {
                         if( (date("G") < 21 && date("G") >= 3) )
                         {
                            
                            $code_unik = mt_rand(1, 999);
                            if( substr($code_unik, -1) == "0" )
                            {
                                $code_unik = $code_unik + mt_rand(1,9);
                            }
                            
                            $nominal_trf = (intval($nominal_trf) + intval($code_unik));
                         
                            for($i=1; $i>=1; $i++)
                            {
                                $check = (int) Deposit::where('nominal_trf', $nominal_trf)->whereDate('created_at', date('Y-m-d'))->whereIn('status', [0,1,3])->count();
                             
                                if( $check <= 0 )
                                {
                                    break;
                                }
                                else
                                {
                                    $code_unik++;
                                    $nominal_trf++;
                                   
                                }
                            }
                            DB::beginTransaction();
                           
                            try{
                                $deposit = new Deposit();
                                $deposit->bank_id = $getbank->id;
                                $deposit->bank_kategori_id = $getbank->bank_kategori_id;
                                $deposit->code_unik = $code_unik;
                                $deposit->nominal = $nominal;
                                $deposit->nominal_trf = $nominal_trf;
                                $deposit->note = "Menunggu pembayaran sebesar Rp ".number_format($nominal_trf, 0, '.', '.');
                                $deposit->user_id = Auth::user()->id;
                                $deposit->save();
                               
                                DB::commit();
                                return redirect()->to('/member/deposit/'.$deposit->id);
                            }catch(\Exception $e){
                                DB::rollback();
                                return redirect()->back()->with('alert-error','Deposit Gagal');
                            }
                        }
                        else
                        {
                            return redirect()->back()->with('alert-error', 'Deposit tidak dapat dilakukan pada pukul 21.00 - 03.00 WIB, silahkan melakukan deposit diluar dari pada jam tersebut.');
                        }
                    }
                    
                    if($provider->name == 'PaymentTripay'){
                        DB::beginTransaction();
                        try{
                            $paymentMethod = $getbank->code;
                           
                            $deposit                   = new Deposit();
                            $deposit->bank_id          = $getbank->id;
                            $deposit->bank_kategori_id = $getbank->bank_kategori_id;
                            $deposit->code_unik        = 0;
                            $deposit->nominal          = $nominal;
                            $deposit->nominal_trf      = $nominal_trf;
                            $deposit->note             = "Menunggu pembayaran sebesar Rp ".number_format($nominal, 0, '.', '.');
                            $deposit->user_id          = $userCek->id;
                            $deposit->save(); 
                            
                            $PaymentTripay = PaymentTripay::trx_close_payment([
                                'method'=>$paymentMethod,
                                'merchant_ref'=>$deposit->id,
                                'amount'=>$nominal_trf,
                                'customer_name'=>$userCek->name,
                                'customer_email'=>$userCek->email,
                                'customer_phone'=>$userCek->phone,
                                'callback_url'=>url('callback/tripay_payment'),
                                'return_url'=>url('member/riwayat_deposit'),
                            ]);
                            
                            
                            $result = json_decode($PaymentTripay);
                           
                            if($result->success == true){
                                $deposit->nominal_trf = $result->data->amount;
                                $deposit->payment_url = $result->data->checkout_url;
                                $deposit->save();

                                DB::commit();
                                return redirect($result->data->checkout_url);
                            }else{
                                DB::rollback();
                            }
                             
                        }catch(\Exception $e){
                            \Log::error($e);
                            DB::rollback();
                            return redirect()->back()->with('alert-error', 'Terjadi Kesalahan');
                        }
                    }
                    
                    return redirect()->back()->with('alert-error', 'Gagal Diproses!.');
                }
            }else{
                if($provider->name == 'PaymentTripay'){
                    $cek_nomer_va = VirtualAccountNumber::where('user_id',$userCek->id)->where('bank_id',$getbank->id)->first();
                 
                    if(!$cek_nomer_va){
                        try{
                            $PaymentTripay = PaymentTripay::trx_open_payment([
                                'method'=>$getbank->code,
                                'merchant_ref'=>$userCek->id,
                                'customer_name'=>$userCek->name,
                            ]);
        
                            $result = json_decode($PaymentTripay);
                          
                            if($result->success == true){
                               $va_number = VirtualAccountNumber::create([
                                    'user_id'=>$userCek->id,
                                    'bank_id'=>$getbank->id,
                                    'number_va'=>$result->data->pay_code,
                                    'uuid'=>$result->data->uuid,
                               ]);
    
                               if($va_number){
                                   DB::commit();
                                   return redirect('member/va_number/'.$va_number->id);
                               }else{
                                   DB::rollback();
                                   return redirect()->back()->with('alert-error','Gagal generate nomer VA');
                               }
                            }else{
                                DB::rollback();
                                return redirect()->back()->with('alert-error','Gagal generate nomer VA');
                            }
                        }catch(\Exception $e){
                            \Log::error($e);
                            DB::rollback();
                            return redirect()->back()->with('alert-error','Gagal Diproses!');
                        }
                    }else{
                        DB::rollback();
                        return redirect('/member/va_number/'.$cek_nomer_va->id);
                    }
                    
                }else{
                    return redirect()->back('alert-error','Gagal diproses!.');
                }
            }
           
        }
        else
        {
            return redirect()->back()->with('alert-error', 'Maaf, No.Hp Anda Diblokir!');
        }
    }
    
    public function showNomerVa($id)
    {
        $number_va = VirtualAccountNumber::with('bank')->with('user')->where('id',$id)->first();
        if(!$number_va){
            return abort(404);
        }
        $instruction = [];

        $instruksi = PaymentTripay::instruction([
            'code'=>$number_va->bank->code,
            'pay_code'=>$number_va->number_va,
            ]);
        $instruksi = json_decode($instruksi);
        
        if($instruction->success == true){
            $instruction = $instruksi->data;
        }
        return view('member.deposit.number_va',compact('number_va','instruction'));
        
    }
}