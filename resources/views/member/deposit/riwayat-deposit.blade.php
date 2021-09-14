@extends('layouts.member')

@section('content')
<section class="content-header hidden-xs">
	<h1>Riwayat <small>Deposit</small></h1>
   <ol class="breadcrumb">
    	<li><a href="{{url('/member')}}" class="btn-loading"><i class="fa fa-dashboard"></i> Home</a></li>
    	<li class="active">Riwayat Deposit</li>
   </ol>
   </section>
   <section class="content">
      <div class="row hidden-xs hidden-sm">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header">
                  <h3 class="box-title">Data Deposit Anda</h3>
               </div><!-- /.box-header -->
               <div class="box-body table-responsive">
               <table id="DataTable" class="table table-hover">
                  <thead>
                     <tr class="custom__text-green">
                        <th>Bank</th>
                        <th>Nominal Transfer</th>
                        <th>Status</th>
                        <th>Expire</th>
                        <th>Tgl Request</th>
                        <th>Tgl Update</th>
                        <th>#</th>
                     </tr>
                  </thead>
                  <tbody>
                  @if($deposit->count() > 0)
                  @foreach($deposit as $data)
                  <tr style="font-size: 14px;">
                     <td>{{ $data->bank->nama_bank }}</td>
                     <td>Rp {{ number_format($data->nominal_trf, 0, '.', '.') }}</td>

                     @if($data->status == 0)
                        <td><span class="label label-warning">MENUNGGU</span></td>
                     @elseif($data->status == 1)
                        <td><span class="label label-success">BERHASIL</span></td>
                     @elseif($data->status == 3)
                        <td><span class="label label-primary">VALIDASI</span></td>
                     @elseif($data->status == 2)
                        <td><span class="label label-danger">GAGAL</span></td>
                     @endif

                     @if($data->expire == 1)
                        <td><span class="label label-info">AKTIF</span></td>
                     @else
                        <td><span class="label label-danger">EXPIRE</span></td>
                     @endif

                     <td>{{$data->created_at}}</td>
                     <td>{{$data->updated_at}}</td>
                     <td>
                        @if($data->status == 0)
                            @if(!empty($data->payment_url))
                              <a href="{{ $data->payment_url }}" class="label label-primary custom__btn-green">Detail</a>
                            @else
                              <a href="{{ url('/member/deposit', $data->id) }}" class="label label-primary custom__btn-green">Detail</a>
                            @endif
                        @else
                            <a href="{{ url('/member/deposit', $data->id) }}" class="label label-primary custom__btn-green">Detail</a>
                        @endif
                    </td>
                  </tr>
                  @endforeach
                  @else
                     <tr>
                     <td colspan='9' align='center'><small style='font-style: italic;'>Data tidak ditemukan</small></td>
                     </tr>
                  @endif
                  </tbody>
               </table>
            </div><!-- /.box-body -->
            
         </div><!-- /.box -->
      </div>
   </div>
   <div class="row hidden-lg hidden-md">
      <div class="col-xs-12">
         <div class="box">
            <div class="box-header">
               <h3 class="box-title">Data Deposit Anda</h3>
            </div><!-- /.box-header -->
            <div class="box-body" style="padding: 0px;">
               <table class="table">
                  @if($deposit->count() > 0)
                  @foreach($deposit as $data)
                  
                    @php
                        $detailURL = $data->status == 0 ? (!empty($data->payment_url) ? $data->payment_url : url('/member/deposit', $data->id)) : url('/member/deposit', $data->id);
                    @endphp
                  
                  <tr>
                     <td>
                        <a href="{{ $detailURL }}" class="btn-loading" style="color: #464646">
                           <div><i class="fa fa-calendar"></i><small> {{date("d M Y", strtotime($data->created_at))}}</small></div>
                           <div style="font-size: 14px;font-weight: bold;">TOPUP Saldo Rp {{number_format($data->nominal, 0, '.', '.')}}</div>
                           <div>{{$data->bank->nama_bank}}</div>
                        </a>
                     </td>
                     <td align="right">
                        <a href="{{ $detailURL }}" class="btn-loading" style="color: #464646">
                           <div><i class="fa fa-clock-o"></i><small> {{date("H:m:s", strtotime($data->created_at))}}</small></div>
                           <div>Rp {{number_format($data->nominal_trf, 0, '.', '.')}}</div>
                           
                           @if($data->status == 0)
                              <div><span class="label label-warning">MENUNGGU</span></div>
                           @elseif($data->status == 1)
                              <div><span class="label label-success">BERHASIL</span></div>
                           @elseif($data->status == 3)
                              <div><span class="label label-primary">VALIDASI</span></div>
                           @elseif($data->status == 2)
                              <div><span class="label label-danger">GAGAL</span></div>
                           @endif
                         
                        </a>
                      </td>
                  </tr>
                  @endforeach
                  @else
                  <tr>
                      <td colspan="2" style="text-align:center;font-style:italic;">Riwayat Transaksi belum tersedia</td>
                  </tr>
                  @endif
               </table>
            </div><!-- /.box-body -->
            <div class="box-footer" align="center" style="padding-top:13px;">
               @include('pagination.default', ['paginator' => $deposit])
            </div>
         </div><!-- /.box -->
      </div>
   </div>
</section>

@endsection
@section('js')
<script>

      $('#DataTable').DataTable();
  
</script>
@endsection