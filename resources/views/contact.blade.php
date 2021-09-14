@extends('layouts.app')
@section('title', 'Kontak Kami | '.$GeneralSettings->nama_sistem.' - '.$GeneralSettings->motto)
@section('description', 'Jika ada pertanyaan, Pengaduan, Kritik dan Saran silahkan Kontak kami '.$GeneralSettings->description)
@section('keywords', 'Distributor, Distributor Puslsa, Pulsa, Server Pulsa, Pulsa H2H, Pulsa Murah, distributor pulsa elektrik termurah dan terpercaya, Pulsa Isi Ulang, Pulsa Elektrik, Pulsa Data, Pulsa Internet, Voucher Game, Game Online, Token Listrik, Token PLN, Pascaprabayar, Prabayar, PPOB, Server Pulsa Terpercaya, Bisnis Pulsa Terpercaya, Bisnis Pulsa termurah, website pulsa, Cara Transaksi, Jalur Transaksi, API, H2H', 'Website')
@section('img', asset('assets/images/banner_1.png'))

@section('content')
<!-- Start Slideshow Section -->
<div class="temp-wrap">
  <section id="content-highlight" class="">
    <section class="content">
     <div class="container">
        <div class="row">
              <div class="col-md-6 col-md-offset-3">
                 <div class="section-heading center" style="margin-bottom: 30px;">
                    <h2 class="page-title">Kontak Kami</span></h2>
                 </div>
                 <p style="margin-top: 10px;margin-bottom: 10px;" class="center">Pusat Pengaduan & Bantuan {{$GeneralSettings->nama_sistem}}</p>
                 <p style="margin-top: 10px;margin-bottom: 80px;" class="center">Email : cs@p-store.net</p>
              </div> <!--/ animate-block -->
              <div class="clearfix"></div>
        </div>
     </div>
    </section>
  </section>
  <!-- End Slideshow Section -->
  <!-- Start Feature Section -->
  <section id="content-highlight" class="">
    <section class="content">
     <div class="container">
        <div class="col-md-12" style="margin-bottom: 30px;">
           <div class="section-heading" style="margin-bottom: 30px;line-height:1.5">
              <p>Dewasa ini, Pulsa, Voucher Game, Kuota Internet, PPOB merupakan kebutuhan yang sangat penting bagi semua kalangan, mulai dari kalangan bawah sampai masyarakat kalangan atas. Oleh karena itu bisnis Pulsa, Voucher Game, Kuota Internet, PPOB pada saat ini sangat menguntungkan.<br/>
  Setiap orang butuh  Pulsa, Voucher Game, Kuota Internet, PPOB entah siang, malem, sore ataupun pagi, akan tetapi banyak outlet/konter yang buka hanya pada jam tertentu.<br/>
  Selain itu terkadang sobat P-Store.Net yang ingin memulai bisnis Pulsa, Voucher Game, Kuota Internet, PPOB belum punya modal besar ataupun kawatir mengeluarkan modal besar untuk bisnis yang baru dijalani, atau mungkin sobat P-Store.Net belum tau dimana Distributor & Server Pulsa, Voucher Game, Kuota Internet, PPOB Termurah dan Terlengkap<br/><br/>
  Atas dasar hal diatas, maka kami P-Store.Net telah menghadirkan anak usaha baru yakni Tripay.co.id dengan tagline “Bisnis Pembayaran Dalam Satu Genggaman”  yang memiliki banyak kelebihan sebagai berikut :<br/><br/></p>
  <p>
  <ol>
      <li><b>Gratis</b><br/>
          Biaya Pendaftaran 100% Gratis, tak perlulah sobat P-Store.Net mengeluarkan uang sepeserpun untuk mendaftar di Tripay.co.id
      </li>
      <li><b>Harga Murah</b><br/>
          Langsung dibuktikan saja bukan cuman ngaku tapi emang beneran murah
      </li>
      <li><b>Transaksi Cepat 24Jam</b><br/>
          Tengah malem maupun siang bolong juga bisa, jadi aman
      </li>
      <li><b>Produk Lengkap</b><br/>
          Mulai dari pulsa, paket data, voucher game, token PLN, sampai bayar cicilan juga bisa
      </li>
      <li><b>CS ramah</b><br/>
          Sumber daya kami sangat ramah & siap menjawab pertanyaan yg ada
      </li>
      <li><b>Transaksi lewat Web & Aplikasi</b><br/>
          Transaksi online 24 jam jadi tidak ada biaya untuk SMS, untung makin banyak, Download aplikasinya di PlayStore : <a href="https://goo.gl/rckn7L">https://goo.gl/rckn7L</a>
      </li>
      <li><b>Minimal Deposit</b><br/>
          Cukup dengan minimal deposit 50rb saja anda sudah bisa menjalankan bisnis pembayaran
      </li>
      <li><b>Bonus Deposit</b><br/>
          Ada sistem bonus deposit untuk referal
      </li>
      <li><b>Aman & Terpercaya️</b>
          Tripay.co.id adalah anak usaha P-Store.Net yang anda tentu sudah mengenal kredibilitas kami selama bertahun-tahun dalam berbisnis online
      </li>
  </ol>
  <br/>
  Bagaimana sobat setelah melihat penjabaran diatas pasti sobat ingin segera berbisnis Pulsa, Voucher Game, Kuota Internet & PPOB, transaksinya juga bisa lewat Aplikasi Android lho <a href="https://goo.gl/rckn7L">https://goo.gl/rckn7L</a> jadi tunggu apalagi, langsung saja daftar ke Tripay.co.id  Distributor & Server Pulsa, Voucher Game, Kuota Internet, PPOB Termurah dan Terlengkap.<br/><br/>
  Ingat ya sobat langsung daftar ke Tripay.co.id Distributor & Server Pulsa, Voucher Game, Kuota Internet, PPOB Termurah dan Terlengkap
  <br/><br/>
  </p>
  <p style="text-align:center">
      <button class="submit btn btn-green" onclick="javascript:window.location.assign('/register');"> DAFTAR SEKARANG </button>
  </p>
           </div>
        </div>
        
     </div>
  </section>
  </section>
  <!-- End Feature Section -->

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
</div>
@endsection