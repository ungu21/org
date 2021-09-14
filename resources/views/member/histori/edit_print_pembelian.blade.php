@extends('layouts.member')

@section('content')
<style>
.table-borderless > tbody > tr > td,
.table-borderless > tbody > tr > th,
.table-borderless > tfoot > tr > td,
.table-borderless > tfoot > tr > th,
.table-borderless > thead > tr > td,
.table-borderless > thead > tr > th {
    border: none;
}

</style>
<section class="content-header hidden-xs">
	<h1>Riwayat <small>Transaksi</small></h1>
   <ol class="breadcrumb">
    	<li><a href="{{url('/member')}}" class="btn-loading"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{url('/member/riwayat-transaksi')}}" class="btn-loading"> Riwayat Transaksi</a></li>
    	<li class="active">Edit Struk Transaksi #{{$trx->id}}</li>
   </ol>
   </section>
   <section class="content">
      <div class="row">
         <div class="col-md-6">
            <div class="box box-default">
               <div class="box-header with-border">
                  <h3 class="box-title"><a href="{{url('/member/riwayat-transaksi')}}" class="btn-loading hidden-lg"><i class="fa fa-arrow-left custom__text-green" style="margin-right:10px;"></i></a>Edit Struk Pembelian</h3>
                  <div class="box-tools pull-right">
                    </div>
               </div>
               <div class="box-body" >
                  
                    <form class="custom__form" role="form" action="{{url('/print-trx-save/'.$trx->id.'.pdf')}}" method="get">
                    <input type="hidden" name="token" value="{{$hash}}">
                    <input type="hidden" name="view" value="{{$view}}">
                    <div class="form-group">
                        <label for="orderID">Order ID</label>
                        <input type="text" class="form-control" value="{{$trx->order_id}}" readonly aria-label="" aria-describedby="basic-addon1" id="orderID" disabled>
                    </div>
                    <div class="form-group">
                        <label for="code">Kode/Produk</label>
                        <input type="text" class="form-control" value="{{$trx->code}}/{{$trx->produk}}" readonly aria-label="" aria-describedby="basic-addon1" id="code" disabled>
                    </div>
                    <div class="form-group">
                        <label for="nomor">No HP</label>
                        <input type="text" class="form-control" value="{{isset($trx->target) ? $trx->target : '-'}}" readonly aria-label="" aria-describedby="basic-addon1" id="nomor" disabled>
                    </div>
                    @if($trx->mtrpln != "-")
                    <div class="form-group">
                        <label for="nomor">ID Pelanggan</label>
                        <input type="text" class="form-control" value="{{isset($trx->mtrpln) ? $trx->mtrpln: '-'}}" readonly aria-label="" readonly aria-describedby="basic-addon1" id="nomor" disabled>
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="status">Status</label>
                        <input type="text" class="form-control" value="TRX {{($trx->status == 0) ? "PROSES" :(($trx->status == 1) ? "BERHASIL" : (($trx->status == 2) ? "GAGAL" : (($trx->status == 3) ? "REFUND" : "-")))}}" aria-label="" aria-describedby="basic-addon1" readonly id="status" disabled>
                    </div>
                    <div class="form-group">
                        <label for="sn">SN/Ref</label>
                        <input type="text" class="form-control" value="{{isset($trx->token)?$trx->token:'-'}}" aria-label="" aria-describedby="basic-addon1" id="sn" readonly disabled>
                    </div>
                    <div class="form-group">
                        <label for="price">Harga Jual</label>
                        <input type="text" class="form-control" value="{{ $trx->total }}" name="harga" aria-label="" aria-describedby="basic-addon1" id="price" required>
                    </div>
                    <div class="text-center mt-5 mb-3">
                        <button type="submit" class="btn btn-success btn-lg btn-block">
                            Cetak
                        </button>
                   </div>
                </form>
               </div>
            </div>
         </div>
       
      </div>
   </section>

</section>
@endsection
@section('js')
<script>
   $('.submit').on('click', function(){
       $('.submit').html("<i class='fa fa-spinner faa-spin animated' style='margin-right:5px;'></i> Loading...");
       $('.submit').attr('style', 'cursor:not-allowed;pointer-events: none;');
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
    $('#price').keyup(function(){
      var nominal=parseInt(price_to_number($('#price').val()));
      var autoMoney = autoMoneyFormat(nominal);
      $('#price').val(autoMoney);
    });

  
</script>
@endsection