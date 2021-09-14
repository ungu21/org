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

class TransaksiTagihanController extends Controller
{
    public function transaksiTagihan()
    {
        $tagihanMobile = Tagihan::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.transaksi.tagihan.index', compact('tagihanMobile'));
    }

    public function transaksiTagihanDatatables(Request $request)
    {
        $setting = Setting::first();
        $columns = array( 
                            0 =>'no', 
                            1 =>'tagihan_id',
                            2=> 'product_name',
                            3=> 'nama',
                            4=> 'jumlah_bayar',
                            5=> 'pengirim',
                            6=> 'via',
                            7=> 'status',
                            8=> 'expired',
                            9=> 'created_at',
                            10=> 'action_detail',
                            11=> 'action_hapus',
                        );
  
        $totalData = Tagihan::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');


        if(empty($request->input('search.value')))
        {            
            $posts = Tagihan::offset($start)
                         ->limit($limit)
                         ->orderBy('created_at', 'DESC')
                         ->get();
        }
        else {
            $search = $request->input('search.value'); 

            if(strtoupper($search) == 'MENUNGGU'){
                $stts = 0;
            }elseif(strtoupper($search) == 'PROSES'){
                $stts = 1;
            }elseif(strtoupper($search) == 'BERHASIL'){
                $stts = 2;
            }elseif(strtoupper($search) == 'GAGAL'){
                $stts = 3;
            }elseif(strtoupper($search) == 'REFUND'){
                $stts = 4;
            };

            if(strtoupper($search) == 'AKTIF'){
                $exp = 1;
            }elseif(strtoupper($search) == 'EXPIRED'){
                $exp = 0;
            };
                  
            $posts =  Tagihan::select('tagihans.id','tagihans.tagihan_id','tagihans.product_name','tagihans.no_pelanggan','tagihans.nama','tagihans.jumlah_bayar','tagihans.periode','tagihans.created_at','tagihans.updated_at','tagihans.status','tagihans.expired','users.id as usid','users.name')
                            ->leftjoin('users','tagihans.user_id','users.id')
                            ->where('tagihans.tagihan_id','LIKE',"%{$search}%")
                            ->orWhere('tagihans.product_name', 'LIKE',"%{$search}%")
                            ->orWhere('tagihans.no_pelanggan', 'LIKE',"%{$search}%")
                            ->orWhere('tagihans.nama', 'LIKE',"%{$search}%")
                            ->orWhere('tagihans.via', 'LIKE',"%{$search}%")
                            ->orWhere('users.name', 'LIKE',"%{$search}%")
                            ->orWhere('tagihans.expired', @$exp)
                            ->orWhere('tagihans.status', @$stts)
                            ->offset($start)
                            ->limit($limit)
                            // ->orderBy($order,$dir)
                            ->orderBy('tagihans.created_at', 'DESC')
                            ->get();

            $totalFiltered = Tagihan::select('tagihans.id','tagihans.tagihan_id','tagihans.product_name','tagihans.no_pelanggan','tagihans.nama','tagihans.jumlah_bayar','tagihans.periode','tagihans.created_at','tagihans.updated_at','tagihans.status','tagihans.expired','users.id as usid','users.name')
                            ->leftjoin('users','tagihans.user_id','users.id')
                            ->where('tagihans.tagihan_id','LIKE',"%{$search}%")
                            ->orWhere('tagihans.product_name', 'LIKE',"%{$search}%")
                            ->orWhere('tagihans.no_pelanggan', 'LIKE',"%{$search}%")
                            ->orWhere('tagihans.nama', 'LIKE',"%{$search}%")
                            ->orWhere('tagihans.via', 'LIKE',"%{$search}%")
                            ->orWhere('users.name', 'LIKE',"%{$search}%")
                            ->orWhere('tagihans.expired', @$exp)
                            ->orWhere('tagihans.status', @$stts)
                            ->orderBy('tagihans.created_at', 'DESC')
                            ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                $nestedData['tagihan_id']    = $post->tagihan_id;
                $nestedData['product_name']  = $post->product_name.'<br>'.$post->no_pelanggan;
                $nestedData['nama']          = $post->nama;
                $nestedData['jumlah_bayar']  = '<td>Rp. '.number_format($post->jumlah_bayar, 0, '.', '.').'<br>'.$post->periode.'</td>';
                $nestedData['pengirim']      = '<td><a href="'.url('/admin/users', (isset($post->user->id)?''.$post->user->id.'':''.$post->usid.'')).'" class="btn-loading">'.(isset($post->user->name)?''.$post->user->name.'':''.$post->name.'').'</a><br>'.$post->pengirim.'</td>';
                $nestedData['via']           = '<code>'.$post->via.'</code>';

                if($post->status == 0){
                    $nestedData['status'] = '<td><span class="label label-warning">MENUNGGU</span></td>';
                }elseif($post->status == 1){
                    $nestedData['status'] = '<td><span class="label label-warning">PROSES</span></td>';
                }elseif($post->status == 2){
                    $nestedData['status'] = '<td><span class="label label-success">BERHASIL</span></td>';
                }else{
                    $nestedData['status'] = '<td><span class="label label-danger">GAGAL</span></td>';
                };

                if($post->expired == 1){
                    $nestedData['expired'] = '<td><span class="label label-info">AKTIF</span></td>';
                }else{
                    $nestedData['expired'] = '<td><span class="label label-danger">EXPIRED</span></td>';
                };

                $nestedData['created_at']    = Carbon::parse($post->created_at)->format('d M Y H:i:s');
                $nestedData['action_detail'] = '<a href="'.url('/admin/transaksi/tagihan', $post->id).'" class="btn-loading btn btn-primary btn-sm" style="padding: 2px 5px;font-size:10px;">Detail</i></a>';
                $nestedData['action_hapus']  = '<td><form method="POST" action="'.url('/admin/transaksi/tagihan/hapus', $post->id).'" accept-charset="UTF-8">
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
    
    public function showTransaksiTagihan($id)
    {
        $setting = Setting::first();
        $tagihan = Tagihan::findOrFail($id);
        return view('admin.transaksi.tagihan.show', compact('tagihan','setting'));
    }
    
    public function hapusTransaksiTagihan($id)
    {
        DB::beginTransaction();
        try{
            $tagihan = Tagihan::findOrFail($id);
            $tagihan->delete();
            DB::commit();
            return redirect()->back()->with('alert-success', 'Berhasil Menghapus Data Tagihan Pembayaran');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }
    
    public function tagihanMenunggu($id)
    {
        DB::beginTransaction();
        try{
            $tagihan = Tagihan::findOrFail($id);
            $tagihan->status = 0;
            $tagihan->expired = 1;
            $tagihan->save();
            DB::commit();
            return back()->with('alert-success', 'Status Data Tagihan Berhasil Dirubah');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
        
    }
    public function tagihanRefund($id){
        DB::beginTransaction();
        try{
            $tagihan = Tagihan::findOrFail($id);
            $tagihan->status = 4;
            $tagihan->expired = 0;
            $tagihan->save();
            
            $user = User::find($tagihan->user->id);
            
            $balance = $user->saldo + $tagihan->jumlah_bayar;
            $user->saldo = $balance;
            $user->save();
            
            $mutasi = new Mutasi();
            $mutasi->user_id = $user->id;
            $mutasi->trxid = $tagihan->id;
            $mutasi->type = 'credit';
            $mutasi->nominal = $tagihan->jumlah_bayar;
            $mutasi->saldo = $balance;
            $mutasi->save();
            
            DB::commit();
            return back()->with('alert-success', 'Status Data Tagihan Berhasil Dirubah');   
        }catch(\Exception $e){
            \Log::error($e);
            DB::rollback();
            return back()->with('alert-error','Gagal');
        }
        
    }
    public function tagihanSuccess($id)
    {
        DB::beginTransaction();
        try{
            $tagihan = Tagihan::findOrFail($id);
            $tagihan->status = 2;
            $tagihan->expired = 0;
            $tagihan->save();
            DB::commit();
            return back()->with('alert-success', 'Status Data Tagihan Berhasil Dirubah');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
        
    }
    
    public function tagihanGagal($id)
    {
        DB::beginTransaction();
        try{
            $tagihan = Tagihan::findOrFail($id);
            $tagihan->status = 3;
            $tagihan->expired = 0;
            $tagihan->save();
            DB::commit();
            return back()->with('alert-success', 'Status Data Tagihan Berhasil Dirubah');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }
}
