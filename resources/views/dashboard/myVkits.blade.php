@extends('dashboard.dashboard')
@section('dashboard_header')
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
@section('module_title')
  <section class="content-header">
    <h1> Favourite Projects </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Vkits </li>
      <li class="active"> Favourite Projects </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
<div class="container">
  <div class="row">
    <div class="col-sm-4 mrgn_10_btm">
      <select class="form-control" id="category" name="category" onchange="showProjects(this);">
        <option>Select Category ...</option>
        @if(count($categories) > 0)
          @foreach($categories as $category)
            <option value="{{$category->id}}">{{$category->name}}</option>
          @endforeach
        @endif
      </select>
    </div>
  </div><br/>
  <div class="row" id="vkitprojects">
	@if(count($projects) > 0)
    @foreach($projects as $project)
      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 slideanim">
        <div class="course-box">
          <a class="img-course-box" href="{{ url('vkitproject')}}/{{$project->id}}">
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
              <a href="{{ url('vkitproject')}}/{{$project->id}}"><i class="fa fa-long-arrow-right" aria-hidden="true"> {{$project->author}}</i>
              </a>
            </div>
          </div>
        </div>
      @endforeach
    @else
      No projects are registered as favourite.
    @endif
  </div>
</div>
  <script type="text/javascript" src="{{ asset('js/read_info.js')}}"></script>
  <script type="text/javascript">
    $('li#vkits').addClass('active');

    function renderVkitProjects(msg){
      projects = document.getElementById('vkitprojects');
      projects.innerHTML = '';
      if( 0 < msg.length){
        $.each(msg, function(idx, obj) {
        var firstDiv = document.createElement('div');
          firstDiv.className = "col-lg-4 col-md-4 col-sm-6 col-xs-12";
          var secondDiv = document.createElement('div');
          secondDiv.className = "course-box";
          var url = "{{ url('vkitproject')}}/"+ obj.id;
          var anc = document.createElement('a');
          anc.className = 'img-course-box';
          anc.href = url;
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
          authorDiv.innerHTML = '<a href="'+ url +'"><i class="fa fa-long-arrow-right" aria-hidden="true">'+ obj.author +'</i></a>';
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
      }
    }

    function showProjects(ele){
      var id = parseInt($(ele).val());
      var userId = parseInt(document.getElementById('user_id').value);
      document.getElementById('vkitprojects').innerHTML = '';
      if( 0 < id && 0 < userId ){
        $.ajax({
          method: "POST",
          url: "{{url('getVkitProjectsByCategoryId')}}",
          data: {id:id, userId:userId}
        })
        .done(function( msg ) {
          renderVkitProjects(msg)
        });
      }
    }
  </script>
  <style type="text/css">
    p.ellipsis{
      display:inline-block;
      width:300px;
      white-space: nowrap;
      overflow:hidden !important;
      text-overflow: ellipsis;
    }
  </style>
@stop