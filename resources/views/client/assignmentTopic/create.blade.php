@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Topic  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Assignment </li>
      <li class="active"> Manage Topic </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($topic->id))
    <form action="{{url('updateAssignmentTopic')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="topic_id" value="{{$topic->id}}">
  @else
   <form action="{{url('createAssignmentTopic')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('institute_course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Institute Course Name:</label>
      <div class="col-sm-3">
        <select class="form-control" name="institute_course" required title="Institute Course" onClick="selectSubject(this);">
            <option value="">Select Institute Course</option>
            @if(count($instituteCourses) > 0)
              @foreach($instituteCourses as $instituteCourse)
                @if( $topic->client_institute_course_id == $instituteCourse->id)
                  <option value="{{$instituteCourse->id}}" selected="true">{{$instituteCourse->name}}</option>
                @else
                  <option value="{{$instituteCourse->id}}">{{$instituteCourse->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
          @if($errors->has('institute_course')) <p class="help-block">{{ $errors->first('institute_course') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('subject')) has-error @endif">
      <label class="col-sm-2 col-form-label">Subject Name:</label>
      <div class="col-sm-3">
        @if(isset($topic->id) && count($subjects) > 0)
          <select class="form-control" name="subject" required title="subject" >
            <option value="">Select Subject</option>
              @foreach($subjects as $subject)
                @if( $topic->client_assignment_subject_id == $subject->id)
                  <option value="{{$subject->id}}" selected="true">{{$subject->name}}</option>
                @else
                  <option value="{{$subject->id}}">{{$subject->name}}</option>
                @endif
              @endforeach
          </select>
          @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
        @else
          <select class="form-control" id="subject" name="subject" required title="Subject">
            <option value="">Select Subject</option>
          </select>
          @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
        @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('topic')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="topic">Topic Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="topic" name="topic" value="{{($topic)?$topic->name:null}}" required="true">
        @if($errors->has('topic')) <p class="help-block">{{ $errors->first('topic') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
    </form>
  </div>
<script type="text/javascript">
  function selectSubject(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getAssignmentSubjectsByCourse')}}",
          data: {institute_course_id:id}
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
</script>
@stop