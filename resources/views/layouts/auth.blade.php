<?php $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Cache-control" content="public">
        <meta name="robot" content="index, follow">
        <title>Masuk | {{$GeneralSettings->nama_sistem}} - {{$GeneralSettings->motto}}</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- For Search Engine Meta Data  -->
        <meta name="description" content="e-Fazz adalah distributor dan agen pulsa All Operator, paket data, token PLN, voucher game dan pembayaran PPOB murah 24 jam transaksi secepat kilat bisnis menjadi untung." />
        <meta name="keywords" content="Agen Pulsa Murah, Pulsa Online 24 Jam, Distributor Pulsa, Bisnis menguntungkan, Pulsa Murah, distributor pulsa elektrik termurah dan terpercaya, Pulsa Isi Ulang, Pulsa Data, Pulsa Internet, Voucher Game, Game Online, Token Listrik, Token PLN, Pascaprabayar, Prabayar, PPOB, Server Pulsa Terpercaya, Bisnis Pulsa Terpercaya, Bisnis Pulsa termurah, website pulsa" />
        <meta name="author" content="{{$GeneralSettings->nama_sistem}}" />
        <meta property="business:contact_data:street_address" content="{{$GeneralSettings->alamat}}" />
        <meta property="business:contact_data:locality" content="Makassar" />
        <meta property="business:contact_data:postal_code" content="90222" />
        <meta property="business:contact_data:country_name" content="Indonesia" />
        <meta property="business:contact_data:email" content="{{$GeneralSettings->email}}" />
        <meta property="business:contact_data:phone_number" content="{{$GeneralSettings->hotline}}" />
        <meta property="business:contact_data:website" content="{{$GeneralSettings->website}}" />
        
        <!-- Social Media Metta -->
        <meta property="fb:admins" content="{{$GeneralSettings->nama_sistem}}"/>
        <meta property="og:site_name" content="{{$GeneralSettings->nama_sistem}}">
        <meta property="og:url" content="{{ $actual_link }}">
        <meta property="og:type" content="website">
        <meta property="og:title" content="Masuk | {{$GeneralSettings->nama_sistem}} - {{$GeneralSettings->motto}}">
        <meta property="og:description" content="Distributor & Server Pulsa Termurah dan Terlengkap yang menyediakan berbagai produk (Pulsa All operator, Pulsa Internet, Voucher Game Online, Token Listrik dan lain - lain).">
        <meta property="og:image" content="https://hijaupay.com/assets/images/slider/slider_ke2.png">
        
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="">
        <meta name="twitter:creator" content="">
        <meta name="twitter:url" content="{{ $actual_link }}">
        <meta name="twitter:title" content="Masuk | {{$GeneralSettings->nama_sistem}} - {{$GeneralSettings->motto}}">
        <meta name="twitter:description" content="Distributor & Server Pulsa Termurah dan Terlengkap yang menyediakan berbagai produk (Pulsa All operator, Pulsa Internet, Voucher Game Online, Token Listrik dan lain - lain).">
        <meta name="twitter:image" content="https://tripay.co.id/assets/images/banner_1.png">
        
        <!-- Add to home screen for mobile -->
        <link rel="manifest" href="/manifest.json">
        <!-- for Safari on iOS -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="#006CAA">
        <meta name="apple-mobile-web-app-title" content="{{$GeneralSettings->nama_sistem}}">
        <link rel="apple-touch-icon" href="/assets/images/icons/icon-152x152.png">
        <!-- for windows -->
        <meta name="msapplication-TileImage" content="/assets/images/icons/icon-144x144.png">
        <meta name="msapplication-TileColor" content="#2F3BA2">
        
        <!-- Favicon -->
        @if(isset($logoku[0]))
            @if($logoku[0]->img !='' || $logoku[0]->img !=null)
            <link rel="shortcut icon" type="image/icon" href=" {{asset('img/logo/'.$logoku[0]->img.'')}}" style="width:16px;height: 16px;"/>
            @endif
        @endif
        <!-- Bootstrap 3.3.5 -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome-animation/0.0.10/font-awesome-animation.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.11/css/AdminLTE.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="{{asset('/admin-lte/plugin/iCheck/square/blue.css')}}">
    
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            input[type=number]::-webkit-inner-spin-button, 
            input[type=number]::-webkit-outer-spin-button { 
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                margin: 0; 
            }
            .login-page,.register-page{
                background:#fff
            }
            
        </style>
        @yield('style')
    </head>
    <body class="hold-transition login-page" style="height:80%;">
        @yield('content')

        <!-- jQuery 2.1.4 -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <!-- Bootstrap 3.3.5 -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <!-- iCheck -->
        <script src="{{asset('/admin-lte/plugin/iCheck/icheck.min.js')}}"></script>
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
            });
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%' // optional
                });
            });
            $('.submit').on('click', function(){
               $('.submit').html("<i class='fa fa-spinner faa-spin animated' style='margin-right:5px;'></i> Loading...");
               $('.submit').attr('style', 'cursor:not-allowed;pointer-events: none;');
            });

            function maxLengthCheck(object)
            {
            if (object.value.length > object.maxLength)
                object.value = object.value.slice(0, object.maxLength)
            }
        </script>
        @yield('js')
    </body>
</html>