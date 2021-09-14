<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AppModel\Setting;
use DB;
class SettingMinTransferController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('admin.pengaturan.min_transfer.index',compact('setting'));
    }

    public function update(Request $request)
    {
        
        $this->validate($request,[
                'min_saldo_user'=>'required',
                'min_nominal_transfer'=>'required',
            ]);
        $setting = Setting::first();
        $min_saldo_user = $request->min_saldo_user;
        $min_nominal_transfer = $request->min_nominal_transfer;

        if($min_nominal_transfer > $min_saldo_user){
            return redirect()->back()->with('alert-error','nominal transfer tidak boleh lebih besar dari minimal saldo!');
        }
        $setting->update([
            'min_saldo_user'=>$min_saldo_user,
            'min_nominal_transfer'=>$min_nominal_transfer,
        ]);
       
        if($setting){
            return redirect()->back()->with('alert-success','Berhasil merubah sistem');
        }else{
            return redirect()->back()->with('alert-error','Gagal merubah sistem');
        }
    }
}
