@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Update Offline Marks  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Update Offline Marks </li>
    </ol>
  </section>
  <style type="text/css">
    .container{
      padding-left: 0px !important;
      padding-right: 0px !important;
    }
  </style>
@stop
@section('dashboard_content')
  <div class="container ">
    @if(count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
   <form action="{{url('college/'.Session::get('college_user_url').'/assignCollegeOfflinePaperMarks')}}" method="POST">
    {{ csrf_field() }}
      <div class="row">
        <b>Note: For absent student, keep mark as empty.</b>
      </div><br/>
      <div class="row">
        <div class="form-group">
            <div class="col-md-3">
                <div style="margin-bottom: 10px">
                    <select class="form-control" id="subject" name="subject" required title="Subject" onChange="selectDepartment(this);">
                      <option value="">Select Subject</option>
                      @if(count($subjects) > 0)
                        @foreach($subjects as $subject)
                            <option value="{{$subject->id}}">{{$subject->name}}</option>
                        @endforeach
                      @endif
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div style="margin-bottom: 10px">
                  <select class="form-control" id="department" name="department" required title="Department">
                    <option value="">Select Department</option>
                  </select>
                </div>
            </div>
            <div class="col-md-3">
                <div style="margin-bottom: 10px">
                  <select class="form-control" id="year" name="year" required title="Year" onChange="selectPapers(this);">
                    <option value="">Select Year</option>
                  </select>
                </div>
            </div>
            <div class="col-md-3">
                <div style="margin-bottom: 10px">
                    <select class="form-control" name="topic" id="topic" onChange="selectCollegeStudent(this);">
                        <option value="">Select Topic</option>
                    </select>
                </div>
            </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12" id="all-result">
          <div class="panel panel-info">
            <div class="panel-heading text-center">
              <span class="">Students</span>
            </div>
            <div class="panel-body">
              <table  class="" id="">
                <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Mark</th>
                    <th>OutOff</th>
                  </tr>
                </thead>
                <tbody id="college_offline_paper_marks" class="">
                </tbody>
              </table>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12">
              <button type="submit" class="btn btn-primary" style="float: right;width: 90px !important;">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
<script type="text/javascript">
  function selectDepartment(ele){
    var subject = $(ele).val();
    if(subject){
      $.ajax({
        method: "POST",
        url: "{{url('getCollegeDepartmentsBySubjectId')}}",
        data: {subject_id:subject}
      })
      .done(function( msg ) {
        var users = document.getElementById('college_offline_paper_marks');
        users.innerHTML = '';
        selectDept = document.getElementById('department');
        selectDept.innerHTML = '';
        var optDept = document.createElement('option');
        optDept.value = '';
        optDept.innerHTML = 'Select Department';
        selectDept.appendChild(optDept);
        if( 0 < msg['collegeDepts'].length){
          $.each(msg['collegeDepts'], function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              selectDept.appendChild(opt);
          });
        }

        selectYears = document.getElementById('year');
        selectYears.innerHTML = '';
        var optYears = document.createElement('option');
        optYears.value = '';
        optYears.innerHTML = 'Select Year';
        selectYears.appendChild(optYears);
        if( 0 < msg['years'].length){
          $.each(msg['years'], function(idx, year) {
              var opt = document.createElement('option');
              opt.value = year;
              if(1 == year){
                opt.innerHTML = 'First';
              } else if(2 == year){
                opt.innerHTML = 'Second';
              } else if(3 == year){
                opt.innerHTML = 'Third';
              } else if(4 == year){
                opt.innerHTML = 'Fourth';
              }
              selectYears.appendChild(opt);
          });
        }
      });
    }
  }

  function selectPapers(ele){
    var year = parseInt($(ele).val());
    var subjectId = document.getElementById('subject').value;
    var departmentId = document.getElementById('department').value;
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    if(subjectId){
      $.ajax({
          method:'POST',
          url: "{{url('getCollegeOfflineExamTopicBySubjectIdByDeptByYear')}}",
          data:{_token:currentToken,subject_id:subjectId,department_id:departmentId,year:year}
      }).done(function( msg ) {
        var users = document.getElementById('college_offline_paper_marks');
        users.innerHTML = '';
        select = document.getElementById('topic');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Topic';
        select.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.topic;
              opt.setAttribute('data-marks', obj.marks);
              select.appendChild(opt);
          });
        }
      });
    }
  }
  function selectCollegeStudent(ele){
    var exam = parseInt($(ele).val());
    var subjectId = document.getElementById('subject').value;
    var departmentId = document.getElementById('department').value;
    var year = document.getElementById('year').value;
    var paperMarks = $(ele).find(':selected').data('marks');
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    if(exam && subjectId && departmentId && year){
      $.ajax({
          method:'POST',
          url: "{{url('getCollegeStudentsAndMarksBySubjectIdByDeptByYearByExamId')}}",
          data:{_token:currentToken,subject_id:subjectId,department_id:departmentId,year:year,exam_id:exam}
      }).done(function( result ) {
          var users = document.getElementById('college_offline_paper_marks');
          users.innerHTML = '';
          if(result['collegeStudents'].length){
            $.each(result['collegeStudents'], function(idx, obj) {
              if(result['studentMarks'][obj.id]){
                users.innerHTML +='<tr class="student" style="overflow: auto;" id="div_student_'+obj.id+'" ><td>'+(idx + 1)+'</td><td>'+obj.name+'</td><td><input type="number" name="'+obj.id+'" id="student_'+obj.id+'" value="'+result['studentMarks'][obj.id].marks+'" max="'+result['studentMarks'][obj.id].total_marks+'" step="any" style="width:100%;"></td><td><input type="text" name="total_marks" value="'+result['studentMarks'][obj.id].total_marks+'" readonly style="width:100%;"></td></tr>';
              } else {
                users.innerHTML +='<tr class="student" style="overflow: auto;" id="div_student_'+obj.id+'" ><td>'+(idx + 1)+'</td><td>'+obj.name+'</td><td><input type="number" name="'+obj.id+'" id="student_'+obj.id+'" value="" max="'+paperMarks+'" step="any" style="width:100%;"></td><td><input type="text" name="total_marks" value="'+paperMarks+'" readonly style="width:100%;"></td></tr>';
              }
            });
          } else {
            users.innerHTML = '<tr class="student"><td colspan="4">No Result!</td></tr>';
          }
      });
    }
  }
</script>
@stop