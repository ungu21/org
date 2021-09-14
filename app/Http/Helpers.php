<?php

function userAuth(){
	return \Auth::user();
}

function setting(){
	return \App\AppModel\setting::first();
}

function price($nominal){
	return 'Rp '. number_format($nominal, 0, '.', '.');
}

function unspace($string){
	return str_replace(' ', '', $string);
}

function dateInd($data){
	return \Carbon\Carbon::parse($data->created_at)->format('d-m-Y H:i:s');
}

function produkMembershipAuth($product_id){
    $personal_role   = '1';
    $admin_role      = '2';
    $agen_role       = '3';
    $enterprise_role = '4';
    
	$rolesId = Auth::user()->roles()->first()->id;
	$user = Auth::user();
	
	if($rolesId == $personal_role || $rolesId == $admin_role){
		$produk = \App\AppModel\V_pembelianproduk_personal::select('id','product_id','pembelianoperator_id','pembeliankategori_id','product_name','desc','price_default','price_markup','status','price','created_at','updated_at')->where('product_id', $product_id)->first();
    }elseif($rolesId == $agen_role){
		$produk = \App\AppModel\V_pembelianproduk_agen::select('id','product_id','pembelianoperator_id','pembeliankategori_id','product_name','desc','price_default','price_markup','status','price','created_at','updated_at')->where('product_id', $product_id)->first();                                                                                         
    }elseif($rolesId == $enterprise_role){
		$produk = \App\AppModel\V_pembelianproduk_enterprise::select('id','product_id','pembelianoperator_id','pembeliankategori_id','product_name','desc','price_default','price_markup','status','price','created_at','updated_at')->where('product_id', $product_id)->first();	
    }

    return $produk;
}

function coderef(){
	return url('/').'/register?ref='.sprintf("%04d", Auth::user()->id);
}

function number_grouping($n, $precision = 1)
{
	if ($n < 900) {
		$n_format = number_format($n, $precision, ',', '');
		$suffix = '';
	} else if ($n < 900000) {
		$n_format = number_format($n/1000, $precision, ',', '');
		$suffix = 'Rb';
	} else if ($n < 900000000) {
		$n_format = number_format($n/1000000, $precision, ',', '');
		$suffix = 'Jt';
	} else if ($n < 900000000000) {
		$n_format = number_format($n/1000000000, $precision, ',', '');
		$suffix = 'M';
	} else {
		$n_format = number_format($n/1000000000000, $precision, ',', '');
		$suffix = 'T';
	}
	
	if ( $precision > 0 ) {
		$dotzero = ',' . str_repeat('0', $precision);
		$n_format = str_replace($dotzero, '', $n_format);
	}
	
	return $n_format . $suffix;
}



