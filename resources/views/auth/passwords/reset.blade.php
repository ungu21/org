@extends('layouts.auth')
@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href="#"><img src="{{asset('img/logo/'.$logoku[1]->img.'')}}" style="max-width:150px"></a>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        
        @if(Session::has('alert-error'))
            <div class="alert alert-danger" role="alert">{{ Session::get('alert-error') }}</div>
        @endif
        
        @if(Session::has('alert-success'))
            <div class="alert alert-danger" role="alert">{{ Session::get('alert-success') }}</div>
        @endif
        
        <form action="{{ url('/password/reset') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                <input type="email" name="email" class="form-control" value="{{ $email ?? old('email') }}" placeholder="Alamat Email">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                {!! $errors->first('email', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                <input type="password" name="password" class="form-control" placeholder="Kata Sandi Baru">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                {!! $errors->first('password', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="form-group has-feedback {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi Kata Sandi Baru">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                {!! $errors->first('password_confirmation', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="form-group">
                <button type="submit" class="submit btn btn-primary btn-block btn-flat">Ubah Kata Sandi</button>
            </div>
        </form>
        <div align="center">
            <span>Ingat Kata Sandi?</span>
            <h5 style="margin-top:5px;margin-bottom:0px;font-weight:bold;font-size:17px;"><a href="{{url('/login')}}" class="custom__text-green">Masuk Sekarang</a></h5>
        </div>

    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->
@endsection