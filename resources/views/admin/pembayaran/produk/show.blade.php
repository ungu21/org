@extends('layouts.admin')

@section('content')
<style>
.nav-tabs>li>a {
    margin-right: 2px;
    line-height: 1.42857143;
    border: 1px solid transparent;
    border-radius: 4px 4px 0 0;
    background-color: #D2D6DE;
    color: #edeff2;
}

.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
    color: white;
    cursor: default;
    background-color: #367FA9;
    border: 1px solid #ddd;
    border-bottom-color: #D2D6DE;
    text-decoration: none;
}


/*.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
    color: #555;
    cursor: default;
    background-color: #fff;
    border: 1px solid #ddd;
    border-bottom-color: transparent;
}
AdminLTE.min.css:7
.nav>li>a:hover, .nav>li>a:active, .nav>li>a:focus {
    color: #444;
    background: #f7f7f7;
}
bootstrap.css:4032
.nav-tabs>li>a:hover {
    border-color: #eee #eee #ddd;
}
navs.less:32
.nav>li>a:focus, .nav>li>a:hover {
    text-decoration: none;
    background-color: #eee;
}
bootstrap.css:4012
.nav-tabs>li>a {
    margin-right: 2px;
    line-height: 1.42857143;
    border: 1px solid transparent;
    border-radius: 4px 4px 0 0;
}
navs.less:23
.nav>li>a {
    position: relative;
    display: block;
    padding: 10px 15px;
}*/
</style>

<section class="content-header hidden-xs hidden-sm">
<h1>Produk <small>{{$kategori->product_name}}</small></h1>
<ol class="breadcrumb">
 	<li><a href="{{url('/admin')}}" class="btn-loading"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="Javascript:;"> Pembayaran</a></li>
    <li><a href="{{url('/admin/pembayaran-produk')}}"> Produk</a></li>
 	<li class="active">{{$kategori->product_name}}</li>
