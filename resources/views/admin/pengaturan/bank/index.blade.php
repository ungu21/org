@extends('layouts.admin')

@section('content')
<section class="content-header">
<h1>Pengaturan <small>Data Bank</small></h1>
<ol class="breadcrumb">
 	<li><a href="{{url('/admin')}}" class="btn-loading"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="Javascript:;">Pengaturan</a></li>
 	<li class="active">Data Bank</li>
</ol>
</section>
<section class="content"> 
   <div class="row">
      <div class="col-xs-12">
         <div class="box">
            <div class="box-header">
               <h3 class="box-title">Data Bank</h3>
               <a href="{{route('bank.create')}}" class="btn-loading btn btn-blue pull-right" data-toggle="tooltip" data-placement="left" title="Tambah Data Bank" style="padding: 3px 7px;"><i class="fa fa-plus"></i></a>
            </div><!-- /.box-header -->
            <div class="box-body">
              <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                @foreach($provider as $item)
                  <div class="panel panel-default">
                      <div class="panel-heading custom-accordion" role="tab" id="headingOne{{$item->id}}">
                        <h4 class="panel-title">
                          <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne{{$item->id}}" aria-expanded="true" aria-controls="collapseOne{{$item->id}}">
                          <img class="accordion-title" src="{{url('img/provider/'.$item->logo)}}" alt="" height="40px">
                          </a>
                        </h4>
                      </div>
                      <div id="collapseOne{{$item->id}}" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne{{$item->id}}">
                        <div class="panel-body">
                          <div class="api-section">
                            <div class="row">
                               @if($item->name=="PaymentTripay")
                                <form action="{{url('admin/bank/edit_data')}}" method="post">
                                  {{csrf_field()}}
                                  <div class="col-md-4 col-sm-8 mt-4">
                                    <div class="d-flex">
                                      <img src="{{url('img/icon/api-key-icon.png')}}" class="img-fluid" alt="">
                                      <div class="api">
                                        <div class="card-tipe-title">
                                          Api Key
                                        </div>
                                        <div class="card-tipe-subtitle">
                                            <input type="hidden" name="id_provider" id="id_provider{{$item->id}}" value="{{$item->id}}">
                                            <input type="text" class="form-copy-text" name="api_key" value="{{$item->api_key}}" id="api_key{{$item->id}}">
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-4 col-sm-8 mt-4">
                                    <div class="d-flex">
                                      <img src="{{url('img/icon/api-signature-icon.png')}}" class="img-fluid" alt="">
                                      <div class="api">
                                        <div class="card-tipe-title">
                                          Private Key
                                        </div>
                                        <div class="card-tipe-subtitle">
                                            <input type="text" class="form-copy-text" name="private_key" value="{{$item->private_key}}" id="private_key{{$item->id}}">
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-4 col-sm-8 mt-4">
                                    <div class="d-flex">
                                      <img src="{{url('img/icon/private-key-icon.png')}}" class="img-fluid" alt="">
                                      <div class="api">
                                        <div class="card-tipe-title">
                                          Mechant Code
                                        </div>
                                        <div class="card-tipe-subtitle">
                                            <input type="text" class="form-copy-text" name="merchant_code" value="{{$item->merchant_code}}" id="merchant_code{{$item->id}}">
                                            <button class="btn btn-blue btn-sm" type="submit">Save</button>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </form>
                               @elseif($item->name == 'CekMutasi')
                                <form action="{{url('admin/bank/edit_data')}}" method="post">
                                  {{csrf_field()}}
                                  <div class="col-md-4 col-sm-8 mt-4" hidden>
                                    <div class="d-flex">
                                      <img src="{{url('img/icon/api-key-icon.png')}}" class="img-fluid" alt="">
                                      <div class="api">
                                        <div class="card-tipe-title">
                                          Api Key
                                        </div>
                                        <div class="card-tipe-subtitle">
                                            <input type="hidden" name="id_provider" id="id_provider{{$item->id}}" value="{{$item->id}}">
                                            <input type="hidden" class="form-copy-text" name="api_key" value="{{$item->api_key}}" id="api_key{{$item->id}}">
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-4 col-sm-8 mt-4" hidden>
                                    <div class="d-flex">
                                      <img src="{{url('img/icon/api-signature-icon.png')}}" class="img-fluid" alt="">
                                      <div class="api">
                                        <div class="card-tipe-title">
                                          Api Signature
                                        </div>
                                        <div class="card-tipe-subtitle">
                                            <input type="hidden" class="form-copy-text" name="api_signature" value="{{$item->api_signature}}" id="api_signature{{$item->id}}">
                                            <button class="btn btn-blue btn-sm" type="submit">Save</button>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </form>
                                @endif
                            </div>
                            <div class="banks-section">
                              <div class="row">
                              @foreach($item->bank as $data)
                                <div class="col-md-4 col-sm-8 ">
                                  <div class="d-flex">
                                    <img src="{{url('img/banks/'.$data->image)}}" height="30px" class="img-fluid" alt="">
                                  </div>
                                </div>
                                <div class="col-md-4 col-sm-8 ">
                                  <div class="d-flex">
                                    <form action="{{url('admin/bank/edit_data_bank')}}" method="POST">
                                      {{csrf_field()}}
                                      <div class="card-tipe-title" >
                                          <input type="text" class="form-copy-text" name="atas_nama" value="{{$data->atas_nama}}" id="atas_nama{{$data->id}}" >
                                      </div>
                                      <div class="card-tipe-subtitle" >
                                        <input type="hidden" name="id" value="{{$data->id}}">
                                        <input type="text" class="form-copy-text"  name="no_rek" value="{{$data->no_rek}}" id="no_rek{{$data->id}}">
                                        @if($item->name == 'CekMutasi')
                                        <button class="btn btn-blue btn-sm" type="submit">Save</button>
                                        @endif
                                      </div>
                                      </form>
                                  </div>
                                </div>
                                <div class="col-md-4 col-sm-8 ">
                                <div class="btn btn-bank">
                                  <form method="POST" action="{{ route('bank.destroy', $data->id) }}" accept-charset="UTF-8">
                                    <input name="_method" type="hidden" value="DELETE">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                                    <a href="{{route('bank.edit', $data->id)}}" class="btn btn-edit"><img src="{{url('img/icon/Icon material-edit.png')}}" alt=""></a>
                                    <input type="checkbox" class="check-status"  id="status{{$data->id}}" value="{{$data->status}}" onchange="status('{{$data->id}}','{{$item->id}}')" {{$data->status == '1' ? 'checked' : ""}}  data-plugin="switchery" data-color="#1bb99a" data-size="small"/>
                                    {{-- <button class="btn btn-delete" onclick="return confirm('Anda yakin akan menghapus data ?');" type="submit"><img src="{{url('img/icon/Icon material-delete.png')}}" alt=""></button> --}}
                                  </form>
                                  </div>
                                </div>
                                @endforeach
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                  </div>
                @endforeach
              </div>
            </div>
        </div>  
      </div>
   </div>
