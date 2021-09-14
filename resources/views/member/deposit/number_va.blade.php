@extends('layouts.member')

@section('content')
<style>
    .number-va{
        font-weight:bold;
        color: black;
        font-size: 16px;
    }
    .input-group-addon:hover{
        background-color: rgb(240, 240, 240);
        cursor: pointer;
    }
    .list-group-item > span{
      padding-right: 2px;
    }
    .index-list{
      border-radius: 100%;
      background-color: black;
      color: antiquewhite;
      padding: 2px 0px 2px 5px;
    }
</style>
<section class="content-header hidden-xs">
	<h1>Nomer <small>Virtual Account</small></h1>
   <ol class="breadcrumb">
    	<li><a href="{{url('/member')}}" class="btn-loading"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{url('/member/deposit')}}" class="btn-loading"> Deposit</a></li>
    	<li class="active">Nomer Virtual Account</li>
   </ol>
   </section>
   <section class="content">
      <div class="row">
         <div class="col-md-6">
            <div class="box box-solid">
               <div class="box-header with-border">
                  <h3 class="box-title"></h3>
               </div><!-- /.box-header -->
               <div class="box-body">
                <div align="center">
                <img src="{{asset('img/banks/'.$number_va->bank->image)}}" alt="{{$number_va->bank->nama_bank}}" width="25%" class="">
                <p><b>{{$number_va->bank->nama_bank}}</b></p>
                <small style="color:red">Dicek 10 menit setelah pembayaran berhasil</small>
                <div class="form-group">
                    <label for="">No. Virtual Account</label>
                    <div class="input-group">
                        <input type="text" id="va_number" class="form-control text-center number-va" value="{{$number_va->number_va}}" readonly aria-describedby="basic-addon1">
                        <span class="input-group-addon copy-text" id="basic-addon1"><i class="fa fa-clone" aria-hidden="true"></i></span>
                    </div>
                </div>
                </div>                  
               </div><!-- /.box-body -->
            </div><!-- /.box -->
         </div>
         <div class="col-md-6">
           <div class="box box-solid">
             <div class="box-header with-border">
                <h4>Instruksi pembayaran</h4>
             </div>
             <div class="box-body">
              <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    @foreach($instruction as $i => $data)
                    <div class="panel-heading custom-accordion" role="tab" id="headingOne{{$i}}">
                      <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne{{$i}}" aria-expanded="true" aria-controls="collapseOne{{$i}}">
                          <b>{{$data->title}}</b>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseOne{{$i}}" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne{{$i}}">
                      <div class="panel-body">
                        <ul class="list-group">
                          @foreach($data->steps as $r => $step)
                          <li class="list-group-item"><span class="index-list"><b>{{$r.'. '}}</b></span> &nbsp;&nbsp;{{$step}}</li>
                          @endforeach
                        </ul>
                      </div>
                    </div>
                    @endforeach
                </div>
              </div>
             </div>
           </div>
         </div>
      </div>
   </section>

</section>


@endsection
@section('js')
<script>
    /** Copy */
   $(document).on('click','.copy-text',function(){
     let text =  $('#va_number').val();
       navigator.clipboard.writeText(text).then(function(){
           toastr.success('Berhasil Menyalin')
       },function(err){
           toastr.error('Gagal Menyalin')
       });
   });
   /** Penutup Copy */
</script>
@endsection