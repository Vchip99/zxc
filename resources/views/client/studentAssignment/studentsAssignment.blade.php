@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Students Assignment  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-tasks"></i> Assignment </li>
      <li class="active"> Students Assignment </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;

  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
      @if(Session::has('message'))
        <div class="alert alert-success" id="message">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ Session::get('message') }}
        </div>
      @endif
          <div class="row">
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" name="batch" id="batch" onChange="selectSubject(this);">
                <option value="">Select Batch</option>
                <option value="All" @if('All' == $selectedAssignmentBatch) selected @endif>All</option>
                @if(count($batches) > 0)
                  @foreach($batches as $batch)
                    @if($selectedAssignmentBatch == $batch->id)
                      <option value="{{$batch->id}}" selected>{{$batch->name}}</option>
                    @else
                      <option value="{{$batch->id}}">{{$batch->name}}</option>
                    @endif
                  @endforeach
                @endif
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
              <option value="0">Select Student</option>
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
                <table  class="" id="clientStudentAssignmentTable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Assignment</th>
                      <th>Batch</th>
                      <th>Subject</th>
                      <th>Topic</th>
                      <th>Remark</th>
                    </tr>
                  </thead>
                  <tbody id="studentAssignment" class="">
                  @if(is_object($assignment))
                    <tr>
                      <td>1</td>
                      <td>{!! mb_strimwidth($assignment->question, 0, 400, "...") !!}</td>
                      <td>
                        @if(0 == $assignment->client_batch_id || empty($assignment->client_batch_id))
                          All
                        @else
                          {{$assignment->batch->name}}
                        @endif
                      </td>
                      <td>{{$assignment->subject->name}}</td>
                      <td>{{$assignment->topic->name}}</td>
                      <td>
                        <a href="{{url('assignmentRemark')}}/{{$assignment->id}}/{{$selectedAssignmentStudent}}" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Assignment Remark" />
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
    var batchId = document.getElementById('batch').value;
    var id = $(ele).val();
    if( 0 < id){
      $.ajax({
        method: "POST",
        url: "{{url('searchStudentForAssignment')}}",
        data: {batch_id:batchId}
      })
      .done(function( msg ) {
        select = document.getElementById('student');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = 0;
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
    } else {
      renderStudent();
    }
  }
  function selectSubject(ele){
    var batchId = $(ele).val();
    $.ajax({
      method: "POST",
      url: "{{url('getAssignmentSubjectsByBatchId')}}",
      data: {batch_id:batchId}
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
    renderTopic();
    renderStudent();
    document.getElementById('studentAssignment').innerHTML = '';
  }
  function selectTopic(ele){
    var id = parseInt($(ele).val());
    document.getElementById('studentAssignment').innerHTML = '';
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getAssignmentTopicsBySubject')}}",
          data: {subject_id:id}
      })
      .done(function( msg ) {
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
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
      });
    } else {
      renderTopic();
    }
    renderStudent();
  }
  function getAssignments(ele){
    var id = parseInt($(ele).val());
    topic = parseInt(document.getElementById('topic').value);
    batch = document.getElementById('batch').value;
    if( 0 < id ){
      $.ajax({
        method: "POST",
        url: "{{url('getAssignmentByTopicForStudent')}}",
        data: {topic:topic, student:id, batch:batch}
      })
      .done(function( msg ) {
        body = document.getElementById('studentAssignment');
        body.innerHTML = '';
        studentId = document.getElementById('student').value;
        if(msg['id']){
          var eleTr = document.createElement('tr');

          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = 1;
          eleTr.appendChild(eleIndex);

          var eleQuestion = document.createElement('td');
          eleQuestion.innerHTML = msg['question'];
          eleTr.appendChild(eleQuestion);

          var eleSubject = document.createElement('td');
          eleSubject.innerHTML = msg['subject'];
          eleTr.appendChild(eleSubject);

          var eleBatch = document.createElement('td');
          eleBatch.innerHTML = msg['batch'];
          eleTr.appendChild(eleBatch);

          var eleTopic = document.createElement('td');
          eleTopic.innerHTML = msg['topic'];
          eleTr.appendChild(eleTopic);

          // var eleAttachment = document.createElement('td');
          // eleAttachment.innerHTML = msg['instituteCourse'];
          // eleTr.appendChild(eleAttachment);

          var url = "{{url('assignmentRemark')}}/"+ msg['id']+"/"+studentId;
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
    } else {
      document.getElementById('studentAssignment').innerHTML = '';
    }
  }
  function renderStudent(){
    select = document.getElementById('student');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = 0;
    opt.innerHTML = 'Select Student';
    select.appendChild(opt);
  }
  function renderTopic(){
    select = document.getElementById('topic');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = 0;
    opt.innerHTML = 'Select Topic';
    select.appendChild(opt);
  }
</script>
@stop