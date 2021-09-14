<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
use DB;
use Carbon\Carbon;

class AntrianTransaksiController extends Controller
{
    public function transaksiAntrian()
    {
        $antrianMobile = Antriantrx::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.transaksi.antrian.index', compact('antrianMobile'));
    }

    public function transaksiAntrianProdukDatatables(Request $request)
    {
        $columns = array(
                            0  =>'no', 
                            1  =>'code',
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
  
        $totalData = Antriantrx::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');


        if(empty($request->input('search.value')))
        {            
            $posts = Antriantrx::offset($start)
                         ->limit($limit)
                         ->orderBy('created_at', 'DESC')
                         ->get();
        }else {
            $search = $request->input('search.value'); 

            if(strtoupper($search) == 'PENDING'){
                $stts = '0';
            }elseif(strtoupper($search) == 'DIPROSES'){
                $stts = '1';
            }elseif(strtoupper($search) == 'GAGAL'){
                $stts = '2';
            }elseif(strtoupper($search) == 'REFUND'){
                $stts = '3';
            };
                  
            $posts =  Antriantrx::select('antriantrxes.id','antriantrxes.code','antriantrxes.produk','antriantrxes.target','antriantrxes.mtrpln','antriantrxes.pengirim','antriantrxes.created_at','antriantrxes.updated_at','antriantrxes.status','users.id as usid','users.name')
                            ->leftjoin('users','antriantrxes.user_id','users.id')
                            ->where('antriantrxes.code','LIKE',"%{$search}%")
                            ->orWhere('antriantrxes.produk', 'LIKE',"%{$search}%")
                            ->orWhere('antriantrxes.target', 'LIKE',"%{$search}%")
                            ->orWhere('antriantrxes.mtrpln', 'LIKE',"%{$search}%")
                            ->orWhere('antriantrxes.via', 'LIKE',"%{$search}%")
                            ->orWhere('users.name', 'LIKE',"%{$search}%")
                            ->orWhere('antriantrxes.status', @$stts)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy('antriantrxes.created_at', 'DESC')
                            ->get();

            $totalFiltered = Antriantrx::select('antriantrxes.id','antriantrxes.code','antriantrxes.produk','antriantrxes.target','antriantrxes.mtrpln','antriantrxes.pengirim','antriantrxes.created_at','antriantrxes.updated_at','antriantrxes.status','users.id as usid','users.name')
                            ->leftjoin('users','antriantrxes.user_id','users.id')
                            ->where('antriantrxes.code','LIKE',"%{$search}%")
                            ->orWhere('antriantrxes.produk', 'LIKE',"%{$search}%")
                            ->orWhere('antriantrxes.target', 'LIKE',"%{$search}%")
                            ->orWhere('antriantrxes.mtrpln', 'LIKE',"%{$search}%")
                            ->orWhere('antriantrxes.via', 'LIKE',"%{$search}%")
                            ->orWhere('users.name', 'LIKE',"%{$search}%")
                            ->orWhere('antriantrxes.status', @$stts)
                            ->orderBy('antriantrxes.created_at', 'DESC')
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
                $nestedData['code']          = $post->code;
                $nestedData['produk']        = $post->produk.'<br>'.$post->target;
                $nestedData['mtrpln']        = $post->mtrpln;
                $nestedData['pengirim']      = '<td><a href="'.url('/admin/users', (isset($post->user->id)?''.$post->user->id.'':''.$post->usid.'')).'" class="btn-loading">'.(isset($post->user->name)?''.$post->user->name.'':''.$post->name.'').'</a><br>'.$post->pengirim.'</td>';
                $nestedData['via']           = '<code>'.$post->via.'</code>';
                $nestedData['created_at']    = Carbon::parse($post->created_at)->format('d M Y H:i:s');
                $nestedData['updated_at']    = Carbon::parse($post->updated_at)->format('d M Y H:i:s');
                if($post->status == 0){
                    $nestedData['status'] = '<td><span class="label label-warning">PENDING</span></td>';
                }elseif($post->status == 1){
                    $nestedData['status'] = '<td><span class="label label-success">DIPROSES</span></td>';
                }else{
                    $nestedData['status'] = '<td><span class="label label-danger">GAGAL</span></td>';
                }

                $nestedData['action_detail'] = '<a href="'.url('/admin/transaksi/antrian', $post->id).'" class="btn-loading btn btn-primary btn-sm" style="padding: 2px 5px;font-size:10px;">Detail</i></a>';
                $nestedData['action_hapus']  = '<td><form method="POST" action="'.url('/admin/transaksi/antrian/hapus', $post->id).'" accept-charset="UTF-8">
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
    
    public function showTransaksiAntrian($id)
    {
        $antrian = Antriantrx::findOrFail($id);
        return view('admin.transaksi.antrian.show', compact('antrian'));
    }

    public function transaksiAntrianHapus($id)
    {
        DB::beginTransaction();
        try{
            $antrian = Antriantrx::findOrFail($id);
            $antrian->delete();
            DB::commit();
            return redirect()->back()->with('alert-success', 'Berhasil Mengahpus Data Antrian');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }
}
