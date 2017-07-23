@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Courses </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Students Dashboard</li>
      <li class="active">Courses </li>
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
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="">
              @if(5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
                <div class="col-md-3 mrgn_10_btm">
                  <select class="form-control" id="dept" onChange="resetYear(this);">
                    <option > Select Department </option>
                    @if(count($collegeDepts) > 0)
                      @foreach($collegeDepts as $collegeDept)
                        @if(is_object($selectedStudent) && $selectedStudent->college_dept_id == $collegeDept->id)
                          <option value="{{$collegeDept->id}}" selected="true">{{$collegeDept->name}}</option>
                        @else
                          <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                        @endif
                      @endforeach
                    @endif
                  </select>
                </div>
              @endif
              <div class="col-md-3 mrgn_10_btm">
                <select class="form-control" id="selected_year" name="year" onChange="showStudents(this);">
                  <option value="0"> Select Year </option>
                  <option value="1" @if(is_object($selectedStudent) &&'1' == $selectedStudent->year) selected="true" @endif >First Year</option>
                  <option value="2" @if(is_object($selectedStudent) &&'2' == $selectedStudent->year) selected="true" @endif >Second Year</option>
                  <option value="3" @if(is_object($selectedStudent) &&'3' == $selectedStudent->year) selected="true" @endif >Third Year</option>
                  <option value="4" @if(is_object($selectedStudent) &&'4' == $selectedStudent->year) selected="true" @endif >Fourth Year</option>
                </select>
              </div>
              <div class="col-md-3 ">
                <select class="form-control" id="student" onChange="showResult(this);">
                  <option value="0">Select Student </option>
                  @if(is_object($selectedStudent) && count($students) > 0)
                    @foreach($students as $student)
                      @if(is_object($selectedStudent) && $selectedStudent->id == $student->id)
                        <option value="{{$student->id}}" selected="true">{{$student->name}}</option>
                      @else
                        <option value="{{$student->id}}">{{$student->name}}</option>
                      @endif
                    @endforeach
                  @endif
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="mrgn_20_btm">
              <div class="col-md-3 mrgn_10_btm">
               <select class="form-control" id="category" id="category" name="category" title="Category" onChange="selectSubcategory(this);">
                <option value="0">Select Category</option>
                @if(count($categories) > 0)
                  @foreach($categories as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                  @endforeach
                @endif
               </select>
              </div>
              <div class="col-md-3 ">
               <select class="form-control" id="subcategory" name="subcategory" title="Sub Category" onChange="showResult(this);">
                <option value="0">Select Sub Category</option>
               </select>
              </div>
          </div>
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
                      <th>Courses</th>
                      <th>Grade</th>
                      <th>Certificate</th>
                    </tr>
                  </thead>
                  <tbody  id="course-result">
                  @if(is_object($selectedStudent) && count($courses) > 0)
                    @foreach($courses as $index => $course)
                      <tr class="">
                        <td>{{ $index + 1 }}</td>
                        <td>{{$course->name}}</td>
                        @if(!empty($course->grade))
                          <td>{{$course->grade}}</td>
                        @else
                          <td>Certificate exam is not given.</td>
                        @endif
                        <td class="center">Certified</td>
                      </tr>
                    @endforeach
                  @elseif(is_object($selectedStudent) && 0 == count($courses))
                    <tr class="">
                      <td colspan="5">No courses are registered for selected user.</td>
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
  function resetYear(){
    document.getElementById('selected_year').value = 0;
    document.getElementById('student').value = 0;
    document.getElementById('category').value = 0;
    document.getElementById('subcategory').value = 0;
    document.getElementById('course-result').innerHTML = '';
  }

  function showStudents(){
    var year = parseInt(document.getElementById('selected_year').value);
    if(document.getElementById("dept")){
        var department = parseInt(document.getElementById("dept").value);
    } else {
        var department = 0;
    }
    document.getElementById('category').value = 0;
    document.getElementById('subcategory').value = 0;
    document.getElementById('course-result').innerHTML = '';
    if(year > 0){
      $.ajax({
            method: "POST",
            url: "{{url('showStudentsByDepartmentByYear')}}",
            data: {year:year,department:department}
        })
        .done(function( msg ) {
          select = document.getElementById('student');
          select.innerHTML = '';
          var opt = document.createElement('option');
          opt.value = '0';
          opt.innerHTML = 'Select Student';
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
    }
  }
  function showResult(ele){
    var student = parseInt(document.getElementById('student').value);
    $.ajax({
        method: "POST",
        url: "{{url('showStudentCourses')}}",
        data: {student:student}
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

          var eleName = document.createElement('td');
          eleName.innerHTML = obj.name;
          eleTr.appendChild(eleName);

          var eleGrade = document.createElement('td');
          if(obj.grade){
            eleGrade.innerHTML = obj.grade;
          } else {
            eleGrade.innerHTML = 'Certificate exam is not given.';
          }
          eleTr.appendChild(eleGrade);

          var eleCertified = document.createElement('td');
          if(1 == obj.certified){
            eleCertified.innerHTML = 'Certified';
          } else {
            eleCertified.innerHTML = 'Non-Certified';
          }
          eleTr.appendChild(eleCertified);
          body.appendChild(eleTr);
        });
      } else {
        var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = 'No courses are registered for selected user.';
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
              url: "{{url('getCourseSubCategories')}}",
              data: {id:id}
          })
          .done(function( msg ) {
            select = document.getElementById('subcategory');
            select.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '0';
            opt.innerHTML = 'Select Sub Category';
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
    }
  }

</script>
@stop