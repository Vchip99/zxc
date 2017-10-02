@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |V-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/box.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
  .vchip_product_itm{
      background: #fff;
      margin-top: 30px;
      width: 100%;
      height:auto;
      padding: 10px;
      -webkit-box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.18);
      -moz-box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.18);
      -ms-box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.18);
      -o-box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.18);
      box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.18);
    }
    .vchip_product_content{ padding:10px 10px 7px 22px;
    }
    h3.h3-heding{
    text-transform: uppercase;
    font-size: 15px;
    font-weight: 600;
    color: #e91e63;
    padding-left: 14px ;
    outline: 0;
    top:0px;
    font-weight: bolder;

    }
    .ellipsed {
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
     }
    h3.ellipsed{
  cursor: pointer;
}
</style>
@stop
@section('header-js')
  @include('layouts.home-js')
@stop
@section('content')
	@include('header.header_menu')
  <section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single">
      <div class="vchip-background-img">
        <figure>
          <img src="{{asset('images/course.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip courses" />
        </figure>
      </div>
      <div class="vchip-background-content">
          <h2 class="animated bounceInLeft">Digital Education</h2>
        </div>
    </div>
  </section>
<!-- Start course section -->
<section id="sidemenuindex" class="v_container">
  <div class="container ">
    <div class="row">
      <div class="col-sm-3 ">
        <h4 class="v_h4_subtitle"> Sorted By</h4>
        <div class="mrgn_20_top_btm" >
          <select id="category" class="form-control" name="category" data-toggle="tooltip" title="Category" onChange="selectWorkshop(this);" required>
            <option value="0">Select Category</option>
            @if(count($workshopCategories) > 0)
              @foreach($workshopCategories as $workshopCategory)
                <option value="{{$workshopCategory->id}}">{{$workshopCategory->name}}</option>
              @endforeach
            @endif
          </select>
        </div>
      </div>
      <div class="col-sm-9 ">
        <div class="row info" id="addWorkshop">
          @if(count($workshops) > 0)
            @foreach($workshops as $workshop)
              <div class="col-lg-6 col-md-6 col-sm-6 slideanim small-img">
                <div class="vchip_product_itm text-left">
                  <figure>
                    <img src="{{ asset($workshop->workshop_image) }}" alt="workshop" class="img-responsive " />
                  </figure>
                  <h3 class="h3-heding ellipsed" title="Workshop">{{ $workshop->name }}</h3>
                  <div class="vchip_product_content">
                    <p class="mrgn_20_top">
                      @if(date('Y-m-d') >= $workshop->start_date && date('Y-m-d') <= $workshop->end_date)
                        <a href="{{ url('workshopDetails')}}/{{$workshop->id}}" class="btn-link ">
                          <i class="fa fa-clock-o"></i>
                          {{ $workshop->start_date }} - {{ $workshop->end_date }}
                          <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </a>
                      @else
                        <a href="" class="btn-link ">
                          <i class="fa fa-clock-o"></i>
                          {{ $workshop->start_date }} - {{ $workshop->end_date }}
                          <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </a>
                      @endif
                    </p>
                  </div>
                </div>
              </div>
            @endforeach
          @else
            No workshops are available.
          @endif
        </div>
      <div style="float: right;" id="pagination">
        {{ $workshops->links() }}
      </div>
      </div>
    </div>
  </div>
  <input type="hidden" id="current_date" name="current_date" value="{{ date('Y-m-d') }}">
</section>
@stop
@section('footer')
	@include('footer.footer')
  <script type="text/javascript" src="{{ asset('js/togleForFilterBy.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/read_info.js')}}"></script>
  <script type="text/javascript">
  function selectWorkshop(ele){
    var id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getWorkshopsByCategory')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        divWorkshop = document.getElementById('addWorkshop');
        divWorkshop.innerHTML = '';
        document.getElementById('pagination').innerHTML = '';
        var currentDate = document.getElementById('current_date').value;
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
            var firstDiv = document.createElement('div');
            firstDiv.className = "col-lg-6 col-md-6 col-sm-6 small-img";
            var secondDiv = document.createElement('div');
            secondDiv.className = "vchip_product_itm text-left";
            secondDiv.innerHTML = '';
            var imgUrl = "{{ asset('') }}" + obj.workshop_image;
            secondDiv.innerHTML +='<figure><img src="'+imgUrl+'" alt="workshop" class="img-responsive"/></figure>';
            secondDiv.innerHTML +=' <h3 class="h3-heding ellipsed" title="Workshop">'+ obj.name +'</h3>';
            var detaulsUrl = "{{ asset('workshopDetails') }}/" + obj.id;

            if(currentDate >= obj.start_date && currentDate <= obj.end_date){
              secondDiv.innerHTML +='<div class="vchip_product_content"><p class="mrgn_20_top"><a href="'+detaulsUrl+'" class="btn-link "> <i class="fa fa-clock-o"></i> '+ obj.start_date + ' - ' + obj.end_date + ' <i class="fa fa-angle-right" aria-hidden="true"></i></a></p></div>';
            } else {
              secondDiv.innerHTML +='<div class="vchip_product_content"><p class="mrgn_20_top"><a href="" class="btn-link "> <i class="fa fa-clock-o"></i> '+ obj.start_date + ' - ' + obj.end_date + ' <i class="fa fa-angle-right" aria-hidden="true"></i></a></p></div>';
            }
            firstDiv.appendChild(secondDiv);
            divWorkshop.appendChild(firstDiv);
          });
        }
      });
    }
  }

  $(".toggle").slideUp();
  $(".trigger").click(function(){
    $(this).next(".toggle").slideToggle("slow");
  });

  var acc = document.getElementsByClassName("accordion");
  var i;
  for (i = 0; i < acc.length; i++) {
    acc[i].onclick = function() {
      this.classList.toggle("active");
      var panel = this.nextElementSibling;
      if (panel.style.maxHeight){
        panel.style.maxHeight = null;
      } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
      }
    }
  }


  </script>
@stop