</section>

@endsection
@section('js')
<script>
   $(document).on('show','.accordion',function(e){
      $(e.target).prev('')
   })
   $()
  
  function copyToClipboard(text) {
  $("#"+text).tooltip("show");
    var input = document.createElement("input");
    if($("#"+text).val().search("-") > 0){
      /* Ketika Ada - */
      var data_text =  $("#"+text).val().split(" - ");

      for(i=0;i<data_text.length;i++){
        input.value += $("#"+text).val().split(" - ")[i];
      }
    }else{
      if($("#"+text).val().search(" ") > 0){
      if($("#"+text).val().search("Rp") >= 0){
          /* Ketika Ada Rp nya */
          input.value = $("#"+text).val().split(" ")[1];
      }else{
        /* Ketika Ada Sepsai Banyak */
        var data_text =$("#"+text).val().split(" ");
        for(i=0;i<data_text.length;i++){
          input.value += $("#"+text).val().split(" ")[i];
        }
      }
      }else{
        /* Ketika Tidak Ada Sepasi */
        input.value = $("#"+text).val();
      }
    }

    input.style.position = "absolute";
    input.style.left = "-1000px";
    input.style.top = "-1000px";
    input.id = "input-copy";

    $("body").append(input);
    input.focus();
    input.select();

    document.execCommand('copy');

    $("#input-copy").remove();
    setTimeout(function(){
      $("#"+text).tooltip("hide");
    },2000);
  }
  
  function status(obj,id_provider){
    var provider = $('#id_provider'+id_provider).val();
    var api_key = $('#api_key'+provider).val();
    var private_key = $('#private_key'+provider).val();
    var merchant_code = $('#merchant_code'+provider).val();
    var ipn_secret = $('#ipn_secret'+provider).val();
    var phrase = $('#phrase'+provider).val();
    var api_signature = $('#api_signature'+provider).val();
    var public_key = $('#public_key'+provider).val(); 

    var status = $('#status'+obj).val();

    var result = "";

    if($('#status'+obj).val()=='1'){
        status = '0'
    }

   if($('#status'+obj).val()=='0'){
        
        if(provider == 1){
            if(api_key == "" && private_key == "" && merchant_code == ""){
                $('#status'+obj).prop("checked",false);
                toastr.error('Harap Masukkan Api Key, Private Key , dan Merchant code');
                return false;
            }
        }
        if(provider == 2){
            if(api_key =="" && api_signature == ""){
                  $('#status'+obj).prop("checked",false);
                 toastr.error('Harap Masukkan Api Key, dan Api signature ');
                 return false;
            }
        }
        status = '1';
   }
  
    $.ajax({
        type: 'put',
        url: 'bank/status/'+obj,
        data:{
            id : obj,
            status:status,
        },
        success:function(data){
            $('#status'+obj).val(data.status);
            if(data.status == '0'){
              status = 'Dimatikan';
            }
            if(data.status == '1'){
              status = 'Aktif';
            }
            toastr.success('status berhasil diupdate', data.nama_bank+' status '+status);
        },error : function(response){
          toastr.error(response.responseText, 'Terjadi Kesalahan');
        }
    });
}
</script>
@endsection