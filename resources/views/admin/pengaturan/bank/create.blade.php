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
	<h1>Tambah  <small>Bank</small></h1>
   <ol class="breadcrumb">
    	<li><a href="{{url('/admin')}}" class="btn-loading"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="Javascript:;">Pengaturan</a></li>
      <li><a href="{{route('bank.index')}}" class="btn-loading">Data Bank</a></li>
      <li class="active">Tambah Bank</li>
   </ol>
   </section>
   <section class="content">
      <div class="row">
         <div class="col-md-6">
            <div class="box box-primary">
               <div class="box-header">
                 <h3 class="box-title"><a href="{{route('bank.index')}}" class="hidden-lg btn-loading"><i class="fa fa-arrow-left" style="margin-right:10px;"></i></a>Tambah Bank</h3>
               </div>
               <form role="form" action="{{route('bank.store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
               {{csrf_field()}}
                  <div class="box-body">
                     <div class="form-group{{ $errors->has('nama_bank') ? ' has-error' : '' }}">
                        <label>Nama Bank : </label>
                        <input type="text" class="form-control" name="nama_bank" value="{{ old('nama_bank') }}"  placeholder="Masukkan Nama Bank">
                        {!! $errors->first('nama_bank', '<p class="help-block"><small>:message</small></p>') !!}
                     </div>
                     <div class="form-group{{ $errors->has('atas_nama') ? ' has-error' : '' }}">
                        <label>Nama Pemilik : </label>
                        <input type="text" class="form-control" name="atas_nama" value="{{ old('atas_nama') }}"  placeholder="Masukkan Nama Pemilik Rekening">
                         {!! $errors->first('atas_nama', '<p class="help-block"><small>:message</small></p>') !!}
                     </div>
                     <div class="form-group{{ $errors->has('no_rek') ? ' has-error' : '' }}">
                        <label>Nomor Rekening : </label>
                        <input type="text" class="form-control" name="no_rek" value="{{ old('no_rek') }}"  placeholder="Masukkan Nomor Rekening Rekening">
                         {!! $errors->first('no_rek', '<p class="help-block"><small>:message</small></p>') !!}
                     </div>
                      <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                        <label>Logo / Gambar Bank : </label>
                        <input type="file" name="image" accept="image/*">
                         {!! $errors->first('image', '<p class="help-block"><small>:message</small></p>') !!}
                     </div>
                      <div class="form-group{{ $errors->has('code_bank_integrasi') ? ' has-error' : '' }}">
                        <label>Kode Bank</label>
                        <input type="text" class="form-control" placeholder="Masukkan Kode bank" name="code_bank_integrasi" >
                         {!! $errors->first('code_bank_integrasi', '<p class="help-block"><small>:message</small></p>') !!}
                     </div>
                     <div class="form-group{{ $errors->has('nama_provider') ? ' has-error' : '' }}" id="provider">
                        <label>Bank Kategori : </label>
                        <select name="bank_kategori_id"  class="form-control" id="category_bank" >
                           @foreach($bank_kategori as $item)
                           <option value="{{$item->id}}">{{$item->paymethod}}</option>
                           @endforeach
                        </select>
                     </div>
                     <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }} d-none" id="type">
                        <label>type</label>
                        <select name="type" class="form-control">
                           <option value="1">Close Payment</option>
                           <option value="0">Open Payment</option>
                        </select>
                     </div>
                     <div class="form-group{{ $errors->has('nama_provider') ? ' has-error' : '' }}" id="provider">
                        <label>Provider : </label>
                        <select name="provider"  class="form-control" id="provider">
                           @foreach($provider as $item)
                           <option value="{{$item->id}}">{{$item->name}}</option>
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