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

class RiwayatDepositController extends Controller
{
    public function __construct()
    {
        $this->settings = Setting::first();
    }

    public function riwayat_deposit(){
        $deposit = Deposit::where('user_id',auth()->user()->id)->OrderBy('id','desc')->paginate(10);
        return view('member.deposit.riwayat-deposit',compact('deposit'));
    }

    public function showDeposit($id)
    {
        $logo = DB::table('logos')->where('id',3)->first();
        $banks = Bank::all();
        $deposits = Deposit::where('user_id', Auth::user()->id)->findOrFail($id);
        return view('member.deposit.show', compact('deposits', 'banks','logo'));
    }
    
    public function konfirmasiPembayaran(Request $request)
    {   
        
        $validator = Validator::make($request->all(),[
                'bukti' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ],[
                'bukti.required'      => 'Bukti pembayaran tidak boleh kosong',
                'bukti.image'         => 'Bukti pembayaran harus berformat gambar',
                'bukti.mimes'         => 'Bukti pembayaran harus dalam format png, jpg, atau jpeg',
                'bukti.max'           => 'Bukti pembayaran Max Size 5MB',
            ]);
        if($validator->fails()){
            return redirect()->back()->with('alert-error',$validator->errors()->first());
        }
        
        $bukti = $request->file('bukti');
        $extension = $bukti->getClientOriginalExtension();
        if(!in_array($extension,['png','jpeg','jpg'])){
            return redirect()->back()->with('alert-error','Format File yang anda upload tidak didukung');
        }
        
        $nameBukti      = 'bukti_'.$request->id.time().'.'.strtolower($bukti->getClientOriginalExtension());
        
        $extension = $bukti->getClientOriginalExtension();
        
        if(!in_array($extension,['png','jpg','jpeg'])){
            return redirect()->back()->with('alert-error','Format Bukti Pembayaran yang anda upload tidak didukung!');
        }
        
        if (!file_exists(public_path('img/validation/deposit/'))) {
            mkdir(public_path('img/validation/deposit'), 0777, true);
        }
        
        
        $destinationIMG      = public_path('img/validation/deposit/');
        $upload_bukti_success  =  $bukti->move($destinationIMG, $nameBukti);
        
            
        $deposit = Deposit::where('user_id', Auth::user()->id)->findOrFail($request->id);
        
        if( $deposit->status == 0 )
        {
            $deposit->status = 3;// status proses
            $deposit->note = 'Pembayaran telah di konfirmasi, proses validasi.';
            $deposit->bukti = $nameBukti;
            $deposit->save();
            
            return redirect()->back();
        }
    }
}
