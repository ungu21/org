<?php

namespace tripay\pulsa;

class Pulsa {

    protected $code;
    protected $trxid;
    protected $start_date;
    protected $end_date;
    protected $inquiry;
    protected $phone;
    protected $target;
    protected $product;
    protected $no_pelanggan;

    public function __construct()
    {
        $this->url_tripay    = config('tripay.api_baseurl');
        $this->apikey_tripay = config('tripay.api_key');
        $this->pin           = config('tripay.pin');
    }

    //MASTER CURL-------------------------------------------------------------------------------
    
    public function curlGET ($endpoint, $data = []){
        $url = ''.$this->url_tripay.'/'.ltrim($endpoint, "/").'';
        
        $header = array(
            'Accept: application/json',
            'Authorization: Bearer '.$this->apikey_tripay.'', // Ganti [apikey] dengan API KEY Anda
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($result == FALSE){
            return response()->json([
                'success'    => false, 
                'message'    => $error,
            ]);
        }

        return json_decode($result);
    }
    //END MASTER CURL----------------------------------------------------------------------------
    
    //MASTER CURL-------------------------------------------------------------------------------
    public function curlPOST ($endpoint, $data = []){
        $url = ''.$this->url_tripay.'/'.ltrim($endpoint, "/").'';
        
        $header = array(
            'Accept: application/json',
            'Authorization: Bearer '.$this->apikey_tripay.'', // Ganti [apikey] dengan API KEY Anda
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
       
        curl_close($ch);
    
        if($result == FALSE){
            // return 'cURL Error : '.curl_error($ch);
            return response()->json([
                'success'    => false, 
                'message'    => $error,
            ]);
        }
        $result = json_decode($result);
        
        return $result;
    }
    //END MASTER CURL----------------------------------------------------------------------------

    //TRANSAKSI
    
    public function trx_pembelian($inquiry, $code, $no_hp,$api_trxid, $no_meter_pln = null)
    {
        if($inquiry == 'PLN'){
            $data = array( 
                'inquiry'      => $inquiry, // konstan I OR PLN
                'code'         => $code, // kode produk
                'no_meter_pln' => $no_meter_pln,
                'phone'        => $no_hp,
                'api_trxid'    => $api_trxid,
                'pin'          => $this->pin, // pin member
            );
        }else{
            $data = array( 
                'inquiry'  => $inquiry, // konstan I OR PLN
                'code'     => $code, // kode produk
                'phone'    => $no_hp, // nohp pembeli
                'api_trxid'=>$api_trxid,
                'pin'      => $this->pin, // pin member
            );
        }

        $result = $this->curlPOST("transaksi/pembelian", $data);
        return $result;
    }   

    public function trx_pembayaran($tagihan_id)
    {
        $data = array( 
            'order_id'        => $tagihan_id,
            'pin'             => $this->pin, // Masukkan PIN user (anda)
        );

        $result = $this->curlPOST("transaksi/pembayaran", $data);
        return $result;
    }  

    //lain-lain
    public function cek_server()
    {
        $result = $this->curlGET("cekserver/");
        return $result;
    }    

    public function cek_saldo()
    {
        $result = $this->curlGET("ceksaldo/");
        return $result;
    }    

    public function history_trx()
    {
        $result = $this->curlGET("histori/transaksi/all/");
        return $result;
    }    

    public function history_trx_detail($trxid)
    {
        $data = array( 
            'trxid' => $trxid, // Kode Operator
        );

        $result = $this->curlPOST("histori/transaksi/detail", $data);

        return $result;
    }   

    public function history_trx_date($start_date, $end_date)
    {
        $data = array( 
            'start_date' => $start_date, // Start Date
            'end_date'   => $end_date, // End Date
        );

        $result = $this->curlPOST("histori/transaksi/bydate", $data);
        return $result;
    }

    //PEMBELIAN
    public function produk_all_list()
    {
        $result = $this->curlGET("pembelian/all-list/");
        return $result;
    }

    public function kategori_pembelian()
    {
        $result = $this->curlGET("pembelian/category/");
        return $result;
    }

    public function operator_pembelian($kategori_id = null)
    {
        if( !empty($kategori_id) ) {
            return $this->curlGET("pembelian/operator/bycategory/", array('id' => $kategori_id));
        }
        
        return $this->curlGET("pembelian/operator/");
    }

    public function produk_pembelian($kategori_id = null, $operator_id = null)
    {
        if( !empty($kategori_id) ) {
            return $this->curlGET("pembelian/produk/bycategory/", array('id' => $kategori_id));
        }
        elseif( !empty($operator_id) ) {
            return $this->curlGET("pembelian/produk/byoperator/", array('id' => $operator_id));
        }
        
        return $this->curlGET("pembelian/produk/");
    }

    public function detail_produk_pembelian($code)
    {
        $data = array( 
            'code' => $code, // Kode Operator
        );

        $result = $this->curlPOST("pembelian/produk/cek", $data);
        return $result;
    }

    //PEMBAYARAN
    public function cek_tagihan($produk, $phone, $no_pelanggan )
    {    
        $data = array( 
            'product'      => $produk,
            'phone'        => $phone,
            'no_pelanggan' => $no_pelanggan,
            'pin'          => $this->pin, // pin member
        );
        $result = $this->curlPOST("pembayaran/cek-tagihan", $data);
        return $result;
    }    
    
    public function delete_cektagihan($id)
    {
        return $this->curlPOST("pembayaran/cek-tagihan/delete/", array('id' => $id));
    }

    public function kategori_pembayaran()
    {
        $result = $this->curlGET("pembayaran/category/");
        return $result;
    }

    public function operator_pembayaran($kategori_id = null)
    {
        if( !empty($kategori_id) ) {
            return $this->curlGET("pembayaran/operator/bycategory/", array('id' => $kategori_id));
        }
            
        return $this->curlGET("pembayaran/operator/");
    }

    public function produk_pembayaran($kategori_id = null, $operator_id = null)
    {
        if( !empty($kategori_id) ) {
            return $this->curlGET("pembayaran/produk/bycategory/", array('id' => $kategori_id));
        }
        elseif( !empty($operator_id) ) {
            return $this->curlGET("pembayaran/produk/byoperator/", array('id' => $operator_id));
        }
        
        return $this->curlGET("pembayaran/produk/");
    }

    public function detail_produk_pembayaran($code)
    {
        return $this->curlPOST("pembayaran/produk/cek", array('code' => $code));
    }

}