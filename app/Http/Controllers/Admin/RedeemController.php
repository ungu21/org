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

class RedeemController extends Controller
{
    public function redeem()
    {
        $redeem = Redeem::orderBy('created_at', 'DESC')->get();
        return view('admin.transaksi.redeem.index', compact('redeem'));
    }
    
    public function redeemDetail($id)
    {
        $redeem = Redeem::findOrFail($id);
        return view('admin.transaksi.redeem.show', compact('redeem'));
    }
    
    public function redeemHapus($id)
    {
        DB::beginTransaction();
        try{
            $redeem = Redeem::findOrFail($id);
            $redeem->delete();
            DB::commit();
            return redirect()->back()->with('alert-success', 'Berhasil menghapus data redeem');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }
}
