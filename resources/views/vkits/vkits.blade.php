@extends('layouts.master')
@section('header-title')
  <title>Hobby Projects in Electronics, IoT, VLSI and Vchip-kit |Vchip-edu </title>
@stop
@section('header-css')
  @include('layouts.home-css')
    <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
    <link href="{{asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
    .vchip_product_item{
      background:#FFF;
      padding: 20px;
      -webkit-box-shadow:0 0 5px 0 rgba(130,130,130,0.4);
      -moz-box-shadow:0 0 5px 0 rgba(130,130,130,0.4);
      box-shadow:0 0 5px 0 rgba(130,130,130,0.4);
      margin-bottom:40px;
      text-align:left
    }
    .vchip_product_item:hover{

      box-shadow: 0 8px 17px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);

    }
    .vchip_product_content{padding:10px 20px}
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
        <img src="{{asset('images/v-kit.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip vkit" />
      </figure>
    </div>
    <div class="vchip-background-content">
        <h2 class="animated bounceInLeft">Digital Education</h2>
    </div>
  </div>
</section>
<section id="sidemenuindex"  class="v_container">
  <div class="container ">
    <div class="row">
      <div class="col-sm-3  hidden-div">
        <h4 class="v_h4_subtitle "> Sort By</h4>
        <div class="mrgn_20_top_btm" id="cat">
          <select class="form-control" id="category" name="category" title="Category" onchange="showProjects(this);">
            <option value="">Select Category ...</option>
            @if(count($vkitCategories) > 0)
              @foreach($vkitCategories as $index => $vkitCategory)
                <option value="{{$vkitCategory->id}}">{{$vkitCategory->name}}</option>
              @endforeach
            @endif
          </select>
        </div>
        <h4 class="v_h4_subtitle mrgn_20_top_btm"> Filter By</h4>
        <div class="panel"></div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" title="Gateway"> Gateway</p>
        <div class="panel">
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="gateway" onclick="searchVkitProjects();">Android</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="2" data-filter="gateway" onclick="searchVkitProjects();">Raspberry-pi</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="3" data-filter="gateway" onclick="searchVkitProjects();">Intel galileo</label>
          </div>
        </div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm"  title="Microcontroller"> Microcontroller</p>
        <div class="panel">
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="microcontroller" onclick="searchVkitProjects();">AVR</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="2" data-filter="microcontroller" onclick="searchVkitProjects();">Atmega328</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="3" data-filter="microcontroller" onclick="searchVkitProjects();">8051/8052</label>
          </div>
        </div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm"  title="Others"> Others</p>
        <div class="panel">
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="upcoming" onclick="searchVkitProjects();">Upcoming</label>
          </div>
        </div>

      </div>
      <div class="col-sm-9 col-sm-push-3">
        <div class="row info" id="vkitprojects">
          @if(count($projects) > 0)
            @foreach($projects as $project)
              <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                <div class="course-box">
                  <a class="img-course-box" href="{{ url('vkitproject')}}/{{$project->id}}" title="{{$project->name}}">
                    @if(!empty($project->front_image_path))
                      <img class="img-responsive " src="{{ asset($project->front_image_path) }}" alt="vckits">
                    @else
                      <img class="img-responsive " src="{{ asset('images/default_course_image.jpg') }}" alt="vckits">
                    @endif
                  </a>
                  <div class="course-box-content" >
                     <h4 class="course-box-title " title="{{$project->name}}" data-toggle="tooltip" data-placement="bottom"> <p class="block-with-text"><a href="{{ url('vkitproject')}}/{{$project->id}}">{{$project->name}}</a></p></h4>
                     <br/>
                    <p class="block-with-text">
                      {{$project->introduction}}
                      <a type="button" class="show " data-show="{{$project->id}}">Read More</a>
                    </p>
                    <div class="corse-detail" id="corse-detail-{{$project->id}}">
                        <div class="corse-detail-heder">
                          <span class="card-title"><b>{{$project->name}}</b></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close"  data-close="{{$project->id}}"><span aria-hidden="true">×</span></button>
                        </div><br/>
                          <p>{{$project->introduction}}</p>
                          <div class="text-center corse-detail-footer" >
                            <a href="{{ url('vkitproject')}}/{{$project->id}}" class="btn btn-primary btn-default" > Start Project</a>
                          </div>
                      </div>
                    </div>
                    <div class="course-auther">
                      <a href="{{ url('vkitproject')}}/{{$project->id}}"><i class="fa fa-long-arrow-right block-with-text" aria-hidden="true" title="{{$project->author}}"> {{$project->author}}</i>
                      </a>
                    </div>
                  </div>
                </div>
            @endforeach
            @else
              No projects are available.
            @endif
          </div>
            <div  id="pagination">
              {{ $projects->links() }}
            </div>
      </div>
      <div class="col-sm-3 col-sm-pull-9">
        <div class="hidden-div1">
             <h4 class="v_h4_subtitle "> Sort By</h4>
              <div class="mrgn_20_top_btm" id="cat">
                <select class="form-control" id="category" name="category" title="Category" onchange="showProjects(this);">
                  <option value="">Select Category ...</option>
                  @if(count($vkitCategories) > 0)
                    @foreach($vkitCategories as $index => $vkitCategory)
                      <option value="{{$vkitCategory->id}}">{{$vkitCategory->name}}</option>
                    @endforeach
                  @endif
                </select>
              </div>
              <h4 class="v_h4_subtitle mrgn_20_top_btm"> Filter By</h4>
              <div class="panel"></div>
              <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" title="Gateway"> Gateway</p>
              <div class="panel">
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="1" data-filter="gateway" onclick="searchVkitProjects();">Android</label>
                </div>
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="2" data-filter="gateway" onclick="searchVkitProjects();">Raspberry-pi</label>
                </div>
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="3" data-filter="gateway" onclick="searchVkitProjects();">Intel galileo</label>
                </div>
              </div>
              <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm"  title="Microcontroller"> Microcontroller</p>
              <div class="panel">
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="1" data-filter="microcontroller" onclick="searchVkitProjects();">AVR</label>
                </div>
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="2" data-filter="microcontroller" onclick="searchVkitProjects();">Atmega328</label>
                </div>
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="3" data-filter="microcontroller" onclick="searchVkitProjects();">8051/8052</label>
                </div>
              </div>
              <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm"  title="Others"> Others</p>
              <div class="panel">
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="1" data-filter="upcoming" onclick="searchVkitProjects();">Upcoming</label>
                </div>
              </div>
        </div>
        <div class="advertisement-area">
            <span class="pull-right create-add"><a href="{{ url('createAd') }}"> Create Ad</a></span>
        </div>
        <br/>
        @if(count($ads) > 0)
          @foreach($ads as $ad)
            <div class="add-1">
              <div class="course-box">
                <a class="img-course-box" href="{{ $ad->website_url }}" target="_blank">
                  <img src="{{asset($ad->logo)}}" alt="{{ $ad->company }}"  class="img-responsive" />
                </a>
                <div class="course-box-content">
                  <h4 class="course-box-title" title="{{ $ad->company }}" data-toggle="tooltip" data-placement="bottom">
                    <a href="{{ $ad->website_url }}" target="_blank">{{ $ad->company }}</a>
                  </h4>
                  <p class="more"> {{ $ad->tag_line }}</p>
                </div>
              </div>
            </div>
          @endforeach
        @endif
        @if(count($ads) < 3)
          @for($i = count($ads)+1; $i <=3; $i++)
            @if(1 == $i)
              <div class="add-1">
                <div class="course-box">
                  <a class="img-course-box" href="http://ssgmce.org/Default.aspx?ReturnUrl=%2f" target="_blank">
                    <img src="{{ asset('images/logo/ssgmce-logo.jpg') }}" alt="SSGMCE"  class="img-responsive" />
                  </a>
                  <div class="course-box-content">
                    <h4 class="course-box-title" title="SSGMCE" data-toggle="tooltip" data-placement="bottom">
                      <a href="http://ssgmce.org/Default.aspx?ReturnUrl=%2f" target="_blank">SSGMCE</a>
                    </h4>
                    <p class="more"> SSGMCE</p>
                  </div>
                </div>
              </div>
            @elseif(2 == $i)
              <div class="add-1">
                <div class="course-box">
                  <a class="img-course-box" href="http://ghrcema.raisoni.net/" target="_blank">
                    <img src="{{ asset('images/logo/ghrcema_logo.png') }}" alt="G H RISONI"  class="img-responsive" />
                  </a>
                  <div class="course-box-content">
                    <h4 class="course-box-title" title="G H RISONI" data-toggle="tooltip" data-placement="bottom">
                      <a href="http://ghrcema.raisoni.net/" target="_blank">G H RISONI</a>
                    </h4>
                    <p class="more"> G H RISONI</p>
                  </div>
                </div>
              </div>
            @elseif(3 == $i)
              <div class="add-1">
                <div class="course-box">
                  <a class="img-course-box" href="http://hvpmcoet.in/" target="_blank">
                    <img src="{{ asset('images/logo/hvpm.jpg') }}" alt="HVPM"  class="img-responsive" />
                  </a>
                  <div class="course-box-content">
                    <h4 class="course-box-title" title="HVPM" data-toggle="tooltip" data-placement="bottom">
                      <a href="http://hvpmcoet.in/" target="_blank">HVPM College of Engineer And Technology</a>
                    </h4>
                    <p class="more"> HVPM College of Engineer And Technology</p>
                  </div>
                </div>
              </div>
            @endif
          @endfor
        @endif
      </div>
    </div>
  </div>
