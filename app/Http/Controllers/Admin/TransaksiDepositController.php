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

class TransaksiDepositController extends Controller
{
    public function transaksiSaldo()
    {
        $depositsMobile = Deposit::orderBy('created_at', 'DESC')->paginate(10);

        return view('admin.transaksi.deposit.index', compact('depositsMobile'));
    }

    public function transaksiSaldoDatatables(Request $request)
    {
        $columns = array( 
                            0 =>'no', 
                            1 =>'id',
                            2=> 'nama_bank',
                            3=> 'nominal_trf',
                            4=> 'nominal_coin',
                            5=> 'status',
                            6=> 'expire',
                            7=> 'name',
                            8=> 'updated_at',
                            9=> 'action_detail',
                            10=> 'action_hapus',
                        );
  
        $totalData = Deposit::count();

        $coinName = Setting::first();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');


        if(empty($request->input('search.value')))
        {            
            $posts = Deposit::offset($start)
                         ->limit($limit)
                         // ->orderBy($order,$dir)
                         ->orderBy('created_at', 'DESC')
                         ->get();
        }else {
            $search = $request->input('search.value'); 

            if(strtoupper($search) == 'MENUNGGU'){
                $stts = '0';
            }elseif(strtoupper($search) == 'BERHASIL'){
                $stts = '1';
            }elseif(strtoupper($search) == 'GAGAL'){
                $stts = '2';
            }elseif(strtoupper($search) == 'VALIDASI'){
                $stts = '3';
            };

            if(strtoupper($search) == 'AKTIF'){
                $stts = '0';
            }elseif(strtoupper($search) == 'EXPIRED'){
                $stts = '1';
            };

            $posts =  Deposit::select('deposits.id','banks.nama_bank','deposits.nominal','deposits.nominal_trf','deposits.status','deposits.expire','deposits.updated_at','users.id as usid','users.name')
                            ->leftjoin('users','deposits.user_id','users.id')
                            ->leftjoin('banks','deposits.bank_id','banks.id')
                            ->where('deposits.id','LIKE',"%{$search}%")
                            ->orWhere('banks.nama_bank', 'LIKE',"%{$search}%")
                            ->orWhere('deposits.expire', @$exp)
                            ->orWhere('deposits.status', @$stts)
                            ->orWhere('users.name', 'LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy('deposits.created_at', 'DESC')
                            ->get();

            $totalFiltered = Deposit::select('deposits.id','banks.nama_bank','deposits.nominal','deposits.nominal_trf','deposits.status','deposits.expire','deposits.updated_at','users.id as usid','users.name')
                            ->leftjoin('users','deposits.user_id','users.id')
                            ->leftjoin('banks','deposits.bank_id','banks.id')
                            ->where('deposits.id','LIKE',"%{$search}%")
                            ->orWhere('banks.nama_bank', 'LIKE',"%{$search}%")
                            ->orWhere('deposits.expire', @$exp)
                            ->orWhere('deposits.status', @$stts)
                            ->orWhere('users.name', 'LIKE',"%{$search}%")
                            ->orderBy('deposits.created_at', 'DESC')
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
                $nestedData['nama_bank']     = (isset($post->bank->nama_bank)?''.$post->bank->nama_bank.'':''.$post->nama_bank.'');

                if($post->bank_id == '5'){
                    $nestedData['nominal_trf'] = '<td>Rp. '.number_format($post->nominal, 0, '.', '.').' ('.$post->nominal_trf.')';
                }else{
                    $nestedData['nominal_trf'] = '<td>Rp. '.number_format($post->nominal_trf, 0, '.', '.');
                }

                if($post->status == 0){
                    $nestedData['status'] = '<td><span class="label label-warning">MENUNGGU</span></td>';
                }elseif($post->status == 1){
                    $nestedData['status'] = '<td><span class="label label-success">BERHASIL</span></td>';
                }elseif($post->status == 2){
                    $nestedData['status'] = '<td><span class="label label-danger">GAGAL</span></td>';
                }elseif($post->status == 3){
                    $nestedData['status'] = '<td><span class="label label-primary">VALIDASI</span></td>';
                };

                if($post->expire == 1){
                    $nestedData['expire'] = '<td><span class="label label-info">AKTIF</span></td>';
                }else{
                    $nestedData['expire'] = '<td><span class="label label-danger">EXPIRED</span></td>';
                };

                 $nestedData['name']      ='<td><div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><a href="'.url('/admin/users', (isset($post->user->id)?''.$post->user->id.'':''.$post->usid.'')).'" class="btn-loading">'.(isset($post->user->name)?''.$post->user->name.'':''.$post->name.'').'</a></div></td>';

                $nestedData['updated_at']    = Carbon::parse($post->updated_at)->format('d M Y H:i:s');

                $nestedData['action_detail'] = '<a href="'.url('/admin/transaksi/deposit/show', $post->id).'" class="btn-loading btn btn-primary btn-sm" style="padding: 2px 5px;font-size:10px;">Detail</i></a>';
                $nestedData['action_hapus']  = '<td><form method="POST" action="'.url('/admin/transaksi/deposit/hapus', $post->id).'" accept-charset="UTF-8">
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

    public function depositShow($id)
    {
        $deposits = Deposit::findOrFail($id);
        return view('admin.transaksi.deposit.show', compact('deposits'));
    }
    
    public function cekPembayaranExp()
    {
        DB::beginTransaction();
        try{
            $tagihan = Tagihan::where('expired',1)->get();
            $results = array();
            foreach($tagihan as $item){
                $now = date("Y-m-d H:i:s");
                $awal = strtotime(date("Y-m-d H:i:s", strtotime($item->created_at)));
                $akhir = strtotime(date("Y-m-d H:i:s"));
                $diff  = $akhir - $awal;
                
                $jam   = floor($diff / (60 * 60));
                $menit = $diff - $jam * (60 * 60);
                
                //3 HARI
                if($jam >= 72.0){
                    $tagihan          = Tagihan::findOrFail($item->id);
                    $tagihan->expired = '0';
                    $tagihan->status  = '3';
                    $tagihan->save();
                }
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
        
    }
    
    public function cekDepo()
    {
        DB::beginTransaction();
        try{
            $deposits = Deposit::where(['status'=> 0, 'expire'=>1])->get();
            $results = array();
            foreach($deposits as $item){
                $now = date("Y-m-d H:i:s");
                $awal = strtotime(date("Y-m-d H:i:s", strtotime($item->created_at)));
                $akhir = strtotime(date("Y-m-d H:i:s"));
                $diff  = $akhir - $awal;
                
                $jam   = floor($diff / (60 * 60));
                $menit = $diff - $jam * (60 * 60);
                if($jam >= 24.0){
                    $updateDeposit         = Deposit::find($id);
                    $updateDeposit->status = 2;
                    $updateDeposit->expire = 0;
                    $updateDeposit->save();
                }
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }
    
    public function depositMenunggu($id)
    {
        DB::beginTransaction();
        try{
            $deposits = Deposit::findOrFail($id);
            if($deposits->status == 3){
                $deposits->note = "Menunggu pembayaran sebesar Rp ".number_format($deposits->nominal_trf, 0, '.', '.');
                $deposits->status = 0;
                $deposits->save();
                DB::commit();
                return redirect()->back();
            }else{
                return redirect()->back()->with('alert-error', 'Perubahan status tidak dapat dilakukan, pastikan status deposit adalah VALIDASI untuk melakukan perubahan status MENUNGGU');
            }
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
        
    }
    
    public function depositValidasi($id)
    {
        DB::beginTransaction();
        try{
            $deposits = Deposit::findOrFail($id);
            if($deposits->status == 0){
                $deposits->note = "Pembayaran telah di konfirmasi, proses validasi.";
                $deposits->status = 3;
                $deposits->save();
                DB::commit();    
                return redirect()->back();
            }else{
                return redirect()->back()->with('alert-error', 'Perubahan status tidak dapat dilakukan, pastikan status deposit adalah MENUNGGU untuk melakukan perubahan status VALIDASI');
            }
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
        
    }

    public function depositSuccess($id)
    {
        DB::beginTransaction();
        try{
            // script deposit saldo
            $deposits = Deposit::findOrFail($id);
            $deposits->status = 1;
            $deposits->expire = 0;
            $deposits->note = "Deposit sebesar Rp ".number_format($deposits->nominal_trf, 0, '.', '.')." berhasil ditambahkan, saldo sekarang Rp ".number_format($deposits->user->saldo + $deposits->nominal_trf, 0, '.', '.');
            $deposits->save();
    
            $users = User::findOrFail($deposits->user_id);
            $saldo = $users->saldo + $deposits->nominal;
            $users->saldo   = $saldo;
            $users->save();
            
            $pesan = 'Yth. '.$users->name.', Deposit Rp '.number_format($deposits->nominal_trf, 0, '.', '.').' SUKSES via '.$deposits->bank->nama_bank.'. Saldo sekarang Rp '.number_format($saldo, 0, '.', '.');
            $notification = SMSGateway::send($users->phone, $pesan);
            
            $mutasi = new Mutasi();
            $mutasi->user_id = $users->id;
            $mutasi->trxid = $deposits->id;
            $mutasi->type = 'credit';
            $mutasi->nominal = $deposits->nominal;
            $mutasi->saldo = $saldo;
            $mutasi->note  = 'DEPOSIT/TOP-UP SALDO';
            $mutasi->save();
            
            DB::commit();
            return redirect()->back()->with('alert-success', 'Berhasil Melakukan Perubahan Status Request Deposit');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }

    public function depositGagal($id)
    {
        DB::beginTransaction();
        try{
            $deposits = Deposit::findOrFail($id);
            $deposits->status = 2;
            $deposits->expire = 0;
            $deposits->note = "Deposit GAGAL, silahkan lakukan kembali request deposit di menu TOP-UP Saldo.";
            $deposits->save();
            DB::commit();
            return redirect()->back()->with('alert-success', 'Berhasil Melakukan Perubahan Status Request Deposit');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }

    public function depositHapus($id)
    {
        DB::beginTransaction();
        try{
            $deposits = Deposit::findOrFail($id);
            $deposits->delete();
            
            DB::commit();
            return redirect()->back()->with('alert-success', 'Berhasil Melakukan Perubahan Status Request Deposit');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }
}
