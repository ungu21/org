<?php

namespace App\Http\Controllers\Member;

use DB, Auth, Response, Validator,Log;
use App\AppModel\Pembayarankategori;
use App\AppModel\Pembayaranoperator;
use App\AppModel\Pembayaranproduk;
use App\AppModel\Antriantrx;
use App\AppModel\BlockPhone;
use App\AppModel\Transaksi;
use App\AppModel\Temptransaksi;
use App\AppModel\Mutasi;
use App\AppModel\Tagihan;
use App\AppModel\Setting;
use App\AppModel\SMSGateway;
use App\AppModel\Komisiref;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Exception;
use Pulsa;

class PembayaranController extends Controller
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
    
    public function index(Request $request, $slug)
    {
        $kategori   = Pembayarankategori::where('slug', $slug)->firstOrFail();
        $operator   = Pembayaranoperator::where('pembayarankategori_id', $kategori->id)->firstOrFail();
        $produk     = Pembayaranproduk::where('pembayarankategori_id', $kategori->id)->where('pembayaranoperator_id', $operator->id)->get();
        $antrian    = Antriantrx::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->Paginate(50);
        $transaksi  = Transaksi::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(3);
        if($kategori->status == 1)
        {
        	return view('member.pembayaran.form', compact('kategori', 'operator', 'antrian', 'transaksi','produk'));
        }
        else
        {
        	return redirect()->back()->with('alert-error', 'Halaman tidak dapat diakses, produk ini masih dalam pengembangan.');
        }
    }

    public function getTypeheadTagihan(Request $request)
    {
        $data        = $request->q;
        $suggestions = Tagihan::select('no_pelanggan')->where('user_id', Auth::user()->id)->where('no_pelanggan', 'LIKE',"%{$data}%")->orderBy('created_at', 'DESC')->limit(10)->get();
        
        $output = array();
        foreach ($suggestions as $key ) {
            $output[] =  $key->no_pelanggan;
        }
        return json_encode($output);
    }
    
    public function findproductpembayaran(Request $request)
    {
        $produk = Pembayaranproduk::where('pembayarankategori_id', $request->pembayarankategori_id)->where('pembayaranoperator_id', $request->pembayaranoperator_id)->get();
        return Response::json($produk);
    }

  
}