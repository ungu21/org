@extends('layouts.admin')
@section('meta', '<meta http-equiv="refresh" content="60">')

@section('content')
<section class="content-header hidden-xs">
<h1>Kontrol menu <small>Menu dan sub menu</small></h1>
<ol class="breadcrumb">
  <li><a href="#"><i class="fa fa-dashboard"></i> Kontrol Menu</a></li>
  <li class="active">Menu dan sub menu</li>
</ol>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-6">
        <div class="box box-solid box-penjelasan">
            <div class="box-header">
                <i class="fa fa-text-width"></i>
                <h3 class="box-title">Edit Kontrol menu</h3>
                <div class="box-tools pull-right box-minus" style="display:none;">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
            </div><!-- /.box-header -->
          
            <div class="box-body">
                <form action="{{url('admin/kontrol-menu/update-menu-dashboard')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{$menu->id}}" name="id">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="">Nama Menu</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{$menu->product_name}}">
                    </div>
                    <div class="form-group{{ $errors->has('icon') ? ' has-error' : '' }}">
                        <label>Icon</label>
                        <input type="file" class="form-control" name="icon" id="icon">
                    </div>
                    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                        <label>status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{$menu->status == 1 ? 'selected' : ''}}>Aktif</option>
                            <option value="0" {{$menu->status == 0 ? 'selected' : ''}}>Non Aktif</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
      </div>
      </div>

   </div>
</section>
@endsection
@section('js')

@endsection