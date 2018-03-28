@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
  @media only screen and (max-width: 760px), (max-device-width: 1024px) and (min-device-width: 768px){
  td {
      padding-left: 50% !important;
  }
}
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Assignments  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-tasks"></i> Assignment </li>
      <li class="active"> My Assignments </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif

  <div class="form-group row">
    <div class="col-md-3 mrgn_10_btm">
      <select class="form-control" id="subject" name="subject" title="Subject" onChange="selectTopic(this);">
        <option value="">Select Subject</option>
        @foreach($subjects as $subject)
          <option value="{{$subject->id}}">{{$subject->name}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3 mrgn_10_btm">
     <select class="form-control" id="topic" name="topic" title="Topic" onChange="getAssignments(this);">
      <option value="0">Select Topic</option>
     </select>
    </div>
  </div>
  <div class="form-group row">
    <table class="" id="clientUserAssignment">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Assignment</th>
          <th>Subject Name</th>
          <th>Topic Name</th>
          <th>Do Assignment</th>
          <!-- <th>Delete Topic</th> -->
        </tr>
      </thead>
      <tbody id="studentAssignment">
        @if(count($assignments) > 0)
          @foreach($assignments as $index => $assignment)
          <tr>
            <td>{{$index + 1}}</td>
            <td>{!! mb_strimwidth($assignment->question, 0, 400, "...") !!}</td>
            <td>{{$assignment->subject->name}}</td>
            <td>{{$assignment->topic->name}}</td>
            <td>
              <a href="{{url('doAssignment')}}/{{$assignment->id}}" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Do Assignment " />
                </a>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No Assignment is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;" id="paginate">
      {{ $assignments->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

  function selectSubject(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      //get assignments
      $.ajax({
        method: "POST",
        url: "{{url('getAssignments')}}",
        data: {institute_course_id:id}
      })
      .done(function( msgs ) {
        body = document.getElementById('studentAssignment');
        body.innerHTML = '';
        if(Object.keys(msgs).length > 0){
          renderRecords(msgs, body);
        } else {
          var eleTr = document.createElement('tr');
          var eleQuestion = document.createElement('td');
          eleQuestion.innerHTML = 'No Assignment for this lecturer';
          eleQuestion.setAttribute('colspan', 6);
          eleTr.appendChild(eleQuestion);
          body.appendChild(eleTr);
        }
      });
      // get assignment subject
      $.ajax({
          method: "POST",
          url: "{{url('getAssignmentSubjectsByCourseForUser')}}",
          data: {institute_course_id:id}
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
  }

  function selectTopic(ele){
    var id = parseInt($(ele).val());
    document.getElementById('studentAssignment').innerHTML = '';
    document.getElementById('paginate').innerHTML = '';
    var topic = document.getElementById('topic').value;
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getAssignmentTopicsBySubjectForUser')}}",
          data: {subject_id:id}
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
        url: "{{url('getAssignments')}}",
        data: {subject:id,topic:topic}
      })
      .done(function( msgs ) {
        body = document.getElementById('studentAssignment');
        body.innerHTML = '';
        if(Object.keys(msgs).length > 0){
          renderRecords(msgs, body);
        } else {
          var eleTr = document.createElement('tr');
          var eleQuestion = document.createElement('td');
          eleQuestion.innerHTML = 'No Assignment for this subject';
          eleQuestion.setAttribute('colspan', 6);
          eleTr.appendChild(eleQuestion);
          body.appendChild(eleTr);
        }
      });
    }
  }

  function getAssignments(ele){
    id = parseInt($(ele).val());
    var subject = document.getElementById('subject').value;
    if( 0 < id ){
      $.ajax({
        method: "POST",
        url: "{{url('getAssignments')}}",
        data: {topic:id,subject:subject}
      })
      .done(function( msgs ) {
        body = document.getElementById('studentAssignment');
        body.innerHTML = '';
        if(Object.keys(msgs).length > 0){
          renderRecords(msgs, body);
        } else {
          var eleTr = document.createElement('tr');

          var eleQuestion = document.createElement('td');
          eleQuestion.innerHTML = 'No Assignment for this course';
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

      var eleQuestion = document.createElement('td');
      eleQuestion.innerHTML = msg['question'];
      eleTr.appendChild(eleQuestion);

      var eleSubject = document.createElement('td');
      eleSubject.innerHTML = msg['subject'];
      eleTr.appendChild(eleSubject);

      var eleTopic = document.createElement('td');
      eleTopic.innerHTML = msg['topic'];
      eleTr.appendChild(eleTopic);

      var url = "{{url('doAssignment')}}/"+ msg['id'];
      var imageSrc = "{{asset('images/edit1.png')}}";
      var eleRemark = document.createElement('td');
      eleRemark.innerHTML = '<a href="'+ url +'" ><img src="'+imageSrc+'" width=\'30\' height=\'30\' title=" Edit Assignment " /></a>';
      eleTr.appendChild(eleRemark);
      body.appendChild(eleTr);
    });
  }
</script>
@stop