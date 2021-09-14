<?php

use Illuminate\Support\Facades\Route;


Route::get('/', 'IndexController@index');
Route::get('/prabayar','IndexController@prabayar');
Route::get('/pascabayar','IndexController@pascabayar');
Route::get('/me', function(){
    $user_id = Auth::user()->id;
    return Response::json($user_id);
});

//======================================================= Route Deposit ==========================================================
Route::get('/deposit', 'DepositController@index');
Route::post('/process/depositsaldo', 'DepositController@depositsaldo')->name('process.bank.deposit');
Route::post('/process/depositsaldo/get-data-bank', 'DepositController@getBankDeposit')->name('get.bank.deposit');
Route::get('/va_number/{id}','DepositController@showNomerVa');
Route::get('/bank-cek/{id}','DepositController@bank_cek');
//======================================================== End Deposit ===========================================================


//====================================================== Riwayat Deposit =========================================================
Route::get('/deposit/{id}', 'RiwayatDepositController@showDeposit');
Route::post('/deposit/konfirmasi', 'RiwayatDepositController@konfirmasiPembayaran');
Route::get('/riwayat_deposit','RiwayatDepositController@riwayat_deposit');
//=================================================== End Riwayat Deposit ========================================================


//=================================================== Route Transfer Saldo =======================================================
Route::get('/transfer-saldo', 'TransferSaldoController@transferSaldo');
Route::post('/transfer-saldo/cek-nomor', 'TransferSaldoController@cekNomor');
Route::post('/transfer-saldo/kirim', 'TransferSaldoController@kirimSaldo');
//================================================== End Route Tranfer Saldo =====================================================


//==================================================== Route Mutasi Saldo ========================================================
Route::get('/mutasi-saldo', 'MutasiSaldoController@mutasiSaldo');
Route::post('/mutasi-saldo-datatables', 'MutasiSaldoController@mutasiSaldoDatatables')->name('get.mutasiSaldo.datatables');
//=================================================== End Route Mutasi Saldo =====================================================


//================================================= Route Transaksi Pascabayar ===================================================
Route::get('/bayar/{slug}', 'PembayaranController@index');
Route::get('/tagihan-pembayaran', 'TagihanController@tagihanPembayaran');
Route::post('/tagihan-pembayaran-datatables', 'TagihanController@tagihanPembayaranDatatables')->name('get.tagihan-pembayaran.datatables');
Route::get('/tagihan-pembayaran/{id}', 'TagihanController@showTagihan');
Route::post('/process/findproductpembayaran', 'PembayaranController@findproductpembayaran');
Route::post('/bayar/getTypehead/tagihan', 'PembayaranController@getTypeheadTagihan')->name('beli.get.typehead.tagihan');
//=============================================== End Route Transaksi Pascabayar =================================================


//=============================================== Route Cek Transaksi Pascabayar =================================================
Route::post('/process/cektagihan', 'CekTagihanController@cektagihan');
Route::post('/process/cektagihan/home', 'CekTagihanController@cektagihanhome');
//============================================= End Route Cek Transaksi Pascabayar ===============================================


//============================================== Route Bayar Transaksi Pascabayar ================================================
Route::post('/process/bayartagihan', 'BayarTagihanController@bayartagihan')->name('bayartagihan');
//============================================ End Route Bayar Transaksi Pascabayar ============================================== 


//================================================= Route Transaksi Prabayar =====================================================
Route::get('/beli/{slug}', 'PembelianController@index');
Route::get('/process/getoperator', 'PembelianController@getOperator');
Route::get('/process/findproduct', 'PembelianController@findproduct');
Route::get('/process/prefixproduct', 'PembelianController@prefixproduct');
Route::post('/beli/getTypehead', 'PembelianController@getTypehead')->name('beli.get.typehead');
Route::post('/beli/getTypehead/pln', 'PembelianController@getTypeheadPLN')->name('beli.get.typehead.pln');
//=============================================== End Route Transaksi Prabayar ===================================================


//============================================= Route Order Prabayar Controller ==================================================
Route::post('/process/orderproduct', 'OrderPrabayarController@orderproduct');
Route::post('/process/orderproduct/home', 'OrderPrabayarController@orderproducthome');
//=========================================== End Route Order Prabayar Controller ================================================


//==================================================== Route Transfer Bank =======================================================
Route::get('/transfer-bank', 'TransferBankController@index');
Route::post('/transfer-bank/process', 'TransferBankController@process');
Route::get('/transfer-bank/history', 'TransferBankController@history');
Route::get('/transfer-bank/history/datatables', 'TransferBankController@historyDatatables');
Route::get('/transfer-bank/history/show/{id}', 'TransferBankController@show');
Route::get('/transfer-bank/history/print/{id}', 'TransferBankController@printStruk');
Route::get('/transfer-bank/get-bank', 'TransferBankController@getBankCode')->name('get.bank.code');
//================================================== End Route Transfer Bank =====================================================


