<?php

namespace App\Http\Controllers\Admin;

use Pulsa, Response, Freesms4Us, Notif;
use App\User;
use App\AppModel\Mutasi;
use App\AppModel\Antriantrx;
use App\AppModel\Transaksi;
use App\AppModel\Pembelianproduk;
use App\AppModel\Deposit;
use App\AppModel\Tagihan;
use App\AppModel\Redeem;
use App\AppModel\SMSGateway;
use App\AppModel\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class TransaksiProdukController extends Controller
{
    public function transaksiProduk()
    {
        $transaksiProdukMobile = Transaksi::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.transaksi.produk.index', compact('transaksiProdukMobile'));
    }

    public function transaksiProdukDatatables(Request $request)
    {
        $columns = array( 
                            0  =>'no', 
                            1  =>'id',
                            2  => 'produk',
                            3  => 'mtrpln',
                            4  => 'pengirim',
                            5  => 'via',
                            6  => 'created_at',
                            7  => 'updated_at',
                            8  => 'status',
                            9  => 'action_detail',
                            10 => 'action_hapus',
                        );
  
        $totalData = Transaksi::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');


        if(empty($request->input('search.value')))
        {            
            $posts = Transaksi::offset($start)
                                ->limit($limit)
                                ->orderBy('created_at', 'DESC')
                                ->get();
        }
        else {
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
                  
            $posts =  Transaksi::select('transaksis.id','transaksis.tagihan_id','transaksis.produk','transaksis.target','transaksis.mtrpln','transaksis.pengirim','transaksis.created_at','transaksis.updated_at','transaksis.status','users.id as usid','users.name')
                            ->leftjoin('users','transaksis.user_id','users.id')
                            ->where(function($q) use ($search, $stts){
                                    $q->where('transaksis.id','LIKE',"%{$search}%");
                                    $q->orWhere('transaksis.produk', 'LIKE',"%{$search}%");
                                    $q->orWhere('transaksis.target', 'LIKE',"%{$search}%");
                                    $q->orWhere('transaksis.mtrpln', 'LIKE',"%{$search}%");
                                    $q->orWhere('transaksis.via', 'LIKE',"%{$search}%");
                                    $q->orWhere('users.name', 'LIKE',"%{$search}%");
                                    if($stts != null){
                                        $q->orWhere('transaksis.status', $stts);
                                    }
                                 })
                            ->offset($start)
                            ->limit($limit)
                            // ->orderBy($order,$dir)
                            ->orderBy('transaksis.created_at', 'DESC')
                            ->get();

            $totalFiltered = Transaksi::select('transaksis.id','transaksis.tagihan_id','transaksis.produk','transaksis.target','transaksis.mtrpln','transaksis.pengirim','transaksis.created_at','transaksis.updated_at','transaksis.status','users.id as usid','users.name')
                            ->leftjoin('users','transaksis.user_id','users.id')
                            ->where(function($q) use ($search, $stts){
                                    $q->where('transaksis.id','LIKE',"%{$search}%");
                                    $q->orWhere('transaksis.produk', 'LIKE',"%{$search}%");
                                    $q->orWhere('transaksis.target', 'LIKE',"%{$search}%");
                                    $q->orWhere('transaksis.mtrpln', 'LIKE',"%{$search}%");
                                    $q->orWhere('transaksis.via', 'LIKE',"%{$search}%");
                                    $q->orWhere('users.name', 'LIKE',"%{$search}%");
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
                $nestedData['no']            = $start+$no;
                $nestedData['id']            = $post->id;
                $nestedData['produk']        = $post->produk.'<br>'.$post->target;
                $nestedData['mtrpln']        = $post->mtrpln;
                $nestedData['pengirim']      = '<td><a href="'.url('/admin/users', (isset($post->user->id)?''.$post->user->id.'':''.$post->usid.'')).'" class="btn-loading">'.(isset($post->user->name)?''.$post->user->name.'':''.$post->name.'').'</a><br>'.$post->pengirim.'</td>';
                $nestedData['via']           = '<code>'.$post->via.'</code>';
                $nestedData['created_at']    = Carbon::parse($post->created_at)->format('d M Y H:i:s');
                $nestedData['updated_at']    = Carbon::parse($post->updated_at)->format('d M Y H:i:s');
                if($post->status == 0){
                    $nestedData['status'] = '<td><span class="label label-warning">PROSES</span></td>';
                }elseif($post->status == 1){
                    $nestedData['status'] = '<td><span class="label label-success">BERHASIL</span></td>';
                }elseif($post->status == 2){                 
                    $nestedData['status'] = '<td><span class="label label-danger">GAGAL</span></td>';
                }elseif($post->status == 3){
                    $nestedData['status'] = '<td><span class="label label-primary">REFUND</span></td>';
                };
                $nestedData['action_detail'] = '<a href="'.url('/admin/transaksi/produk', $post->id).'" class="btn-loading btn btn-primary btn-sm" style="padding: 2px 5px;font-size:10px;">Detail</i></a>';
                $nestedData['action_hapus']  = '<td><form method="POST" action="'.url('/admin/transaksi/produk/hapus', $post->id).'" accept-charset="UTF-8">
                            <input name="_method" type="hidden" value="DELETE">
                            <input name="_token" type="hidden" value="'.csrf_token().'">
                            <button class="btn btn-danger btn-sm" onClick=\"return confirm(Anda yakin akan menghapus data ?")\" type="submit" style="padding: 2px 5px;font-size:10px;">Hapus</button>
                            </form></td>';
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
    
    
    public function showTransaksiProduk($id)
    {
        $transaksiProduk = Transaksi::where(['id' => $id, 'tagihan_id' => NULL])->firstOrFail();
        return view('admin.transaksi.produk.show', compact('transaksiProduk'));
    }

    public function transaksiHapus($id)
    {
        DB::beginTransaction();
        try{
            $transaksiProduk = Transaksi::findOrFail($id);
            $transaksiProduk->delete();
            DB::commit();
            return redirect()->back()->with('alert-success', 'Berhasil Menghapus Data Order');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }
    
    public function refundTransaksiProduk(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            $transaksi = Transaksi::findOrFail($id);
            $produk = produkMembershipAuth($transaksi->code);
            
            // $produk = Pembelianproduk::where('product_id', $transaksi->code)->first();
            $hargaProduk = $transaksi->total;
        
            $users = User::findOrFail($transaksi->user_id);
            
            $sisaSaldo = $users->saldo + $hargaProduk;
            $users->saldo =$sisaSaldo;
            $users->save();
           
            
            $mutasi = new Mutasi();
            $mutasi->user_id = $users->id;
            $mutasi->trxid = $transaksi->id;
            $mutasi->type = 'credit';
            $mutasi->nominal = $hargaProduk;
            $mutasi->saldo  = $sisaSaldo;
            $mutasi->note  = 'REFUND TRX '.$produk->product_name.' '.$transaksi->target;
            $mutasi->save();
        
            $transaksi->note = "[manual] Trx Direfund";
            $transaksi->status = 3;
            $transaksi->saldo_after_trx = $transaksi->saldo_before_trx;
            $transaksi->save();
            
            DB::commit();
            
            return Response::json($transaksi);
        }catch(\Exception $e){
            \Log::error($e);
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }

    public function ubahStatusTransaksiProduk(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            $transaksi = Transaksi::findOrFail($id);
            
            if($request->stt == 0)
            {
                $transaksi->note = "[manual] Transaksi berhasil diproses";
                $transaksi->token = $request->sn;
            }
            elseif($request->stt == 1)
            {
                $transaksi->note = "[manual] Transaksi success";
                $transaksi->token = $request->sn;
            }
            elseif($request->stt == 2)
            {
                $transaksi->note = "[manual] Transaksi gagal";
                $transaksi->token = $request->sn;
                
                if( in_array($transaksi->status, [0, 1]) )
                {
                    $users = User::findOrFail($transaksi->user_id);
                    $sisaSaldo = $users->saldo + $transaksi->total;
                    $users->saldo = $sisaSaldo;
                    $users->save();
                    
                    $mutasi = new Mutasi();
                    $mutasi->trxid = $transaksi->id;
                    $mutasi->user_id = $users->id;
                    $mutasi->type = 'credit';
                    $mutasi->nominal = $transaksi->total;
                    $mutasi->saldo  = $sisaSaldo;
                    $mutasi->note  = $transaksi->mtrpln != '-' ? 'TRANSAKSI '.$transaksi->produk.' '.$transaksi->mtrpln.' GAGAL' : 'TRANSAKSI '.$transaksi->produk.' '.$transaksi->target.' GAGAL';
                    $mutasi->save();
                    
                    //hapus temptransaksis
                     DB::table('temptransaksis')
                        ->where('transaksi_id', $transaksi->id)
                        ->delete();
                }
            }
            
            $transaksi->status = $request->stt;
            $transaksi->save();
            
            DB::commit();
            
            return Response::json($transaksi);
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }
}
