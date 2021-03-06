@extends('layouts.admin')

@section('content')
<section class="content-header">
	<h1>Setting <small>Setting Bonus</small></h1>
   <ol class="breadcrumb">
    	<li><a href="{{url('/admin')}}" class="btn-loading"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="Javascript:;">Pengaturan</a></li>
      <li><a href="{{route('setting.bonus.index')}}" class="btn-loading">Setting Bonus</a></li>
      <li class="active">Setting Bonus</li>
   </ol>
   </section>
   <section class="content">
      <div class="row">
         <div class="col-md-6">
            <div class="box box-green">
               <div class="box-header">
                 <h3 class="box-title"><a href="{{route('bank.index')}}" class="hidden-lg btn-loading"><i class="fa fa-arrow-left" style="margin-right:10px;"></i></a>Set Price Bonus Transaksi</h3>
               </div>
               <form role="form" action="{{url('/admin/setting-bonus/update/'.$getDataTrx->id.'')}}" method="post">
               <input name="_method" type="hidden" value="PATCH">
               {{csrf_field()}}
                  <div class="box-body">
                     <div class="form-group{{ $errors->has('bonus_trx') ? ' has-error' : '' }}">
                        <label>Bonus Ref : </label>
                        <input type="text" class="form-control" name="bonus_trx" id="bonus_trx" value="{{number_format($getDataTrx->komisi, 0, '.', '.')}}"  placeholder="Set Bonus Trx">
                        {!! $errors->first('bonus_trx', '<p class="help-block"><small>:message</small></p>') !!}
                     </div>
                  </div>

                  <div class="box-footer">
                     <button type="reset" class="btn btn-default">Reset</button>
                     <input type="submit" class="submit btn btn-primary" value="Update Bonus Ref">
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

// $(document).ready(function() {

    $('#bonus_trx').keyup(function(){
        var bonus_trx=parseInt(price_to_number($('#bonus_trx').val()));
        var autoMoney = autoMoneyFormat(bonus_trx);
        $('#bonus_trx').val(autoMoney);
    });
    
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
   function number_format(id){
      var bonus_trx=parseInt(price_to_number($(id).val()));
      var autoMoney = autoMoneyFormat(bonus_trx);
      $(id).val(autoMoney);
   }
</script>
@endsection