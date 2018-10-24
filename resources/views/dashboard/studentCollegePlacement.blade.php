@extends('dashboard.dashboard')
<style>
    .memberinfotop{
      margin-top: 100px;
    }
    .memberinfo{
      margin:10px;
    }
    /*.image{
      height:150px;
      width:150px;
    }*/
    .topcontent{
      padding-top:20px;
    }
    .content{
      /*padding-top: 20px;*/
    }

    .button{
      float:right;

    }
    .button1{
      float:left;
    }
    @media only screen and (max-width: 418px){
      body{
        font-size: 13px;
      }
    }
    @media only screen and (max-width: 386px){
      body{
        font-size: 12px;
      }
    }
    @media only screen and (max-width: 375px){
      body{
        font-size: 11px;
      }
    }

    @media (max-width: 1190px) {
      .navbar-header {
          float: none;
      }
      .navbar-left,.navbar-right {
          float: none !important;
      }
      .navbar-toggle {
          display: block;
      }
      .navbar-collapse {
          border-top: 1px solid transparent;
          box-shadow: inset 0 1px 0 rgba(255,255,255,0.1);
          min-height: 410px;
      }
      .navbar-fixed-top {
          top: 0;
          border-width: 0 0 1px;
      }
      .navbar-collapse.collapse {
          display: none!important;
      }
      .navbar-nav {
          float: none!important;
          margin-top: 7.5px;
      }
      .navbar-nav>li {
          float: none;
      }
      .navbar-nav>li>a {
          padding-top: 10px;
          padding-bottom: 10px;
      }
      .collapse.in{
          display:block !important;
      }
    }
    .iframe-container iframe{
      width: 100% !important;
    }
    .vid {position: relative; padding-bottom: 56.25%; padding-top: 30px; height: 0; overflow: hidden; }
    .vid iframe, .vid object,.vid embed {position: absolute; top: 0; left: 0; width: 100%; height: 100%;}

