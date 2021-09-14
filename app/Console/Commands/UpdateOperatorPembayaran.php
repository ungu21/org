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

class UpdateOperatorPembayaran extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'operatorpembayaran:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Operator Pembayaran';

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
        $kategori_produk = Pembayarankategori::orderby('last_update','asc')->take(1)->get();

        $operator = Pulsa::operator_pembayaran($kategori_produk[0]->provider_id);
        
        if( $operator->success !== true ) {
            return;
        }
        
        $operator = $operator->data;

        foreach($operator as $data){
            $operator_pembayaran = Pembayaranoperator::firstOrNew([
                'provider_id'=>$data->id
            ]);
            $operator_pembayaran->provider_id           = $data->id;
            $operator_pembayaran->apiserver_id          = 1;
            $operator_pembayaran->product_name          = $data->product_name;
            $operator_pembayaran->status                = $data->status;
            $operator_pembayaran->pembayarankategori_id = $kategori_produk[0]->id;
            $operator_pembayaran->save();
        }

        $update_kategori = Pembayarankategori::where('id',$kategori_produk[0]->id)->update([
            'last_update'=>Carbon::now()
        ]);
    }
}