//======================================================= Route Profile ==========================================================
Route::get('/profile', 'ProfilController@index');
Route::get('/biodata', 'ProfilController@biodata');
Route::post('/biodata', 'ProfilController@storeBiodata');
Route::get('/pusat-informasi', 'ProfilController@pusatInformasi');
Route::get('/ubah-password', 'ProfilController@password');
Route::post('/ubah-password', 'ProfilController@updatePassword');
Route::get('/picture', 'ProfilController@picture');
Route::post('/picture', 'ProfilController@updatePicture');
Route::get('/testimonial', 'ProfilController@testimonial');
Route::post('/testimonial', 'ProfilController@sendTestimonial');
Route::get('/rekening-bank', 'ProfilController@rekening')->name('index.rekening-bank');
Route::post('/tambah-rekening-bank', 'ProfilController@insertRekening');
Route::get('/pin', 'ProfilController@pin')->name('get.profile.pin');
Route::get('/pin/request', 'ProfilController@getPinSend')->name('get.profile.request.pin');
Route::get('/pin/generate', 'ProfilController@getPinGenerate')->name('get.profile.generate.pin');
Route::post('/pin/ubah', 'ProfilController@ubahPin')->name('get.profile.ubah.pin');
Route::get('/api-credentials', 'ProfilController@apiCredentials')->name('profile.api_credentials');
Route::post('/api-credentials', 'ProfilController@updateApiCredentials')->name('profile.api_credentials.update');
Route::post('/api-key', 'ProfilController@getApiKey')->name('profile.api_key');
//===================================================== End Route Profile ========================================================


//==================================================== Route Validasi User =======================================================
Route::get('/validasi-users', 'ValidationUserController@index')->name('validation.user.index');
Route::post('/validasi-users/store', 'ValidationUserController@store')->name('validation.user.store');
//================================================== End Route Validasi User =====================================================


//===================================================== Route Membership =========================================================
Route::group(['prefix' => 'membership'], function() {
    Route::get('/', 'MembershipController@index');
    Route::post('/upgrade-level', 'MembershipController@upgradelevel')->name('membership.upgrade.level');
    Route::post('/pay-upgrade-level', 'MembershipController@payUpgradelevel')->name('pay.membership.upgrade.level');
    Route::post('/extend-level', 'MembershipController@extendLevel')->name('pay.membership.extend.level');
    Route::post('/buy-periode', 'MembershipController@buyperiodemembership');
});
//==================================================== End Route Membership ======================================================


//====================================================== Route Referral ==========================================================
Route::get('/referral', 'ReferralController@referral');
Route::post('/referral/kode_referral','ReferralController@kode_referral');
Route::get('/referral-datatables', 'ReferralController@referralDatatables')->name('get.referral.datatables');
Route::get('/bonus-transaksi', 'ReferralController@bonusTransaksi');
Route::get('/komisi-trx-ref', 'ReferralController@bonusKomisi');
Route::post('/komisi-trx-ref-datatablesOne', 'ReferralController@bonusKomisiDatatablesOne')->name('get.komisi-trx-ref.datatablesOne');
Route::post('/komisi-trx-ref-datatables', 'ReferralController@bonusKomisiDatatables')->name('get.komisi-trx-ref.datatables');
//===================================================== End Route Referral ======================================================= 


//============================================== Route Produk dan Riwayat Transaksi ==============================================
Route::get('/trx-print/{id}', 'RiwayatController@printEdit');
Route::post('/trx-print/{id}', 'RiwayatController@printShow');
Route::post('/trigger-online', 'IndexController@triggerOnline');
Route::get('/harga-produk/pembelian/{slug}', 'IndexController@pricePembelian');
Route::get('/harga-produk/pembayaran/{slug}', 'IndexController@pricePembayaran');
Route::post('/beli/riwayat-transaksi-datatables', 'PembelianController@riwayatTransaksiDatatables')->name('beli.get.riwayat.datatables');
Route::get('/riwayat-transaksi', 'RiwayatController@riwayatTransaksi');
Route::post('/riwayat-transaksi-datatables', 'RiwayatController@riwayatTransaksiDatatables')->name('get.riwayat.datatables');
Route::get('/riwayat-transaksi/{id}', 'RiwayatController@showTransaksi');
Route::get('/trx-print/{id}', 'RiwayatController@printShow');
//============================================ End Route Produk dan Riwayat Transaksi ============================================ 


//==================================================== Route Layanan Bantuan =====================================================
Route::get('/layanan-bantuan', 'LayananBantuan@index');
Route::get('/messages', 'MessageController@index');
Route::get('/messages-show/{id}', 'MessageController@show')->name('member.message.show');
Route::delete('/messages/delete/{id}', 'MessageController@destroy')->name('member.message.delete');
Route::post('/messages/kirim', 'MessageController@store');
Route::post('/messages/reply', 'MessageController@reply');
//================================================== End Route Layanan Bantuan ===================================================


//===================================================== Route Voucher ============================================================
Route::get('/redeem-voucher', 'RedeemController@index');
Route::post('/redeem-voucher', 'RedeemController@redeemVoucher');
//=================================================== End Route Voucher ==========================================================


Route::get('/pin/request/success', function(){
    return redirect()->route('get.profile.pin')->with('alert-success', 'Behasil Mengirim Pin ke no anda!');
})->name('get.profile.request.pin.success');

Route::get('/pin/generate/success', function(){
    return redirect()->route('get.profile.pin')->with('alert-success', 'Behasil Regenerate Pin dan dikirm ke no anda!');
})->name('get.profile.generate.pin.success');

Route::get('/pin/ubah/success', function(){
    return redirect()->route('get.profile.pin')->with('alert-success', 'Behasil Merubah Pin anda!');
})->name('get.profile.ubah.pin.success');

Route::get('/pin/ubah/error', function(){
    return redirect()->route('get.profile.pin')->with('alert-error', 'Gagal Merubah Pin, Password anda salah!');
})->name('get.profile.ubah.pin.error');

Route::get('/pin/ubah/invalid', function(){
    return redirect()->route('get.profile.pin')->with('alert-error', 'Gagal Merubah Pin, PIN harus dalam format angka 4 digit!');
})->name('get.profile.ubah.pin.invalid');    