</style>
@section('module_title')
  <style>
    .memberinfotop{
      margin-top: 100px;
    }
    .memberinfo{
      margin:10px;
    }
    /*.image{
      height:150px;
      width:150px;
    }*/
    .topcontent{
      padding-top:20px;
    }
    .content{
      /*padding-top: 20px;*/
    }

    .button{
      float:right;

    }
    .button1{
      float:left;
    }
    @media only screen and (max-width: 418px){
      body{
        font-size: 13px;
      }
    }
    @media only screen and (max-width: 386px){
      body{
        font-size: 12px;
      }
    }
    @media only screen and (max-width: 375px){
      body{
        font-size: 11px;
      }
    }

    @media (max-width: 1190px) {
      .navbar-header {
          float: none;
      }
      .navbar-left,.navbar-right {
          float: none !important;
      }
      .navbar-toggle {
          display: block;
      }
      .navbar-collapse {
          border-top: 1px solid transparent;
          box-shadow: inset 0 1px 0 rgba(255,255,255,0.1);
          min-height: 410px;
      }
      .navbar-fixed-top {
          top: 0;
          border-width: 0 0 1px;
      }
      .navbar-collapse.collapse {
          display: none!important;
      }
      .navbar-nav {
          float: none!important;
          margin-top: 7.5px;
      }
      .navbar-nav>li {
          float: none;
      }
      .navbar-nav>li>a {
          padding-top: 10px;
          padding-bottom: 10px;
      }
      .collapse.in{
          display:block !important;
      }
    }
    .iframe-container iframe{
      width: 100% !important;
    }
    .vid {position: relative; padding-bottom: 56.25%; padding-top: 30px; height: 0; overflow: hidden; }
    .vid iframe, .vid object,.vid embed {position: absolute; top: 0; left: 0; width: 100%; height: 100%;}
    .btn-primary{
      width: 150px;
    }
  </style>
  <section class="content-header">
    <h1> Placement </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Users Info</li>
      <li class="active">Placement </li>
    </ol>
  </section>
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div id="placement" class="">
      <div class="top mrgn_40_btm"">
        <div class="container">
          <div class="row">
            <a href="{{ url('college/'.Session::get('college_user_url').'/studentCollegePlacement')}}" class="btn btn-primary" >College Placement</a>&nbsp;
            <a href="{{ url('college/'.Session::get('college_user_url').'/studentVchipPlacement')}}" class="btn btn-default">Vchip Placement</a>
          </div>
          <br>
          <div class="row">
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="dept" onChange="resetYear(this);">
                <option value="0"> Select Department </option>
                <option value="All"> All </option>
                @if($department > 0 && count($collegeDepts) > 0)
                  @foreach($collegeDepts as $collegeDept)
                    @if($department == $collegeDept->id)
                      <option value="{{$collegeDept->id}}" selected="true">{{$collegeDept->name}}</option>
                    @else
                      <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                    @endif
                  @endforeach
                @else
                  @foreach($collegeDepts as $collegeDept)
                      <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="selected_year" name="year" onChange="showStudents(this);">
                <option value="0"> Select Year </option>
                <option value="All"> All </option>
                <option value="1" @if('1' == $year) selected="true" @endif >First Year</option>
                <option value="2" @if('2' == $year) selected="true" @endif >Second Year</option>
                <option value="3" @if('3' == $year) selected="true" @endif >Third Year</option>
                <option value="4" @if('4' == $year) selected="true" @endif >Fourth Year</option>
              </select>
            </div>
            <div class="col-md-3 ">
              <input class="form-control" type="text" name="student" id="student" placeholder="Search Student" onkeyup="searchStudent();">
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="" id="allUsers">
          @if(count($students) > 0)
            @foreach($students as $student)
            <div style="border:1px solid black;">
              <div class="row memberinfo" >
                <div class="col-md-5 ">
                  <div class="vid">
                    @if(!empty($student->recorded_video))
                      {!! $student->recorded_video !!}
                    @else
                      No Video
                    @endif
                  </div>
                </div>
                <div class="col-md-7">
                  @php
                    if(!empty($student->skills)){
                      $skillArr = explode(',',$student->skills);
                    }else{
                      $skillArr = [];
                    }
                  @endphp
                  <h4><strong>{{ $student->name }}</strong></h4>
                  <p><strong>Education:</strong>BE</p>
                  <p><strong>Skills:</strong>
                    @if(count($skillArr) > 0)
                      @foreach($skillArr as $skillId)
                        #{{$userSkills[$skillId]}}
                      @endforeach
                    @endif
                  </p>
                  @if(!empty($student->resume) && is_file($student->resume))
                  <div style="padding-left: 10px;"><a href="{{asset($student->resume)}}" download><button type="button"  class="btn btn-success ">Resume <i class="fa fa-download"></i></button></a></div>
                  @endif
                </div>
              </div>
            </div>
            <br>
            @endforeach
          @else
            No Data
          @endif
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">
  function resetYear(){
    document.getElementById('selected_year').value = 0;
    document.getElementById('student').value = '';
    document.getElementById('allUsers').innerHTML = '';
  }

  function searchStudent(){
    var student = document.getElementById('student').value;
    var year = parseInt(document.getElementById('selected_year').value);
    var department = parseInt(document.getElementById("dept").value);
    document.getElementById('allUsers').innerHTML = '';
    if(department > 0 && year > 0 && student.length > 0){
      $.ajax({
          method: "POST",
          url: "{{url('searchStudentByDeptByYearByName')}}",
          data: {year:year,department:department,student:student}
      })
      .done(function( msg ) {
        renderData(msg);
      });
    } else {
      showStudents();
    }
  }

  function showStudents(){
    var year = document.getElementById('selected_year').value;
    var department = document.getElementById("dept").value;
    document.getElementById('allUsers').innerHTML = '';
    if(department&& year){
      $.ajax({
          method: "POST",
          url: "{{url('showPlacementVideoByDepartmentByYear')}}",
          data: {year:year,department:department}
      })
      .done(function( msg ) {
        renderData(msg);
      });
    }
  }

  function renderData(msg){
    divUsers = document.getElementById('allUsers');
    divUsers.innerHTML = '';
    if(Object.keys(msg['users']).length) {
      $.each(msg['users'], function(id, userData) {
          var firstDiv = document.createElement('div');
          firstDiv.setAttribute('style', 'border:1px solid black;');

          var secondDiv = document.createElement('div');
          secondDiv.className = 'row memberinfo';

          var thirdDiv = document.createElement('div');
          thirdDiv.className = 'col-md-5';
          if(userData.recorded_video){
            thirdDiv.innerHTML = '<div class="vid">'+userData.recorded_video+'</div>';
          } else {
            thirdDiv.innerHTML = '<div class="vid">No Video</div>';
          }
          secondDiv.appendChild(thirdDiv);

          var fourthDiv = document.createElement('div');
          fourthDiv.className = 'col-md-7 topcontent';
          fourthDivInnerHtml = '';
          fourthDivInnerHtml += '<h4><strong>'+userData.name+'</strong></h4><p><strong>Education:</strong> BE</p>';
          if(userData.skills){
            var studenSkills = userData.skills.split(',');
            if(studenSkills.length){
              var skillStr = '';
              $.each(studenSkills,function(idx, skillId){
                if(msg['skills'][skillId]){
                  if(0 == idx){
                    skillStr = ' #'+msg['skills'][skillId];
                  } else {
                    skillStr += '#'+msg['skills'][skillId];
                  }
                }
              });
              fourthDivInnerHtml += '<p><strong>Skills:</strong>'+skillStr+'</p>';
            }
          }
          if(userData.resume){
            fourthDivInnerHtml += '<div style="padding-left: 30px;"><a href="'+ userData.resume +'" download><button type="button"  class="btn btn-success ">Resume <i class="fa fa-download"></i></button></a></div>';
          }
          fourthDiv.innerHTML = fourthDivInnerHtml;
          secondDiv.appendChild(fourthDiv);
          firstDiv.appendChild(secondDiv);
          divUsers.appendChild(firstDiv);
          var brEle = document.createElement('br');
          divUsers.appendChild(brEle);
      });
    } else {
      divUsers.innerHTML = 'No Result!';
    }
  }

  function toggleVideo(state) {
    // if state == 'hide', hide. Else: show video
    var div = document.getElementById("iframe-video");
    if(div.getElementsByTagName("iframe").length > 0){
      var iframe = div.getElementsByTagName("iframe")[0].contentWindow;
      func = state == 'hide' ? 'pauseVideo' : 'playVideo';
      iframe.postMessage('{"event":"command","func":"' + func + '","args":""}','*');
    }
  }
  // Get the modal
  var modal = document.getElementById("student_video");
  window.onclick = function(event) {
    if(event.target == modal) {
      toggleVideo('hide');
    }
  }
</script>
@stop
