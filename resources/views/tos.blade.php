@extends('layouts.app')
@section('title', 'Terms of Service | '.$GeneralSettings->nama_sistem)
@section('description', 'Terms of Service Agreement. '.$GeneralSettings->description)
@section('keywords', 'Distributor, Distributor Pulsa, Pulsa, Server Pulsa, Pulsa H2H, Pulsa Murah, distributor pulsa elektrik termurah dan terpercaya, Pulsa Isi Ulang, Pulsa Elektrik, Pulsa Data, Pulsa Internet, Voucher Game, Game Online, Token Listrik, Token PLN, Pascaprabayar, Prabayar, PPOB, Server Pulsa Terpercaya, Bisnis Pulsa Terpercaya, Bisnis Pulsa termurah, website pulsa, Cara Transaksi, Jalur Transaksi, API, H2H', 'Website')
@section('img', asset('assets/images/banner_1.png'))

@section('content')
      <section class="content">
         <div class="container">
            <div class="row static-page">
             @include('layouts/navpage')
               <div class="col-sm-8">
                  <div>
                     <h2 class="page-title">Ketentuan Layanan</h2>
                     <div class="content-page">
                        <div> 
                           <p>Syarat & ketentuan yang ditetapkan di bawah ini mengatur pemakaian jasa yang ditawarkan oleh {{$GeneralSettings->nama_sistem}} terkait penggunaan situs {{ url('/') }}. Pengguna disarankan membaca dengan seksama karena dapat berdampak kepada hak dan kewajiban Pengguna di bawah hukum.</p>
                           <br>
                           <p>Dengan mendaftar dan/atau menggunakan situs {{ url('/') }}, maka pengguna dianggap telah membaca, mengerti, memahami dan menyutujui semua isi dalam Syarat & ketentuan. Syarat & ketentuan ini merupakan bentuk kesepakatan yang dituangkan dalam sebuah perjanjian yang sah antara Pengguna dengan {{$GeneralSettings->nama_sistem}}. Jika pengguna tidak menyetujui salah satu, sebagian, atau seluruh isi Syarat & ketentuan, maka pengguna tidak diperkenankan menggunakan layanan di {{ url('/') }}.</p>
                           <!-- <h3>A. Hal Umum</h3> -->
                           <ol>
                              <li>
                                 @foreach($tos as $data)
                                    <h2 id="{{$data->slug}}"><strong style="text-transform: capitalize;">{{$data->title}}</strong></h2>
                                    {!! $data->content !!}
                                 @endforeach
                              </li>
                           </ol>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
@endsection