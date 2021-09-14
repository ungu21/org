<?php

namespace App\Http\Controllers\Member;

use Auth, Response, Validator, ZenzivaSMS, Cekmutasi, PDF;
use App\User;
use App\AppModel\Bank;
use App\AppModel\Bank_swif;
use App\AppModel\Mutasi;
use App\AppModel\MenuSubmenu;
use App\AppModel\Transaksi;
use App\AppModel\Mutasi_saldobank;
use App\AppModel\Users_validation;
use App\AppModel\Deposit;
use App\AppModel\Setting;
use App\AppModel\SendSMS;
use App\AppModel\SettingOvoTransfer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use DB;
use Mail;
use Formater;

class TransferBankController extends Controller
{
    public function __construct()
    {
        $this->settings = Setting::first();
    }
    
    public function index(Request $request)
    {
        $URL_uri = request()->segment(1).'/'.request()->segment(2);
        $datasubmenu2 = MenuSubmenu::getSubMenuOneMemberURL($URL_uri)->first();
        
        $banks = Bank::all();
        $bank_swifs = DB::table('bank_swifs')
        ->select('*')
        ->get();   
        return view('member.transfer-bank.form', compact('banks', 'bank_swifs'));
    }

    public function process(Request $request)
    {
        $this->validate($request,[
            'penerima'          =>'required',
            'pilih_jenis_bank'  =>'required',
            'no_rek'            =>'required',
            'nominal'           =>'required',
            'pin'               =>'required',
        ],[
            'penerima.required'         =>'Nama Penerima Transfer tidak boleh kosong',
            'pilih_jenis_bank.required' =>'Bank tidak boleh kosong',
            'no_rek.required'           =>'Nomor Rekening tidak boleh kosong',
            'nominal.required'          =>'Nominal tidak boleh kosong',
            'pin.required'              =>'PIN tidak boleh kosong'
        ]);

        $userCek = User::where('id',Auth::user()->id)->firstOrFail();
        if($userCek->pin == $request->pin)
        {
            $validation_user = Users_validation::where('user_id',Auth::user()->id)->first();
            
            if($validation_user == null){
                
                return redirect()->back()->with('alert-error','Silahkan melakukan validasi terlebih dahilu untuk menggunakan fitur ini');
            
            }elseif($validation_user->status == 0){

                return redirect()->back()->with('alert-erorr','Validasi anda belum disetujui oleh admin, silahkan hubungi admin untuk mempercepat proses');

            }else{

                $trxCount = Transaksi::where('user_id',auth()->user()->id)->where('status',1)->count();
                if($trxCount <= 0){
                    return redirect()->back()->with('alert-error','Mohon Maaf, anda tidak diperbolehkan menggunakan fitur ini, anda belum mempunyai transaksi success');
                }

                if(Carbon::now()->format('l') == 'Sunday'){
                    if(date('G') < 14){
                        return redirect()->back()->with('alert-error','Mohon maaf, hari minggu Transfer Saldo ke rekening dapat dilakukan mulai pukul 14.00 WIB!');
                    }
                }

                $nominal_ori = intval(str_replace('.','',$request->nominal));

                if(substr($nominal_ori,-3) != 000){
                    return redirect()->back()->with('alert-error','Nominal transfer harus dalam kelipatan 1.000, contoh 10000, 11000, 100000');
                }

                $nominal_sum = $nominal_ori + $this->setting->fee_tf_bank;
                $user        = User::where('id',Auth::user()->id)->firstOrFail();
                $saldo_now = $user->saldo;
                
                if($nominal_ori < $this->setting->min_tf_bank){
                
                    return redirect()->back()->with('alert-error','Minimal Transfer Rp. '.number_format(intval($this->setting->min_tf_bank),0,'.','.'));
                
                }elseif($nominal_ori > $this->setting->max_tf_bank){

                    return redirect()->back()->with('aleret-error','Maximal Transfer Rp. '.number_format(intval($this->setting->max_tf_bank),0,'.','.'));

                }elseif($nominal_ori > $user->saldo ){

                    return redirect()->back()->with('alert-error','Jumlah saldo tidak mencukupi saldo anda saat ini Rp. '.number_format($user->saldo,0,'.','.'));

                }else{
                    if(date('G') <= 21 && date('G') >= 7){
                        DB::beginTransaction();
                        try{
                            $getCodeBank = Bank_swif::find($request->pilih_jenis_bank);
    
                            if(!$getCodeBank){
                                return redirect()->back()->with('alret-error','Bank Tidak Ditemukan');
                            }
    
                            $dataMutasi = Mutasi_saldobank::max('trxid');
                            if($dataMutasi == null){
                                $trxid = 1;
                            }else{
                                $trxid = $dataMutasi+1;
                            }
    
                            //insert ke mutasissaldo_bank
                            $simpanMutasiTF              = new Mutasi_saldobank();
                            $simpanMutasiTF->user_id     = Auth::user()->id;
                            $simpanMutasiTF->trxid       = $trxid;
                            $simpanMutasiTF->penerima    = $request->penerima;
                            $simpanMutasiTF->nominal     = $nominal_ori;
                            $simpanMutasiTF->code_bank   = $getCodeBank->code;
                            $simpanMutasiTF->jenis_bank  = $getCodeBank->name;
                            $simpanMutasiTF->no_rekening = $request->no_rek;
                            $simpanMutasiTF->status      = 0;
                            $simpanMutasiTF->note        = 'Transaksi Transfer ke Bank. No.rekening '.$request->no_rek.' ke bank '.$getCodeBank->name.' sebesar '.number_format($nominal_ori, 0, '.', '.').'  Sedang di proses';
                            $simpanMutasiTF->save();
    
    
                            $potongSaldo1 = $saldo_now - $nominal_ori;
    
                            //insert ke mutasi
                            $mutasi = new Mutasi();
                            $mutasi->user_id = Auth::user()->id;
                            $mutasi->type = 'debit';
                            $mutasi->nominal = $nominal_ori;
                            $mutasi->saldo  = $potongSaldo1;
                            $mutasi->note  = 'TRANSAKSI Pemindahan Saldo Rp. '.number_format($nominal_ori, 0, '.', '.').' Ke '.$getCodeBank->name.' ('.$getCodeBank->code.') no.rekening '.$request->no_rek.'';
                            $mutasi->save();
                            
                            sleep(2);
                            
                            $potongSaldo2 = $potongSaldo1 - $this->setting->fee_tf_bank;
                            //insert ke mutasi
                            $mutasi_admin = new Mutasi();
                            $mutasi_admin->user_id = Auth::user()->id;
                            $mutasi_admin->type = 'debit';
                            $mutasi_admin->nominal = $this->setting->fee_tf_bank;
                            $mutasi_admin->saldo  = $potongSaldo2;
                            $mutasi_admin->note  = 'Biaya Admin Transfer Saldo Ke Bank ID: #'.$mutasi->id.'';
                            $mutasi_admin->save();
                        
                            $sisaSaldo = $saldo_now - $nominal_sum;
                            //update saldo
                            $user = Auth::user();
                            $user->saldo = $sisaSaldo;
                            $user->save();
                            
                            $setting = Setting::first();
                                
                            $data = [
                                    'trx'        => $simpanMutasiTF, 
                                ];
                            
                            DB::commit();
                            
                            return redirect()->back()->with('alert-success','Transfer Saldo Ke Rekening '.$request->no_rek.' jenis bank '.$getCodeBank->name.' ('.$getCodeBank->code.') nominal Rp. '.number_format($nominal_ori, 0, '.', '.').' sedang di proses.');
                    
                        }catch(\Exception $e){
                            DB::rollback();
                            \Log::error($e);
                            return redirect()->back()->with('alert-error','Transaksi gagal, Mohon ulangi');
                        }
                    }else{
                        return redirect()->back()->with('alert-error','Transfer Saldo ke Rekenig tidak dapat dilakukan pada pukul 21.00 - 07.00 WIB, silahkan melakukan transfer diluar dari pada jam tersebut');
                    }
                }

            }
        }
    }
    
