@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> User Courses </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> All Users </li>
      <li class="active"> User Courses </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="col-md-3 mrgn_10_btm" id="student">
              <select class="form-control" id="selected_student" name="student" onChange="showResult();">
                <option value="0"> Select User </option>
                 @if(count($students) > 0)
                  @foreach($students as $student)
                    @if(is_object($selectedStudent) && $selectedStudent->id == $student->id)
                      <option value="{{$student->id}}" selected="true"> {{$student->name}} </option>
                    @else
                      <option value="{{$student->id}}"> {{$student->name}} </option>
                    @endif
                  @endforeach
                @endif
              </select>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
                User Result
              </div>
              <div class="panel-body">
                <table>
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Courses</th>
                      <th>Grade</th>
                      <th>Certificate</th>
                    </tr>
                  </thead>
                  <tbody id="course-result">
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

  function showResult(ele){
    var studentId = parseInt(document.getElementById('selected_student').value);
    $.ajax({
            method: "POST",
            url: "{{url('showUserCourses')}}",
            data: {student_id:studentId}
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
          eleIndex.setAttribute('colspan', 4);
          eleTr.appendChild(eleIndex);
          body.appendChild(eleTr);
        }
    });
  }
  function showStudents(){
    var courseId = document.getElementById('course').value;
    document.getElementById('selected_student').value = 0;
    document.getElementById('course-result').innerHTML = '';
    if(courseId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('searchUsers')}}",
        data:{course_id:courseId}
      })
      .done(function( msg ) {
        select = document.getElementById('selected_student');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '0';
        opt.innerHTML = 'Select User';
        select.appendChild(opt);
        if( 0 < msg['users'].length){
          $.each(msg['users'], function(idx, obj) {
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