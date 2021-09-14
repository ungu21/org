@extends('layouts.admin')

@section('content')
<section class="content-header">
  <h1>Setting <small>Minimal Deposit</small></h1>
   <ol class="breadcrumb">
      <li><a href="{{url('/admin')}}" class="btn-loading"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="Javascript:;">Pengaturan</a></li>
      <li><a href="{{route('setting.deposit.index')}}" class="btn-loading">Minimal Deposit</a></li>
      <li class="active">Minimal Deposit</li>
   </ol>
   </section>
   <section class="content">
      <div class="row">
         <div class="col-md-6">
            <div class="box box-green">
               <div class="box-header">
                 <h3 class="box-title"><a href="{{route('bank.index')}}" class="hidden-lg btn-loading"><i class="fa fa-arrow-left" style="margin-right:10px;"></i></a>Set Minimal & Maximal Transfer Bank</h3>
               </div>
               <form role="form" action="{{url('admin/setting-transfer-bank/update')}}" method="post">
               {{csrf_field()}}
                  <div class="box-body">
                    <div class="col-md-6">
                         <div class="form-group{{ $errors->has('min_tf') ? ' has-error' : '' }}">
                            <label>Minimal Transfer Bank : </label>
                            <input type="text" class="form-control" name="min_tf" id="min_tf_bank" value="{{number_format($setting->min_tf_bank, 0, '.', '.')}}"  placeholder="Set Minimal Transfer Bank">
                            {!! $errors->first('min_tf', '<p class="help-block"><small>:message</small></p>') !!}
                         </div>
                         <div class="form-group{{ $errors->has('max_tf_bank') ? ' has-error' : '' }}">
                            <label>Maximal Transfer Bank : </label>
                            <input type="text" class="form-control" name="max_tf" id="max_tf_bank" value="{{number_format($setting->max_tf_bank, 0, '.', '.')}}"  placeholder="Set Maximal Transfer Bank">
                            {!! $errors->first('max_tf', '<p class="help-block"><small>:message</small></p>') !!}
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
                 <h3 class="box-title"><a href="{{route('bank.index')}}" class="hidden-lg btn-loading"><i class="fa fa-arrow-left" style="margin-right:10px;"></i></a>Set Fee Transfer Bank</h3>
               </div>
               <form role="form" action="{{url('admin/setting-transfer-bank/update-fee')}}" method="post">
               {{csrf_field()}}
                  <div class="box-body">
                    <div class="col-md-6">
                         <div class="form-group{{ $errors->has('fee_tf_bank') ? ' has-error' : '' }}">
                            <label>Fee Transfer Bank : </label>
                            <input type="text" class="form-control" name="fee_tf_bank" id="fee_tf_bank" value="{{number_format($setting->fee_tf_bank, 0, '.', '.')}}"  placeholder="Set Fee Transfer Bank">
                            {!! $errors->first('min_tf', '<p class="help-block"><small>:message</small></p>') !!}
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

    $('#min_tf_bank').keyup(function(){
        var min_tf_bank=parseInt(price_to_number($('#min_tf_bank').val()));
        var autoMoney = autoMoneyFormat(min_tf_bank);
        $('#min_tf_bank').val(autoMoney);
    });
    
    $('#max_tf_bank').keyup(function(){
        var min_deposit_admin=parseInt(price_to_number($('#max_tf_bank').val()));
        var autoMoney = autoMoneyFormat(min_deposit_admin);
        $('#max_tf_bank').val(autoMoney);
    });

    $('#fee_tf_bank').keyup(function(){
        var min_deposit_admin=parseInt(price_to_number($('#fee_tf_bank').val()));
        var autoMoney = autoMoneyFormat(min_deposit_admin);
        $('#fee_tf_bank').val(autoMoney);
    });
});

</script>
@endsection