    public function history()
    {
        $transaksisMobile = Mutasi_saldobank::where('user_id', Auth::user()->id)
                    ->orderBy('created_at', 'DESC')
                    ->paginate(10);

        return view('member.transfer-bank.history', compact('transaksisMobile'));
    }
    
    public function historyDatatables()
    {
            $data = Mutasi_saldobank::where('user_id', Auth::user()->id)
                        ->orderBy('created_at', 'DESC')
                        ->get();
            
             return DataTables::collection($data)

             ->editColumn('id',function($data){
                    return '#'.$data->id.'';
             })

             ->editColumn('nominal',function($data){
                    return '<td><span class="label label-info">Rp. '.number_format($data->nominal, 0, '.', '.').'</span></td>';
             })
                        
             ->editColumn('created_at',function($data){
                    return Carbon::parse($data->created_at)->format('d-m-Y H:i:s');
             })

             ->editColumn('status',function($data){
                if($data->status == 0){
                    return '<td><span class="label label-warning">PROSES</span></td>';
                }elseif($data->status == 1){
                    return '<td><span class="label label-success">BERHASIL</span></td>';
                }elseif($data->status == 2){
                    return '<td><span class="label label-danger">GAGAL</span></td>';
                }elseif($data->status == 3){
                    return '<td><span class="label label-primary">REFUND</span></td>';
                };
            })
            
             ->editColumn('action_print',function($data){
                    return '<td><a href="'.url('/member/transfer-bank/history/print', $data->id).'" class="btn-loading btn btn-primary btn-sm" style="padding: 2px 5px;font-size:10px;" target="_blank">Print Struk</i></a></td>';
             })
                
             ->editColumn('action_detail',function($data){
                    return '<td><a href="'.url('/member/transfer-bank/history/show', $data->id).'" class="btn-loading btn btn-primary btn-sm" style="padding: 2px 5px;font-size:10px;">Detail</i></a></td>';
             })

             ->rawColumns(['id','nominal','created_at','status','action_print','action_detail'])
             ->make(true);
    }
    
