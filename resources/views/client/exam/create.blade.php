@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Exam </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Syllabus Management </li>
      <li class="active"> Manage Exam </li>
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
  @if(isset($exam->id))
    <form action="{{url('updateClientExam')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="exam_id" value="{{$exam->id}}">
  @else
   <form action="{{url('createClientExam')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="batch">Batch:</label>
      <div class="col-md-3">
        <div style="margin-bottom: 10px">
          @if(isset($exam->id))
            @if(0 == $exam->client_batch_id)
              <input type="text" class="form-control" name="batch_text" value="All" readonly>
              <input type="hidden" class="form-control" name="batch" value="0">
            @endif
            @if(count($batches) > 0)
              @foreach($batches as $batch)
                @if($exam->client_batch_id ==$batch->id)
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
    <div class="form-group row  @if ($errors->has('name')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="name">Exam Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="name" name="name" value="{{($exam->name)?$exam->name:null}}" placeholder="Exam Name" required>
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('subject')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="subject">Subject:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="subject" name="subject" value="{{($exam->subject)?$exam->subject:null}}" placeholder="subject" required>
        @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('topic')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="topic">Topic:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="topic" name="topic" value="{{($exam->topic)?$exam->topic:null}}" placeholder="topic" required>
        @if($errors->has('topic')) <p class="help-block">{{ $errors->first('topic') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label for="" class="col-sm-2 col-form-label">Date:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="date" id="date" @if(isset($exam->id)) value="{{$exam->date}}" @endif placeholder="Date" required>
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
        <input type="text"  class="form-control" name="from_time" id="from_time" @if(isset($exam->id)) value="{{$exam->from_time}}" @endif placeholder="From Time" required>
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
        <input type="text"  class="form-control" name="to_time" id="to_time" @if(isset($exam->id)) value="{{$exam->to_time}}" @endif placeholder="To Time" required>
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