<?php

Route::get('/tes', function() {
    
});

Route::post('/captcha', function() {
    $captcha = Captcha::chars('0123456789')->length(4)->size(130, 50)->generate();
    return response()->json([
        'success'   => true,
        'id'        => $captcha->id(),
        'image'     => $captcha->image()
        ]);
});

Route::post('/callback/tripay_payment', 'PaymentTripayController@callbackPaymentTripay');
Route::post('/callback/tripay', 'TripayCallbackController@listen');

Route::get('/', 'HomeController@index');
Route::get('/page/{slug}','HomeController@staticPage');
Route::get('/cara-transaksi', 'HomeController@caraTransaksi');
Route::get('/price/pembelian/{slug}', 'HomeController@pricePembelian');
Route::get('/price/pembayaran/{slug}', 'HomeController@pricePembayaran');
Route::get('/deposit', 'HomeController@deposit');
Route::get('/testimonial', 'HomeController@testimonial');
Route::get('/faq', 'HomeController@faq');
Route::post('/messages', 'HomeController@sendMessage');
Route::get('/api-docs', 'HomeController@apiDocs');
Route::get('/contact','HomeController@contact');

Route::group(['prefix' => 'process'], function() {
    Route::get('/findproduct', 'HomeController@findproduct');
    Route::get('/findproduct/pembayaran', 'HomeController@findproductPembayaran');
    Route::get('/prefixproduct', 'HomeController@prefixproduct');
    Route::get('/getoperator', 'HomeController@getoperator');
});

Route::post('voucher/generate-code', 'Admin\VoucherController@generateCode')->name('voucher.generateCode');
Route::get('/transaksi-pembayaran/process', 'Member\PembayaranController@transaksiProcess');
Route::get('/transaksi/process', 'Member\PembelianController@transaksiProcess');

Route::get('/print/{id}.pdf', 'PrintTransaksiController@printShow');
Route::get('/print-trx-save/{id}.pdf', 'PrintTransaksiController@printSave');
Route::get('/print-trx/{id}', 'PrintTransaksiController@printEdit');

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');


Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');