  @extends('admin.master')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Motivational Video </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-microphone"></i> Motivational Speech </li>
      <li class="active"> Manage Motivational Video </li>
    </ol>
  </section>
@stop
@section('admin_content')
    <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  <div class="container admin_div">
  @if(isset($motivationalSpeechVideo->id))
    <form action="{{url('admin/updateMotivationalSpeechVideo')}}" method="POST" enctype="multipart/form-data">
    {{method_field('PUT')}}
    <input type="hidden" id="motivational_video_id" name="motivational_video_id" value="{{$motivationalSpeechVideo->id}}">
  @else
    <form action="{{url('admin/createMotivationalSpeechVideo')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('motivational_speech_detail_id')) has-error @endif">
      <label class="col-sm-2 col-form-label">Motivational Speech Name:</label>
      <div class="col-sm-3">
        @if(count($motivationalSpeechDetails) > 0 && isset($motivationalSpeechVideo->id))
          @foreach($motivationalSpeechDetails as $motivationalSpeechDetail)
            @if( isset($motivationalSpeechVideo->id) && $motivationalSpeechVideo->motivational_speech_detail_id == $motivationalSpeechDetail->id)
              <input type="text" class="form-control" name="speech_text" value="{{$motivationalSpeechDetail->name}}" readonly="true">
              <input type="hidden" name="motivational_speech_detail_id" value="{{$motivationalSpeechDetail->id}}">
            @endif
          @endforeach
        @else
          <select id="motivational_speech_detail_id" class="form-control" name="motivational_speech_detail_id" title="Motivational Speech" required >
          <option value="">Select Motivational Speech</option>
            @foreach($motivationalSpeechDetails as $motivationalSpeechDetail)
              @if( isset($motivationalSpeechVideo->id) && $motivationalSpeechVideo->motivational_speech_detail_id == $motivationalSpeechDetail->id)
                <option value="{{$motivationalSpeechDetail->id}}" selected="true">{{$motivationalSpeechDetail->name}}</option>
              @else
                <option value="{{$motivationalSpeechDetail->id}}">{{$motivationalSpeechDetail->name}}</option>
              @endif
            @endforeach
          </select>
        @endif
        @if($errors->has('motivational_speech_detail_id')) <p class="help-block">{{ $errors->first('motivational_speech_detail_id') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('name')) has-error @endif">
      <label for="name" class="col-sm-2 col-form-label">Motivational Video Name:</label>
      <div class="col-sm-3">
        @if(isset($motivationalSpeechVideo->id))
          <input type="text" class="form-control" name="name" value="{{$motivationalSpeechVideo->name}}" readonly="true">
        @else
          <input type="text" class="form-control" name="name" value="" placeholder="Name" required="true">
        @endif
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('video_path')) has-error @endif">
      <label for="video_path" class="col-sm-2 col-form-label">Motivational Video Path:</label>
      <div class="col-sm-3">
        @if(isset($motivationalSpeechVideo->id))
          <input type="text" class="form-control" name="video_path" value="{{$motivationalSpeechVideo->video_path}}">
        @else
          <input type="text" class="form-control" name="video_path" value="" placeholder="video path" required="true">
        @endif
        @if($errors->has('video_path')) <p class="help-block">{{ $errors->first('video_path') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
  </div>
</form>
@stop