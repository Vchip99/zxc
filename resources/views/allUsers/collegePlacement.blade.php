@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> User Placement </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-group"></i> Users Info </li>
      <li class="active"> User Placement </li>
    </ol>
  </section>
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
@stop
@section('admin_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <a href="{{ url('admin/collegePlacement')}}" class="btn btn-primary" >College Placement</a>&nbsp;
            <a href="{{ url('admin/vchipPlacement')}}" class="btn btn-default">Vchip Placement</a>
          </div>
          <br>
          <div class="row">
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="college" name="college" onChange="showDepartments();">
                <option value="0"> Select College </option>
                @if(count($colleges) > 0)
                  @foreach($colleges as $college)
                    @if($selectedCollege == $college->id)
                      <option value="{{$college->id}}" selected="true">{{$college->name}}</option>
                    @else
                      <option value="{{$college->id}}">{{$college->name}}</option>
                    @endif
                  @endforeach
                @endif
              </select>
            </div>
            <div class="col-md-3 mrgn_10_btm" id="dept">
              <select class="form-control" id="selected_dept" name="departemnt" onChange="resetYear();">
                <option value="0"> Select Departemnt </option>
                <option value="All" @if('All' == $selectedDept) selected="true" @endif> All </option>
                @if(count($collegeDepts) > 0)
                  @foreach($collegeDepts as $collegeDept)
                    @if($selectedDept == $collegeDept->id)
                      <option value="{{$collegeDept->id}}" selected="true"> {{$collegeDept->name}} </option>
                    @else
                      <option value="{{$collegeDept->id}}"> {{$collegeDept->name}} </option>
                    @endif
                  @endforeach
                @endif
              </select>
            </div>
            <div class="col-md-3 mrgn_10_btm" id="showYears">
              <select class="form-control" id="selected_year" name="year" onChange="showStudents(this.value);">
                <option value="0"> Select Year </option>
                  <option value="All" @if('All' == $selectedYear) selected="true" @endif> All </option>
                  <option value="1" @if('1' == $selectedYear) selected="true" @endif >First Year</option>
                  <option value="2" @if('2' == $selectedYear) selected="true" @endif >Second Year</option>
                  <option value="3" @if('3' == $selectedYear) selected="true" @endif >Third Year</option>
                  <option value="4" @if('4' == $selectedYear) selected="true" @endif >Fourth Year</option>
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
  }

  function searchStudent(){
    var student = document.getElementById('student').value;
    var college = document.getElementById('college').value;
    var selected_dept = document.getElementById('selected_dept').value;
    var selected_year = document.getElementById('selected_year').value;
    document.getElementById('allUsers').innerHTML = '';
    if(college && selected_dept && selected_year && student.length > 0){
      $.ajax({
          method: "POST",
          url: "{{url('admin/searchCollegeStudentByCollegeByDeptByYearByName')}}",
          data: {college_id:college,department:selected_dept,year:selected_year,student:student}
      })
      .done(function( msg ) {
        renderData(msg);
      });
    } else {
      showStudents();
    }
  }

  function showStudents(){
    var college = document.getElementById('college').value;
    var selected_dept = document.getElementById('selected_dept').value;
    var selected_year = document.getElementById('selected_year').value;
    $.ajax({
      method: "POST",
      url: "{{url('admin/showCollegePlacementVideoByCollegeIdByDeptIdByYear')}}",
      data:{college_id:college,department:selected_dept,year:selected_year}
    })
    .done(function( msg ) {
      renderData(msg);
    });
  }

  function showDepartments(){
    var college = document.getElementById('college').value;
    document.getElementById('dept').classList.remove('hide');
    document.getElementById('showYears').classList.remove('hide');

    document.getElementById('selected_dept').value = 0;
    document.getElementById('selected_year').value = 0;

    if(college > 0){
      $.ajax({
        method: "POST",
        url: "{{url('admin/getDepartments')}}",
        data:{college:college}
      })
      .done(function( msg ) {
        select = document.getElementById('selected_dept');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '0';
        opt.innerHTML = 'Select Department';
        select.appendChild(opt);
        var optAll = document.createElement('option');
        optAll.value = 'All';
        optAll.innerHTML = 'All';
        select.appendChild(optAll);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
      });
    } else {
      document.getElementById('dept').classList.add('hide');
      document.getElementById('showYears').classList.add('hide');
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
    var iframe = div.getElementsByTagName("iframe")[0].contentWindow;
    func = state == 'hide' ? 'pauseVideo' : 'playVideo';
    iframe.postMessage('{"event":"command","func":"' + func + '","args":""}','*');
  }

  window.onclick = function(event) {
    var modelId = $(event.target).attr('id');
    if('student_video' == modelId){
      toggleVideo('hide');
    }
  }
</script>
@stop