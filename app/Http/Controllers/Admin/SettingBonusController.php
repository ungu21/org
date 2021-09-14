<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\AppModel\Bonus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
class SettingBonusController extends Controller
{
    public function index()
    {
        $getDataTrx = Bonus::getKomisi('2');
        return view('admin.pengaturan.bonus.index', compact('getDataTrx'));
    }

    public function update(Request $request)
    {
        $nominal = str_replace('.','',$request->bonus_trx);
        $this->validate($request,[
                'bonus_trx'=>'required',
            ],[
                'bonus_trx.required'=>'Harap isi Bonus Transaksi Referral',
            ]);
        
       
        if($request->bonus_trx){
            DB::table('settings_komisi')
                ->where('id',2)
                ->update([
                    'komisi'=>$nominal
                ]);
        }
        

        return redirect()->back()->with('alert-success', 'Berhasil Melakukan Perubahan Data Sistem');
    }

}