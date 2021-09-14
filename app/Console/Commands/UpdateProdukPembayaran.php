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
class UpdateProdukPembayaran extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'produkpembayaran:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update produk pembayaran';

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
        DB::statement("SET session wait_timeout=600");

        $operator_produk = Pembayaranoperator::orderby('last_update','asc')->first();

        $produk = Pulsa::produk_pembayaran(null, $operator_produk->provider_id);
        
        if( $produk->success !== true ) {
            return;
        }
        
        $produk = $produk->data;
        
        foreach($produk as $res)
        {
            $produk_pembayaran = Pembayaranproduk::firstOrNew([
                'provider_id' => $res->id,
            ]);
            $produk_pembayaran->provider_id            = $res->id;
            $produk_pembayaran->apiserver_id           = 1;
            $produk_pembayaran->pembayaranoperator_id  = $operator_produk->id;
            $produk_pembayaran->pembayarankategori_id  = $operator_produk->pembayarankategori_id;
            $produk_pembayaran->product_name           = $res->product_name;
            $produk_pembayaran->code                   = $res->code;
            $produk_pembayaran->price_default          = $res->biaya_admin;
            $produk_pembayaran->markup                 =  !empty($produk_pembayaran->markup) ? $produk_pembayaran->markup : 0;
            $produk_pembayaran->price_markup           = $produk_pembayaran->price_default + $produk_pembayaran->price_markup;
            $produk_pembayaran->status                 = $res->status;
            $produk_pembayaran->save();
        }

        Pembayaranoperator::where('id', $operator_produk->id)
            ->update([
                'last_update' => Carbon::now()
            ]);
    }
}