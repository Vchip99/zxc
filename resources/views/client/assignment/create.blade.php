@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Assignment  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-tasks"></i> Assignment </li>
      <li class="active"> Manage Assignment </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
    <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  <div class="container admin_div">
  @if(isset($assignment->id))
    <form action="{{url('updateAssignment')}}" method="POST" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    <input type="hidden" name="assignment_id" value="{{$assignment->id}}">
  @else
   <form action="{{url('createAssignment')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
    <div class="form-group row">
    <label class="col-sm-2 col-form-label">Type:</label>
    @if(isset($assignment->id))
      <input type="radio" name="type_text" value="assignment" @if(!empty($assignment->question)) checked @endif disabled>Assignment
      <input type="radio" name="type_text" value="document" @if(empty($assignment->question)) checked @endif disabled>Document
      @if(!empty($assignment->question))
        <input type="hidden" name="type" value="assignment">
      @else
        <input type="hidden" name="type" value="document">
      @endif
    @else
      <input type="radio" name="type" value="assignment" checked onClick="toggleType(this);">Assignment
      <input type="radio" name="type" value="document" onClick="toggleType(this);">Document
    @endif
  </div>
    <div class="form-group row  @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="batch">Batch Name:</label>
      <div class="col-sm-3">
        @if(isset($assignment->id))
          @if(0 == $assignment->client_batch_id || empty($assignment->client_batch_id))
            <input type="text" class="form-control" name="batch_text" value="All" readonly>
            <input type="hidden" name="batch" value="0">
          @else
            @if(count($batches) > 0)
              @foreach($batches as $batch)
                @if($batch->id == $assignment->client_batch_id)
                  <input type="text" class="form-control" name="batch_text" value="{{$batch->name}}" readonly>
                  <input type="hidden" name="batch" value="{{$batch->id}}">
                @endif
              @endforeach
            @endif
          @endif
        @else
          <select class="form-control" name="batch" id="batch" onChange="selectSubject(this);">
            <option value="">Select Batch</option>
            <option value="All">All</option>
            @if(count($batches) > 0)
              @foreach($batches as $batch)
                <option value="{{$batch->id}}">{{$batch->name}}</option>
              @endforeach
            @endif
          </select>
        @endif
        @if($errors->has('batch')) <p class="help-block">{{ $errors->first('batch') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('subject')) has-error @endif">
      <label class="col-sm-2 col-form-label">Subject Name:</label>
      <div class="col-sm-3">
        @if(isset($assignment->id) && count($subjects) > 0)
          @foreach($subjects as $subject)
            @if( $assignment->client_assignment_subject_id == $subject->id)
              <input class="form-control" type="text" name="subject_text" value="{{$subject->name}}" readonly="true">
              <input type="hidden" name="subject" value="{{$subject->id}}">
            @endif
          @endforeach
          @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
        @else
          <select class="form-control" id="subject" name="subject" required title="Subject" onClick="selectTopic(this);">
            <option value="">Select Subject</option>
            @foreach($subjects as $subject)
              <option value="{{ $subject->id }}">{{ $subject->name }}</option>
            @endforeach
          </select>
          @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
        @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('topic')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="topic">Topic Name:</label>
      <div class="col-sm-3">
        @if(isset($assignment->id) && count($topics) > 0)
          @foreach($topics as $topic)
            @if( $assignment->client_assignment_topic_id == $topic->id)
              <input class="form-control" type="text" name="topic_text" value="{{$topic->name}}" readonly="true">
              <input type="hidden" name="topic" value="{{$topic->id}}">
            @endif
          @endforeach
          @if($errors->has('topic')) <p class="help-block">{{ $errors->first('topic') }}</p> @endif
        @else
          <select class="form-control" id="topic" name="topic" required title="Topic" onClick="checkAssignmentExist(this);">
            <option value="">Select Topic</option>
          </select>
          @if($errors->has('topic')) <p class="help-block">{{ $errors->first('topic') }}</p> @endif
        @endif
      </div>
    </div>
  <div class="form-group row has-error hide" id="message_error">
    <label class="col-sm-2 col-form-label">Warning:</label>
    <div class="col-sm-10">
     <p> Assignment is exists for above criteria. Click on edit button to edit assignment. <a href="" id="assignment" class="btn btn-primary" style="width: 120px;">Edit Assignment</a></p>

    </div>
  </div>
  <div class="form-group row @if ($errors->has('question')) has-error @endif" id="questionDiv">
    @if(empty($assignment->id) || !empty($assignment->question))
      <label for="question" class="col-sm-2 col-form-label">Assignment:</label>
    @endif
    <div class="col-sm-10">
        @if($errors->has('question')) <p class="help-block">{{ $errors->first('question') }}</p> @endif
        @if(!empty($assignment->id) && ($assignment->created_by == $loginUser->id  || 1 == $loginUser->admin_approve))
          <textarea name="question" cols="60" rows="4" id="question" placeholder="Enter your Question" required>
            @if(!empty($assignment->id))
              {!! $assignment->question !!}
            @endif
          </textarea>
          <script type="text/javascript">
            CKEDITOR.replace( 'question', { enterMode: CKEDITOR.ENTER_BR } );
          </script>
        @elseif(empty($assignment->id))
          <textarea name="question" cols="60" rows="4" id="question" placeholder="Enter your Question" required>
          </textarea>
          <script type="text/javascript">
            CKEDITOR.replace( 'question', { enterMode: CKEDITOR.ENTER_BR } );
          </script>
        @else
          @if(!empty($assignment->question))
            {!! $assignment->question !!}
          @endif
        @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('attached_link')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="attached_link">Attachment:</label>
      <div class="col-sm-3">
        @if(!empty($assignment->id) && ($assignment->created_by == $loginUser->id || 1 == $loginUser->admin_approve))
          <input type="file" class="form-control"  name="attached_link" id="attached_link">
          @if($errors->has('attached_link')) <p class="has-error">{{ $errors->first('attached_link') }}</p> @endif
          <b><span>Existing Attachment: {!! basename($assignment->attached_link) !!}</span></b>
        @elseif(empty($assignment->id))
          <input type="file" class="form-control"  name="attached_link" id="attached_link">
          @if($errors->has('attached_link')) <p class="has-error">{{ $errors->first('attached_link') }}</p> @endif
          <b><span>Existing Attachment: {!! basename($assignment->attached_link) !!}</span></b>
        @else
          @if(!empty($assignment->attached_link))
            <a data-path="{{asset($assignment->attached_link)}}" class="btn btn-primary" data-toggle="modal" data-target="#dynamic-modal-{{$assignment->id}}" data-document_id="{{$assignment->id}}" style="width: 120px !important;"> {{basename($assignment->attached_link)}} </a>
            <div id="dynamic-modal-{{$assignment->id}}" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button class="close" data-dismiss="modal">×</button>
                    <h2  class="modal-title">{{basename($assignment->attached_link)}}</h2>
                  </div>
                  <div class="modal-body">
                      <div class="iframe-container">
                        <iframe src="{{asset($assignment->attached_link)}}" frameborder="0"></iframe>
                      </div>
                  </div>
                  <div class="modal-footer ">
                    <a href="{{asset($assignment->attached_link)}}" download class="btn btn-primary download" id="myBtn" style="width: 90px !important;"><i class="fa fa-download" aria-hidden="true"></i></a>
                  </div>
                </div>
              </div>
            </div>
          @endif
      @endif
      </div>
    </div>
    <div class="form-group row" id="submit">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        @if(!empty($assignment->id) && ($assignment->created_by == $loginUser->id || 1 == $loginUser->admin_approve))
          <button type="submit" class="btn btn-primary" style="width: 90px !important;" >Submit</button>
        @elseif(empty($assignment->id))
          <button type="submit" class="btn btn-primary" style="width: 90px !important;" >Submit</button>
        @endif
      </div>
    </div>
    </form>
  </div>
<script type="text/javascript">
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
  }

  function selectTopic(ele){
    id = parseInt($(ele).val());
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
    }
  }

  function checkAssignmentExist(ele){
    var id = parseInt($(ele).val());
    var subject_id = document.getElementById('subject').value;
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('checkAssignmentExist')}}",
          data: {topic_id:id,subject_id:subject_id}
      })
      .done(function( msg ) {
        if('true' == msg['status']){
          document.getElementById('message_error').classList.remove('hide');
          document.getElementById('assignment').setAttribute('href', 'assignment/'+msg['id']+'/edit');
          document.getElementById('submit').classList.add('hide');

        } else {
          document.getElementById('message_error').classList.add('hide');
          document.getElementById('submit').classList.remove('hide');
        }
      });
    }
  }

  function toggleType(ele){
    if('document' ==  $(ele).val()){
      $('#questionDiv').addClass('hide');
    } else {
      $('#questionDiv').removeClass('hide');
    }
  }
</script>
@stop