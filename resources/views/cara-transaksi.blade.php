@extends('layouts.app')
@section('title', 'Cara Transaksi | '.$GeneralSettings->nama_sistem.' - '.$GeneralSettings->motto)
@section('description', 'Cara Transaksi di '.$GeneralSettings->nama_sistem.'. '.$GeneralSettings->description)
@section('keywords', 'Distributor, Distributor Puslsa, Pulsa, Server Pulsa, Pulsa H2H, Pulsa Murah, distributor pulsa elektrik termurah dan terpercaya, Pulsa Isi Ulang, Pulsa Elektrik, Pulsa Data, Pulsa Internet, Voucher Game, Game Online, Token Listrik, Token PLN, Pascaprabayar, Prabayar, PPOB, Server Pulsa Terpercaya, Bisnis Pulsa Terpercaya, Bisnis Pulsa termurah, website pulsa, Cara Transaksi, Jalur Transaksi, API, H2H', 'Website')
@section('img', asset('assets/images/banner_1.png'))

@section('content')
<!-- Start Slideshow Section -->
<section id="slideshow">
   <div class="container">
      <div class="row">
         <div class="no-slider" style="margin-top: 100px;">
            <div class="animate-block" style="text-align: center;">
            <div class="col-md-6 col-md-offset-3">
               <h2><span id="word-rotating">Cara Transaksi</span></h2>
               <p style="margin-top: 10px;margin-bottom: 80px;">Bersama Kami kembangkan bisnis anda.</p>
              </div>
            </div> <!--/ animate-block -->
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
</section>
<!-- End Slideshow Section -->

<!-- Start Feature Section -->
<section id="feature" class="padding-2x">
   <div class="container">
      <div class="row">
         <div class="col-md-12" style="margin-bottom: 30px;">
            <div class="section-heading text-center" style="margin-bottom: 30px;">
               <h2 class="title">Mulai Bisnis Anda Bersama {{ $GeneralSettings->nama_sistem }}</h2>
               <p>3 Langkah Mudah memulai bisnis anda bersama kami</p>
            </div>
         </div>
         <div class="col-sm-4 col-md-4">
            <!-- Start User Friendly Block -->
            <div class="box-content text-center">
               <div class="block-icon">
                  <span aria-hidden="true" class="icon-user-follow fa-3x text-primary"></span>
               </div>
               <h3>Melakukan Pendaftaran</h3>
               <p>Pendaftaran tanpa biaya 100% Gratis, setelah mendaftar akun anda langsung aktif dan dapat melakukan deposit.</p>
            </div>
            <!-- End Block -->
         </div>
         <div class="col-sm-4 col-md-4">
            <!-- Start Supper Fast Block -->
            <div class="box-content text-center">
               <div class="block-icon">
                  <span class="icon-wallet fa-3x text-primary"></span>
               </div>
               <h3>Melakukan Deposit Saldo</h3>
               <p>Langkah selanjutnya melakukan Deposit Saldo minimal Rp 10.000 agar dapat digunakan untuk transaksi semua produk terlengkap dari kami.</p>
            </div>
            <!-- End Block -->
         </div>
         <div class="col-sm-4 col-md-4">
            <!-- Start Analytics Block -->
            <div class="box-content text-center">
               <div class="block-icon">
                  <span class="icon-basket fa-3x text-primary"></span>
               </div>
               <h3>Melakukan Transaksi</h3>
               <p>Langkah terakhir melakukan transaksi pulsa anda dengan produk pulsa terlengkap dan termurah dari kami {{$GeneralSettings->nama_sistem}}.</p>
            </div>
            <!-- End Block -->
         </div>
      </div>
   </div>
</section>
<!-- End Feature Section -->

<section id="feature" class="grey-bg padding-2x">
    <div class="container">
        <div class="row">
         <div class="col-md-12" style="margin-bottom: 30px;">
            <div class="section-heading text-center" style="margin-bottom: 30px;">
               <h2 class="title">Jalur Transaksi</h2>
               <p>Kami memiliki beberapa jalur untuk melakukan transaksi.</p>
            </div>
         </div>
         <div class="col-sm-4 col-md-4">
            <!-- Start User Friendly Block -->
            <div class="box-content text-center">
               <div class="block-icon">
                  <span aria-hidden="true" class="fa fa-globe fa-3x"></span>
               </div>
               <h3>Transaksi Via Website</h3>
               <p>Jalur transaksi kami saat ini adalah melalui website yang dapat di akses melalui perangkat komputer atau perangkat smartphone anda.</p>
            </div>
            <!-- End Block -->
         </div>
         {{-- <div class="col-sm-4 col-md-4">
            <!-- Start Supper Fast Block -->
            <div class="box-content text-center">
               <div class="block-icon">
                  <span class="fa fa-mobile fa-3x text-primary"></span>
               </div>
               <h3>Melalui Aplikasi</h3>
               <p>Jalur transaksi selanjutnya juga kami menyediakan melalui aplikasi yang dapat di download melalui playstore (coming soon).</p>
            </div>
            <!-- End Block -->
         </div> --}}
         <div class="col-sm-4 col-md-4">
            <!-- Start Analytics Block -->
            <div class="box-content text-center">
               <div class="block-icon">
                  <span class="fa fa-mobile fa-3x text-primary"></span>
               </div>
               <h3>Transaksi Via Aplikasi</h3>
                <p>Kami juga menyediakan Transaksi via aplikasi yang dapat di download di 
                <a href="https://play.google.com/store/apps/details?id=notfound">Play Store</a></p>
            </body>

            </div>
            <!-- End Block -->
         </div>
         <div class="col-sm-4 col-md-4">
            <div class="box-content text-center">
               <div class="block-icon">
                  <span class="fa fa-code fa-3x text-primary"></span>
               </div>
               <h3>Transaksi Via API</h3>
               <p>API {{$GeneralSettings->nama_sistem}} merupakan jalur yang dapat digunakan agen/mitra/host untuk bertransaksi dengan cepat dan stabil (coming soon).</p>
            </div>
         </div>
      </div>
    </div>
</section>

<section id="twitter-feed" class="grey-bg padding-1x">
   <div class="container">
      <div class="row">
         <div class="col-md-8 col-md-offset-2">
            <div class="section-heading text-center">
               <h2 class="title" style="font-style: italic;">"{{$GeneralSettings->motto}}"</h2>
            </div>
         </div>
      </div>
   </div>
</section>
@endsection