@extends('layouts.member')

@section('content')
<style>
   .help-block{
      color:red;
   }
</style>
<section class="content-header hidden-xs">
	<h1>Transaksi <small>Transfer Bank</small></h1>
   <ol class="breadcrumb">
    	<li><a href="{{url('/member')}}" class="btn-loading"><i class="fa fa-dashboard"></i> Home</a></li>
    	<li class="active">Transfer</li>
   </ol>
   </section>
   <section class="content">
      <div class="row">
         <div class="col-md-6">
            <div class="box box-default">
               <div class="box-header">
                 <h3 class="box-title"><a href="{{url('/member')}}" class="btn-loading hidden-lg"><i class="fa fa-arrow-left" style="margin-right:10px;"></i></a>Transfer Bank</h3>
               </div>
               <form role="form" action="{{url('/member/transfer-bank/process')}}" method="post">
               {{csrf_field()}}
                  <div class="box-body">
                     <div class="form-group">
                        <label for="penerima">Penerima Transfer</label>
                        <input type="text" class="form-control" id="penerima" min="0" name="penerima" value="{{old('penerima')}}" autofocus required>
                        {!! $errors->first('penerima', '<p class="help-block"><small>:message</small></p>') !!}
                     </div>
                    <div class="form-group{{ $errors->has('pilih_jenis_bank') ? ' has-error' : '' }}">
                        <label>Bank Tujuan : </label>
                        <select class="form-control" id="pilih_jenis_bank" name="pilih_jenis_bank" style="width:100% !important;" required></select>
                        {!! $errors->first('pilih_jenis_bank', '<p class="help-block"><small>:message</small></p>') !!}
                    </div>

                    <div class="form-group{{ $errors->has('no_rek') ? ' has-error' : '' }}">
                        <label>No. Rekening : </label>
                        <div class="input-group" style="width: 100%;">
                           <input type="text" name="no_rek" id="no_rek" class="form-control" min="0" placeholder="Masukkan No.Rekening" value="{{ old('no_rek') ?: '' }}" autofocus required>
                        </div>
                        {!! $errors->first('no_rek', '<p class="help-block"><small>:message</small></p>') !!}
                    </div>
                     
                    <div class="form-group{{ $errors->has('nominal') ? ' has-error' : '' }}">
                        <label>Jumlah : </label>
                        <div class="input-group">
                           <div class="input-group-addon">Rp. </div>
                           <input type="text" name="nominal" id="nominal" class="form-control" value="{{ old('nominal') }}" placeholder="Masukkan Jumlah Transfer" autofocus>
                        </div>
                        {!! $errors->first('nominal', '<p class="help-block"><small>:message</small></p>') !!}
                        <p><i>Minimum transfer Rp {{ number_format($GeneralSettings->min_tf_bank, 0, '.', '.') }} dan maksimum transfer Rp {{ number_format($GeneralSettings->max_tf_bank, 0, '.', '.') }}</i><br/>
                        <i>Biaya admin Rp. {{number_format($GeneralSettings->fee_tf_bank,0,'.','.')}}/transaksi langsung terpotong dari saldo.</i></p>
                     </div>
                    
                    <div class="form-group{{ $errors->has('pin') ? ' has-error' : '' }}">
                        <label>Pin : </label>
                        <input type="number" id="number" name="pin" class="form-control pin" placeholder="Masukkan PIN anda" autocomplete="off" autofocus required>
                        {!! $errors->first('pin', '<p class="help-block"><small>:message</small></p>') !!}
                        <p><i>Untuk melihat pin anda,silahkan lihat di profile!</i></p>
                    </div>
                     <div id="hitung">
                     </div>
                  </div>

                  <div class="box-footer">
                     <button type="submit" class="submit btn btn-primary btn-block">&nbsp;&nbsp;Selanjutnya&nbsp;&nbsp;</button>
                  </div>
               </form>
            </div>
         </div>
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header">
                    <h3 class="box-title"><a href="{{url('/member')}}" class="btn-loading hidden-lg"><i class="fa fa-arrow-left" style="margin-right:10px;"></i></a>Syarat & Ketentuan</h3>
                </div>
                <div class="box-body" align="left">
                    <p><b>Syarat :</b> 
                    <br/>1. Silahkan Melakukan Validasi terlebih dahulu untuk menggunakan fitur ini ke <a href="{{url('/member/validasi-users')}}">SINI</a>
                    <br/>2. Silahkan Upload dengan KTP + Selfie (sambil pegang KTP).
                    <br/>3. Team kami akan melakukan pengecekan ke Validan Data anda. 
                    <br/>4. Setelah Dinyatakan Valid anda sudah langsung bisa menggunakan fitur ini.</p>
                    <p>
                    <p><b>Ketentuan :</b>
                    <br/>1. Biaya Admin Rp. {{number_format($GeneralSettings->fee_tf_bank,0,'.','.')}}/transaksi 
                    <br/>2. Transaksi Hanya Bisa dilakukan antara jam 01.00 - 22.00 WIB</p>
              </div>
          </div>
      </div>
      <div class="row hidden-xs hidden-sm">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header">
                  <h3 class="box-title">Data Transfer Anda</h3>
               </div><!-- /.box-header -->
               <div class="box-body table-responsive">
               <table id="DataTable"  class="table table-hover">
                  <thead>
                     <tr class="custom__text-green">
                        <th>No.</th>
                        <th>Penerima</th>
                        <th>Nominal</th>
                        <th>Code Bank</th>
                        <th>Bank</th>
                        <th>No.Rekening</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                     </tr>
                  </thead>
                  <tbody>
                  </tbody>
               </table>
            </div><!-- /.box-body -->
         </div><!-- /.box -->
      </div>

</section>
@endsection
@section('js')
<script>
$(document).ready(function() {
    var table = $('#DataTable').DataTable({
        // deferRender: true,
        processing: true,
        serverSide: false,
        autoWidth: false,
        info: false,
        ajax:{
            url : "{{ url('/member/transfer-bank/history/datatables') }}",
        },
        // order: [[ 7, "desc" ]],
        columns:[
                  {data: null, width: "50px", sClass: "text-center", orderable: false},
                  {data: 'penerima', defaulContent: '-'},
                  {data: 'nominal', defaulContent: '-', sClass: "text-right"},
                  {data: 'code_bank', defaulContent: '-'},
                  {data: 'jenis_bank', defaulContent: '-'},
                  {data: 'no_rekening', defaulContent: '-' },
                  {data: 'status', defaulContent: '-' },
                  {data: 'created_at', defaulContent: '-' },
                ]
     });
     table.on( 'order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
          cell.innerHTML = i+1;
        } );
     }).draw();
});
   
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
   var amount=parseInt(price_to_number($('#nominal').val()));
   var autoMoney = autoMoneyFormat(amount);
   $('#nominal').val(autoMoney);
});
  
function format(item) {
   return item.name;
}



$('#pilih_jenis_bank').select2({
        placeholder: "Pilih Jenis Bank",
        ajax: {
            url: "{{ route('get.bank.code') }}",            
            data: function(params){
                 return {
                     q: params.term
                 };
             },
            processResults: function(data){
                $('#pilih_member').empty();
                return {
                    results : data,
                }
            }

        }
    });
</script>
@endsection