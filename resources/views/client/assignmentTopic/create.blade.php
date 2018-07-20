@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Topic  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-tasks"></i> Assignment </li>
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
    <div class="form-group row  @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="batch">Batch Name:</label>
      <div class="col-sm-3">
        @if(isset($topic->id))
          @if(0 == $topic->client_batch_id || empty($topic->client_batch_id))
            <input type="text" class="form-control" name="batch_text" value="All" readonly>
            <input type="hidden" name="batch" value="0">
          @else
            @if(count($batches) > 0)
              @foreach($batches as $batch)
                @if($batch->id == $topic->client_batch_id)
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
        @if(isset($topic->id) && count($subjects) > 0)
          @foreach($subjects as $subject)
            @if($topic->client_assignment_subject_id == $subject->id)
              <input type="text" class="form-control" name="subject_text" value="{{$subject->name}}" readonly>
              <input type="hidden" name="subject" value="{{$subject->id}}">
            @endif
          @endforeach
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
</script>
@stop