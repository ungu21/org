<div class="col-sm-4 static-nav hidden-xs">
   <ul>
      <li class="{{Request::segment(1) == 'testimonial' ? 'active' : ''}}">
         <a href="{{url('/testimonial')}}">
         <span class="glyphicon glyphicon-play"></span> Testimonial
         </a>
      </li>
      <li class="{{Request::segment(1) == 'about' ? 'active' : ''}}">
         <a href="{{url('/about')}}">
         <span class="glyphicon glyphicon-play"></span> About
         </a>
      </li>
      <li class="{{Request::segment(1) == 'faq' ? 'active' : ''}}">
         <a href="{{url('/faq')}}">
         <span class="glyphicon glyphicon-play"></span> F.A.Q.
         </a>
      </li>
      <li class="{{Request::segment(1) == 'tos' ? 'active' : ''}}">
         <a href="{{url('/tos')}}">
         <span class="glyphicon glyphicon-play"></span> Syarat &amp; Ketentuan
         </a>
      </li>
      <!-- <li class="{{Request::segment(1) == 'contact-us' ? 'active' : ''}}">
         <a href="{{url('/contact-us')}}">
         <span class="glyphicon glyphicon-play"></span> Hubungi Kami
         </a>
      </li> -->
   </ul>
</div>
<div class="col-sm-4 static-nav left-menu-select visible-xs">
   <div class="input-group">
      <span class="input-group-addon" style="background-color: transparent"><i class="fa fa-bars"></i></span>
      <select class="form-control" onchange="window.location.href=this.value">
         <optgroup label="Halaman">
            <option value="{{url('/testimonial')}}" {{Request::segment(1) == 'testimonial' ? 'selected' : ''}}>Testimonial</option>
            <option value="{{url('/about')}}" {{Request::segment(1) == 'about' ? 'selected' : ''}}>About</option>
            <option value="{{url('/faq')}}" {{Request::segment(1) == 'faq' ? 'selected' : ''}}>F.A.Q.</option>
            <option value="{{url('/tos')}}" {{Request::segment(1) == 'tos' ? 'selected' : ''}}>Syarat &amp; Ketentuan</option>
            <!-- <option value="{{url('/contact-us')}}" {{Request::segment(1) == 'contact-us' ? 'selected' : ''}}>Hubungi Kami</option> -->
         </optgroup>
      </select>
   </div>
</div>