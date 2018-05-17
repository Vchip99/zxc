@extends('dashboard.dashboard')
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
      {{method_field('PUT')}}
      <input type="hidden" name="topic_id" value="{{$topic->id}}">
  @else
      <form action="{{url('createAssignmentTopic')}}" method="POST">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('subject')) has-error @endif">
    <label class="col-sm-2 col-form-label">Subject Name:</label>
    <div class="col-sm-3">
      <select class="form-control" name="subject" required title="Subject">
          <option value="">Select Subject</option>
          @if(count($subjects) > 0)
            @foreach($subjects as $subject)
              @if( $topic->assignment_subject_id == $subject->id)
                <option value="{{$subject->id}}" selected="true">{{$subject->name}}</option>
              @else
                <option value="{{$subject->id}}">{{$subject->name}}</option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('topic')) has-error @endif">
    <label for="topic" class="col-sm-2 col-form-label">Topic Name:</label>
    <div class="col-sm-3">
      @if(isset($topic))
        <input type="text" class="form-control" name="topic" value="{{$topic->name}}" required="true">
      @else
        <input type="text" class="form-control" name="topic" value="" required="true">
      @endif
      @if($errors->has('topic')) <p class="help-block">{{ $errors->first('topic') }}</p> @endif
    </div>
  </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
  </div>
</form>
@stop