    public function show(Request $request, $id ='')
    {
        $transaksi = DB::table('mutasisaldo_bank')
                ->select('mutasisaldo_bank.*','users.name')
                ->leftjoin('users','mutasisaldo_bank.user_id','users.id')
                ->where('mutasisaldo_bank.id', $id)
                ->orderBy('mutasisaldo_bank.created_at','DESC')
                ->first();
                
        return view('member.transfer-bank.show', compact('transaksi'));
    }
    
    public function printStruk($id)
    {
        $transaksi = DB::table('mutasisaldo_bank')
            ->select('mutasisaldo_bank.*','users.name')
            ->leftjoin('users','mutasisaldo_bank.user_id','users.id')
            ->where('mutasisaldo_bank.id', $id)
            ->first();
        $user            = User::where('id',$transaksi->user_id)->first();
        $GeneralSettings = $this->settings;
        
        $pdf             = new PDF();
        $customPaper     = array(0,0,200,250);
        $pdf             = PDF::loadView('member.transfer-bank.print', compact('transaksi','user','GeneralSettings'))->setPaper($customPaper);
       
        $SavePrintName = 'trf'.strtolower($transaksi->id).'_'. date('d-m-Y_H:i:s').'';
        return $pdf->stream(''.$SavePrintName.'.pdf',array("Attachment"=>0));
    }
    
    public function getBankCode(Request $request)
    {
        $params = [
            'search_text' => $request->input('q'),
        ];

        $data = DB::table('bank_swifs')
            ->select('*')
            ->where(DB::raw('lower(name)'), 'like','%'.strtolower((isset($params['search_text'])?$params['search_text']:'')).'%')
            ->orWhere(DB::raw('lower(code)'), 'like','%'.strtolower((isset($params['search_text'])?$params['search_text']:'')).'%')
            ->get();

        $results=[];
        foreach ($data as $key ) {
            $results[] = [
              'id'     => $key->id,
              'text'   => $key->code.' - '.$key->name,

            ];
        }
        return $results;
    }
}