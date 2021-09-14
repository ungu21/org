@extends('layouts.app')
@section('title', 'Tentang Kami | '.$GeneralSettings->nama_sistem.' - '.$GeneralSettings->motto)
@section('description', 'Perkenalan singkat mengenai kami '.$GeneralSettings->nama_sistem.'. '.$GeneralSettings->description)
@section('keywords', 'Distributor, Distributor Pulsa, Pulsa, Server Pulsa, Pulsa H2H, Pulsa Murah, distributor pulsa elektrik termurah dan terpercaya, Pulsa Isi Ulang, Pulsa Elektrik, Pulsa Data, Pulsa Internet, Voucher Game, Game Online, Token Listrik, Token PLN, Pascaprabayar, Prabayar, PPOB, Server Pulsa Terpercaya, Bisnis Pulsa Terpercaya, Bisnis Pulsa termurah, website pulsa, Cara Transaksi, Jalur Transaksi, API, H2H', 'Website')
@section('img', asset('assets/images/banner_1.png'))

@section('content')
<style>
    @media screen and (max-width: 780px) {
        
        #nama{
            font-size:12px;
        }
        
    }
</style>

      <section class="content">
         <div class="container">
            <div class="row static-page">
             @include('layouts/navpage')
               <div class="col-sm-8">
                  <div>
                     <h2 class="page-title">Tentang Kami</h2>
                     <div class="content-page">
                        <div>
                           <h2 style="margin-bottom: 0px;">Ya, kami {{$GeneralSettings->nama_sistem}} ({{$GeneralSettings->motto}})</h2>
                           Kami adalah penyedia layanan isi ulang Pulsa, Paket Internet, Token PLN, Voucher Game Online, Voucher TV dan layanan PPOB secara online 24 jam Non Stop.
                        </div>
                        <div>
                           <br/>
                           "Kami percaya semua orang mempunyai kemampuan dalam berbisnis tetapi kesempatan itu yang belum datang kepada mereka. Kami hadir memberikan peluang kepada anda untuk memulai bisnis anda dan menjadi bagian dari kami dalam mewujudkan INDONESIA MANDIRI."
                        </div>
                        <div>
                           <br/>
                           Berdiri di tahun 2017, {{$GeneralSettings->nama_sistem}} merupakan bisnis rintisan dengan layanan Distributor, Server & Agen Pulsa All Operator berbasis web yang berfokus terhadap kualitas layanan pembelian, pembayaran pulsa, pengalaman konsumen & Kualitas harga termurah. Kami terus berupaya memberikan pengalaman layanan terbaik serta membangun sistem automasi yang efektif dan efisien demi kenyamanan konsumen dalam bertransaksi. {{-- Kami telah melayani lebih dari 32.000 customer dari seluruh penjuru Indonesia --}}
                        </div>
                        <div>
                           <br/>
			               <h2 style="margin-bottom: 0px;">Tim Kami</h2>
			               Kami lebih dari sekedar rekan bisnis.<br> kami adalah Teman yang lebih dari Sahabat dan lebih dalam seperti Keluarga.
                        </div>

                     </div>
                  </div>
                  <div>
                     <h2 class="page-title">Alamat</h2>
                     <div class="content-page">
                        <div>
                           <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126933.05719128522!2d106.5799930239489!3d-6.176512298030374!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f8e853d2e38d%3A0x301576d14feb9c0!2sTangerang%2C+Kota+Tangerang%2C+Banten!5e0!3m2!1sid!2sid!4v1463841572371"
                              width="100%" height="350" frameborder="0" style="border:0">
                           </iframe>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
@endsection