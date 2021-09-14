@extends('layouts.auth')
@section('style')
<style>
     .verification-otp{
            font-size: 14px;
            color: #8799AD;
            text-align: left !important;
            margin-top: 0.5rem;
        }

        .verification-otp a{
            color: #0CB4FF !important;
            cursor: pointer;
        }

        .verification-otp a:hover{
            color: #0083BC !important;
        }
</style>
@endsection
@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href="#"><img src="{{asset('img/logo/'.$logoku[1]->img.'')}}" style="max-width:150px"></a>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        
         @if(Session::has('alert-success'))
            <div class="alert alert-success alert-dismissable">
               <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
               <h4><i class="fa fa-check"></i>Berhasil</h4>
               <p>{!! Session::get('alert-success') !!}</p>
            </div>
          @endif
         @if(Session::has('alert-error'))
            <div class="alert alert-danger alert-dismissable">
               <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
               <h4><i class="fa fa-check"></i>Error</h4>
               <p>{!! Session::get('alert-error') !!}</p>
            </div>
          @endif
          
        <form action="{{ url('/cek_otp') }}" method="post">
            {{ csrf_field() }}
           <input type="hidden" name="id" value="{{session('otp_id')}}">
            <div class="form-group has-feedback {{ $errors->has('kode_otp') ? ' has-error' : '' }}">
                <input type="number" name="kode_otp" class="form-control" value="" placeholder="Masukan Kode OTP" autocomplete="off">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                {!! $errors->first('kode_otp', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="form-group">
                <button type="submit" class="submit btn btn-primary btn-block btn-flat">Submit</button>
            </div>

            <div class="verification-otp">
                @if(session('countDown') > time())
                <span id="countDown" class="text__countDown">{{ gmdate('i:s', session('countDown') - time()) }}</span>
                @else
                    <span id="resend_text">Belum menerima Kode OTP ?</span>
                    <a onclick="resend()" id="resend">Kirim Ulang</a>
                @endif
            </div>
        </form>

    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->
@endsection 
@section('js')
<script>
    function startTimer(duration, display){
        console.log(duration);
        var timer = duration, minutes, seconds;
        setInterval(function(){
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if(--timer < 0){
                timer = duration;
                console.log(timer);
                $('#resend_text').remove();
                $('#resend').remove();
                $('#countDown').remove();
                $('.verification-otp').append('<span id="resend_text">Belum menerima kode otp ? </span><a onclick="resend()" id="resend">Kirim Ulang</a>');
            }
        }, 1000);
    }

    window.onload = function(){
        var twoMinutes = {{ session('countDown') - time() }}
            display = document.querySelector('#countDown');
        startTimer(twoMinutes, display);
    };

    function resend(){
        var form = '<form action="{{ url('/create_otp') }}" method="post" id="formResend">';
            form += '<input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '">';
            form += '<input type="hidden" name="id" value="{{ session('otp_id') }}">';
            form += '<input type="hidden" name="is_resend" value="1">';
            form += '</form>';

            $('body').append(form);

            $form = $("#formResend");
            $form.submit();
    }
</script>
@endsection