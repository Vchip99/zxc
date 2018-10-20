@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Assignment  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Assignment </li>
      <li class="active"> Manage Assignment </li>
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
         <select class="form-control" id="department" name="department" title="department" onChange="getDepartmentLecturers(this);">
          <option value="">Select Department</option>
          @if(count($departments) > 0)
            @foreach($departments as $department)
              <option value="{{ $department->id }}">{{ $department->name }}</option>
            @endforeach
          @endif
         </select>
        </div>
      @if(4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
        <div class="col-md-3 mrgn_10_btm">
         <select class="form-control" id="lecturer" name="lecturer" title="Lecturer" onChange="getLecturerAssignments(this);">
          <option value="">Select User</option>
          @if(count($assignmentTeachers) > 0)
            @foreach($assignmentTeachers as $assignmentTeacher)
              <option value="{{ $assignmentTeacher->id }}">{{ $assignmentTeacher->name }}</option>
            @endforeach
          @endif
         </select>
        </div>
      @endif
      <div class="col-md-3 mrgn_10_btm">
        <select class="form-control" id="year" name="year" required title="year" onChange="selectSubject(this);">
          <option value="">Select Year</option>
          <option value="1">First </option>
          <option value="2">Second </option>
          <option value="3">Third </option>
          <option value="4">Fourth </option>
        </select>
      </div>
      <div class="col-md-3 mrgn_10_btm">
       <select class="form-control" id="subject" name="subject" title="Subject" onChange="selectTopic(this);">
        <option value="">Select Subject</option>
       </select>
      </div>
      <div class="col-md-3 mrgn_10_btm">
       <select class="form-control" id="topic" name="topic" title="Topic" onChange="getAssignment(this);">
        <option value="">Select Topic</option>
       </select>
      </div>
      <div id="addTopicDiv">
        <a id="addTopic" href="{{url('college/'.Session::get('college_user_url').'/createAssignment')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Assignment">Add New Assignment</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="assignmentTable">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Assignment</th>
          <th>Attachment</th>
          <th>Subject </th>
          <th>Topic </th>
          <th>Edit/Read </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody id="studentAssignment">
        @if(count($assignments) > 0)
          @foreach($assignments as $index => $assignment)
          <tr style="overflow: auto;">
            <td>{{$index + $assignments->firstItem()}}</td>
            <td>{!! mb_strimwidth($assignment->question, 0, 400, "...") !!}</td>
            <td>{!! basename($assignment->attached_link) !!}</td>
            <td>{{$allSubjects[$assignment->college_subject_id]}}</td>
            <td>{{$allTopics[$assignment->assignment_topic_id]}}</td>
            <td>
              @if($assignment->lecturer_id == Auth::user()->id || (4 == Auth::user()->user_type || 5 == Auth::user()->user_type))
              <a href="{{url('college/'.Session::get('college_user_url').'/assignment')}}/{{$assignment->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit Assignment" />
                </a>
              @endif
            </td>
            <td>
              @if($assignment->lecturer_id == Auth::user()->id || (4 == Auth::user()->user_type || 5 == Auth::user()->user_type))
              <a id="{{$assignment->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$assignment->title}}" />
                  </a>
                  <form id="deleteAssignment_{{$assignment->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteAssignment')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="assignment_id" value="{{$assignment->id}}">
                  </form>
              @endif
              </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="7">No Assignment is created by you.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;" id="paginate">
      {{ $assignments->links() }}
    </div>
  </div>
  <input type="hidden" id="user_id" name="user_id" value="{{Auth::user()->id}}">
  <input type="hidden" id="user_type" name="user_type" value="{{Auth::user()->user_type}}">
  </div>

<script type="text/javascript">

  function getDepartmentLecturers(ele){
    id = parseInt($(ele).val());
    renderTopic();
    renderSubject();
    document.getElementById('year').selectedIndex = 0;
    if(id > 0){
      // get assignments
      $.ajax({
        method: "POST",
        url: "{{url('getAssignments')}}",
        data: {department:id}
      })
      .done(function( msgs ) {
        body = document.getElementById('studentAssignment');
        body.innerHTML = '';
        if(Object.keys(msgs).length > 0){
          renderRecords(msgs, body);
        } else {
          var eleTr = document.createElement('tr');
          var eleQuestion = document.createElement('td');
          eleQuestion.innerHTML = 'No Assignment for this department';
          eleQuestion.setAttribute('colspan', 7);
          eleTr.appendChild(eleQuestion);
          body.appendChild(eleTr);
        }
      });

      if(document.getElementById('user_type').value > 3){
        // get dept lecturer
        $.ajax({
          method: "POST",
          url: "{{url('getDepartmentLecturers')}}",
          data: {department:id}
        })
        .done(function( msg ) {
          select = document.getElementById('lecturer');
          select.innerHTML = '';
          var opt = document.createElement('option');
          opt.value = 0;
          opt.innerHTML = 'Select Lecturer';
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
  }

  function getLecturerAssignments(ele){
      id = parseInt($(ele).val());
      document.getElementById('paginate').innerHTML = '';
      document.getElementById('studentAssignment').innerHTML = '';
      document.getElementById('year').selectedIndex = 0;
      renderSubject();
      renderTopic();
      if(document.getElementById('department')){
        var department = document.getElementById('department').value;
      } else {
        var department = '';
      }
      if(id > 0){
        // get assignments
        $.ajax({
          method: "POST",
          url: "{{url('getAssignments')}}",
          data: {lecturer_id:id,department:department}
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
            eleQuestion.setAttribute('colspan', 7);
            eleTr.appendChild(eleQuestion);
            body.appendChild(eleTr);
          }
        });
      }
  }

  function selectSubject(ele){
    id = parseInt($(ele).val());
    document.getElementById('studentAssignment').innerHTML = '';
    document.getElementById('paginate').innerHTML = '';
    if(document.getElementById('lecturer')){
      var lecturer = document.getElementById('lecturer').value;
    } else {
      var lecturer = '';
    }
    if(document.getElementById('department')){
      var department = document.getElementById('department').value;
    } else {
      var department = '';
    }
    renderTopic();
      $.ajax({
        method: "POST",
        url: "{{url('getCollegeSubjectByYear')}}",
        data: {year:id, lecturer:lecturer,department:department}
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

      // get assignments
      $.ajax({
        method: "POST",
        url: "{{url('getAssignments')}}",
        data: {year:id, lecturer_id:lecturer,department:department}
      })
      .done(function( msgs ) {
        body = document.getElementById('studentAssignment');
        body.innerHTML = '';
        if(Object.keys(msgs).length > 0){
          renderRecords(msgs, body);
        } else {
          var eleTr = document.createElement('tr');
          var eleQuestion = document.createElement('td');
          eleQuestion.innerHTML = 'No Assignment for this year';
          eleQuestion.setAttribute('colspan', 7);
          eleTr.appendChild(eleQuestion);
          body.appendChild(eleTr);
        }
      });
  }

  function renderRecords(msgs, body){
    if(document.getElementById('lecturer')){
      var lecturer = document.getElementById('lecturer').value;
    } else {
      var lecturer = '';
    }
    var userId = document.getElementById('user_id').value;
    var userType = document.getElementById('user_type').value;

    $.each(msgs, function(idx, msg) {
      var eleTr = document.createElement('tr');
      eleTr.setAttribute("style","overflow: auto;");

      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = idx;
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

      if( userId == msg['lecturer_id'] || ( 4 == userType || 5 == userType)){
        var url = "{{url('college/'.Session::get('college_user_url').'/assignment')}}/"+ msg['id']+"/edit";
        var imageSrc = "{{asset('images/edit1.png')}}";
        var eleRemark = document.createElement('td');
        eleRemark.innerHTML = '<a href="'+ url +'" ><img src="'+imageSrc+'" width=\'30\' height=\'30\' title=" Edit Assignment " /></a>';
        eleTr.appendChild(eleRemark);
      } else {
        var eleRemark = document.createElement('td');
        eleRemark.innerHTML = '';
        eleTr.appendChild(eleRemark);
      }

      var url = "{{url('college/'.Session::get('college_user_url').'/deleteAssignment')}}";
      var imageSrc = "{{asset('images/delete2.png')}}";
      var csrfField = '{{ csrf_field() }}';
      var deleteMethod ='{{ method_field("DELETE") }}';
      var eleDelete = document.createElement('td');
      if( userId == msg['lecturer_id'] || ( 4 == userType || 5 == userType)){
        eleDelete.innerHTML = '<a id="'+ msg['id']+'" onclick="confirmDelete(this);" ><img src="'+imageSrc+'" width=\'30\' height=\'30\' title=" Delete Assignment " /></a>';
        eleDelete.innerHTML += '<form id="deleteAssignment_'+ msg['id']+'" action="'+url+'" method="POST" style="display: none;">'+csrfField+''+deleteMethod+'<input type="hidden" name="assignment_id" value="'+ msg['id']+'"></form>';
      } else {
        eleDelete.innerHTML = '<a id="'+ msg['id']+'" ><img src="'+imageSrc+'" width=\'30\' height=\'30\' title="Can not  delete others assignment" /></a>';
      }
      eleTr.appendChild(eleDelete);

      body.appendChild(eleTr);
    });
  }

  function selectTopic(ele){
    id = parseInt($(ele).val());
    document.getElementById('studentAssignment').innerHTML = '';
    if(document.getElementById('department')){
      var department = document.getElementById('department').value;
    } else {
      var department = '';
    }
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

      year = document.getElementById('year').value;
      // get assignments
      $.ajax({
        method: "POST",
        url: "{{url('getAssignments')}}",
        data: {year:year, subject:id, department:department}
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
          eleQuestion.setAttribute('colspan', 7);
          eleTr.appendChild(eleQuestion);
          body.appendChild(eleTr);
        }
      });
    } else {
      renderTopic();
    }
  }

  function getAssignment(ele){
    id = parseInt($(ele).val());
    year = document.getElementById('year').value;
    department = document.getElementById('department').value;
    subject = document.getElementById('subject').value;
    document.getElementById('studentAssignment').innerHTML = '';
    if( 0 < id ){
      $.ajax({
        method: "POST",
        url: "{{url('getAssignments')}}",
        data: {year:year,subject:subject,department:department,topic:id}
      })
      .done(function( msgs ) {
        body = document.getElementById('studentAssignment');
        body.innerHTML = '';
        if(Object.keys(msgs).length > 0){
          renderRecords(msgs, body);
        } else {
          var eleTr = document.createElement('tr');

          var eleQuestion = document.createElement('td');
          eleQuestion.innerHTML = 'No Assignment for this topic';
          eleQuestion.setAttribute('colspan', 7);
          eleTr.appendChild(eleQuestion);
          body.appendChild(eleTr);
        }
      });
    }
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

  $(document).ready(function(){
    if(document.getElementById('department')){
      document.getElementById('department').value = '';
    }
    if(document.getElementById('lecturer')){
      document.getElementById('lecturer').value = '';
    }
    document.getElementById('year').value = '';
    document.getElementById('subject').value = '';
    document.getElementById('topic').value = '';
  });

  function confirmDelete(ele){
    $.confirm({
      title: 'Confirmation',
      content: 'If you delete this assignment, all answers of this assignment will be deleted?',
      type: 'red',
      typeAnimated: true,
      buttons: {
        Ok: {
            text: 'Ok',
            btnClass: 'btn-red',
            action: function(){
              var id = $(ele).attr('id');
              formId = 'deleteAssignment_'+id;
              document.getElementById(formId).submit();
            }
        },
        Cancle: function () {
        }
      }
    });
  }
</script>
@stop