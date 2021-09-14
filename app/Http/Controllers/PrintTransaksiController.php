<?php

namespace App\Http\Controllers;

use Auth, Pulsa, PDF, DB,Session;
use App\AppModel\Transaksi;
use App\AppModel\MenuSubmenu;
use App\AppModel\Tagihan;
use App\AppModel\Setting;
use App\User;
use Crypt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class PrintTransaksiController extends Controller
{
    public function printShow(Request $request,$id)
    {
        $key = Crypt::decrypt($id);
        $key = explode(":",$key);
        $id_transaksi = $key[0];
        $id_user      = $key[1]; 
       
        $user = User::where('id',$id_user)->firstOrFail();
                
        $trx              = Transaksi::where('id', $id_transaksi)->where('user_id', $user->id)->firstOrFail();
       
        $user             = User::where('id', $trx->user_id)->firstOrFail();
      
        if(!empty($trx->tagihan_id)){
            $tagihan          = Tagihan::where('tagihan_id', $trx->tagihan_id)->first();    
        }
        
        $GeneralSettings  = setting();
        
        if( !empty($tagihan) )
        {
            $pdf         = new PDF();
            $customPaper = array(0,0,200,300);
            $pdf         = PDF::loadView('member.histori.print_pembayaran', compact('trx','tagihan','user','GeneralSettings'))->setPaper($customPaper);
        }
        else
        {
            $pdf         = new PDF();
            $customPaper = array(0,0,200,250);
            $pdf         = PDF::loadView('member.histori.print_pembelian', compact('trx','user','GeneralSettings'))->setPaper($customPaper);
        }
        
        $SavePrintName = 'trx_'.strtolower($trx->code).'_'.(!empty($trx->mtrpln) && $trx->mtrpln != '-' ? $trx->mtrpln : $trx->target).'-'.date('d-m-Y_H-i-s');
        
        if($request->view == 'desktop'){
            return $pdf->stream($SavePrintName.'.pdf', array("Attachment" => 0));    
        }else{
            return $pdf->download($SavePrintName.'.pdf', array("Attachment" => 0));
        }
    }
    
    public function printEdit(Request $request,$id)
    {
        $user             = User::where('id', auth()->user()->id)->firstOrFail();
        
        $trx              = Transaksi::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $key = $trx->id.":".$user->id; 
        
        $hash = hash('sha256',$key);
        $view = $request->view;
        
        if(!empty($trx->tagihan_id)){
            $tagihan          = Tagihan::where('tagihan_id', $trx->tagihan_id)->first();    
        }
        
        $GeneralSettings  = setting();
        
        if( !empty($tagihan) )
        {
            return view('member.histori.edit_print_pembayaran', compact('trx','tagihan','user','GeneralSettings','hash','view'));
        }
        else
        {
            return view('member.histori.edit_print_pembelian', compact('trx','user','GeneralSettings','hash','view'));
        }
    }
    
    public function printSave(Request $request, $id)
    {
        $trx        = Transaksi::where('id', $id)->firstOrFail();
        $user       = User::where('id',$trx->user_id)->firstOrFail();
        
        $key = $trx->id.":".$user->id; 
        
        $signature = hash('sha256',$key);
        
        if(!hash_equals($signature,$request->token)){
            return redirect()->back()->with('alert-error','Transaksi Tidak Valid');
        }
        $harga      = str_replace(".", "", $request->input('harga'));
        $harga_markup = str_replace(".", "", $request->input('harga_markup'));
        
        $trx->total = $request->has('harga') ? intval($harga) : $trx->total;
        $trx->harga_markup = $request->has('harga_markup') ? intval($harga_markup) : $trx->harga_markup;
        
        $tagihan    = Tagihan::where('tagihan_id',$trx->tagihan_id)->first();
        $GeneralSettings = Setting::first();
        $view = $request->view;
        $harga = $request->harga;
       
        if( !empty($tagihan) )
        {
            $pdf         = new PDF();
            $customPaper = array(0,0,200,300);
            $pdf         = PDF::loadView('member.histori.print_pembayaran', compact('trx','tagihan','user','GeneralSettings'))->setPaper($customPaper);
        }
        else
        {
            $pdf         = new PDF();
            $customPaper = array(0,0,200,250);
            $pdf         = PDF::loadView('member.histori.print_pembelian', compact('trx','user','GeneralSettings'))->setPaper($customPaper);
        }
        
        $SavePrintName = 'trx_'.strtolower($trx->code).'_'.(!empty($trx->mtrpln) && $trx->mtrpln != '-' ? $trx->mtrpln : $trx->target).'-'.date('d-m-Y_H-i-s');
        
        return $pdf->stream($SavePrintName.'.pdf', array("Attachment" => 0));  
    }
   
}