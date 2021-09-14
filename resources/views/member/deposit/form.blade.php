@extends('layouts.member')

@section('content')
<section class="content-header hidden-xs">
	<h1>Transaksi <small>Deposit Saldo</small></h1>
   <ol class="breadcrumb">
    	<li><a href="{{url('/member')}}" class="btn-loading"><i class="fa fa-dashboard"></i> Home</a></li>
    	<li class="active">Deposit</li>
   </ol>
   </section>
   <section class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="">
               <div class="box-header">
                 <h3 class="box-title"><a href="{{url('/member')}}" class="btn-loading hidden-lg"><i class="fa fa-arrow-left custom__text-green" style="margin-right:10px;"></i></a>Deposit Saldo</h3>
               </div>
                  <!-- Virtual Wallet -->
                  @foreach($bank as $data)
                  <!-- Subjudul -->
                  <div id="virtual_wallet" class="deposit__title mt-5 mr-lg-2">
                      {{$data->paymethod}}
                  </div>
                  <!-- Penutup subjudul -->
                  <div class="row">
                      @foreach($data->bank as $item)
                      <div class="col-lg-2 col-4 mt-3">
                          <div class="card-payment">
                              <div class="payment-card" onclick="deposit('{{$data->id}}','{{$item->id}}')">
                                @if($item->is_close == 0)
                                <form action="{{url('member/process/depositsaldo')}}" id="form-open-payment" method="post">
                                  {{csrf_field()}}
                                    <input type="hidden" name="bank_id" id="open_bank_id" value="">
                                    <input type="hidden" name="id_category_bank" id="open_category_id" value="">
                                    <input type="hidden" name="nominal" id="nominal" value="0">
                                  </form>
                                @endif
                                  <img src="{{asset('/img/banks/'.$item['image'])}}" class="img-bank" width="auto" height="30px">
                                  @if(in_array($data->code,['01','02','04','05']) )
                                  <!-- label 24 jam -->
                                  <div class="card-img-overlay mt-2">    
                                      <div  class="p-1 label-jam link-overlay">
                                          24 Jam
                                      </div>		
                                  </div>
                                  <!-- Penutup label 24 jam -->
                                  @endif
                              </div>
                          </div>
                      </div>
                      @endforeach
                  </div>
                  <!-- Penutup Virtual Walet -->
                  @endforeach
            </div>
         </div>
      </div>
   </div>
   <div class="modal fade" id="deposit-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Masukkan Nominal Deposit</h4>
        </div>
        <form action="{{url('member/process/depositsaldo')}}" id="form" method="post">
          {{csrf_field()}}
          <div class="modal-body">
            <div class="form-group">
              <label for="nominal">Nominal Deposit</label>
              <input type="text" class="form-control" name="nominal" id="nominal">
            </div>
            <input type="hidden" name="id_category_bank" id="bank_kategori_id" value="">
            <input type="hidden" name="bank_id" id="bank_id" value="">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
</section>

@endsection
@section('js')
<script>
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

  $('#nominal').keyup(function(){
    var nominal=parseInt(price_to_number($('#nominal').val()));
    var autoMoney = autoMoneyFormat(nominal);
    $('#nominal').val(autoMoney);
  });

  
});
function deposit(id_category,id_bank)
{
  $.ajax({
    type:'get',
    url:base_url('/member/bank-cek/'+id_bank),
    success:function(data){
      if(data.is_close == 1){
        $('#bank_kategori_id').val(id_category);
        $('#bank_id').val(id_bank);
        $('#deposit-modal').modal('show');
      }else{
        $('#open_bank_id').val(id_bank);
        $('#open_category_id').val(id_category);
        $('#form-open-payment').submit();
      }
    },error:function(e){
      toastr.error(e.ResponseJson.message);
    }
  })
  
}
</script>
@endsection