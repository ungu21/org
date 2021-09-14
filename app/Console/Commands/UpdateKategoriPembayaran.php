<?php

namespace App\Console\Commands;

use DB, Exception, Pulsa,Log;
use Illuminate\Console\Command;
use App\AppModel\Pembayarankategori;
use App\AppModel\Pembayaranoperator;
use App\AppModel\Pembayaranproduk;
use App\AppModel\Apiserver;
use App\AppModel\Prefix_phone;
use Carbon\Carbon;

class UpdateKategoriPembayaran extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kategoripembayaran:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Kategori Pembayaran';

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
        $kategori = Pulsa::kategori_pembayaran();
        
        if( $kategori->success !== true ) {
            return;
        }
        
        $kategori = $kategori->data;
        
        foreach($kategori as $item){
            switch($item->name){
                case 'PEMBAYARAN PLN': 
                    $icon = 'lightbulb-o';
                    break;

                case 'PEMBAYARAN BPJS': 
                    $icon = 'heartbeat';
                    break;
                case 'PEMBAYARAN KERETA API':
                    $icon = 'train';
                    break;
                case 'PEMBAYARAN ASURANSI':
                    $icon = 'slideshare';
                    break;
                case 'PEMBAYARAN TV': 
                    $icon = 'television';
                    break;
                case 'PEMBAYARAN PDAM': 
                    $icon = 'tint';
                    break;
                case 'PEMBAYARAN TELEPHONE KABEL': 
                    $icon = 'tty';
                    break; 
                case 'PEMBAYARAN PASCABAYAR': 
                    $icon = 'volume-control-phone'; 
                    break;
                case 'ZAKAT': 
                    $icon = 'asl-interpreting'; 
                    break;
                case 'PEMBAYARAN MULTIFINANCE': 
                    $icon = 'handshake-o';
                    break;
                default: 
                    break;
            }

            $kategori_pembayaran = Pembayarankategori::firstOrNew([
                'provider_id'=>$item->id,
            ]);
            $kategori_pembayaran->provider_id       = $item->id;
            $kategori_pembayaran->apiserver_id      = 1;
            $kategori_pembayaran->product_name      = strtoupper($item->name);
            $kategori_pembayaran->icon              = $icon;
            $kategori_pembayaran->slug              = str_slug($item->name);
            $kategori_pembayaran->type              = strtoupper($item->name);
            $kategori_pembayaran->status            = $item->status;
            $kategori_pembayaran->jenis             = 'pembayaran';
            $kategori_pembayaran->save();
        }
    }
}
