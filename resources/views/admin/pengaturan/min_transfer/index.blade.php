@extends('layouts.admin')

@section('content')
<section class="content-header">
  <h1>Setting <small>Minimal Transfer Saldo</small></h1>
   <ol class="breadcrumb">
      <li><a href="{{url('/admin')}}" class="btn-loading"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="Javascript:;">Pengaturan</a></li>
      <li><a href="{{route('setting.deposit.index')}}" class="btn-loading">Minimal Transfer Saldo</a></li>
      <li class="active">Minimal Transfer Saldo</li>
   </ol>
   </section>
   <section class="content">
      <div class="row">
         <div class="col-md-6">
            <div class="box box-green">
               <div class="box-header">
                 <h3 class="box-title"><a href="{{url('/admin')}}" class="hidden-lg btn-loading"><i class="fa fa-arrow-left" style="margin-right:10px;"></i></a>Set Minimal Transfer Saldo</h3>
               </div>
               <form role="form" action="{{url('/admin/setting-transfer-saldo')}}" method="post">
              
               {{csrf_field()}}
                  <div class="box-body">
                    <div class="col-md-6">
                         <div class="form-group{{ $errors->has('min_saldo_user') ? ' has-error' : '' }}">
                            <label>Minimal Saldo User : </label>
                           <input type="text" class="form-control" name="min_saldo_user" id="min_saldo_user" value="{{number_format($setting->min_saldo_user, 0, '.', '.')}}"  placeholder="Set minimal saldo user">
                            {!! $errors->first('min_saldo_user', '<p class="help-block"><small>:message</small></p>') !!}
                         </div>
                         <div class="form-group{{ $errors->has('min_nominal_transfer') ? ' has-error' : '' }}">
                            <label>Minimal Nominal Transfer : </label>
                           <input type="text" class="form-control" name="min_nominal_transfer" id="min_nominal_transfer" value="{{number_format($setting->min_nominal_transfer, 0, '.', '.')}}"  placeholder="Minimal nominal transfer">
                            {!! $errors->first('min_nominal_transfer', '<p class="help-block"><small>:message</small></p>') !!}
                         </div>
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
            <div class="box box-green">
               <div class="box-header">
                 <h3 class="box-title"><a href="{{url('/admin')}}" class="hidden-lg btn-loading"><i class="fa fa-arrow-left" style="margin-right:10px;"></i></a>Note:</h3>
               </div>
               <div class="box-body">
                <dl>
                    <dt>Minimal saldo</dt>
                    <dd style="font-size: 12px;">Harap isi nominal saldo user untuk transfer saldo antar user</dd>
                    <dt>minimal Nominal Transfer</dt>
                    <dd style="font-size: 12px;">Isi nominal transfer</dd>
                   
                </dl>
               </div>   
            </div>
         </div>
      </div>
   </section>
</section>
@endsection

@section('js')
<script type="text/javascript">

$(document).ready(function() {

    function autoMoneyFormat(b){
        var _minus = false;
        if (b<0) _minus = true;
        b = b.toString();
        b=b.replace(".","");
        b=b.replace("-","");
        c = "";
        panjang = b.length;
        j = 0;
        for (i = panjang; i > 0; i--){
        j = j + 1;
        if (((j % 3) == 1) && (j != 1)){
        c = b.substr(i-1,1) + "." + c;
        } else {
        c = b.substr(i-1,1) + c;
        }
        }
        if (_minus) c = "-" + c ;
        return c;
    }

    function price_to_number(v){
    if(!v){return 0;}
        v=v.split('.').join('');
        v=v.split(',').join('');
    return Number(v.replace(/[^0-9.]/g, ""));
    }
    
    function number_to_price(v){
    if(v==0){return '0,00';}
        v=parseFloat(v);
        // v=v.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        v=v.toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        v=v.split('.').join('*').split(',').join('.').split('*').join(',');
    return v;
    }
    
    function formatNumber (num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")
    }

   
    
});

</script>
@endsection