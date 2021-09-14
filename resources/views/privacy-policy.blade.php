@extends('layouts.app')
@section('title', 'Kebijakan Privasi | '.$GeneralSettings->nama_sistem)
@section('description', 'Kebijakan Privasi '.$GeneralSettings->nama_sistem.'. '.$GeneralSettings->description)
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
               <h2><span id="word-rotating">Kebijakan Privasi</span></h2>
               <p style="margin-top: 10px;margin-bottom: 80px;">Pemutakhiran terakhir 01 Juli 2017</p>
              </div>
            </div> <!--/ animate-block -->
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
</section>
<!-- End Slideshow Section -->


<!-- Start In-depth Section -->
<section id="feature" class="padding-2x">
   <div class="container">
      <p>Sebagai penyedia layanan/Distributor & Agen Pulsa Murah All Operator, kami {{$GeneralSettings->nama_sistem}} sangat menjunjung tinggi privasi customer/member. Hal ini karena informasi pribadi merupakan hal yang sangat krusial dan tidak boleh diketahui siapapun. Berikut akan kami jelaskan mengenai informasi apa saja yang kami terima dan kami kumpulkan pada saat Anda mengunjungi situs {{$GeneralSettings->nama_sistem}}. Serta, tentang bagaimana kami menyimpan dan menjaga informasi tersebut. Kami tegaskan bahwa kami tidak akan pernah memberikan informasi tersebut kepada siapapun.</p><br>
      <p>
         <strong>Tentang file log</strong><br>
         Seperti situs lain pada umumnya, kami mengumpulkan dan menggunakan data yang terdapat pada file log. Informasi yang terdapat pada file log termasuk alamat IP (Internet Protocol) Anda, ISP (Internet Service Provider), browser yang Anda gunakan, waktu pada saat Anda berkunjung serta halaman mana saja yang Anda buka selama berkunjung di {{$GeneralSettings->nama_sistem}}.
      </p><br>
      <p>
         <strong>Tentang cookies</strong><br>
         Situs kami menggunakan cookies untuk menyimpan berbagai informasi seperti preferensi pribadi pada saat mengunjungi situs {{$GeneralSettings->nama_sistem}} serta informasi login. {{$GeneralSettings->nama_sistem}} juga menggunakan layanan tracking dari pihak ketiga untuk mendukung situs kami. Beberapa layanan tersebut mungkin menggunakan cookies ketika melakukan tracking di situs kami. {{$GeneralSettings->nama_sistem}} bekerja sama dengan layanan tracker seperti Google AdWords, Google Analytics, AdRoll serta CrazyEgg. Dimana informasi yang dikirim dapat berupa alamat IP, ISP, browser, sistem operasi yang Anda pakai, dan sebagainya. Hal ini tentu saja memiliki tujuan yaitu digunakan untuk penargetan iklan berdasarkan relevansi informasi.
      </p>
	</div>
</section>

         <section class="download-separator hidden-xs">&nbsp;</section>
         <section id="download" class="download">
            <div class="container">
               <div class="download-text">
                  <p class="title">Download Aplikasi {{$GeneralSettings->nama_sistem}}</p>
                  <!-- <p>{{$GeneralSettings->motto}}</p> -->
                  <div><a href="https://play.google.com/store/apps/details?id=apps.tripay.co.id" class="download-link googleplay">&nbsp;</a></div>
                  <h2 class="title" style="font-style: italic;"><p>"{{$GeneralSettings->motto}}"</p></h2>
               </div>
               <div class="download-img hidden-xs">&nbsp;</div>
            </div>
         </section>
@endsection