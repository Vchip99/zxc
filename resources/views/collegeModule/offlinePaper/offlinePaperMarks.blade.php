@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Offline Exam  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Offline Exam </li>
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
                    <select class="form-control" id="subject" name="subject" required title="Subject" onChange="selectPapers(this);">
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
                    <select class="form-control" name="paper" id="paper" onChange="selectCollegeStudent(this);">
                        <option value="">Select Paper</option>
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

  function selectPapers(ele){
    var subjectId = parseInt($(ele).val());
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    if(subjectId){
      $.ajax({
          method:'POST',
          url: "{{url('getCollegeOfflinePapersBySubjectId')}}",
          data:{_token:currentToken,subject_id:subjectId}
      }).done(function( msg ) {
        select = document.getElementById('paper');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Paper';
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
  function selectCollegeStudent(ele){
    var paperId = parseInt($(ele).val());
    var subjectId = document.getElementById('subject').value;
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    if(paperId && subjectId){
      $.ajax({
          method:'POST',
          url: "{{url('getCollegeStudentsAndMarksBySubjectIdByPaperId')}}",
          data:{_token:currentToken,subject_id:subjectId,paper_id:paperId}
      }).done(function( result ) {
          var users = document.getElementById('college_offline_paper_marks');
          users.innerHTML = '';
          if(result['collegeStudents'].length){
            $.each(result['collegeStudents'], function(idx, obj) {
              if(result['studentMarks'][obj.id]){
                users.innerHTML +='<tr class="student" style="overflow: auto;" id="div_student_'+obj.id+'" ><td>'+(idx + 1)+'</td><td>'+obj.name+'</td><td><input type="number" name="'+obj.id+'" id="student_'+obj.id+'" value="'+result['studentMarks'][obj.id].marks+'" max="'+result['studentMarks'][obj.id].total_marks+'" step="any"></td><td><input type="text" name="total_marks" value="'+result['studentMarks'][obj.id].total_marks+'" readonly></td></tr>';
              } else {
                users.innerHTML +='<tr class="student" style="overflow: auto;" id="div_student_'+obj.id+'" ><td>'+(idx + 1)+'</td><td>'+obj.name+'</td><td><input type="number" name="'+obj.id+'" id="student_'+obj.id+'" value="" max="'+obj.marks+'" step="any"></td><td><input type="text" name="total_marks" value="'+obj.marks+'" readonly></td></tr>';
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