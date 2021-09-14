@extends('layouts.admin')
@section('style')
<style>
    .d-none{
        display:none;
    }
    .d-block{
        display:block;
    }
</style>
@endsection
@section('content')
<section class="content-header">
	<h1>Tambah <small>Bank</small></h1>
   <ol class="breadcrumb">
    	<li><a href="{{url('/admin')}}" class="btn-loading"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="Javascript:;">Pengaturan</a></li>
      <li><a href="{{route('bank.index')}}" class="btn-loading">Data Bank</a></li>
      <li class="active">Ubah Data Bank</li>
   </ol>
   </section>
   <section class="content">
      <div class="row">
         <div class="col-md-6">
            <div class="box box-primary">
               <div class="box-header">
                 <h3 class="box-title"><a href="{{route('bank.index')}}" class="hidden-lg btn-loading"><i class="fa fa-arrow-left" style="margin-right:10px;"></i></a>Ubah Data Bank</h3>
               </div>
               <form role="form" action="{{route('bank.update', $banks->id)}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
               <input name="_method" type="hidden" value="PATCH">
               {{csrf_field()}}
                  <div class="box-body">
                     <div class="form-group{{ $errors->has('nama_bank') ? ' has-error' : '' }}">
                        <label>Nama Bank : </label>
                        <input type="text" class="form-control" name="nama_bank" value="{{ $banks->nama_bank}}"  placeholder="Masukkan Nama Bank">
                        {!! $errors->first('nama_bank', '<p class="help-block"><small>:message</small></p>') !!}
                     </div>
                     <div class="form-group{{ $errors->has('atas_nama') ? ' has-error' : '' }}">
                        <label>Nama Pemilik : </label>
                        <input type="text" class="form-control" name="atas_nama" value="{{ $banks->atas_nama}}"  placeholder="Masukkan Nama Pemilik Rekening">
                         {!! $errors->first('atas_nama', '<p class="help-block"><small>:message</small></p>') !!}
                     </div>
                     <div class="form-group{{ $errors->has('no_rek') ? ' has-error' : '' }}">
                        <label>Nomor Rekening : </label>
                        <input type="text" class="form-control" name="no_rek" value="{{ $banks->no_rek }}"  placeholder="Masukkan Nomor Rekening Rekening">
                         {!! $errors->first('no_rek', '<p class="help-block"><small>:message</small></p>') !!}
                     </div>
                     <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
                        <label>Kode Bank : </label>
                        <input type="text" class="form-control" name="code" value="{{ $banks->code}}"  placeholder="Masukkan Kode Bank">
                         {!! $errors->first('code', '<p class="help-block"><small>:message</small></p>') !!}
                     </div>
                    
                     <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                        <label>Logo / Gambar Bank : </label>
                        <input type="file" name="image" accept="image/*">
                        <img src="{{asset('img/banks/'.$banks->image)}}" class="img-thumbnail" style="height: 40px;margin-top: 5px;"  alt="{{$banks->nama_bank}}">
                         {!! $errors->first('image', '<p class="help-block"><small>:message</small></p>') !!}
                     </div>
                     <div class="form-group{{ $errors->has('nama_provider') ? ' has-error' : '' }}" id="provider">
                        <label>Bank Kategori : </label>
                        <select name="bank_kategori_id"  class="form-control" id="category_bank">
                           @foreach($bank_kategori as $item)
                           <option {{$banks->bank_kategori_id ==  $item->id ? 'selected':''}} value="{{$item->id}}">{{$item->paymethod}}</option>
                           @endforeach
                        </select>
                     </div>
                      <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }} d-none" id="type">
                        <label>Type</label>
                        <select name="type" class="form-control">
                           <option value="1" {{$banks->is_closed == 1 ? 'selected' : ''}}>Close Payment</option>
                           <option value="0" {{$banks->is_closed == 0 ? 'selected' : ''}}>Open Payment</option>
                        </select>
                     </div>
                     <div class="form-group{{ $errors->has('nama_provider') ? ' has-error' : '' }}" id="provider">
                        <label>Provider : </label>
                        <select name="provider"  class="form-control" value="{{$banks->provider_id}}">
                           @foreach($provider as $item)
                           <option {{$banks->provider_id ==  $item->id ? 'selected':''}} value="{{$item->id}}">{{$item->name}}</option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="box-footer">
                     <button type="reset" class="btn btn-default">Reset</button>
                     <button type="submit" class="submit btn btn-primary">Simpan</button>
                  </div>
               </form>
            </div>
         </div>
         <div class="col-md-6">
            <div class="box box-solid box-penjelasan">
               <div class="box-header with-border">
                    <i class="fa fa-text-width"></i>
                    <h3 class="box-title">Penjelasan Form</h3>
                    <div class="box-tools pull-right box-minus" style="display:none;">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
               </div><!-- /.box-header -->
               <div class="box-body">
                  <dl>
                     <dt>Nama Bank</dt>
                     <dd style="font-size: 12px;">Isi dengan Nama Bank (contoh : BRI, BNI, BCA, Dll)</dd>
                     <dt>Nama Pemilik</dt>
                     <dd style="font-size: 12px;">Isi dengan Nama Pemilik Rekening yang tercantum dalam buku rekening.</dd>
                     <dt>Nomor Rekening</dt>
                     <dd style="font-size: 12px;">Isi dengan Nomor Rekening Bank.</dd>
                     <dt>Logo / Gambar Bank</dt>
                     <dd style="font-size: 12px;">Isi dengan Logo / Gambar dari Bank</dd>
                     <dt>Kode Bank</dt>
                     <dd style="font-size: 12px;">Isi Kode bank dengan kode bank yang didapatkan dari provider untuk integrasi</dd>
                     <dt>Note!!:</dt>
                     <dt>Hanya ganti provider apabila memang perlu!</dt>
                  </dl>
               </div><!-- /.box-body -->
            </div><!-- /.box -->
         </div>
      </div>
   </section>

</section>
@endsection
@section('js')
<script>
    $(document).ready(function(){
         var category = $('select[name=bank_kategori_id] option').filter(':selected').val();
         if(category == 3){
            $('#type').removeClass('d-none');
            $('#type').addClass('d-block');
        }else{
            $('#type').addClass('d-none');
            $('#type').removeClass('d-block');
        }
    });
    $('#category_bank').change(function(){
        var category = $('select[name=bank_kategori_id] option').filter(':selected').val();
        
        if(category == 3){
            $('#type').removeClass('d-none');
            $('#type').addClass('d-block');
        }else{
            $('#type').addClass('d-none');
            $('#type').removeClass('d-block');
        }
    })
</script>
@endsection