@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Class </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Calendar Management </li>
      <li class="active"> Manage Class </li>
    </ol>
  </section>
  <style type="text/css">
    .timepicker-picker .btn-primary{
      width: 50px !important;
    }
  </style>
@stop
@section('dashboard_content')
  <div class="container admin_div">
    <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  @if(isset($class->id))
    <form action="{{url('updateClientClass')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="class_id" value="{{$class->id}}">
  @else
   <form action="{{url('createClientClass')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="batch">Batch:</label>
      <div class="col-md-3">
        <div style="margin-bottom: 10px">
          @if(isset($class->id))
            @if(0 == $class->client_batch_id)
              <input type="text" class="form-control" name="batch_text" value="All" readonly>
              <input type="hidden" class="form-control" name="batch" value="0">
            @endif
            @if(count($batches) > 0)
              @foreach($batches as $batch)
                @if($class->client_batch_id ==$batch->id)
                  <input type="text" class="form-control" name="batch_text" value="{{$batch->name}}" readonly>
                  <input type="hidden" class="form-control" name="batch" value="{{$batch->id}}">
                @endif
              @endforeach
            @endif
          @else
            <select class="form-control" name="batch" id="batch" required>
              <option value="">Select Batch</option>
              <option value="0">All</option>
              @if(count($batches) > 0)
                  @foreach($batches as $batch)
                    <option value="{{$batch->id}}">{{$batch->name}}</option>
                  @endforeach
              @endif
            </select>
          @endif
        </div>
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('teacher')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="teacher">Teacher:</label>
      <div class="col-md-3">
        <div style="margin-bottom: 10px">
          @if(isset($class->id))
            @if(0 == $class->clientuser_id)
              <input type="text" class="form-control" name="teacher_text" value="{{$clientName}}" readonly>
              <input type="hidden" class="form-control" name="teacher" value="0">
            @endif
            @if(count($teachers) > 0)
              @foreach($teachers as $teacher)
                @if($class->clientuser_id ==$teacher->id)
                  <input type="text" class="form-control" name="teacher_text" value="{{$teacher->name}}" readonly>
                  <input type="hidden" class="form-control" name="teacher" value="{{$teacher->id}}">
                @endif
              @endforeach
            @endif
          @else
            <select class="form-control" name="teacher" id="teacher" required>
              <option value="">Select Teacher</option>
              <option value="0">{{$clientName}}</option>
              @if(count($teachers) > 0)
                @foreach($teachers as $teacher)
                  <option value="{{$teacher->id}}">{{$teacher->name}}</option>
                @endforeach
              @endif
            </select>
          @endif
        </div>
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('subject')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="subject">Subject:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="subject" name="subject" value="{{($class->subject)?$class->subject:null}}" placeholder="subject" required>
        @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('topic')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="topic">Topic:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="topic" name="topic" value="{{($class->topic)?$class->topic:null}}" placeholder="topic" required>
        @if($errors->has('topic')) <p class="help-block">{{ $errors->first('topic') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label for="" class="col-sm-2 col-form-label">Date:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="date" id="date" @if(isset($class->id)) value="{{$class->date}}" @endif placeholder="Date" required>
      </div>
      <script type="text/javascript">
          $(function () {
              $('#date').datetimepicker({
                format: 'YYYY-MM-DD'
              });
          });
      </script>
    </div>
    <div class="form-group row">
      <label for="" class="col-sm-2 col-form-label">From Time:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="from_time" id="from_time" @if(isset($class->id)) value="{{$class->from_time}}" @endif placeholder="From Time" required>
      </div>
      <script type="text/javascript">
          $(function () {
              $('#from_time').datetimepicker({
                format: 'LT'
              });
          });
      </script>
    </div>
    <div class="form-group row">
      <label for="" class="col-sm-2 col-form-label">To Time:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="to_time" id="to_time" @if(isset($class->id)) value="{{$class->to_time}}" @endif placeholder="To Time" required>
      </div>
      <script type="text/javascript">
          $(function () {
              $('#to_time').datetimepicker({
                format: 'LT'
              });
          });
      </script>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
    </form>
  </div>
@stop