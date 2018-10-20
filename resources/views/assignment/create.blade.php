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
	&nbsp;
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  <div class="container admin_div">
  @if(isset($assignment->id))
    <form action="{{url('college/'.Session::get('college_user_url').'/updateAssignment')}}" method="POST" enctype="multipart/form-data">
      {{method_field('PUT')}}
      <input type="hidden" name="assignment_id" value="{{$assignment->id}}">
  @else
      <form action="{{url('college/'.Session::get('college_user_url').'/createAssignment')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('subject')) has-error @endif">
    <label class="col-sm-2 col-form-label">Subject Name:</label>
    <div class="col-sm-3">
       @if(isset($assignment->id))
          @if(count($subjects) > 0)
            @foreach($subjects as $subject)
              @if( $assignment->college_subject_id == $subject->id)
                <input class="form-control" type="text" name="subject_text" value="{{$subject->name}}" readonly="true">
                <input type="hidden" name="subject" value="{{$subject->id}}">
              @endif
            @endforeach
          @endif
      @else
      <select class="form-control" id="subject" name="subject" required title="Subject" onChange="selectTopic(this);">
        <option value="">Select Subject</option>
        @if(count($subjects) > 0)
          @foreach($subjects as $subject)
            <option value="{{$subject->id}}">{{$subject->name}}</option>
          @endforeach
        @endif
      </select>
      @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
      @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('topic')) has-error @endif">
    <label class="col-sm-2 col-form-label">Topic Name:</label>
    <div class="col-sm-3">
      @if(isset($assignment->id))
          @if(count($topics) > 0)
            @foreach($topics as $topic)
              @if($assignment->assignment_topic_id == $topic->id)
                <input class="form-control" type="text" name="topic_text" value="{{$topic->name}}" readonly="true">
                <input type="hidden" name="topic" value="{{$topic->id}}">
              @endif
            @endforeach
          @endif
      @else
      	<select class="form-control" id="topic" name="topic" required title="Topic" onChange="checkAssignment(this);">
        	<option value="">Select Topic</option>
        	@if(count($topics) > 0)
        		@foreach($topics as $topic)
              <option value="{{$topic->id}}">{{$topic->name}}</option>
        		@endforeach
        	@endif
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
  <div class="form-group row @if ($errors->has('question')) has-error @endif">
    <label for="question" class="col-sm-2 col-form-label">Assignment:</label>
    <div class="col-sm-10">
      @if(!empty($assignment->id) && $assignment->lecturer_id == Auth::user()->id)

        @if($errors->has('question')) <p class="help-block">{{ $errors->first('question') }}</p> @endif
        <textarea name="question" cols="60" rows="4" id="question" placeholder="Enter your Question" required>
    			@if(isset($assignment->id))
     				{!! $assignment->question !!}
     			@endif
  		  </textarea>
  	  	<script type="text/javascript">
  	    	CKEDITOR.replace( 'question', { enterMode: CKEDITOR.ENTER_BR } );
  	  	</script>
      @elseif(empty($assignment->id))
        @if($errors->has('question')) <p class="help-block">{{ $errors->first('question') }}</p> @endif
        <textarea name="question" cols="60" rows="4" id="question" placeholder="Enter your Question" required>
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'question', { enterMode: CKEDITOR.ENTER_BR } );
        </script>
      @else
        {!! $assignment->question !!}
      @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('attached_link')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="attached_link">Attachment:</label>
      <div class="col-sm-3">
        @if(!empty($assignment->id) && $assignment->lecturer_id == Auth::user()->id)
          <input type="file" class="form-control"  name="attached_link" id="attached_link">
          @if($errors->has('attached_link')) <p class="has-error">{{ $errors->first('attached_link') }}</p> @endif
          @if(!empty($assignment->attached_link))
            <b><span>Existing Attachment: {!! basename($assignment->attached_link) !!}</span></b>
          @endif
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
        @if(!empty($assignment->id) && $assignment->lecturer_id == Auth::user()->id)
          <input type="submit" class="btn btn-primary" style="width: 90px !important;" />
        @elseif(empty($assignment->id))
          <input type="submit" class="btn btn-primary" style="width: 90px !important;" />
        @endif
      </div>
    </div>
  </div>
</form>
<script type="text/javascript">

	function selectTopic(ele){
    id = parseInt($(ele).val());
    document.getElementById('message_error').classList.add('hide');
    document.getElementById('submit').classList.remove('hide');
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

  function checkAssignment(ele){
    var subject = document.getElementById('subject').value;
    var topic = document.getElementById('topic').value;

    if( subject && topic ){
      $.ajax({
          method: "POST",
          url: "{{url('checkAssignmentIsExist')}}",
          data: {subject:subject,topic:topic}
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
    // function selectSubject(ele){
    //   id = parseInt($(ele).val());
    //   document.getElementById('topic').value = 0;
    //   document.getElementById('message_error').classList.add('hide');
    //   document.getElementById('submit').classList.remove('hide');
    //   if( 0 < id ){
    //     // get subjects
    //     $.ajax({
    //       method: "POST",
    //       url: "{{url('getAssignmentSubjectsByYear')}}",
    //       data: {year:id}
    //     })
    //     .done(function( msg ) {
    //       select = document.getElementById('subject');
    //       select.innerHTML = '';
    //       var opt = document.createElement('option');
    //       opt.value = 0;
    //       opt.innerHTML = 'Select Subject';
    //       select.appendChild(opt);
    //       if( 0 < msg.length){
    //         $.each(msg, function(idx, obj) {
    //             var opt = document.createElement('option');
    //             opt.value = obj.id;
    //             opt.innerHTML = obj.name;
    //             select.appendChild(opt);
    //         });
    //       }
    //     });

    //   }
    // }
</script>
@stop