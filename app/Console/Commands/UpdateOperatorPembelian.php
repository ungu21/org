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
class UpdateOperatorPembelian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'operatorpembelian:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Operator Pembelian';

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
        $kategori_produk = Pembeliankategori::orderby('last_update','asc')->take(1)->get();
    
        //Insert Operator Produk PerKategori
        $operator = Pulsa::operator_pembelian($kategori_produk[0]->provider_id);
        
        if( $operator->success !== true ) {
            return;
        }
    
        $operator = $operator->data;

        foreach($operator as $data){
            $operator_produk = Pembelianoperator::firstOrNew([
                'provider_id'=>$data->id,
            ]);
            $operator_produk->apiserver_id          = 1;
            $operator_produk->product_id            = $data->product_id;
            $operator_produk->provider_id           = $data->id;
            $operator_produk->product_name          = $data->product_name;
            $operator_produk->prefix                = $data->prefix;
            $operator_produk->status                = $data->status;
            $operator_produk->pembeliankategori_id  = $kategori_produk[0]->id;
            $operator_produk->save();
        }
        //update Kategori Produk last update
        $update_kategori = Pembeliankategori::where('id',$kategori_produk[0]->id)->update([
            'last_update'=>Carbon::now(),
        ]);
    }
}