</ol>
</section>
<section class="content">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#produk" data-toggle="tab"><b>PRODUK</b></a></li>
        <li><a href="#marking_produk" data-toggle="tab"><b>MARKING PRODUK</b></a></li>
    </ul>
      <div class="tab-content clearfix">
        <div class="tab-pane active" id="produk">
          <div class="row hidden-xs hidden-sm">
            <div class="col-md-12">
               <div class="box">
                  <div class="box-header with-border">
                     <h3 class="box-title"><span class="hidden-xs">Produk {{$kategori->product_name}}</span></h3>
                     <div class="box-tools pull-right">
                          <a href="{{url('/admin/pembayaran-produk')}}" class="btn btn-primary btn-sm">Home Produk</a>
                     </div>
                  </div><!-- /.box-header -->
                  <div style="text-align: center;margin: 5px;">
                      <div id="desktop" class="hidden-xs hidden-sm">
                        <div class="row">
                          <div class="col-md-12">
                              <div class="btn-group">
                                  @foreach($KategoriPembayaran as $data)
                                  <?php
                                    $stopword = array('tagihan');
                                    $text = (trim(strtolower($data->product_name)));
                                    $sub_kalimat = (strtoupper(trim(str_replace($stopword, '', $text))));
                                  ?>
                                  <a href="{{url('/admin/pembayaran-produk', $data->slug)}}" class="btn-loading btn btn-primary {{ url('/admin/pembayaran-produk', $data->slug) == request()->url() ? 'active' : '' }}" style="padding: 3px 7px;margin: 2px">{{$sub_kalimat}}</a>
                                  @endforeach
                              </div>
                            </div>
                        </div>
                      </div>
                  </div>
                  @if(count($kategori->pembayaranoperator) > 0)
                  @foreach($kategori->pembayaranoperator as $data)
                  <h4 style="font-weight: bold;text-align: center;">{{$data->product_name}}</h4>
                  <div class="box-body table-responsive no-padding">
                     <table class="table table-hover" id="tabel-{{strtolower(str_replace(' ', '', $data->product_name))}}" style="margin-bottom:20px;" border="0">
                        <thead>
                          <tr class="custom__text-green">
                             <th>ID</th>
                             <th>Kode</th>
                             <th>Nama Produk</th>
                             <th>Operator</th>
                             <th>Kategori</th>
                             <th>Biaya Server</th>
                             <th>Markup</th>
                             <th>Harga Jual</th>
                             <th>Update Terakhir</th>
                             <th>Status</th>
                             <th>Action</th>
                          </tr>
                        </thead>
                        <?php $no=1; ?>
                        @if(count($data->pembayaranproduk) > 0)
                        <tbody>
                        @foreach($data->pembayaranproduk as $produk)
                        <tr style="font-size: 13px;">
                           <td>{{$produk->id}}</td>
                           <td>{{$produk->code}}</td>
                           <td>{{$produk->product_name}}</td>
                           <td>{{$produk->pembayaranoperator->product_name}}</td>
                           <td>{{$produk->pembayarankategori->product_name}}</td>
                            <td>Rp. {{number_format($produk->price_default, 0, '.', '.')}}</td>
                            <td>Rp. {{number_format($produk->markup, 0, '.', '.')}}</td>
                            <td>Rp. {{number_format($produk->price_markup, 0, '.', '.')}}</td>
                           <td>{{$produk->updated_at}}</td>
                           @if($produk->status == 1)
                           <td><label class="label label-success">Tersedia</label></td>
                           @else
                           <td><label class="label label-danger">Gangguan</label></td>
                           @endif
                           <td>
                              <a href="{{url('/admin/pembayaran-produk/'.$kategori->slug.'/edit/'.$produk->id)}}" class="btn-loading btn btn-primary btn-sm" style="padding: 2px 5px;font-size: 10px;"><i class="fa fa-pencil"></i></a>
                           </td>
                        </tr>
                        @endforeach
                        </tbody>
                        @else
                        <tbody>
                        <tr>
                            <td colspan="7" style="font-style:italic;text-align:center;background-color:#F3F3F3;">Data Produk tidak ditemukan</td>
                        </tr>
                        </tbody>
                        @endif
                     </table>
                  </div><!-- /.box-body -->
                  @endforeach
                  @else
                  <div style="text-align:center;">
                      <i class="fa fa-frown-o fa-5x"></i>
                      <h4 style="font-weight:bold;padding-bottom:50px;font-style:italic;">Operator & Produk dari Kategori ini belum tersedia</h4>
                  </div>
                  @endif
               </div><!-- /.box -->
            </div>
          </div>
          <div class="row hidden-lg hidden-md">
              <div class="col-xs-12">
                  <div class="box">
                      <div class="box-header">
                          <h3 class="box-title" style="font-size:18px;"><a href="{{url('/admin/pembayaran-produk')}}" class="hidden-lg btn-loading"><i class="fa fa-arrow-left" style="margin-right:10px;"></i></a>{{$kategori->product_name}}</h3>
                          <div class="box-tools pull-right">
                              <!--<a href="{{url('/admin/pembayaran-produk/create', $kategori->slug)}}" class="btn-loading btn btn-primary btn-sm">Tambah Produk</a>-->
                          </div>
                      </div><!-- /.box-header -->
                      <div style="text-align: center;margin-bottom: 10px;margin-top: 10px;">
                          <div id="mobile" class="hidden-lg hidden-md">
                              @foreach($KategoriPembayaran as $data)
                              <a href="{{url('/admin/pembayaran-produk', $data->slug)}}" class="btn-loading btn btn-primary {{ url('/admin/pembayaran-produk', $data->slug) == request()->url() ? 'active' : '' }}" style="width:30px;padding: 3px 7px;"><i class="fa fa-{{$data->icon}}"></i><span class="hidden-xs hidden-sm" style="margin-left:5px;">{{$data->product_name}}</span></a>
                              @endforeach
                          </div>
                      </div>
                      @if(count($kategori->pembayaranoperator) > 0)
                      @foreach($kategori->pembayaranoperator as $data)
                      <h4 style="font-weight: bold;text-align: center;">{{$data->product_name}}</h4>
                      <div class="box-body" style="padding: 0px">
                          <table class="table table-hover">
                              @if(count($data->pembayaranproduk) > 0)
                              @foreach($data->pembayaranproduk as $produk)
                              <tr>
                                  <td>
                                      <div><small>{{$produk->pembayarankategori->product_name}}</small></div>
                                      <div style="font-size: 14px;font-weight: bold;">{{$produk->product_name}}</div>
                                      <div>{{$produk->pembayaranoperator->product_name}}</div>
                                  </td>
                                  <td align="right" style="width:35%;">
                                      <div><small>ID : #{{$produk->id}}</small></div>
                                      <div>{{$produk->code}}</div>
                                      @if($produk->status == 1)
                                      <div><span class="label label-success">Tersedia</span></div>
                                      @else
                                      <div><span class="label label-danger">Gangguan</span></div>
                                      @endif
                                      <div style="margin-top:5px;"><a href="{{url('/admin/pembayaran-produk/'.$kategori->slug.'/edit/'.$produk->id)}}" class="btn-loading btn btn-primary btn-sm" style="padding: 2px 5px;font-size: 10px;">Ubah Data</a></div>
                                  </td>
                              </tr>
                              @endforeach
                              @else
                              <tr>
                                  <td class="colspan" style="text-align:center;font-style:italic;">produk tidak tersedia</td>
                              </tr>
                              @endif
                          </table>
                      </div><!-- /.box-body -->
                      @endforeach
                      @else
                      <div style="text-align:center;">
                          <i class="fa fa-frown-o fa-5x"></i>
                          <h4 style="font-weight:bold;padding-bottom:50px;font-style:italic;">Operator & Produk dari Kategori ini belum tersedia</h4>
                      </div>
                      @endif
                  </div><!-- /.box -->
              </div>
          </div>
      </div>
      <div class="tab-pane" id="marking_produk">
          <div class="row hidden-xs hidden-sm">
              <div class="col-md-12">
                 <div class="box">
                  <div class="box-header with-border">
                     <h3 class="box-title"><span class="hidden-xs">Produk {{$kategori->product_name}}</span></h3>
                     <div class="box-tools pull-right">
                          <a href="{{url('/admin/pembayaran-produk')}}" class="btn btn-primary btn-sm">Home Produk</a>
                          <!--<a href="{{url('/admin/pembayaran-produk/create', $kategori->slug)}}" class="btn btn-primary btn-sm">Tambah Produk</a>-->
                     </div>
                  </div><!-- /.box-header -->

                  <div class="box-header with-border">
                      <!-- price default -->
                      <div class="col-md-4">
                        <div class="panel-heading">Reset All Data</div>
                        <div class="panel panel-default">
                          <div class="panel-body text-center">
                            <div class="form-group">
                              <button class="btn btn-sm btn-primary btn-fungsi-hidden" onclick="updateHargaSemua();">Reset Biaya Admin Semua Produk</button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- price markup -->
                      <div class="col-md-4">
                        <div class="panel-heading">Reset Data By Optional</div>
                        <div class="panel panel-default">
                          <div class="panel-body text-center">
                            <div class="form-group">
                              <div class="input-group input-group-sm">
                                <select class="form-control" id="pilih_kategori">
                                    <option value="">Pilih Kategori ...</option>
                                    @foreach($kategori_all as $data)
                                    <option value="{{$data->id}}">{{$data->product_name}}</option>
                                    @endforeach
                                </select>
                                <span class="input-group-btn">
                                  <button class="btn btn-sm btn-primary" type="button" onclick="updateHargaPerKategori();">Reset Markup Biaya By Kategori</button>
                                </span>
                              </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group input-group-sm">
                                  <select name="pilih_operator" id="pilih_operator" class="form-control">
                                      <option value="">Pilih Operator ...</option>
                                  </select>
                                  <span class="input-group-btn">
                                    <button class="btn btn-sm btn-primary" type="button" onclick="updateHargaPerOperator('{{$kategori->id}}');">Reset Markup Biaya By Operator</button>
                                  </span>
                                </div>
                            </div>

                          </div>
                        </div>
                      </div>

                      <!-- nominal tambah -->

                      <div class="col-md-4">
                        <div class="panel-heading"> + / - Nominal Markup</div>
                        <div class="panel panel-default">
                          <div class="panel-body text-center">
                            <div class="form-group">
                                <select class="form-control" name="aksi" id="aksi">
                                    <option value="+">Tambahkan (+)</option>
                                    <option value="-">Kurangkan (-)</option>
                                </select>
                            </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">Rp</div>
                                <input type="text" class="form-control" name="nominal" id="nominal" placeholder="Masukkan Nominal" value="0">
                            </div>
                        </div>
                            <div class="form-group">
                              <label class="radio-inline"><input type="radio" name="selectplusminus" id="chkYes" value="all_data" checked="checked">Semua Data</label>
                              <label class="radio-inline"><input type="radio" name="selectplusminus" id="chkNo" value="option_data">Bersarkan optional</label>
                            </div>
                          <div id="option-show-hide" style="display: none">
                            <div class="form-group">
                              <div class="input-group input-group-sm">
                                  <select class="form-control" id="pilih_kategori_plusminus">
                                      <option value="">Pilih Kategori ...</option>
                                      @foreach($kategori_all as $data)
                                      <option value="{{$data->id}}">{{$data->product_name}}</option>
                                      @endforeach
                                  </select>
                                  <span class="input-group-btn">
                                    <button class="btn btn-sm btn-primary" type="button" onclick="sumMarkingByKategori();">Update Markup Biaya By Kategori</button>
                                  </span>
                                </div>
                              </div>
                            <div class="form-group">
                                <div class="input-group input-group-sm">
                                  <select name="pilih_operator" id="pilih_operator_plusminus" class="form-control">
                                      <option value="">Pilih Operator ...</option>
                                  </select>
                                  <span class="input-group-btn">
                                    <button class="btn btn-sm btn-primary" type="button" onclick="sumMarkingByOperator();">Update Markup Biaya By Operator</button>
                                  </span>
                                </div>
                              </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" id="proses">Update Markup Biaya</button>
                            </div>
                          </div>
                        </div>
                      </div>
                   </div>

                 </div>
              </div>
          </div>
      </div>
    </div>
