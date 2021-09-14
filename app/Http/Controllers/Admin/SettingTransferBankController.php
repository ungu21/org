<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AppModel\Setting;
class SettingTransferBankController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('admin.pengaturan.transfer_bank.index',compact('setting'));
    }

    public function update(Request $request)
    {
        $min_tf = str_replace('.','',$request->min_tf);
        $max_tf = str_replace('.','',$request->max_tf);
      

        $setting = Setting::first()->update([
            'min_tf_bank'=>$min_tf,
            'max_tf_bank'=>$max_tf,
        ]);

        if($setting){
            return redirect()->back()->with('alert-success','Berhasil Mengubah Data');
        }else{
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }

    public function update_fee(Request $request){
      
        $fee_tf = str_replace('.','',$request->fee_tf_bank);

        $setting = Setting::first()->update([
            'fee_tf_bank'=>$fee_tf
        ]);

        if($setting){
            return redirect()->back()->with('alert-success','Berhasil Mengubah Data');
        }else{
            return redirect()->back()->with('alert-error','Terjadi Kesalahan');
        }
    }
}
