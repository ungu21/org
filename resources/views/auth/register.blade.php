@extends('layouts.auth')
@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href="#"><img src="{{asset('img/logo/'.$logoku[1]->img.'')}}" style="max-width:150px"></a>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        <form action="{{url('/register')}}" method="post">
            {{csrf_field()}}
            <div class="form-group has-feedback {{ $errors->has('name') ? ' has-error' : '' }}">
                <input type="text" name="name" class="form-control" placeholder="Nama Lengkap" value="{{ old('name') }}">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                {!! $errors->first('name', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                <input type="email" name="email" class="form-control" placeholder="Alamat Email" value="{{ old('email') }}">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                {!! $errors->first('email', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="form-group has-feedback {{ $errors->has('phone') ? ' has-error' : '' }}">
                <input type="number" name="phone" class="form-control" placeholder="Nomor Handphone" value="{{ old('phone') }}">
                <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                {!! $errors->first('phone', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="form-group has-feedback {{ $errors->has('city') ? ' has-error' : '' }}">
                <input type="text" name="city" class="form-control" placeholder="Kota Sekarang" value="{{ old('city') }}">
                <span class="glyphicon glyphicon-map-marker form-control-feedback"></span>
                {!! $errors->first('city', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                <input type="password" name="password" class="form-control" placeholder="Kata Sandi">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                {!! $errors->first('password', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="form-group has-feedback {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi Kata Sandi">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                {!! $errors->first('password_confirmation', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            <div class="form-group has-feedback {{ $errors->has('pin') ? ' has-error' : '' }}">
                <input type="number" name="pin" minlength="4" maxlength="4" class="form-control" placeholder="Buat 4 digit PIN transaksi">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                {!! $errors->first('pin', '<p class="help-block"><small>:message</small></p>') !!}
            </div>
            
            
            @if( !empty(Cookie::get('ref')) || !empty(request()->get('ref')) )
            <div class="form-group has-feedback">
            <input type="text" class="form-control" name="kode_referral" placeholder="Kode Refferal (opsional)" value="{{ !empty(Cookie::get('ref')) ? Cookie::get('ref') : request()->get('ref') }}" readonly disabled>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            @else
            <div class="form-group has-feedback {{ $errors->has('kode_referral') ? ' has-error' : '' }}">
            <input type="text" name="kode_referral" maxlength="4" class="form-control" placeholder="Kode Refferal (opsional)">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            @endif
            
            <p style="font-size: 13px;text-align:center;">Dengan menekan Daftar Akun, saya mengkonfirmasi telah menyetujui <a href="{{url('/tos')}}" class="custom__text-green" style="color: #378CFF;text-decoration: underline;">Syarat dan Ketentuan</a>, serta <a href="{{url('/privacy-policy')}}" class="custom__text-green" style="color: #378CFF;text-decoration: underline;">Kebijakan Privasi</a> {{$GeneralSettings->nama_sistem}}.</p>
            <div class="form-group">
                <button type="submit" class="submit btn btn-primary btn-block btn-flat">Daftar</button>
            </div>
        </form>
        <div align="center">
            <span>Sudah punya akun?</span>
            <h5 style="margin-top:5px;margin-bottom:0px;font-weight:bold;font-size:17px;"><a href="{{url('/login')}}" class="custom__text-green">Masuk Sekarang</a></h5>
        </div>

    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->
@endsection