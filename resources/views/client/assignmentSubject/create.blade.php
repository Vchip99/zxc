@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Subject  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-tasks"></i> Assignment </li>
      <li class="active"> Manage Subject </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($subject->id))
    <form action="{{url('updateAssignmentSubject')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="subject_id" value="{{$subject->id}}">
  @else
   <form action="{{url('createAssignmentSubject')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="batch">Batch Name:</label>
      <div class="col-sm-3">
        @if(isset($subject->id))
          @if(0 == $subject->client_batch_id || empty($subject->client_batch_id))
            <input type="text" class="form-control" name="batch_text" value="All" readonly>
            <input type="hidden" name="batch" value="0">
          @else
            @if(count($batches) > 0)
              @foreach($batches as $batch)
                @if($batch->id == $subject->client_batch_id)
                  <input type="text" class="form-control" name="batch_text" value="{{$batch->name}}" readonly>
                  <input type="hidden" name="batch" value="{{$batch->id}}">
                @endif
              @endforeach
            @endif
          @endif
        @else
          <select class="form-control" name="batch" id="batch">
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
    <div class="form-group row  @if ($errors->has('subject')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="subject">Subject Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="subject" name="subject" value="{{($subject)?$subject->name:null}}" required="true">
        @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
    </form>
  </div>
@stop