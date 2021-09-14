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

class PembelianController extends Controller
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

    public function index($title)
    {
        $kategori   = Pembeliankategori::where('slug', $title)->where('status', 1)->firstOrFail();
        $antrian    = Antriantrx::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        $transaksi  = Transaksi::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(3);
        
        $trxForOption = Transaksi::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        $transaksisMobile = $trxForOption;
        $setting = Setting::first();
        $komisi = Setting::settingsBonus(2);

        if ( count($kategori->pembelianoperator) == 1 )
        {
            $operator = Pembelianoperator::where('id', $kategori->pembelianoperator->first()->id)->first();
            return view('member.pembelian.form', compact('kategori', 'operator', 'antrian', 'transaksi','trxForOption','transaksisMobile','setting','komisi'));
        }
        else
        {
            return view('member.pembelian.form', compact('kategori', 'antrian', 'transaksi','trxForOption','transaksisMobile','setting','komisi'));
        }
    }    
    
    public function getTypehead(Request $request)
    {
        $data        = $request->q;
        $suggestions = Transaksi::select('target')->where('user_id', Auth::user()->id)->where('target', 'LIKE',"%{$data}%")->orderBy('created_at', 'DESC')->limit(10)->get();
        
        $output = array();
        foreach ($suggestions as $key ) {
            $output[] =  $key->target;
        }
        return json_encode($output);
    }
    
    public function getTypeheadPLN(Request $request)
    {
        $data        = $request->q;
        $suggestions = Transaksi::select('mtrpln')->where('user_id', Auth::user()->id)->whereNotIn('mtrpln', ['-'])->where('mtrpln', 'LIKE',"%{$data}%")->orderBy('created_at', 'DESC')->limit(10)->get();
        
        $output = array();
        foreach ($suggestions as $key ) {
            $output[] =  $key->mtrpln;
        }
        return json_encode($output);
    }

    public function riwayatTransaksiDatatables(Request $request)
    {
        $columns = array( 
                            0 =>'no', 
                            1 =>'id',
                            2 => 'total',
                            3 => 'target',
                            4 => 'mtrpln',
                            5 => 'pengirim',
                            6 => 'via',
                            7 => 'created_at',
                            8 => 'status',
                            9 => 'action',
                        );
  
        $totalData = Transaksi::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');


        if(empty($request->input('search.value')))
        {            
            $posts = Transaksi::where('user_id', Auth::user()->id)
                             ->offset($start)
                             ->limit($limit)
                             ->orderBy('created_at', 'DESC')
                             ->get();
        }else {
            $search = $request->input('search.value'); 

            if(strtoupper($search) == 'PROSES'){
                $stts = '0';
            }elseif(strtoupper($search) == 'BERHASIL'){
                $stts = '1';
            }elseif(strtoupper($search) == 'GAGAL'){
                $stts = '2';
            }elseif(strtoupper($search) == 'REFUND'){
                $stts = '3';
            }else{
                $stts = null;
            };
                  
            $posts =  Transaksi::select('transaksis.id','transaksis.produk','transaksis.target','transaksis.mtrpln','transaksis.pengirim','transaksis.total','transaksis.created_at','transaksis.updated_at','transaksis.status')
                        ->where('transaksis.user_id', Auth::user()->id)
                        ->where(function($q) use ($search, $stts){
                                $q->where('transaksis.id','LIKE',"%{$search}%");
                                $q->orWhere('transaksis.produk', 'LIKE',"%{$search}%");
                                $q->orWhere('transaksis.target', 'LIKE',"%{$search}%");
                                $q->orWhere('transaksis.mtrpln', 'LIKE',"%{$search}%");
                                $q->orWhere('transaksis.via', 'LIKE',"%{$search}%");
                                if($stts != null){
                                    $q->orWhere('transaksis.status', $stts);
                                }
                          })
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('transaksis.created_at', 'DESC')
                        ->get();

             $totalFiltered = Transaksi::select('transaksis.id','transaksis.produk','transaksis.target','transaksis.mtrpln','transaksis.pengirim','transaksis.total','transaksis.created_at','transaksis.updated_at','transaksis.status')
                                ->where('transaksis.user_id', Auth::user()->id)
                                ->where(function($q) use ($search, $stts){
                                    $q->where('transaksis.id','LIKE',"%{$search}%");
                                    $q->orWhere('transaksis.produk', 'LIKE',"%{$search}%");
                                    $q->orWhere('transaksis.target', 'LIKE',"%{$search}%");
                                    $q->orWhere('transaksis.mtrpln', 'LIKE',"%{$search}%");
                                    $q->orWhere('transaksis.via', 'LIKE',"%{$search}%");
                                    if($stts != null){
                                        $q->orWhere('transaksis.status', $stts);
                                    }
                                  })
                                ->orderBy('transaksis.created_at', 'DESC')
                                ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            $no = 0;
            foreach ($posts as $post)
            {
                $no++;
                $nestedData['no']         = $start+$no;
                $nestedData['id']         = '#'.$post->id;
                $nestedData['produk']     = (isset($post->produk)?substr($post->produk, 0, 20).(strlen($post->produk) > 20 ? ' ...' : '') : '-');
                $nestedData['total']      = '<td> Rp'.number_format($post->total, 0, '.', '.').'</td>';
                $nestedData['target']     = $post->target;
                $nestedData['mtrpln']     = $post->mtrpln;
                $nestedData['pengirim']   = $post->pengirim;
                $nestedData['via']        = '<code>'.$post->via.'<code>';
                $nestedData['created_at'] = Carbon::parse($post->created_at)->format('d M Y H:i:s');

                if($post->status == 0){
                    $nestedData['status'] = '<td><span class="label label-warning">PROSES</span></td>';
                }elseif($post->status == 1){
                    $nestedData['status'] = '<td><span class="label label-success">BERHASIL</span></td>';
                }elseif($post->status == 2){
                    $nestedData['status'] = '<td><span class="label label-danger">GAGAL</span></td>';
                }elseif($post->status == 3){
                    $nestedData['status'] = '<td><span class="label label-primary">REFUND</span></td>';
                };
                $nestedData['action'] = '<td><a href="'.url('/member/riwayat-transaksi', $post->id).'" class="btn-loading label label-primary">Detail</a></td>';
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

    public function prefixproduct(Request $request)
    {
        $pembeliankategori_id = $request->parent;
        
        $prefix = trim(urldecode($request->prefix));

        if( substr($prefix, 0, 3) === '+62' )
        {
            $prefix = '0'.substr($prefix, 3);
        }
        elseif( substr($prefix, 0, 2) === '62' )
        {
            $prefix = '0'.substr($prefix, 2);
        }
        
        $prefixBolt = ["998","999"];
        $_s = substr($prefix, 0, 3);

        $prefix1 = substr($prefix, 0, 4);
        $prefix2 = substr($prefix, 0, 6);

        if( in_array($_s, $prefixBolt) )
        {
            $pembelianoperator = Pembelianoperator::where('pembeliankategori_id', $pembeliankategori_id)
               ->where('prefix', 'LIKE', '%'.$_s.'%')
               ->orderBy('product_name', 'ASC')
               ->get();
        }
        else
        {
            $pembelianoperator = Pembelianoperator::where('pembeliankategori_id', $pembeliankategori_id)
              ->whereRaw('(prefix RLIKE ? OR prefix RLIKE ?)', ['(|,)'.$prefix1.'(,|)','(|,)'.$prefix2.'(,|)'])
              ->orderBy('product_name', 'ASC')
              ->get();
        }
        $operator = count($pembelianoperator) > 0 ? $pembelianoperator[0] : null;
        if( !$operator ) return Response::json([]);

        $pembelianoperator_id = $operator->id;

        $produk = $request->no_product == '0' ? $this->produkLevel($pembelianoperator_id) : [];
        return Response::json(array('operator'=>$pembelianoperator, 'produk' => $produk));
    }

    public function findproduct(Request $request)
    {
        $id = $request->pembelianoperator_id;
        $produk = $this->produkLevel($id);
        return Response::json($produk);
    }

     public function produkLevel($id)
     {
        $roleId = Auth::user()->roles()->first()->id;

      
        if($roleId == $this->personal_role || $roleId == $this->admin_role)
        {
           
            $produk = Personal::select('id', 'product_id', 'product_name', 'desc','price_default','price_markup','price', 'status')->where('pembelianoperator_id', $id)->orderBy('price_default', 'ASC')->get();
            
        }
        elseif($roleId == $this->agen_role)
        {
            $produk = Agen::select('id', 'product_id', 'product_name', 'desc','price_default','price_markup','price', 'status')->where('pembelianoperator_id', $id)->orderBy('price_default', 'ASC')->get();
        }
        elseif($roleId == $this->enterprise_role)
        {
            $produk = Enterprise::select('id', 'product_id', 'product_name', 'desc','price_default','price_markup','price', 'status')->where('pembelianoperator_id', $id)->orderBy('price_default', 'ASC')->get();
        }
        else
        {
            $produk = Pembelianproduk::select('id', 'product_id', 'product_name', 'desc','price_default','price_markup','price', 'status')->where('pembelianoperator_id', $id)->orderBy('price_default', 'ASC')->get();
        }
        return $produk;
    }
}