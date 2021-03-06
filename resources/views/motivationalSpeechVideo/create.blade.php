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
    <form action="{{url('admin/updateMotivationalSpeechVideo')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" id="motivational_video_id" name="motivational_video_id" value="{{$motivationalSpeechVideo->id}}">
  @else
    <form action="{{url('admin/createMotivationalSpeechVideo')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('motivational_speech_category_id')) has-error @endif">
      <label class="col-sm-2 col-form-label">Speaker Name:</label>
      <div class="col-sm-3">
        @if(count($motivationalSpeechCategories) > 0 && isset($motivationalSpeechVideo->id))
          @foreach($motivationalSpeechCategories as $motivationalSpeechCategory)
            @if( isset($motivationalSpeechVideo->id) && $motivationalSpeechVideo->motivational_speech_category_id == $motivationalSpeechCategory->id)
              <input type="text" class="form-control" name="category_text" value="{{$motivationalSpeechCategory->name}}" readonly="true">
              <input type="hidden" name="motivational_speech_category_id" id="motivational_speech_category_id" value="{{$motivationalSpeechCategory->id}}">
            @endif
          @endforeach
        @else
          <select id="motivational_speech_category_id" class="form-control" name="motivational_speech_category_id" title="Speaker" onchange="selectSpeech(this.value);" required>
          <option value="">Select Speaker</option>
            @foreach($motivationalSpeechCategories as $motivationalSpeechCategory)
              @if( isset($motivationalSpeechVideo->id) && $motivationalSpeechVideo->motivational_speech_category_id == $motivationalSpeechCategory->id)
                <option value="{{$motivationalSpeechCategory->id}}" selected="true">{{$motivationalSpeechCategory->name}}</option>
              @else
                <option value="{{$motivationalSpeechCategory->id}}">{{$motivationalSpeechCategory->name}}</option>
              @endif
            @endforeach
          </select>
        @endif
        @if($errors->has('motivational_speech_category_id')) <p class="help-block">{{ $errors->first('motivational_speech_category_id') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('motivational_speech_detail_id')) has-error @endif">
      <label class="col-sm-2 col-form-label">Motivational Speech Name:</label>
      <div class="col-sm-3">
        @if(count($motivationalSpeechDetails) > 0 && isset($motivationalSpeechVideo->id))
          @foreach($motivationalSpeechDetails as $motivationalSpeechDetail)
            @if( isset($motivationalSpeechVideo->id) && $motivationalSpeechVideo->motivational_speech_detail_id == $motivationalSpeechDetail->id)
              <input type="text" class="form-control" name="speech_text" value="{{$motivationalSpeechDetail->name}}" readonly="true">
              <input type="hidden" name="motivational_speech_detail_id" id="motivational_speech_detail_id" value="{{$motivationalSpeechDetail->id}}">
            @endif
          @endforeach
        @else
          <select id="motivational_speech_detail_id" class="form-control" name="motivational_speech_detail_id" title="Motivational Speech" required >
          <option value="">Select Motivational Speech</option>
            <!-- @foreach($motivationalSpeechDetails as $motivationalSpeechDetail)
              @if( isset($motivationalSpeechVideo->id) && $motivationalSpeechVideo->motivational_speech_detail_id == $motivationalSpeechDetail->id)
                <option value="{{$motivationalSpeechDetail->id}}" selected="true">{{$motivationalSpeechDetail->name}}</option>
              @else
                <option value="{{$motivationalSpeechDetail->id}}">{{$motivationalSpeechDetail->name}}</option>
              @endif
            @endforeach -->
          </select>
        @endif
        @if($errors->has('motivational_speech_detail_id')) <p class="help-block">{{ $errors->first('motivational_speech_detail_id') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('name')) has-error @endif">
      <label for="name" class="col-sm-2 col-form-label">Motivational Video Name:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="name" id="video" value="{{$motivationalSpeechVideo->name}}" required="true">
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
        <span class="hide" id="videoError" style="color: white;">Given name is already exist with selected speaker and speech.Please enter another name.</span>
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
          <button type="button" class="btn btn-primary" onclick="searchVideo();">Submit</button>
        </div>
      </div>
  </div>
</form>
<script type="text/javascript">
  function selectSpeech(speaker){
    if(speaker){
      $.ajax({
        method:'POST',
        url: "{{url('admin/getMotivationalSpeechesByCategoryByAdmin')}}",
        data:{category:speaker}
      }).done(function( msg ) {
        selectSub = document.getElementById('motivational_speech_detail_id');
        selectSub.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Motivational Speech';
        selectSub.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              selectSub.appendChild(opt);
          });
        }
      });
    }
  }

  function searchVideo(){
    var category = document.getElementById('motivational_speech_category_id').value;
    var speech = document.getElementById('motivational_speech_detail_id').value;
    var video = document.getElementById('video').value;

    if(document.getElementById('motivational_video_id')){
      var videoId = document.getElementById('motivational_video_id').value;
    } else {
      var videoId = 0;
    }
    if(category && speech && video){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isMotivationalSpeechVideoExist')}}",
        data:{category:category,speech:speech,video:video,video_id:videoId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('videoError').classList.remove('hide');
          document.getElementById('videoError').classList.add('has-error');
        } else {
          document.getElementById('videoError').classList.add('hide');
          document.getElementById('videoError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else if(!category){
      alert('please select speaker.');
    } else if(!speech){
      alert('please select speech.');
    } else {
      alert('please enter name.');
    }
    }
</script>
@stop