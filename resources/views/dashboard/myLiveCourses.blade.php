@extends('dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Live Courses </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-pie-chart"></i> Live Courses</li>
      <li class="active">My Live Courses</li>
    </ol>
  </section>
@stop
@section('dashboard_content')
<div class="container">
  <div class="row">
    <div class="col-sm-4 mrgn_10_btm">
        <select id="category" class="form-control" name="category" onChange="showLiveCourses(this);"  required>
            <option>Select Category ...</option>
            @if(count($categoryIds) > 0)
              @foreach($categoryIds as $categoryId)
                @if( 1 == $categoryId )
                  <option value="{{$categoryId}}">Technology</option>
                @else if(2 == $categoryId )
                  <option value="{{$categoryId}}">Science</option>
                @endif
              @endforeach
            @endif
          </select>
    </div>
  </div><br/>
  <div class="row" id="all_live_courses">
	@if(count($liveCourses) > 0)
    @foreach($liveCourses as $liveCourse)
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 slideanim">
      <div class="course-box">
        <a class="img-course-box" href="{{ url('liveCourse')}}/{{$liveCourse->id}}">
          @if(!empty($liveCourse->image_path))
            <img class="img-responsive " src="{{ asset($liveCourse->image_path) }}" alt="course">
          @else
            <img class="img-responsive " src="{{ asset('images/live-video.jpg') }}" alt="course">
          @endif
        </a>
        <div class="topleft">@if( 1 == $liveCourse->certified )Certified @else Non Certified @endif</div>
        <div class="topright">{{($liveCourse->price > 0)? 'Paid' : 'Free' }}</div>
        <div class="course-box-content" >
           <h4 class="course-box-title " title="{{$liveCourse->name}}" data-toggle="tooltip" data-placement="bottom"> <p class="block-with-text"><a href="{{ url('liveCourse')}}/{{$liveCourse->id}}">{{$liveCourse->name}}</a></p></h4>
           <div class="categoery">
             <a  href="{{ url('liveCourse')}}/{{$liveCourse->id}}">
              @if( 1 == $liveCourse->category_id )Technology @else Science @endif
              </a>
           </div>
           <br/>
          <p class="block-with-text">
            {{$liveCourse->description}}
            <a type="button" class="show " data-show="{{$liveCourse->id}}">Read More</a>
          </p>
          <div class="corse-detail" id="corse-detail-{{$liveCourse->id}}" data-close="{{$liveCourse->id}}">
              <div class="corse-detail-heder">
                <span class="card-title"><b>{{$liveCourse->name}}</b></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close"  data-close="{{$liveCourse->id}}"><span aria-hidden="true">×</span></button>
              </div><br/>
                <p>{{$liveCourse->description}}</p>
                <div class="text-center corse-detail-footer" >
                  <a href="{{ url('liveCourse')}}/{{$liveCourse->id}}" class="btn btn-primary btn-default" > Start Course</a>
                </div>
            </div>
          </div>
          <div class="course-auther">
            <a href="{{ url('liveCourse')}}/{{$liveCourse->id}}"><i class="fa fa-long-arrow-right block-with-text" aria-hidden="true" title="{{$liveCourse->author}}"> {{$liveCourse->author}}</i>
            </a>
          </div>
        </div>
      </div>
    @endforeach
  @else
    No Live Courses are registered.
  @endif
  </div>
</div>
  <script type="text/javascript" src="{{ asset('js/read_info.js')}}"></script>
<script type="text/javascript">
  function renderLiveCourse(msg){
    divAllLiveCourses = document.getElementById('all_live_courses');
    divAllLiveCourses.innerHTML = '';
    if(undefined !== msg['liveCourses'] && 0 < msg['liveCourses'].length) {
      $.each(msg['liveCourses'], function(idx, obj) {
        var firstDiv = document.createElement('div');
        firstDiv.className = "col-lg-4 col-md-4 col-sm-6 col-xs-12";
        var secondDiv = document.createElement('div');
        secondDiv.className = "course-box";
        var url = "{{ url('liveCourse')}}/"+ obj.id;
        var anc = document.createElement('a');
        anc.className = 'img-course-box';
        anc.href = url;
        var img = document.createElement('img');
        img.className = "img-responsive";
        if(obj.image_path){
          img.src = "{{ asset('') }}" + obj.image_path;
        } else {
          img.src = "{{ asset('images/live-video.jpg') }}";
        }
        anc.appendChild(img);
        secondDiv.appendChild(anc);
        var topleftEle = document.createElement('div');
        topleftEle.className = "topleft";
        if(1 == obj.certified ){ certifiedVal = 'Certified';} else {certifiedVal='Non Certified'}
        topleftEle.innerHTML = certifiedVal;
        secondDiv.appendChild(topleftEle);
        var toprightEle = document.createElement('div');
        toprightEle.className = "topright";
        if( obj.price > 0 ){ price = 'Paid';} else { price='Free';}
        toprightEle.innerHTML = price;
        secondDiv.appendChild(toprightEle);

        var thirdDiv = document.createElement('div');
        thirdDiv.className = "course-box-content";
        if(1 == obj.category_id ){ category = 'Technology';} else {category='Science'}
        var courseContent = '<h4 class="course-box-title" title="'+ obj.name +'" data-toggle="tooltip" data-placement="bottom"><p class="block-with-text"><a href="'+ url +'">'+ obj.name +'</a></p></h4>';
         courseContent += '<div class="categoery"><a  href="'+ url +'">'+ category +'</a></div><br/><p class="block-with-text">'+ obj.description+'<a type="button" class="show " data-show="'+ obj.id +'">Read More</a></p>';

        courseContent += '<div class="corse-detail" id="corse-detail-'+ obj.id +'"><div class="corse-detail-heder"><span class="card-title"><b>'+ obj.name +'</b></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close"  data-close="'+ obj.id +'"><span aria-hidden="true">×</span></button></div></br/><p>'+ obj.description +'</p><div class="text-center corse-detail-footer" ><a href="'+ url +'" class="btn btn-primary btn-default" > Start Course</a></div></div>';

        thirdDiv.innerHTML = courseContent;
        secondDiv.appendChild(thirdDiv);

        var authorDiv = document.createElement('div');
        authorDiv.className = "course-auther";
        authorDiv.innerHTML = '<a href="'+ url +'"><i class="fa fa-long-arrow-right block-with-text" aria-hidden="true" title="'+ obj.author +'">'+ obj.author +'</i></a>';
        secondDiv.appendChild(authorDiv);
        firstDiv.appendChild(secondDiv);
        divAllLiveCourses.appendChild(firstDiv);
      });
      $(function(){
          $('.show').on('click',function(){
            id = $(this).data('show')        ;
              $('[id ^=corse-detail-'+id).slideToggle('slow');
          });
          $(' .close').on('click',function(){
              $('[id ^=corse-detail-'+id).slideToggle('slow');
          });
      });
    }
  }

  function showLiveCourses(ele){
    var catId = parseInt($(ele).val());
    var userId = parseInt(document.getElementById('user_id').value);
    document.getElementById('all_live_courses').innerHTML = '';
    if(catId > 0 && userId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('getLiveCourseByCatId')}}",
        data: {catId:catId, userId:userId}
      })
      .done(function( msg ) {
        renderLiveCourse(msg);
        var searches = document.getElementsByClassName('search');
        $.each(searches, function(ind, obj){
          $(obj).attr('checked', false);
        });
      });
    }
  }
</script>
@stop