</section>
@endsection
@section('js')
<script type="text/javascript">

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
        console.log('masmas');
        var data_nominal = parseInt(price_to_number($('#nominal').val()));
        var autoMoney = autoMoneyFormat(data_nominal);
        $('#nominal').val(autoMoney);
    });

    function loading() {
        $('#body').html('<div class="text-center" style="margin-top: 20px; margin-bottom:20px;"><i class="fa fa-spinner fa-2x faa-spin animated"></i></div>');
    }
    $('#proses').on('click', function(){
        var aksi = $('#aksi').val();
        var nominal = $('#nominal').val();
        // var val_radio = document.getElementsByName('optradio').value;
        // var val_radio = $("input[name='optradio']:checked").val();
        var val_radio = $("input[name='selectplusminus']:checked").val();
        console.log('val_radio',val_radio);
        loading();
        $('#proses').attr('disabled', true);
        $('#proses').text('Loading...');

       $.blockUI({ css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        } });
        if(val_radio =='option_data'){
          $.ajax({
              url: '/admin/pembayaran-produk/import',
              type: "post",
              data: {
                  '_token': '{{csrf_token()}}',
                  'aksi':aksi,
                  'nominal':nominal,
                  'type':{{$kategori->id}},
              },

              success: function(data){
                  $.unblockUI();
                  location.reload();
              }
          });
          }else{

          $.ajax({
              url: '/admin/pembayaran-produk/importAllData',
              type: "post",
              data: {
                  '_token': '{{csrf_token()}}',
                  'aksi':aksi,
                  'nominal':nominal,
              },

              success: function(data){
                  $.unblockUI();
                  location.reload();
              }
          });
          }

    });

    function updateHargaSemua()
    {
      $.blockUI({ css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        } });
       $.ajax({
            type: "GET",
            url : "{{ route('update.pembayaran.harga.semua') }}",
            data: {'_token': '{{csrf_token()}}'},
            success: function( msg ) {
              // console.log(msg);
              if(msg == 'error'){
                $.unblockUI();
                swal("Error!", "Update gagal,Api tidak terhubung!", "error");
                return false;
              }
              $.unblockUI();
              swal("Success!", "Success update data all data!", "success");
              location.reload();
            }
        });
    }

    function updateHargaPerOperator($kategori)
    {
      var id_kategori=$('#pilih_kategori').val();
      var id_operator=$('#pilih_operator').val();
      console.log('id_kategori:',id_kategori);
      console.log('id_operator:',id_operator);
      $.blockUI({ css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        } });
       $.ajax({
            type: "GET",
            url : "{{ route('update.pembayaran.harga.peroperator') }}",
            data: {'_token': '{{csrf_token()}}',id_operator: id_operator,id_kategori:id_kategori},
            success: function( msg ) {
              // console.log(msg);
              if(msg == 'error'){
                $.unblockUI();
                swal("Error!", "Update gagal,Api tidak terhubung!", "error");
                return false;
              }
              $.unblockUI();
              swal("Success!", "Success update data kategori!", "success");
              location.reload();
            }
        });
    }

    function updateHargaPerKategori()
    {
      var id_kategori=$('#pilih_kategori').val();
      $.blockUI({ css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        } });
       $.ajax({
            type: "GET",
            url : "{{ route('update.pembayaran.harga.perkategori') }}",
            data: {'_token': '{{csrf_token()}}',id_kategori:id_kategori},
            success: function( msg ) {
              // console.log(msg);
              if(msg == 'error'){
                $.unblockUI();
                swal("Error!", "Update gagal,Api tidak terhubung!", "error");
                return false;
              }
              $.unblockUI();
              swal("Success!", "Success update data kategori!", "success");
              location.reload();
            }
        });
    }

    function sumMarkingByKategori()
    {
      var id_kategori=$('#pilih_kategori_plusminus').val();
      console.log('id_kategori',id_kategori);
      // return false;
      var aksi = $('#aksi').val();
      var nominal = $('#nominal').val();
      $.blockUI({ css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        } });
       $.ajax({
            type: "GET",
            url : "{{ route('update.pembayaran.harga.sum.markup.perkategori') }}",
            data: {'_token': '{{csrf_token()}}',id_kategori:id_kategori,aksi:aksi,nominal:nominal},
            success: function( msg ) {
              // console.log(msg);
              if(msg == 'error'){
                $.unblockUI();
                swal("Error!", "Update gagal,Api tidak terhubung!", "error");
                return false;
              }
              $.unblockUI();
              swal("Success!", "Success update data kategori!", "success");
              location.reload();
            }
        });
    }

    function sumMarkingByOperator()
    {
      var id_operator=$('#pilih_operator_plusminus').val();
      // console.log('id_operator:',id_operator);
      var aksi = $('#aksi').val();
      var nominal = $('#nominal').val();
      $.blockUI({ css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        } });
       $.ajax({
            type: "GET",
            url : "{{ route('update.pembayaran.harga.sum.markup.peroperator') }}",
            data: {'_token': '{{csrf_token()}}',id_operator:id_operator,aksi:aksi,nominal:nominal},
            success: function( msg ) {
              // console.log(msg);
              if(msg == 'error'){
                $.unblockUI();
                swal("Error!", "Update gagal,Api tidak terhubung!", "error");
                return false;
              }
              $.unblockUI();
              swal("Success!", "Success update data kategori!", "success");
              location.reload();
            }
        });
    }


  $(function () {
      var index, len;
      var operator = <?php echo json_encode($kategori->pembayaranoperator);?>;
      var jenis_produk = <?php echo json_encode($kategori->slug);?>;
      var datachecked = [];
      for (index = 0, len = operator.length; index < len; ++index) {
        var dinamis_id_table = operator[index].product_name.split(' ').join('').toLowerCase();
           $('#tabel-'+dinamis_id_table+'').DataTable({
             "searching": false,
             "lengthChange": false,
             "paging": false,
             "info": false
           });
      }
  });

  $('#pilih_kategori').on('change', function(e){
   var kategori_id = e.target.value;
   $('#pilih_operator').empty();
   $('#pilih_operator').append('<option value="" selected="selected">Loading...</option>');
   $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '/admin/process-cari-pembayaran/findproduct',
        dataType: "json",
        type: "GET",
        data: {
            'kategori_id': kategori_id,
        },
        success: function (response) {
            $('#pilih_operator').empty();
            // $('#produk').append('<option value="" selected="selected">-- Pilih Produk --</option>');
            if(response.length != 0){
                $.each(response, function(index, produkObj){
                    harga = parseInt(produkObj.price);
                    if (produkObj.status == 0) {
                        $('#pilih_operator').append('<option value="'+produkObj.id+'" style="color: #C8C8C8;" disabled>'+produkObj.product_name+'</option>');
                    }else{
                        $('#pilih_operator').append('<option value="'+produkObj.id+'">'+produkObj.product_name+'</option>');
                    }
                });

            }else{
                toastr.error("Sistem {{$GeneralSettings->nama_sistem}} sedang melakukan MAINTENANCE, untuk itu kami mohon untuk tidak melakukan transaksi terlebih dahulu. Trimakasih");
            }

        },
        error: function (response) {
            $('#produk').empty();
            $('#produk').append('<option value="" selected="selected">-- Pilih Produk --</option>');
            toastr.error("TERJADI KESALAHAN, SILAHKAN REFRESH HALAMAN DAN LAKUKAN LAGI.");
        }

    });
});

    $('#showProduk').on('change', function(e){
        $('.loading').html("<div class='hidden-lg' style='text-align:center;'><i class='fa fa-spinner fa-4x faa-spin animated text-primary' style='margin-top:100px;'></i></div>");
        $('.sidebar-mini').removeClass('sidebar-open');
        var slug = e.target.value;
        //ajax
        if (slug != '') {
            $.get('/admin/pembayaran-produk/'+ slug, function(data){
                //success data
                window.location.href='http://localhost:8000/admin/pembayaran-produk/'+ slug;
            });
        }else{
            window.location.href='http://localhost:8000';
        }
    });

 $('#pilih_kategori_plusminus').on('change', function(e){
   var kategori_id = e.target.value;
   $('#pilih_operator_plusminus').empty();
   $('#pilih_operator_plusminus').append('<option value="" selected="selected">Loading...</option>');
   $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '/admin/process-cari-pembayaran/findproduct',
        dataType: "json",
        type: "GET",
        data: {
            'kategori_id': kategori_id,
        },
        success: function (response) {
            $('#pilih_operator_plusminus').empty();
            // $('#produk').append('<option value="" selected="selected">-- Pilih Produk --</option>');
            if(response.length != 0){
                $.each(response, function(index, produkObj){
                    harga = parseInt(produkObj.price);
                    if (produkObj.status == 0) {
                        $('#pilih_operator_plusminus').append('<option value="'+produkObj.id+'" style="color: #C8C8C8;" disabled>'+produkObj.product_name+'</option>');
                    }else{
                        $('#pilih_operator_plusminus').append('<option value="'+produkObj.id+'">'+produkObj.product_name+'</option>');
                    }
                });

            }else{
                toastr.error("Sistem {{$GeneralSettings->nama_sistem}} sedang melakukan MAINTENANCE, untuk itu kami mohon untuk tidak melakukan transaksi terlebih dahulu. Trimakasih");
            }

        },
        error: function (response) {
            $('#produk').empty();
            $('#produk').append('<option value="" selected="selected">-- Pilih Produk --</option>');
            toastr.error("TERJADI KESALAHAN, SILAHKAN REFRESH HALAMAN DAN LAKUKAN LAGI.");
        }

    });
});
    // show or hide
    $(function () {
        $("input[name='selectplusminus']").click(function () {
            if ($("#chkYes").is(":checked")) {
                $("#option-show-hide").hide();
                $('#pilih_operator_plusminus').empty();
                $('#pilih_operator_plusminus').append('<option value="">Pilih Operator ...</option>');
                $("#pilih_kategori_plusminus").val($("#pilih_kategori_plusminus").data("default-value"));
                $("#pilih_operator_plusminus").val($("#pilih_operator_plusminus").data("default-value"));
                $("#proses").show();

            } else {
                $("#option-show-hide").show();
                $('#pilih_operator_plusminus').empty();
                $('#pilih_operator_plusminus').append('<option value="">Pilih Operator ...</option>');
                $("#pilih_kategori_plusminus").val($("#pilih_kategori_plusminus").data("default-value"));
                $("#pilih_operator_plusminus").val($("#pilih_operator_plusminus").data("default-value"));
                $("#proses").hide();
            }
        });
     });

</script>
@endsection