</section>

  @stop
  @section('footer')
  @include('footer.footer')
  <script type="text/javascript" src="{{ asset('js/togleForFilterBy.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/read_info.js')}}"></script>
  <script type="text/javascript">

  function renderVkitProjects(msg){
    projects = document.getElementById('vkitprojects');
    projects.innerHTML = '';
    document.getElementById('pagination').innerHTML = '';
    if( undefined !== msg.length && 0 < msg.length){
      $.each(msg, function(idx, obj) {
        var firstDiv = document.createElement('div');
          firstDiv.className = "col-lg-4 col-md-4 col-sm-6 col-xs-6";
          var secondDiv = document.createElement('div');
          secondDiv.className = "course-box";
          var url = "{{ url('vkitproject')}}/"+ obj.id;
          var anc = document.createElement('a');
          anc.className = 'img-course-box';
          anc.href = url;
          anc.setAttribute('title', obj.name);
          var img = document.createElement('img');
          img.className = "img-responsive";
          if(obj.front_image_path){
            img.src = "{{ asset('') }}" + obj.front_image_path;
          } else {
            img.src = "{{ asset('images/default_course_image.jpg') }}";
          }
          anc.appendChild(img);
          secondDiv.appendChild(anc);

          var thirdDiv = document.createElement('div');
          thirdDiv.className = "course-box-content";

          var courseContent = '<h4 class="course-box-title" title="'+ obj.name +'" data-toggle="tooltip" data-placement="bottom"><p class="block-with-text"><a href="'+ url +'">'+ obj.name +'</a></p></h4>';
           courseContent += '<br/><p class="block-with-text">'+ obj.introduction+'<a type="button" class="show " data-show="'+ obj.id +'">Read More</a></p>';

          courseContent += '<div class="corse-detail" id="corse-detail-'+ obj.id +'"><div class="corse-detail-heder"><span class="card-title"><b>'+ obj.name +'</b></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-close="'+ obj.id +'"><span aria-hidden="true">×</span></button></div></br/><p>'+ obj.introduction +'</p><div class="text-center corse-detail-footer" ><a href="'+ url +'" class="btn btn-primary btn-default" > Start Project</a></div></div>';

          thirdDiv.innerHTML = courseContent;
          secondDiv.appendChild(thirdDiv);

          var authorDiv = document.createElement('div');
          authorDiv.className = "course-auther";
          authorDiv.innerHTML = '<a href="'+ url +'"><i class="fa fa-long-arrow-right block-with-text" aria-hidden="true" title="'+ obj.author +'">'+ obj.author +'</i></a>';
          secondDiv.appendChild(authorDiv);
          firstDiv.appendChild(secondDiv);
          projects.appendChild(firstDiv);
      });
       $(function(){
          $('.show').on('click',function(){
            id = $(this).data('show')        ;
              $('[id ^=corse-detail-'+id).slideToggle('slow');
          });
          $('.close').on('click',function(){
            id = $(this).data('close')
              $('[id ^=corse-detail-'+id).slideToggle('slow');
          });
      });
    } else {
      projects.innerHTML = 'No Result Found.';
    }
  }

    function searchVkitProjects(){
      var searches = document.getElementsByClassName('search');
      var arrGateway = [];
      var arrMicrocontroller = [];
      var arr = [];
      var upcoming = 0;
      $.each(searches, function(ind, obj){
        if(true == $(obj).is(':checked')){
          var filter = $(obj).data('filter');
          var filterVal = $(obj).val();
          if(false == (arrGateway.indexOf(filter) > -1)){
            if('gateway' == filter) {
              arrGateway.push(filterVal);
              arr.push(filterVal);
            }
            if('microcontroller' == filter) {
              arrMicrocontroller.push(filterVal);
              arr.push(filterVal);
            }
            if('upcoming' == filter) {
              upcoming = 1;
              arr.push(filterVal);
            }
          }
        }
      });
      if(arr instanceof Array ){
        categoryId = document.getElementById('category').value;
        var arrJson = {'gateway' : arrGateway, 'microcontroller' : arrMicrocontroller, 'upcoming' : upcoming, 'categoryId' : categoryId };
        $.ajax({
          method: "POST",
          url: "{{url('getVkitProjectsBySearchArray')}}",
          data: {arr:JSON.stringify(arrJson)}
        })
        .done(function( msg ) {;
          renderVkitProjects(msg)
        });
      }
    }

    function showProjects(ele){
      id = parseInt($(ele).val());
      if( 0 < id ){
        $.ajax({
          method: "POST",
          url: "{{url('getVkitProjectsByCategoryId')}}",
          data: {id:id}
        })
        .done(function( msg ) {
          renderVkitProjects(msg)
          var searches = document.getElementsByClassName('search');
          $.each(searches, function(ind, obj){
            $(obj).attr('checked', false);
          });
        });
      }
    }
  </script>

  <script >
    $(".toggle").slideUp();
    $(".trigger").click(function(){
      $(this).next(".toggle").slideToggle("slow");
    });
  </script>
  <script type="text/javascript">
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