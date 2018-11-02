@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |V-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/placement.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
  .advertisement {
    position:relative;
    overflow:hidden;
  }
  .caption {
    position:absolute;
    top:0;
    right:0;
    background:rgba(66, 139, 202, 0.75);
    width:100%;
    height:100%;
    padding:2%;
    display: none;
    text-align:center;
    color:#fff !important;
    z-index:2;
  }
.caption p{ margin-top: 35%;}
@media(min-width: 548px) and(max-width: 768px)
{
  .caption p{ margin-top: 10%;}
}
.caption a{ font-weight: bolder; }
.add-link{font-weight: bolder;}

.ellipsed {
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}
 .sponsored a, .create-add a{color:#A9A9A9;
    font-weight: bolder;
  }
    .ellipsed {
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}
p.ellipsed{
  cursor: pointer;
}
  .v_p_heding{font-weight: bolder;}
 .panel-heading img {
  width: 30px;
  height: 30px;
  float: left;
  border: 2px solid #d2d6de;
  padding: 1px;
}
.username{
margin-left: 10px;
margin-right: 10px;

}
.username {
  font-size: 16px;
  font-weight: 600;
  color:#b6b6b6;
}
.fa-calendar-o{ font-weight: bolder;
margin-right: 5px;
}
.date{
color: #b6b6b6;
}
.ckeditor-list-style ul{
  list-style: none !important;

}
.ckeditor-list-style ul li:before{
  content: "\f192" !important;
  font-family: FontAwesome !important;
  display: inline-block;
  font-size: 20px;
  color: #339999;
  margin-left: -20px;
  margin-right: 5px;
  width: 20px;
}

