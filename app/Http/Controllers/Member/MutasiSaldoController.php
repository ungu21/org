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


class MutasiSaldoController extends Controller
{   
    public function __construct()
    {
        $this->settings = Setting::first();
    }

    public function mutasiSaldo()
    {
        $URL_uri = request()->segment(1).'/'.request()->segment(2);
        $datasubmenu2 = MenuSubmenu::getSubMenuOneMemberURL($URL_uri)->first();

        if($datasubmenu2->status_sub != 0 )
        {
            $setting = Setting::first();
            $mutasiWeb = Mutasi::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->get();
            $mutasiMobile = Mutasi::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
    	    return view('member.deposit.mutasi-saldo.index', compact('mutasiWeb', 'mutasiMobile','setting'));
        }
        else
        {
            abort(404);
        }
    }    

    public function mutasiSaldoDatatables(Request $request)
    {
        $columns = array( 
                            0 =>'no', 
                            1 =>'created_at',
                            2=> 'type',
                            4=> 'nominal',
                            5=> 'saldo',
                            6=> 'trxid',
                            7=> 'note',
                        );
  
        $totalData = Mutasi::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');


        if(empty($request->input('search.value')))
        {            
            $posts = Mutasi::where('user_id', Auth::user()->id)
                             ->offset($start)
                             ->limit($limit)
                             ->orderBy('created_at', 'DESC')
                             ->get();
        }
        else
        {
            $search = $request->input('search.value'); 

            if(strtoupper($search) == 'DEBET'){
                $type = 'debit';
            }elseif(strtoupper($search) == 'KREDIT'){
                $type = 'credit';
            }else{
                $type = null;
            };
                  
            $posts =  Mutasi::where('user_id', Auth::user()->id)
                        ->where(function($q) use ($search,$type){
                                $q->where('trxid','LIKE',"%{$search}%");
                                $q->orWhere('note','LIKE',"%{$search}%");
                                if($type != null){
                                    $q->orWhere('type', $type);
                                }
                          })
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('created_at', 'DESC')
                        ->get();

             $totalFiltered = Mutasi::where('user_id', Auth::user()->id)
                                ->where(function($q) use ($search,$type){
                                        $q->where('trxid','LIKE',"%{$search}%");
                                        $q->orWhere('note','LIKE',"%{$search}%");
                                        if($type != null){
                                            $q->orWhere('type', $type);
                                        }
                                  })
                                ->orderBy('created_at', 'DESC')
                                ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            $no = 0;
            foreach ($posts as $post)
            {
                $no++;
                $nestedData['no']            = $start+$no;
                $nestedData['created_at']    = Carbon::parse($post->created_at)->format('d M Y H:i:s');
                if($post->type == 'debit'){
                    $nestedData['type'] = '<td><label class="label label-danger">debet</label></td>';
                }else{
                    $nestedData['type'] = '<td><label class="label label-success">kredit</label></td>';
                };
              
                $nestedData['nominal']   = '<td> Rp. '.number_format($post->nominal, 0, '.', '.').'</td>';
                $nestedData['saldo']     = '<td> Rp.'.number_format($post->saldo, 2, '.', '.').'</td>';
                $nestedData['trxid']         = $post->trxid != null?$post->trxid:'-';
                $nestedData['note']          = $post->note;

                $data[] = $nestedData;

            }
        }
          
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        return json_encode($json_data);
    }
}
