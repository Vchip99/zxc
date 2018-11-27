@extends('dashboard.dashboard')
@section('dashboard_header')
  <style type="text/css">
    .btn-primary{
      width: 120px;
    }
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> Lecturer Courses </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-group"></i> Users Info</li>
      <li class="active">Lecturer Courses </li>
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
    <div class="">
      <div class="top mrgn_20_btm">
        <div class="container">
          <div class="row">
            <div class="">
              <input type="hidden" id="login_User_Type" name="login_User_Type" value="{{Auth::user()->user_type}}">
              @if(5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
                <div class="col-md-3 mrgn_10_btm">
                  <select class="form-control" id="dept" onChange="resetLecturers();">
                    <option value="0"> Select Department </option>
                    @if(count($collegeDepts) > 0)
                      @foreach($collegeDepts as $collegeDept)
                        @if(is_object($selectedUser) && $selectedUser->college_dept_id == $collegeDept->id)
                          <option value="{{$collegeDept->id}}" selected="true">{{$collegeDept->name}}</option>
                        @else
                          <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                        @endif
                      @endforeach
                    @endif
                  </select>
                </div>
              @endif
              @if(4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
              <div class="col-md-3 mrgn_10_btm" id="showUsers">
                <select class="form-control" id="user" name="user_type" onChange="showStudents();" required="true">
                  <option value="0">Select User Type</option>
                  <option value="3" @if(is_object($selectedUser) && '3' == $selectedUser->user_type) selected="true" @endif >Lecturer</option>
                  @if(5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
                    <option value="4" @if(is_object($selectedUser) && '4' == $selectedUser->user_type) selected="true" @endif >Hod</option>
                  @endif
                </select>
              </div>
              @endif
              <div class="col-md-3 ">
                <select class="form-control" id="lecturer" onChange="showResult(this);">
                  <option value="0">Select User </option>
                  @if(count($users) > 0)
                    @foreach($users as $user)
                        @if(is_object($selectedUser) && $selectedUser->id == $user->id)
                          <option value="{{$user->id}}" selected="true">{{$user->name}}</option>
                        @else
                          <option value="{{$user->id}}">{{$user->name}}</option>
                        @endif
                    @endforeach
                  @endif
                </select>
              </div>
              <a href="{{ url('college/'.Session::get('college_user_url').'/lecturerPapers')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title=" Test Result"><i class="fa fa-files-o"></i></a>&nbsp;
              <a href="{{ url('college/'.Session::get('college_user_url').'/lecturerCourses')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="Courses"><i class="fa fa-dashboard"></i></a>&nbsp;
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
                Courses
              </div>
              <div class="panel-body">
                <table  class="" id="dataTables-example">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Category</th>
                      <th>Sub Category</th>
                      <th>Courses</th>
                    </tr>
                  </thead>
                  <tbody  id="course-result">
                  @if(count($results) > 0)
                    @foreach($results as $index => $result)
                      <tr class="" style="overflow: auto;">
                        <td>{{$index + 1}}</td>
                        <td>{{$result->category}}</td>
                        <td>{{$result->subcategory}}</td>
                        <td>{{$result->course}}</td>
                      </tr>
                    @endforeach
                  @elseif(is_object($selectedUser) && 0 == count($results))
                    <tr class="">
                      <td colspan="5">No courses are created by selected user.</td>
                    </tr>
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
<script type="text/javascript">

  function showStudents(){
    if(document.getElementById('user')){
      var user_type = parseInt(document.getElementById('user').value);
    } else {
      var login_User_Type = parseInt(document.getElementById('login_User_Type').value);
      if(4 == login_User_Type){
        var user_type = 3;
      }
    }
    if(document.getElementById("dept")){
        var department = parseInt(document.getElementById("dept").value);
    } else {
        var department = 0;
    }
    document.getElementById('course-result').innerHTML = '';
    if(user_type > 0){
      $.ajax({
        method: "POST",
        url: "{{url('showStudentsByUserType')}}",
        data: {user_type:user_type,department:department}
      })
      .done(function( msg ) {
        select = document.getElementById('lecturer');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '0';
        opt.innerHTML = 'Select User';
        select.appendChild(opt);
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
      select = document.getElementById('lecturer');
      select.innerHTML = '';
      var opt = document.createElement('option');
      opt.value = '0';
      opt.innerHTML = 'Select User';
      select.appendChild(opt);
    }
  }

  function showResult(ele){
    var lecturer = parseInt(document.getElementById('lecturer').value);
    if(lecturer > 0){
      renderResult(lecturer);
    } else {
      body = document.getElementById('course-result');
      body.innerHTML = '';
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = 'Select user.';
      eleIndex.setAttribute('colspan' ,4);
      eleTr.appendChild(eleIndex);
      body.appendChild(eleTr);
    }
  }

  function renderResult(lecturer){
     $.ajax({
          method: "POST",
          url: "{{url('getLecturerCourses')}}",
        data: {lecturer:lecturer}
      })
      .done(function( msg ) {
        body = document.getElementById('course-result');
        body.innerHTML = '';
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
            var eleTr = document.createElement('tr');
            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = idx + 1;
            eleTr.appendChild(eleIndex);

            var eleCategory = document.createElement('td');
            eleCategory.innerHTML = obj.category;
            eleTr.appendChild(eleCategory);

            var eleSubCategory = document.createElement('td');
            eleSubCategory.innerHTML = obj.subcategory;
            eleTr.appendChild(eleSubCategory);

            var eleCourse = document.createElement('td');
            eleCourse.innerHTML = obj.course;
            eleTr.appendChild(eleCourse);

            body.appendChild(eleTr);
          });
        } else {
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = 'No courses are created by selected user.';
          eleIndex.setAttribute('colspan' ,4);
          eleTr.appendChild(eleIndex);
          body.appendChild(eleTr);
        }
      });
  }
  function selectSubcategory(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
              method: "POST",
              url: "{{url('getCollegeCourseSubCategories')}}",
              data: {id:id}
          })
          .done(function( msg ) {
            select = document.getElementById('subcategory');
            select.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '';
            opt.innerHTML = 'Select Sub Category';
            select.appendChild(opt);
            var allOpt = document.createElement('option');
            allOpt.value = '0';
            allOpt.innerHTML = 'All';
            select.appendChild(allOpt);
            if( 0 < msg.length){
              $.each(msg, function(idx, obj) {
                  var opt = document.createElement('option');
                  opt.value = obj.id;
                  opt.innerHTML = obj.name;
                  select.appendChild(opt);
              });
            }
      });
    }
  }
  function resetUser(){
    select = document.getElementById('student');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '0';
    opt.innerHTML = 'Select User';
    select.appendChild(opt);
  }
</script>
@stop