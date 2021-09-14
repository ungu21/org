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
        @if (session('alert-error'))
            <div class="alert alert-error alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                <h4><i class="fa fa-ban fa-fw" style="margin-right: 5px;"></i>{{ session('alert-error') }}</h4>
            </div>
        @endif
        
        <form action="{{url('/login')}}" method="post">
            {{csrf_field()}}
            <div class="form-group has-feedback {{ $errors->has('phone') ? ' has-error' : '' }}">
                <input type="number" name="phone" class="form-control" placeholder="Nomor Handphone">
                <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                {!! $errors->first('phone', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}" style="margin-bottom:0px;">
                <input type="password" name="password" class="form-control" placeholder="Kata Sandi">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                {!! $errors->first('password', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="row" style="margin-bottom: 0px;">
                <div class="col-xs-6">
                    <div class="checkbox icheck" align="left">
                        <label>
                            <input type="checkbox" class="custom__check-green" name="remember" checked> <span style="margin-left:5px;">Ingat Saya</span>
                        </label>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="checkbox icheck" align="right">
                        <a href="{{ url('/password/reset') }}" class="custom__text-green" style="text-decoration: underline;"><span>Lupa Kata Sandi</span></a>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="submit btn btn-primary btn-block btn-flat">Masuk</button>
            </div>
        </form>
        
        <!--<div class="social-auth-links text-center">
            <p>- OR -</p>
           <a href="{{ url('auth/facebook') }}" class="btn btn-block btn-social btn-facebook">
                <i class="fa fa-facebook"></i> Login dengan Facebook
            </a>
            <a href="{{ url('auth/google') }}" class="btn btn-block btn-social btn-google">
                <i class="fa fa-google-plus" style="font-size:17px;"></i> Login dengan Google+
            </a>
        </div>-->
        <div align="center">
            <span>Belum punya akun?</span>
            <h5 style="margin-top:5px;margin-bottom:0px;font-weight:bold;font-size:17px;"><a href="{{url('/register')}}" class="custom__text-green">Daftar Sekarang</a></h5>
        </div>

    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->
@endsection 