table {
  width: 100%;
  border-collapse: collapse;
}
/* Zebra striping */
tr:nth-of-type(odd) {
  background: #eee;
}
th {
  background: #333;
  color: white;
  font-weight: bold;
}
td, th {
  padding: 6px;
  border: 1px solid #ccc;
  text-align: left;
}
/*
Max width before this PARTICULAR table gets nasty
This query will take effect for any screen smaller than 760px
and also iPads specifically.
*/
@media
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {

  /* Force table to not be like tables anymore */
  table, thead, tbody, th, td, tr {
    display: block;
  }

  /* Hide table headers (but not display: none;, for accessibility) */
  thead tr {
    position: absolute;
    top: -9999px;
    left: -9999px;
  }

  tr { border: 1px solid #ccc; }

  td {
    /* Behave  like a "row" */
    border: none;
    border-bottom: 1px solid #eee;
    position: relative;
    padding-left: 5%;
  }
.name{text-align: center;
font-weight: bold;}
  td:before {
    /* Now like a table header */
    position: absolute;
    /* Top/left values mimic padding */
    top: 6px;
    left: 6px;
    width: 45%;
    padding-right: 10px;
    white-space: nowrap;
  }


/*student record*/
/*  #student-record td:nth-of-type(1):before { content: "Company Name :";  font-weight: bolder;}
  #student-record td:nth-of-type(2):before { content: "Job Description :"; font-weight: bolder;}
  #student-record td:nth-of-type(3):before { content: "Mock Test :"; font-weight: bolder;}
  #student-record td:nth-of-type(4):before { content: "Apply :"; font-weight: bolder;}*/
}


  .advertisement {
    position:relative;
    overflow:hidden;
}
    .caption {
    position:absolute;
    top:0;
    right:0;
    background:rgba(66, 139, 202, 0.75);
    width:100%;
    height:100%;
    padding:2%;
    display: none;
    text-align:center;
    color:#fff !important;
    z-index:2;
}
.caption p{ margin-top: 35%;}
@media(min-width: 548px) and(max-width: 768px)
{
  .caption p{ margin-top: 10%;}
}
.caption a{ font-weight: bolder; }
.add-link{font-weight: bolder;}

.ellipsed {
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}
 .sponsored a, .create-add a{color:#A9A9A9;
    font-weight: bolder;
  }
ul.table_list{ margin-left: -10px; }
@media(max-width: 768px){
ul.table_list{ margin-left: -30px; }
}
  .modal-header h2{font-size: 15px; font-weight: bold; color:#e91e63; }
</style>
@stop
@section('header-js')
  @include('layouts.home-js')
    <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
@stop
@section('content')
	@include('header.header_menu')
  <section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single">
      <div class="vchip-background-img">
        <figure>
          <img src="{{asset('images/placement-bg.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Placement" />
        </figure>
      </div>
      <div class="vchip-background-content">
          <h2 class="animated bounceInLeft">Digital Education</h2>
        </div>
    </div>
  </section>
<!-- Start course section -->
<section>
  <div class="container ">
    <div class="row">
      <div class="col-sm-9 col-sm-push-3 data" id="placement-box">
         <div class="portlet box grey-cascade" style="border: 1px solid grey;">
            <div class="portlet-body">
              <div class="tabbable">
                <div class="tab-content">
                  <div id="tab_4" class="">
                     <div class="panel panel-default">
                        <div class="panel-body" style="padding: 0px;">
                          <table  class="" id="student-record">
                            <thead>
                              <tr>
                                <th>Company Name</th>
                                <th>Job Description</th>
                                <th>Mock Test</th>
                                <th>Apply</th>
                              </tr>
                            </thead>
                            <tbody>
                              @if(count($applyJobs) > 0)
                                @foreach($applyJobs as $applyJob)
                                  <tr>
                                    <td class="name"><b>{{ $applyJob->company }}</b></td>
                                    <td> {!! mb_strimwidth( $applyJob->job_description , 0, 400, '...') !!}</br>
                                         <a type="button" class="btn btn-info btn-circle btn-xs" title="Read" data-toggle="modal" data-placement="bottom" href="#company_{{$applyJob->id }}">Read More</a>
                                    </td>
                                    <td>
                                      <a class="btn btn-primary btn-xs delet-bt delet-btn" href="{{ $applyJob->mock_test }}">Mock Test</a>
                                    </td>
                                    <td>
                                     <a class="btn btn-primary btn-xs delet-bt delet-btn" href="{{ $applyJob->job_url }}" target="_blank">Apply</a>
                                    </td>
                                  </tr>
                                @endforeach
                              @else
                                <tr><td colspan="4">No data available.</td></tr>
                              @endif
                            </tbody>
                          </table>
                        </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
         </div>
      </div>
      @if(count($applyJobs) > 0)
        @foreach($applyJobs as $applyJob)
          <div id="company_{{ $applyJob->id }}" class="modal fade" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content" style="background-color: white;">
                <div class="modal-header">
                  <button class="close" data-dismiss="modal">×</button>
                  <h2  class="modal-title">{{ $applyJob->company }}</h2>
                </div>
                <div class="modal-body">{!! $applyJob->job_description !!}</div>
              </div>
            </div>
          </div>
        @endforeach
      @endif
      <div class="col-sm-3 col-sm-pull-9">
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
                  <a class="img-course-box" href="http://www.ssgmce.org" target="_blank">
                    <img src="{{ asset('images/logo/ssgmce-logo.jpg') }}" alt="Shri Sant Gajanan Maharaj College of Engineering"  class="img-responsive" />
                  </a>
                  <div class="course-box-content">
                    <h4 class="course-box-title" title="Shri Sant Gajanan Maharaj College of Engineering" data-toggle="tooltip" data-placement="bottom">
                      <a href="http://www.ssgmce.org/" target="_blank">Shri Sant Gajanan Maharaj College of Engineering</a>
                    </h4>
                    <p class="more"> Shri Sant Gajanan Maharaj College of Engineering</p>
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
  <script type="text/javascript" src="{{ asset('js/scrolling-nav.js')}}"></script>
  <script type="text/javascript">
    $( document ).ready(function() {
      $("[rel='tooltip']").tooltip();
      if(window.location.hash){
        $.each($('ul.nav-tabs-lg > li > a'), function(idx, obj){
          if(window.location.hash == $(obj).attr('href')){
            $(obj).attr('aria-expanded', true);
            $(obj).parent().addClass('active');
            $(obj).addClass('active in')
          } else {
            $(obj).attr('aria-expanded', false);
            $(obj).parent().removeClass('active');
            $(obj).removeClass('active in')
          }
        });
      }
      $('.advertisement').hover(
          function(){
              $(this).find('.caption').slideDown(250); //.fadeIn(250)
          },
          function(){
              $(this).find('.caption').slideUp(250); //.fadeOut(205)
          }
      );
      showMore();
    });
  </script>
<script type="text/javascript">
  function showMore(){
     var showChar = 400;
      var ellipsestext = "...";
      var moretext = "Read more";
      var lesstext = "less";
      $('.more').each(function() {
        var content = $(this).html();

        if(content.length > showChar) {

          var c = content.substr(0, showChar);
          var h = content.substr(0, content.length);
          var html = '<div class="zxc">'+ c + '<span style="color:#01bafd; margin-left:5px;">' + ellipsestext+ '</span><br /><a href="" class="morelink" style="color:#01bafd";>' + moretext + '</a></div><div class="zxc1" style="display:none;">'+ h + '<br /><a href="" class="morelink1" style="color:#01bafd";>' + lesstext + '</a></div>';

          $(this).html(html);
        }

      });

      $(".morelink").click(function(){
        $(this).closest('.zxc').toggle();
        $(this).closest('.zxc').siblings('.zxc1').toggle();
        return false;
      });
      $(".morelink1").click(function(){
        $(this).closest('.zxc1').toggle();
        $(this).closest('.zxc1').siblings('.zxc').toggle();
        return false;
      });
  }
</script>
@stop