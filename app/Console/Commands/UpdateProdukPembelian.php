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
class UpdateProdukPembelian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'produkpembelian:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'FOR UPDATE PRODUCT';

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

        $operator = Pembelianoperator::orderby('last_update', 'asc')->first();
        
        $produk = Pulsa::produk_pembelian(null, $operator->provider_id);
        
        if( $produk->success !== true ) {
            return;
        }
        
        $produk = $produk->data;

        foreach($produk as $res)
        {
            $produk_pembelian = Pembelianproduk::firstOrNew([
                'provider_id'=>$res->id,
            ]);
            
            $produk_pembelian->product_id               = $res->code;
            $produk_pembelian->provider_id              = $res->id;
            $produk_pembelian->apiserver_id             = 1;
            $produk_pembelian->pembelianoperator_id     = $operator->id;
            $produk_pembelian->pembeliankategori_id     = $operator->pembeliankategori_id;
            $produk_pembelian->product_name             = $res->product_name;
            $produk_pembelian->desc                     = $res->product_name;
            $produk_pembelian->price_default            = $res->price;
            $produk_pembelian->price_markup             = !empty($produk_pembelian->price_markup) ? $produk_pembelian->price_markup : 0;
            $produk_pembelian->price                    = $produk_pembelian->price_default + $produk_pembelian->price_markup;
            $produk_pembelian->status                   = $res->status;
            $produk_pembelian->save();
        }

        Pembelianoperator::where('id', $operator->id)->update([
            'last_update' => Carbon::now(),
        ]);
    }
}