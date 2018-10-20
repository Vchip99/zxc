@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Students Assignment  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Assignment </li>
      <li class="active"> Students Assignment </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="dept" onChange="resetYear(this);">
                <option value=""> Select Department </option>
                @if(count($collegeDepts) > 0)
                  @foreach($collegeDepts as $collegeDept)
                    @if($selectedAssignmentDepartment == $collegeDept->id)
                      <option value="{{$collegeDept->id}}" selected>{{$collegeDept->name}}</option>
                    @else
                      <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                    @endif
                  @endforeach
                @endif
              </select>
            </div>
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="year" name="year" required title="year" onChange="selectSubject(this);">
                <option value="">Select Year</option>
                <option value="1" @if($selectedAssignmentYear > 0 && 1 == $selectedAssignmentYear) selected @endif >First </option>
                <option value="2" @if($selectedAssignmentYear > 0 && 2 == $selectedAssignmentYear) selected @endif >Second </option>
                <option value="3" @if($selectedAssignmentYear > 0 && 3 == $selectedAssignmentYear) selected @endif >Third </option>
                <option value="4" @if($selectedAssignmentYear > 0 && 4 == $selectedAssignmentYear) selected @endif >Fourth </option>
              </select>
            </div>
            <div class="col-md-3 mrgn_10_btm">
             <select class="form-control" id="subject" name="subject" title="Subject" onChange="selectTopic(this);">
              <option value="0">Select Subject</option>
              @if($selectedAssignmentSubject > 0 && count($assignmentSubjects) > 0)
                @foreach($assignmentSubjects as $assignmentSubject)
                  @if($selectedAssignmentSubject == $assignmentSubject->id)
                    <option value="{{ $assignmentSubject->id }}" selected="true">{{ $assignmentSubject->name }}</option>
                  @else
                    <option value="{{ $assignmentSubject->id }}">{{ $assignmentSubject->name }}</option>
                  @endif
                @endforeach
              @endif
             </select>
            </div>
            <div class="col-md-3 mrgn_10_btm">
             <select class="form-control" id="topic" name="topic" title="Topic" onChange="selectStudent(this);">
              <option value="0">Select Topic</option>
              @if($selectedAssignmentTopic > 0 && count($assignmentTopics) > 0)
                @foreach($assignmentTopics as $assignmentTopic)
                  @if($selectedAssignmentTopic == $assignmentTopic->id)
                    <option value="{{ $assignmentTopic->id }}" selected="true">{{ $assignmentTopic->name }}</option>
                  @else
                    <option value="{{ $assignmentTopic->id }}">{{ $assignmentTopic->name }}</option>
                  @endif
                @endforeach
              @endif
             </select>
            </div>
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="student" name="student" title="Student" onChange="getAssignments(this);">
              <option value="">Select Student</option>
              @if($selectedAssignmentStudent > 0 && count($assignmentUsers) > 0)
                @foreach($assignmentUsers as $assignmentUser)
                  @if($selectedAssignmentStudent == $assignmentUser->id)
                    <option value="{{ $assignmentUser->id }}" selected="true">{{ $assignmentUser->name }}</option>
                  @else
                    <option value="{{ $assignmentUser->id }}">{{ $assignmentUser->name }}</option>
                  @endif
                @endforeach
              @endif
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
                Assignments
              </div>
              <div class="panel-body">
                <table  class="" id="studentAssignments">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Assignment</th>
                      <th>Attachment</th>
                      <th>Subject </th>
                      <th>Topic </th>
                      <th>Remark</th>
                    </tr>
                  </thead>
                  <tbody id="studentAssignment" class="">
                  @if(is_object($assignment))
                    <tr style="overflow: auto;">
                      <td>1</td>
                      <td>{!! mb_strimwidth($assignment->question, 0, 400, "...") !!}</td>
                      <td>{!! basename($assignment->attached_link) !!}</td>
                      <td>{{$assignment->subject->name}}</td>
                      <td>{{$assignment->topic->name}}</td>
                      <td>
                        <a href="{{url('college/'.Session::get('college_user_url').'/assignmentRemark')}}/{{$assignment->id}}/{{$selectedAssignmentStudent}}" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Assignment Remark" />
                          </a>
                      </td>
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

    function selectStudent(ele){
      document.getElementById('studentAssignment').innerHTML = '';
      year = document.getElementById('year').value;
      topic = parseInt(document.getElementById('topic').value);
      if(document.getElementById('dept')){
        var department = document.getElementById('dept').value;
      } else {
        var department = 0;
      }
      user_type = 2;
      if( 0 < year && 0 < topic){
        $.ajax({
          method: "POST",
          url: "{{url('searchStudent')}}",
          data: {year:year,user_type:user_type,department:department}
        })
        .done(function( msg ) {
          select = document.getElementById('student');
          select.innerHTML = '';
          var opt = document.createElement('option');
          opt.value = '';
          opt.innerHTML = 'Select Student';
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
      } else {
        renderStudent();
      }
    }

    function selectSubject(ele){
      id = parseInt($(ele).val());
      var department = document.getElementById('dept').value;
      document.getElementById('subject').value = 0;
      document.getElementById('studentAssignment').innerHTML = '';
      renderTopic();
      renderStudent();
      if( 0 < id ){
        // get subjects
        $.ajax({
          method: "POST",
          url: "{{url('getCollegeSubjectByYear')}}",
          data: {year:id,department:department}
        })
        .done(function( msg ) {
          select = document.getElementById('subject');
          select.innerHTML = '';
          var opt = document.createElement('option');
          opt.value = '';
          opt.innerHTML = 'Select Subject';
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
    function selectTopic(ele){
      id = parseInt($(ele).val());
      document.getElementById('studentAssignment').innerHTML = '';
      renderStudent();
      if( 0 < id ){
        $.ajax({
                method: "POST",
                url: "{{url('getAssignmentTopics')}}",
                data: {id:id}
            })
            .done(function( msg ) {
              select = document.getElementById('topic');
              select.innerHTML = '';
              var opt = document.createElement('option');
              opt.value = 0;
              opt.innerHTML = 'Select Topic';
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
        renderTopic();
      }
    }
    function getAssignments(ele){
      id = parseInt($(ele).val());
      topic = parseInt(document.getElementById('topic').value);
      subject = document.getElementById('subject').value;
      year = document.getElementById('year').value;
      if(document.getElementById('dept')){
        var department = document.getElementById('dept').value;
      } else {
        var department = 0;
      }
      if( 0 < id && topic && subject && year){
        $.ajax({
          method: "POST",
          url: "{{url('getAssignmentByTopicForStudent')}}",
          data: {topic:topic, student:id,subject:subject,year:year,department:department}
        })
        .done(function( msg ) {
          body = document.getElementById('studentAssignment');
          body.innerHTML = '';
          studentId = document.getElementById('student').value;
          if(msg['id']){
            var eleTr = document.createElement('tr');
            eleTr.setAttribute("style","overflow: auto;");

            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = 1;
            eleTr.appendChild(eleIndex);

            var text = msg['question'];
            var count = 400;
            var resultString = text.slice(0, count) + (text.length > count ? "..." : "");

            var eleQuestion = document.createElement('td');
            eleQuestion.innerHTML = resultString;
            eleTr.appendChild(eleQuestion);

            var eleAttachment = document.createElement('td');
            eleAttachment.innerHTML = msg['attached_link'];
            eleTr.appendChild(eleAttachment);

            var eleSubject = document.createElement('td');
            eleSubject.innerHTML = msg['subject'];
            eleTr.appendChild(eleSubject);

            var eleTopic = document.createElement('td');
            eleTopic.innerHTML = msg['topic'];
            eleTr.appendChild(eleTopic);

            var url = "{{url('college/'.Session::get('college_user_url').'/assignmentRemark')}}/"+ msg['id']+"/"+studentId;
            var imageSrc = "{{asset('images/edit1.png')}}";
            var eleRemark = document.createElement('td');
            eleRemark.innerHTML = '<a href="'+ url +'" ><img src="'+imageSrc+'" width=\'30\' height=\'30\' title=" Assignment Remark" /></a><form id="form_'+ msg['id']+'" action="" method="GET"></form>';

            eleTr.appendChild(eleRemark);
            body.appendChild(eleTr);
          } else {
            var eleTr = document.createElement('tr');

            var eleQuestion = document.createElement('td');
            eleQuestion.innerHTML = 'No Assignment for selected topic';
            eleQuestion.setAttribute('colspan', 6);
            eleTr.appendChild(eleQuestion);
            body.appendChild(eleTr);
          }
        });
      }
    }

  function renderStudent(){
    select = document.getElementById('student');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '';
    opt.innerHTML = 'Select Student';
    select.appendChild(opt);
  }

  function renderTopic(){
    select = document.getElementById('topic');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '';
    opt.innerHTML = 'Select Topic';
    select.appendChild(opt);
  }

  function renderSubject(){
    select = document.getElementById('subject');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '';
    opt.innerHTML = 'Select Subject';
    select.appendChild(opt);
  }

  function resetYear(){
    document.getElementById('year').selectedIndex = '';
    document.getElementById('studentAssignment').innerHTML = '';
    renderSubject();
    renderTopic();
    renderStudent();
  }
</script>
@stop