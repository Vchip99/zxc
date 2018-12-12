@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Documents  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-tasks"></i> Assignment </li>
      <li class="active"> Documents </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
  <div class="form-group row">
    <div class="col-md-3 mrgn_10_btm">
      <select class="form-control" id="lecturer" name="lecturer" title="Lecturer" onChange="getLecturerDocuments(this);">
        <option value="">Select Lecturer</option>
        @if(count($assignmentTeachers) > 0)
          @foreach($assignmentTeachers as $assignmentTeacher)
            <option value="{{ $assignmentTeacher->id }}"> {{ $assignmentTeacher->name }}</option>
          @endforeach
        @endif
      </select>
    </div>
    <div class="col-md-3 mrgn_10_btm">
      <select class="form-control" id="subject" name="subject" title="Subject" onChange="selectTopic(this);">
        <option value="">Select Subject</option>
      </select>
    </div>
    <div class="col-md-3 mrgn_10_btm">
     <select class="form-control" id="topic" name="topic" title="Topic" onChange="getAssignDocuments(this);">
      <option value="0">Select Topic</option>
     </select>
    </div>
  </div>
  <div class="form-group row">
    <table class="" id="dataTables-example">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Attachment</th>
          <th>Topic Name</th>
          <th>Subject Name</th>
          <th>Download</th>
        </tr>
      </thead>
      <tbody id="studentAssignment">
        @if(count($assignments) > 0)
          @foreach($assignments as $index => $assignment)
          <tr style="overflow: auto;">
            <td>{{$index + 1}}</td>
            <td>{!! basename($assignment->attached_link) !!}</td>
            <td>{{$assignment->topic->name}}</td>
            <td>{{$assignment->subject->name}}</td>
            <td>
              <a href="{{asset($assignment->attached_link)}}" download class="btn btn-primary download" style="width: 39px;">
                <i class="fa fa-download" aria-hidden="true" title="Download Document"></i>
                </a>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No Document is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;" id="paginate">
      {{ $assignments->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

  function getLecturerDocuments(ele){
      id = parseInt($(ele).val());
      // get assignments
      $.ajax({
        method: "POST",
        url: "{{url('getAssignDocuments')}}",
        data: {lecturer_id:id}
      })
      .done(function( msgs ) {
        body = document.getElementById('studentAssignment');
        body.innerHTML = '';
        if(Object.keys(msgs).length > 0){
          renderRecords(msgs, body);
        } else {
          var eleTr = document.createElement('tr');
          var eleQuestion = document.createElement('td');
          eleQuestion.innerHTML = 'No Documents by this lecturer';
          eleQuestion.setAttribute('colspan', 6);
          eleTr.appendChild(eleQuestion);
          body.appendChild(eleTr);
        }
      });
      // get assignment subject
      $.ajax({
          method: "POST",
          url: "{{url('getAssignmentSubjectsOfGivenAssignmentByLecturer')}}",
          data: {lecturer_id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('subject');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = 0;
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

  function selectTopic(ele){
    id = parseInt($(ele).val());
    document.getElementById('studentAssignment').innerHTML = '';
    document.getElementById('paginate').innerHTML = '';
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getAssignDocumentTopics')}}",
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

      // get assignments
      $.ajax({
        method: "POST",
        url: "{{url('getAssignDocuments')}}",
        data: {subject:id}
      })
      .done(function( msgs ) {
        body = document.getElementById('studentAssignment');
        body.innerHTML = '';
        if(Object.keys(msgs).length > 0){
          renderRecords(msgs, body);
        } else {
          var eleTr = document.createElement('tr');
          var eleQuestion = document.createElement('td');
          eleQuestion.innerHTML = 'No Documents for this subject';
          eleQuestion.setAttribute('colspan', 6);
          eleTr.appendChild(eleQuestion);
          body.appendChild(eleTr);
        }
      });
    }
  }

  function getAssignDocuments(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
        method: "POST",
        url: "{{url('getAssignDocumentByTopic')}}",
        data: {topic:id}
      })
      .done(function( msg ) {
        body = document.getElementById('studentAssignment');
        body.innerHTML = '';
        if(msg['id']){
          var eleTr = document.createElement('tr');

          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = 1;
          eleTr.appendChild(eleIndex);

          var eleAttachment = document.createElement('td');
          eleAttachment.innerHTML = msg['attached_link_name'];
          eleTr.appendChild(eleAttachment);

          var eleTopic = document.createElement('td');
          eleTopic.innerHTML = msg['topic'];
          eleTr.appendChild(eleTopic);

          var eleSubject = document.createElement('td');
          eleSubject.innerHTML = msg['subject'];
          eleTr.appendChild(eleSubject);

          var url = "{{asset('')}}"+ msg['attached_link'];
          var eleRemark = document.createElement('td');
          eleRemark.innerHTML = '<a href="'+ url +'" download class="btn btn-primary download" style="width: 39px;" ><i class="fa fa-download" aria-hidden="true" title="Download Document"></i></a>';
          eleTr.appendChild(eleRemark);
          body.appendChild(eleTr);
        } else {
          var eleTr = document.createElement('tr');

          var eleQuestion = document.createElement('td');
          eleQuestion.innerHTML = 'No Documents for this topic';
          eleQuestion.setAttribute('colspan', 6);
          eleTr.appendChild(eleQuestion);
          body.appendChild(eleTr);
        }
      });
    }
  }

  function renderRecords(msgs, body){
    $.each(msgs, function(idx, msg) {
      var eleTr = document.createElement('tr');

      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = idx;
      eleTr.appendChild(eleIndex);

      var eleAttachment = document.createElement('td');
      eleAttachment.innerHTML = msg['attached_link_name'];
      eleTr.appendChild(eleAttachment);

      var eleTopic = document.createElement('td');
      eleTopic.innerHTML = msg['topic'];
      eleTr.appendChild(eleTopic);

      var eleSubject = document.createElement('td');
      eleSubject.innerHTML = msg['subject'];
      eleTr.appendChild(eleSubject);

      var url = "{{asset('')}}"+ msg['attached_link'];
      var eleRemark = document.createElement('td');
      eleRemark.innerHTML = '<a href="'+ url +'" download class="btn btn-primary download" style="width: 39px;" ><i class="fa fa-download" aria-hidden="true" title="Download Document"></i></a>';
      eleTr.appendChild(eleRemark);
      body.appendChild(eleTr);
    });
  }
</script>
@stop