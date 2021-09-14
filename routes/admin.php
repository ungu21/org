<?php
Route::group(['middleware'=>['auth', 'role:admin']], function() {
    Route::get('/block-phone', 'BlockPhoneController@index')->name('admin.blokir.telephone.index');
    Route::post('/block-phone/store', 'BlockPhoneController@store')->name('admin.blokir.telephone.store');
    Route::post('/block-phone/update', 'BlockPhoneController@update')->name('admin.blokir.telephone.update');
    Route::delete('/block-phone/destroy/{id}', 'BlockPhoneController@destroy')->name('admin.blokir.telephone.destroy');

    Route::get('/log-viewer-laravel', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::get('/pin', 'IndexController@getPin');

    Route::get('/', 'IndexController@indexNew');
    Route::get('/GetDataByRage', 'IndexController@getByDate');
    Route::get('/GetDataByMonth', 'IndexController@getByMonth');

    Route::group(['prefix' => 'validasi-users'], function() {
        Route::get('/', 'ValidasiUserController@index')->name('data.validasi-users.index');
        Route::get('/get-data', 'ValidasiUserController@getDatatable')->name('data.validasi-users.datatables');
        Route::get('/{id}', 'ValidasiUserController@showDetail')->name('data.validasi-users.show-detail');
        Route::get('/approve/{id}', 'ValidasiUserController@approveValidasi');
        Route::get('/nonapprove/{id}', 'ValidasiUserController@nonapproveValidasi');
    });

    Route::post('/users/lock', 'UserController@lockUsers')->name('lock.admin.users');
    Route::post('/users/unlock', 'UserController@unlockUsers')->name('unlock.admin.users');
    Route::get('/users/block-saldo/{id}','UserController@lockSaldo');
    Route::get('/users/unblock-saldo/{id}','UserController@unlockSaldo');

    Route::group(['prefix' => 'membership'], function() {
        Route::get('/', 'MembershipController@index')->name('data.validasi-upgrade.index');
        Route::get('/show/{id}','MembershipController@show');
        Route::post('/get-data', 'MembershipController@getDatatable')->name('data.validasi-membership.datatables');
        Route::get('/approve/{id}', 'MembershipController@approveValidasi');
        Route::get('/nonapprove/{id}', 'MembershipController@nonapproveValidasi');
    });

    Route::post('/mode', 'IndexController@mode');
    Route::get('/get-member', 'IndexController@getMember');
    Route::get('/ceksaldo', 'IndexController@ceksaldo');

    Route::group(['prefix' => 'transaksi'], function() {
        Route::get('/antrian', 'AntrianTransaksiController@transaksiAntrian');
        Route::post('/antrian/datatables', 'AntrianTransaksiController@transaksiAntrianProdukDatatables');
        Route::get('/antrian/{id}', 'AntrianTransaksiController@showTransaksiAntrian');
        Route::delete('/antrian/hapus/{id}', 'AntrianTransaksiController@transaksiAntrianHapus');
        Route::get('/produk', 'TransaksiProdukController@transaksiProduk');
        Route::post('/produk/datatables', 'TransaksiProdukController@transaksiProdukDatatables');
        Route::get('/produk/{id}', 'TransaksiProdukController@showTransaksiProduk');
        Route::delete('/produk/hapus/{id}', 'TransaksiProdukController@transaksiHapus');
        Route::post('/produk/refund/{id}', 'TransaksiProdukController@refundTransaksiProduk');
        Route::post('/produk/ubahStatus/{id}', 'TransaksiProdukController@ubahStatusTransaksiProduk');
        Route::get('/deposit', 'TransaksiDepositController@transaksiSaldo');
        Route::post('/deposit/datatables', 'TransaksiDepositController@transaksiSaldoDatatables');
        Route::get('/deposit/show/{id}', 'TransaksiDepositController@depositShow');
        Route::get('/deposit/menunggu/{id}', 'TransaksiDepositController@depositMenunggu');
        Route::get('/deposit/validasi/{id}', 'TransaksiDepositController@depositValidasi');
        Route::get('/deposit/success/{id}', 'TransaksiDepositController@depositSuccess');
        Route::get('/deposit/gagal/{id}', 'TransaksiDepositController@depositGagal');
        Route::delete('/deposit/hapus/{id}', 'TransaksiDepositController@depositHapus');
        Route::get('/tagihan', 'TransaksiTagihanController@transaksiTagihan');
        Route::post('/tagihan/datatables', 'TransaksiTagihanController@transaksiTagihanDatatables');
        Route::get('/tagihan/{id}', 'TransaksiTagihanController@showTransaksiTagihan');
        Route::delete('/tagihan/hapus/{id}', 'TransaksiTagihanController@hapusTransaksiTagihan');
        Route::get('/tagihan/menunggu/{id}', 'TransaksiTagihanController@tagihanMenunggu');
        Route::get('/tagihan/refund/{id}', 'TransaksiTagihanController@tagihanRefund');
        Route::get('/tagihan/success/{id}', 'TransaksiTagihanController@tagihanSuccess');
        Route::get('/tagihan/gagal/{id}', 'TransaksiTagihanController@tagihanGagal');
        Route::get('/redeem', 'RedeemController@redeem');
        Route::get('/redeem/detail/{id}', 'RedeemController@redeemDetail');
        Route::delete('/redeem/hapus/{id}', 'RedeemController@redeemHapus');
        
        Route::get('/transfer-bank/datatables', 'TransferBankController@datatables');
        Route::group(['prefix' => 'transfer-bank'], function() {
            Route::get('/', 'TransferBankController@index');
            Route::get('/{id}', 'TransferBankController@show');
            Route::get('/pending/{id}', 'TransferBankController@statusPending');
            Route::get('/refund/{id}', 'TransferBankController@statusRefund');
            Route::get('/success/{id}', 'TransferBankController@statusSuccess');
            Route::post('/failed/{id}', 'TransferBankController@statusFailed');
        });

        
    });

    Route::get('/static-page/{slug}', 'StaticPageController@edit');
    Route::post('/static-page/{slug}', 'StaticPageController@store');

    Route::get('/setting/security', 'SettingSecurityController@index');
    Route::post('/setting/security', 'SettingSecurityController@store');

    Route::get('/setting', 'SettingController@indexSetting');
    Route::post('/setting/{id}', 'SettingController@storeSetting');

    Route::post('/send-informasi', 'IndexController@sendInformasi');
    Route::delete('/delete-informasi/{id}', 'IndexController@deleteInformasi');

    Route::post('/users/deposit-manual', 'UserController@depositManual');
    Route::post('/users/ubah-saldo-manual', 'UserController@ubahSaldoManual');

    Route::get('/broadcast-sms', 'BroadcastController@indexSMS');
    Route::post('/broadcast-sms/check', 'BroadcastController@checkSMS');
    Route::post('/broadcast-sms/send', 'BroadcastController@sendBroadcastSMS');

    Route::get('/broadcast-email', 'BroadcastController@indexEmail');
    Route::post('/broadcast-email/test', 'BroadcastController@testEmail');
    Route::post('/broadcast-email/send', 'BroadcastController@sendBroadcastEmail');

    Route::get('/pembayaran-produk/update-harga-semua', 'PembayaranprodukController@updateHargaSemua')->name('update.pembayaran.harga.semua');
    Route::get('/pembayaran-produk/update-harga-peroperator', 'PembayaranprodukController@updateHargaPerOperator')->name('update.pembayaran.harga.peroperator');
    Route::get('/pembayaran-produk/update-harga-perkategori', 'PembayaranprodukController@updateHargaPerKategori')->name('update.pembayaran.harga.perkategori');

    Route::get('/pembayaran-produk/update-harga-sum-markup-bykategori', 'PembayaranprodukController@updateHaragSumMakupPerKategori')->name('update.pembayaran.harga.sum.markup.perkategori');
    Route::get('/pembayaran-produk/update-harga-sum-markup-byoperator', 'PembayaranprodukController@updateHaragSumMakupPerOperator')->name('update.pembayaran.harga.sum.markup.peroperator');
    Route::delete('/pembayaran-produk/delete/{id}', 'PembayaranprodukController@destroy')->name('admin.pembayaranProduk.delete');

    Route::get('/process-cari-pembayaran/findproduct', 'PembayaranprodukController@findproduct');

    Route::post('/pembayaran-produk/import', 'PembayaranprodukController@import');
    Route::post('/pembayaran-produk/importAllData', 'PembayaranprodukController@importAllData');
    Route::get('/pembayaran-produk', 'PembayaranprodukController@index')->name('admin.pembayaranProduk.index');
    Route::get('/pembayaran-produk/create/{slug}', 'PembayaranprodukController@create');
    Route::post('/pembayaran-produk/store', 'PembayaranprodukController@store');
    Route::get('/pembayaran-produk/{slug}/edit/{id}', 'PembayaranprodukController@edit');
    Route::patch('/pembayaran-produk/update/{id}', 'PembayaranprodukController@update');
    Route::get('/pembayaran-produk/{slug}', 'PembayaranprodukController@showbyKategori');

    Route::group(['prefix' => 'pembelian-produk'], function() {
        Route::group(['prefix' => '/markup'], function() {
            Route::group(['prefix' => '/role-personal'], function() {
                Route::get('/', 'Markup\PembelianprodukPersonalController@index')->name('admin.produkPersonal.index');
                Route::get('/update-harga-semua', 'Markup\PembelianprodukPersonalController@updateHargaSemua');
                Route::get('/update-harga-peroperator', 'Markup\PembelianprodukPersonalController@updateHargaPerOperator');
                Route::get('/update-harga-perkategori', 'Markup\PembelianprodukPersonalController@updateHargaPerKategori');
                Route::post('/update-harga-sum-markup-bykategori', 'Markup\PembelianprodukPersonalController@updateHaragSumMakupPerKategori');
                Route::post('/update-harga-sum-markup-byoperator', 'Markup\PembelianprodukPersonalController@updateHaragSumMakupPerOperator');
                Route::delete('/produk/delete/{id}', 'Markup\PembelianprodukPersonalController@destroy')->name('admin.produkPersonal.delete');

                Route::get('/{slug}', 'Markup\PembelianprodukPersonalController@showbyKategori');
                Route::get('/{slug}/edit/{id}', 'Markup\PembelianprodukPersonalController@edit');
                Route::patch('/update/{id}', 'Markup\PembelianprodukPersonalController@update');
                Route::post('/delete', 'Markup\PembelianprodukPersonalController@deleteAllProduk');
                Route::get('/plusminusmarkup', 'Markup\PembelianprodukPersonalController@import');
                Route::post('/plusminusmarkupAllData', 'Markup\PembelianprodukPersonalController@plusminusmarkupAllData');

                Route::get('/process-cari/findproduct', 'Markup\PembelianprodukPersonalController@findproduct');
            });

            Route::group(['prefix' => '/role-agen'], function() {
                Route::get('/', 'Markup\PembelianprodukAgenController@index')->name('admin.produkAgen.index');
                Route::get('/update-harga-semua', 'Markup\PembelianprodukAgenController@updateHargaSemua');
                Route::get('/update-harga-peroperator', 'Markup\PembelianprodukAgenController@updateHargaPerOperator');
                Route::get('/update-harga-perkategori', 'Markup\PembelianprodukAgenController@updateHargaPerKategori');
                Route::post('/update-harga-sum-markup-bykategori', 'Markup\PembelianprodukAgenController@updateHaragSumMakupPerKategori');
                Route::post('/update-harga-sum-markup-byoperator', 'Markup\PembelianprodukAgenController@updateHaragSumMakupPerOperator');
                Route::delete('/produk/delete/{id}', 'Markup\PembelianprodukAgenController@destroy')->name('admin.produkAgen.delete');

                Route::get('/{slug}', 'Markup\PembelianprodukAgenController@showbyKategori');
                Route::get('/{slug}/edit/{id}', 'Markup\PembelianprodukAgenController@edit');
                Route::patch('/update/{id}', 'Markup\PembelianprodukAgenController@update');
                Route::post('/delete', 'Markup\PembelianprodukAgenController@deleteAllProduk');
                Route::get('/plusminusmarkup', 'Markup\PembelianprodukAgenController@import');
                Route::post('/plusminusmarkupAllData', 'Markup\PembelianprodukAgenController@plusminusmarkupAllData');

                Route::get('/process-cari/findproduct', 'Markup\PembelianprodukAgenController@findproduct');
            });

            Route::group(['prefix' => '/role-enterprise'], function() {
                Route::get('/', 'Markup\PembelianprodukEnterpriseController@index')->name('admin.produkEnterprise.index');
                Route::get('/update-harga-semua', 'Markup\PembelianprodukEnterpriseController@updateHargaSemua');
                Route::get('/update-harga-peroperator', 'Markup\PembelianprodukEnterpriseController@updateHargaPerOperator');
                Route::get('/update-harga-perkategori', 'Markup\PembelianprodukEnterpriseController@updateHargaPerKategori');
                Route::post('/update-harga-sum-markup-bykategori', 'Markup\PembelianprodukEnterpriseController@updateHaragSumMakupPerKategori');
                Route::post('/update-harga-sum-markup-byoperator', 'Markup\PembelianprodukEnterpriseController@updateHaragSumMakupPerOperator');
                Route::delete('/produk/delete/{id}', 'Markup\PembelianprodukEnterpriseController@destroy')->name('admin.produkEnterprise.delete');

                Route::get('/{slug}', 'Markup\PembelianprodukEnterpriseController@showbyKategori');
                Route::get('/{slug}/edit/{id}', 'Markup\PembelianprodukEnterpriseController@edit');
                Route::patch('/update/{id}', 'Markup\PembelianprodukEnterpriseController@update');
                Route::post('/delete', 'Markup\PembelianprodukEnterpriseController@deleteAllProduk');
                Route::get('/plusminusmarkup', 'Markup\PembelianprodukEnterpriseController@import');
                Route::post('/plusminusmarkupAllData', 'Markup\PembelianprodukEnterpriseController@plusminusmarkupAllData');

                Route::get('/process-cari/findproduct', 'Markup\PembelianprodukEnterpriseController@findproduct');
            });
        });
    });

    Route::post('/users/get-Users', 'UserController@datataBlesUsers')->name('get.admin.users.datatables');


    Route::post('usersedit/pin/generate', 'UserController@getPinGenerate')->name('get.usersedit.generate.pin');

    Route::resource('pembelian-kategori', 'PembeliankategoriController');
    Route::resource('pembelian-operator', 'PembelianoperatorController');
    Route::resource('pembayaran-kategori', 'PembayarankategoriController');
    Route::resource('pembayaran-operator', 'PembayaranoperatorController');
    Route::resource('pusat-informasi', 'InformasiController');
    Route::resource('voucher', 'VoucherController');
    Route::resource('users', 'UserController');
    Route::resource('bank', 'BankController');
    Route::resource('testimonial', 'TestimonialController');
    Route::resource('faqs', 'FaqController');
    Route::resource('messages', 'MessageController');
    Route::resource('tos', 'TosController');

    Route::get('/messages/show/{id}', 'MessageController@show')->name('admin.message.show');
    Route::delete('/messages/delete/{id}', 'MessageController@destroy')->name('admin.message.delete');
    Route::post('/messages/reply', 'MessageController@reply');
    Route::post('/messages/kirim', 'MessageController@store');

    Route::group(['prefix' => 'kontrol-menu'], function() {
        Route::get('/', 'KontrolMenuController@index')->name('kontrol.menu.index');
        Route::get('get-data-menu', 'KontrolMenuController@getDataMenu')->name('kontrol.menu.getdata.menu');
        Route::get('get-data-submenu', 'KontrolMenuController@getDataSubMenu')->name('kontrol.menu.getdata.submenu');

        //edit
        Route::get('edit-menu/{id}', 'KontrolMenuController@editMenu');
        Route::get('edit-submenu/{id}', 'KontrolMenuController@editSubmenu');
        Route::get('edit-submenu2/{id}', 'KontrolMenuController@editSubmenu2');
        Route::patch('updateSaveMenu/{id}', 'KontrolMenuController@updateSaveMenu');
        Route::patch('updateSaveSubmenu/{id}', 'KontrolMenuController@updateSaveSubmenu');
        Route::patch('updateSaveSubmenu2/{id}', 'KontrolMenuController@updateSaveSubmenu2');

        Route::post('nonaktifkan-menu', 'KontrolMenuController@nonaktifkanMenu')->name('menu.kontrol.nonaktifkan');
        Route::post('aktifkan-menu', 'KontrolMenuController@aktifkanMenu')->name('menu.kontrol.aktifkan');
        Route::post('nonaktifkan-sub-menu', 'KontrolMenuController@nonaktifkanSubMenu')->name('sub.menu.kontrol.nonaktifkan');
        Route::post('aktifkan-sub-menu', 'KontrolMenuController@aktifkanSubMenu')->name('sub.menu.kontrol.aktifkan');
        Route::post('nonaktifkan-sub2-menu', 'KontrolMenuController@nonaktifkanSubMenu2')->name('sub2.menu.kontrol.nonaktifkan');
        Route::post('aktifkan-sub2-menu', 'KontrolMenuController@aktifkanSubMenu2')->name('sub2.menu.kontrol.aktifkan');

        //aktif-nonaktif all menu

        Route::post('aktifkan-all-menu1', 'KontrolMenuController@aktifkanAllMenu1')->name('all.menu.aktifkan.menu1');
        Route::post('nonaktifkan-all-menu1', 'KontrolMenuController@nonaktifkanAllMenu1')->name('all.menu.nonaktifkan.menu1');
        Route::post('aktifkan-all-menu2', 'KontrolMenuController@aktifkanAllMenu2')->name('all.menu.aktifkan.menu2');
        Route::post('nonaktifkan-all-menu2', 'KontrolMenuController@nonaktifkanAllMenu2')->name('all.menu.nonaktifkan.menu2');
        Route::post('aktifkan-all-menu3', 'KontrolMenuController@aktifkanAllMenu3')->name('all.menu.aktifkan.menu3');
        Route::post('nonaktifkan-all-menu3', 'KontrolMenuController@nonaktifkanAllMenu3')->name('all.menu.nonaktifkan.menu3');

        //Menu Dashboard
        Route::post('aktif-all-menu_dashboard','KontrolMenuController@statusAllMenuDashboard');
        Route::post('status/menu-dashboard','KontrolMenuController@statusMenuDashboard');
        Route::get('edit-menu-dashboard/{id}','KontrolMenuController@editMenuDashboard');
        Route::post('update-menu-dashboard','KontrolMenuController@updateMenuDashboard');
    });

    Route::group(['prefix' => 'banner'], function() {
        Route::get('/', 'BannerMenuController@index')->name('banner.menu.index');
        Route::get('/create', 'BannerMenuController@create')->name('banner.menu.create');
        Route::post('/store', 'BannerMenuController@store')->name('upload-gambar.store');
        Route::post('/update', 'BannerMenuController@update')->name('upload-gambar.update');
        Route::delete('/delete/{id}', 'BannerMenuController@delete')->name('delete.banner');
        Route::get('/edit-banner/{id}', 'BannerMenuController@edit')->name('edit.banner');
    });

    Route::group(['prefix' => 'logo'], function() {
        Route::get('/', 'LogoController@index');
        Route::post('/store', 'LogoController@store');
    });


    Route::group(['prefix' => 'setting-layanan-bantuan'], function() {
        Route::get('/', 'SettingLayananBantuanController@index');
        Route::post('/store', 'SettingLayananBantuanController@store');
    });

    Route::group(['prefix' => 'setting-deposit'], function() {
        Route::get('/', 'SettingDepositController@index')->name('setting.deposit.index');
        Route::patch('/update', 'SettingDepositController@update')->name('setting.deposit.update');
        Route::post('/fee_deposit','SettingDepositController@fee_deposit')->name('setting.fee.deposit.update');
    });

    Route::group(['prefix'=>'setting-transfer-bank'],function(){
        Route::get('/','SettingTransferBankController@index');
        Route::post('/update','SettingTransferBankController@update');
        Route::post('/update-fee','SettingTransferBankController@update_fee');
    });

    Route::group(['prefix' => 'setting-bonus'], function() {
        Route::get('/', 'SettingBonusController@index')->name('setting.bonus.index');
        Route::patch('/update/{id}', 'SettingBonusController@update')->name('setting.bonus.update');
    });

    Route::group(['prefix' => 'setting-kurs'], function() {
        Route::get('/', 'SettingKursController@index')->name('setting.kurs.index');
        Route::patch('/update', 'SettingKursController@update')->name('setting.kurs.update');
    });

    Route::group(['prefix' => 'sms-gateway'], function(){
        Route::get('/', 'SMSController@index');
        Route::get('/outbox', 'SMSController@outbox');
        Route::delete('/outbox/hapus/{id}', 'SMSController@outboxDelete');
        Route::post('/outbox/datatables', 'SMSController@outboxDatatables');
        Route::post('/send', 'SMSController@send');
        Route::get('/setting', 'SMSController@setting');
        Route::post('/setting', 'SMSController@updateSetting');
    });

    Route::get('/pengumuman', 'PengumumanController@index');
    Route::post('/pengumuman', 'PengumumanController@store');

        
    Route::post('bank/edit_data_provider','BankController@edit_data_provider');
    Route::put('bank/status/{id}','BankController@status');
    Route::post('bank/edit_data_bank','BankController@edit_data_bank');
    Route::post('bank/edit_data','BankController@edit_data');
    Route::post('bank/akunPaypal','BankController@akunpaypal');
    Route::post('bank/editakunPaypal','BankController@edit_akun_paypal');

    Route::get('/setting-transfer-saldo','SettingMinTransferController@index');
    Route::post('/setting-transfer-saldo','SettingMinTransferController@update'); 
});

?>