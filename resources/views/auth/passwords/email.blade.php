@extends('layouts.auth')
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
          
        <form action="{{ url('/password/email') }}" method="post">
            {{ csrf_field() }}
            {{ $captcha->form_field() }}
            <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Email akun" autocomplete="off">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                {!! $errors->first('email', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="row">
                <div class="col-md-8">
                   <div class="form-group has-feedback {{ $errors->has('captcha') ? ' has-error' : '' }}">
                      <input id="captcha" name="captcha" class="form-control" placeholder="Masukkan kode Captcha disamping" type="text">
                      <span class="fa fa-lock form-control-feedback"></span>
                      {!! $errors->first('captcha', '<p class="help-block"><small>:message</small></p>') !!}
                   </div>
                </div>
                <div class="col-md-4">
                   <div class="form-group">
                      {{ $captcha->html_image(['style' => 'max-height: 32px']) }}
                   </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="submit btn btn-primary btn-block btn-flat">Reset Password</button>
            </div>
        </form>
        <div align="center">
            <span>Ingat Kata Sandi?</span>
            <h5 style="margin-top:5px;margin-bottom:0px;font-weight:bold;font-size:17px;"><a href="{{url('/login')}}" class="custom__text-green">Masuk Sekarang</a></h5>
        </div>

    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->
@endsection