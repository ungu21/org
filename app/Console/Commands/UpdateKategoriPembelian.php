<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Pulsa, DB,Exception ,Log;
use App\AppModel\Pembeliankategori;
use App\AppModel\Pembelianoperator;
use App\AppModel\Pembelianproduk;
use App\AppModel\Apiserver;
use App\AppModel\Prefix_phone;
use Carbon\Carbon;
class UpdateKategoriPembelian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kategoripembelian:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Kategori Pembelian';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
           //insert Kategori Produk
           $kategori = Pulsa::kategori_pembelian();
           
           if( $kategori->success !== true ) {
               return;
           }
           
           $kategori = $kategori->data;
           
           foreach($kategori as $item){
               switch($item->product_name){
                   case 'PULSA ALL OPERATOR': 
                       $icon = 'mobile';
                       break;
                   case 'PAKET DATA': 
                       $icon = 'internet-explorer';
                       break;
                   case 'VOUCHER GOOGLE PLAY': 
                       $icon = 'google-wallet';
                       break;
                   case 'PULSA SMS TELEPHONE': 
                       $icon = 'envelope';
                       break;
                   case 'PULSA TRANSFER': 
                       $icon = 'forward';
                       break;
                   case 'ITUNES GIFT CARD': 
                       $icon = 'music';
                       break;
                   case 'VOUCHER GAME': 
                       $icon = 'gamepad';
                       break;
                   case 'PUBG MOBILE UC': 
                       $icon = 'gamepad';
                       break;
                   case 'VOUCHER WIFI.ID': 
                       $icon = 'wifi';
                       break;
                   case 'E-MONEY': 
                       $icon = 'money';
                       break;
                   case 'TOKEN LISTRIK': 
                       $icon = 'bolt';
                       break;
                   case 'E-TOLL': 
                       $icon = 'road';
                       break;
                   case 'FREE FIRE DIAMOND': 
                       $icon = 'gamepad';
                       break;
                   case 'MALAYSIA TOPUP': 
                       $icon = 'mobile';
                       break;
                   case 'SINGAPORE TOPUP': 
                       $icon = 'mobile';
                       break;
                   default: 
                   break;
               }
               $kategori_produk = Pembeliankategori::firstOrNew([
                   'provider_id' => $item->id,
               ]);
               $kategori_produk->apiserver_id = 1;
               $kategori_produk->sort_product = $kategori_produk->sort_product ? $kategori_produk->sort_product : 0;
               $kategori_produk->provider_id  = $item->id;
               $kategori_produk->product_name = $item->product_name;
               $kategori_produk->type         = $item->type;
               $kategori_produk->icon         = $icon;
               $kategori_produk->slug         = str_slug($item->product_name);
               $kategori_produk->status       = $item->status;
               $kategori_produk->jenis        = 'pembelian';
               $kategori_produk->save();
           }
    }
}
