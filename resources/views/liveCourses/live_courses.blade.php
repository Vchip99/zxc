@extends('layouts.master')
@section('header-title')
  <title>Live Online Courses by Industrial Expert |Vchip-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
    <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
    <link href="{{ asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
    /*==================
 BENIFIT SECTION
 ====================*/
 #floating-signup-btn {
  text-align: center;
  margin-bottom: 100px;
}
#floating-signup-btn .v-theme-color-btn{
  background-color: #01bafd;
  color:#fff;
  padding-left:  20px;
  padding-right:  20px;
  font-size: 20px;
  font-weight: bold;
  text-align: center;
  padding-top: 20px;
  padding-bottom:20px;
  border-radius: 20px;
  text-decoration: none;
}
@media (max-width: 935px) {
  #floating-signup-btn .v-theme-color-btn{
    padding-left:  10px;
    padding-right:  10px;
    font-size: 15px;
  }
}
@media (max-width: 768px) {
  #floating-signup-btn .v-theme-color-btn{
    padding-left:  10px;
    padding-right:  10px;
    font-size: 10px;
  }
 }
.benefits-section {
  background-color:#00688B;
}
.box{
  background: transparent;
  padding: 10px;
  margin-top: 10px;
}
.icon i{
  font-size: 50px;
  color: #fff;
  padding: 10px 20px;
  border: 1px solid #fff;
  border-radius: 50%;
}
.box h3{
  color: #302B54;
  font-weight: bold;
}
.box p{
  color: #fff;
}
.box .read_more{
  color: #fff;
  font-weight: bold;
  background: #01bafd;
  padding: 3px;
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
        <img src="{{asset('images//live-course.png')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip live courses" />
      </figure>
    </div>
    <div class="vchip-background-content">
     <h2 class="animated bounceInLeft">Digital Education</h2>
   </div>
 </div>
</section>
<section id="sidemenuindex" class="v_container">
  <div class="container ">
    <div class="row">
      <div class="col-sm-3 hidden-div">
        <h4 class="v_h4_subtitle"> Sort By</h4>
        <div class="dropdown mrgn_20_top_btm" id="cat">
          <select id="category" class="form-control" name="category" title="Category" onChange="showLiveCourses(this);"  required>
            <option value="">Select Category ...</option>
            <!-- @if(in_array(1,$liveCourseCategoryIds))
              <option value="1">Technology</option>
            @endif
            @if(in_array(2,$liveCourseCategoryIds))
              <option value="2">Science</option>
            @endif -->
            <option value="1">Technology</option>
            <option value="2">Science</option>
          </select>
        </div>
        <h4 class="v_h4_subtitle mrgn_20_top_btm"> Filter By</h4>
        <div class="panel"></div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" data-toggle="tooltip" title="Difficulty" > Difficulty</p>
        <div class="panel">
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="difficulty" onclick="searchLiveCourse();"> Beginner</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="2" data-filter="difficulty" onclick="searchLiveCourse();">Intermediate</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="3" data-filter="difficulty" onclick="searchLiveCourse();"> Advanced</label>
          </div>
        </div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" data-toggle="tooltip" title="Others" > Others</p>
        <div class="panel">
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="certified" onclick="searchLiveCourse();">Certified</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="onDemand" onclick="searchLiveCourse();">On demand courses</label>
          </div>
        </div>

      </div>
      <div class="col-sm-9 col-sm-push-3 ">
        <div class="row info" id="all_live_courses">
          @if(count($liveCourses) > 0)
            @foreach($liveCourses as $liveCourse)
              <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
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
                       <a  href="{{ url('liveCourse')}}/{{$liveCourse->id}}" title="@if( 1 == $liveCourse->category_id )Technology @else Science @endif">
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
                            <a href="{{ url('liveCourse')}}/{{$liveCourse->id}}" class="btn btn-primary btn-default" title="Start Course"> Start Course</a>
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
              No courses are available.
            @endif
          </div>
          <div  id="pagination">
            {{ $liveCourses->links() }}
          </div>
      </div>
      <div class="col-sm-3 col-sm-pull-9">
        <div class="hidden-div1">
          <h4 class="v_h4_subtitle"> Sort By</h4>
          <div class="dropdown mrgn_20_top_btm" id="cat">
            <select id="category" class="form-control" name="category" title="Category" onChange="showLiveCourses(this);"  required>
              <option value="">Select Category ...</option>
              <!-- @if(in_array(1,$liveCourseCategoryIds))
                <option value="1">Technology</option>
              @endif
              @if(in_array(2,$liveCourseCategoryIds))
                <option value="2">Science</option>
              @endif -->
              <option value="1">Technology</option>
              <option value="2">Science</option>
            </select>
          </div>
          <h4 class="v_h4_subtitle mrgn_20_top_btm"> Filter By</h4>
          <div class="panel"></div>
          <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" data-toggle="tooltip" title="Difficulty" > Difficulty</p>
          <div class="panel">
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="1" data-filter="difficulty" onclick="searchLiveCourse();"> Beginner</label>
            </div>
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="2" data-filter="difficulty" onclick="searchLiveCourse();">Intermediate</label>
            </div>
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="3" data-filter="difficulty" onclick="searchLiveCourse();"> Advanced</label>
            </div>
          </div>
          <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" data-toggle="tooltip" title="Others" > Others</p>
          <div class="panel">
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="1" data-filter="certified" onclick="searchLiveCourse();">Certified</label>
            </div>
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="1" data-filter="onDemand" onclick="searchLiveCourse();">On demand courses</label>
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
<section style="height: 0;">
  <div class="container">
    <div class="row button" id="floating-signup-btn">
     <a href="{{ asset('saveTimeSecurity')}}" class="v-theme-color-btn" role="button">Benefits of Live online tutoring</a>
   </div>
 </div>
</section>
<section id="" class="v_container  benefits-section">
  <div class="container text-center v_container">
    <div class="row text-center ">
      <div class="col-md-4 col-sm-6 text-center">
        <div class="box">
        <a href="{{ asset('saveTimeSecurity')}}" target="_blank">
          <div class="icon" title="PERSONALIZED LIVE TEACHING"><i class="fa fa-user " aria-hidden="true"></i></div>
          <h3>PERSONALIZED LIVE TEACHING</h3>
          <p>Dedicated Teacher, 100% Personal attention</p>
          <p>Teaching at student's pace</p>
          <a class="read_more" href="{{ asset('saveTimeSecurity')}}">Read More</a>
          </a>
        </div>
      </div>
      <div class="col-md-4 col-sm-6">
        <div class="box">
        <a href="{{ asset('saveTimeSecurity')}}" target="_blank">
          <div class="icon " title="BETTER THAN  RECORDED LECTURES"><i class="fa fa-refresh" aria-hidden="true"></i></div>
          <h3>BETTER THAN  RECORDED LECTURES</h3>
          <p>2 way interaction</p>
          <p>Monitoring & Counseling</p>
          <a class="read_more" href="{{ asset('saveTimeSecurity')}}">Read More</a>
          </a>
        </div>
      </div>
      <div class="col-md-4 col-sm-6">
        <div class="box">
        <a href="{{ asset('saveTimeSecurity')}}" target="_blank">
          <div class="icon " title="SAVE ON TIME"><i class="fa fa-clock-o" aria-hidden="true"></i></div>
          <h3>SAVE ON TIME</h3>
          <p>No commuting</p>
          <p>No looking for the right teacher</p>
          <a class="read_more" href="{{ asset('saveTimeSecurity')}}">Read More</a>
          </a>
        </div>
      </div>
      <div class="col-md-4 col-md-offset-2 col-sm-6" >
        <div class="box">
          <a href="{{ asset('saveTimeSecurity')}}" target="_blank">
            <div class="icon " title="ANYTIME ANYWHERE LEARNING"><i class="fa fa-sun-o" aria-hidden="true"></i></div>
            <h3>ANYTIME ANYWHERE LEARNING</h3>
            <p>Choice of Web and Mobile Platform</p>
            <p>Choose your own topic, time & place</p>
            <a class="read_more" href="{{ asset('saveTimeSecurity')}}">Read More</a>
          </a>
        </div>
      </div>
      <div class="col-md-4 col-sm-12 ">
        <div class="box" >
          <a href="{{ asset('saveTimeSecurity')}}" target="_blank">
            <div class="icon " title="SAFETY"><i class="fa fa-expeditedssl" aria-hidden="true"></i></div>
            <h3>SAFETY</h3>
            <p>Learn safety @ home</p>
            <p>No traffic annoyance</p>
            <a class="read_more" href="{{ asset('saveTimeSecurity')}}">Read More</a>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
@stop
@section('footer')
  @include('footer.footer')
  <script src="{{asset('js/togleForFilterBy.js?ver=1.0')}}"></script>
  <script type="text/javascript" src="js/read_info.js"></script>
  <script type="text/javascript">
  function renderLiveCourse(msg){
    divAllLiveCourses = document.getElementById('all_live_courses');
    divAllLiveCourses.innerHTML = '';
    document.getElementById('pagination').innerHTML = '';
    if(undefined !== msg['liveCourses'] && 0 < msg['liveCourses'].length) {
      $.each(msg['liveCourses'], function(idx, obj) {
        var firstDiv = document.createElement('div');
          firstDiv.className = "col-lg-4 col-md-4 col-sm-6 col-xs-6";
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
           courseContent += '<div class="categoery"><a  href="'+ url +'" title="'+category+'">'+ category +'</a></div><br/><p class="block-with-text">'+ obj.description+'<a type="button" class="show " data-show="'+ obj.id +'">Read More</a></p>';

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
    } else {
      divAllLiveCourses.innerHTML = 'No Result Found.';
    }
  }

  function searchLiveCourse(){
    var searches = document.getElementsByClassName('search');
    var arrDifficulty = [];
    var arrCertified = [];
    var arrOnDemand = [];
    var arr = [];
    var arrFees = [];
    $.each(searches, function(ind, obj){
      if(true == $(obj).is(':checked')){
        var filter = $(obj).data('filter');
        var filterVal = $(obj).val();
        if(false == (arrDifficulty.indexOf(filter) > -1)){
          if('difficulty' == filter) {
            arrDifficulty.push(filterVal);
            arr.push(filterVal);
          }
          if('certified' == filter) {
            arrCertified.push(filterVal);
            arr.push(filterVal);
          }
          if('onDemand' == filter) {
            arrOnDemand.push(filterVal);
            arr.push(filterVal);
          }
          if('fees' == filter) {
            arrFees.push(filterVal);
            arr.push(filterVal);
          }
        }
      }
    });
    if(arr instanceof Array ){
      categoryId = document.getElementById('category').value;
      var arrJson = {'difficulty' : arrDifficulty, 'certified' : arrCertified, 'onDemand' : arrOnDemand, 'fees' : arrFees, 'categoryId' : categoryId };
      $.ajax({
        method: "POST",
        url: "{{url('getLiveCourseBySearchArray')}}",
        data: {arr:JSON.stringify(arrJson)}
      })
      .done(function( msg ) {
        renderLiveCourse(msg);
      });
    }
  }

  function showLiveCourses(ele){
    var catId = parseInt($(ele).val());
    if(catId > 0 ){
      $.ajax({
        method: "POST",
        url: "{{url('getLiveCourseByCatId')}}",
        data: {catId:catId}
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

