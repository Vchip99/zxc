@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Event/Message </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-envelope"></i> Event/Message </li>
      <li class="active"> Manage Event/Message </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
    <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  <div class="container admin_div">
  @if(isset($message->id))
    <form action="{{url('college/'.Session::get('college_user_url').'/updateMessage')}}" method="POST" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    <input type="hidden" name="message_id" value="{{$message->id}}">
  @else
   <form action="{{url('college/'.Session::get('college_user_url').'/createMessage')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('photo')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="photo">Type:</label>
      <div class="col-sm-3">
        <input type="radio" name="type" value="1" checked onClick="toggleType(this.value)"> Message
        <input type="radio" name="type" value="0" onClick="toggleType(this.value)"> Event
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('departments')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="departments">Departments:</label>
      <div class="col-sm-3">
        @if(isset($message->id))
          @if(count($departments) > 0)
            @php
              $depts = explode(',',$message->college_dept_ids);
            @endphp
            @foreach($departments as $department)
              @if(in_array($department->id,$depts))
                <input type="text" class="form-control" name="department_text" value="{{$department->name}}" readonly>
                <input type="hidden" name="departments[]" value="{{$department->id}}">
              @endif
            @endforeach
          @endif
        @else
          <select class="form-control" name="departments[]" id="departments" required multiple>
            <option value="">Select Departments</option>
            @if(count($departments) > 0)
              @foreach($departments as $department)
                <option value="{{$department->id}}">{{$department->name}}</option>
              @endforeach
            @endif
          </select>
        @endif
        @if($errors->has('departments')) <p class="help-block">{{ $errors->first('departments') }}</p> @endif
      </div>
    </div>
    @if(!empty($message->start_date) && !empty($message->end_date))
      <div class="form-group row hide" id="yearDiv">
    @else
      <div class="form-group row" id="yearDiv">
    @endif
        <label class="col-sm-2 col-form-label" for="years">Years:</label>
        <div class="col-sm-3">
          @if(isset($message->id))
            @if(count($years) > 0)
              @php
                $msgYears = explode(',',$message->years);
              @endphp
              @foreach($years as $year)
                @if(in_array($year,$msgYears))
                  <input type="text" class="form-control" name="years_text" value="{{$year}}" readonly>
                  <input type="hidden" name="years[]" value="{{$year}}">
                @endif
              @endforeach
            @endif
          @else
            <select class="form-control" name="years[]" id="years" required multiple>
              <option value="">Select Years</option>
              @if(count($years) > 0)
                @foreach($years as $year)
                  <option value="{{$year}}">{{$year}}</option>
                @endforeach
              @endif
            </select>
          @endif
          @if($errors->has('years')) <p class="help-block">{{ $errors->first('years') }}</p> @endif
        </div>
      </div>
    <div class="form-group row  @if ($errors->has('photo')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="photo">Photo:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control" id="photo" name="photo">
      </div>
    </div>
    @if(isset($message->id))
      <div class="form-group row">
        <label class="col-sm-2 col-form-label" for="">Existing Photo:</label>
        <div class="col-sm-3">
          {{ basename($message->photo) }}
        </div>
      </div>
    @endif
    <div class="form-group row  @if ($errors->has('message')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="message">Message:</label>
      <div class="col-sm-6">
        <textarea class="form-control" id="message" name="message" required="true" rows="5">{{($message)?$message->message:null}}</textarea>
        @if($errors->has('message')) <p class="help-block">{{ $errors->first('message') }}</p> @endif
      </div>
    </div>
    @if(!empty($message->start_date) && !empty($message->end_date))
      <div id="dateDiv" class="">
    @else
      <div id="dateDiv" class="hide">
    @endif
      <div class="form-group row  @if ($errors->has('start_date')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="start_date">Start Date:</label>
        <div class="col-sm-3">
          <input type="text"  class="form-control" name="start_date" id="start_date" @if(isset($message->id)) value="{{$message->start_date}}" @endif placeholder="Start Date">
        </div>
        <script type="text/javascript">
            $(function () {
                $('#start_date').datetimepicker({
                  format: 'YYYY-MM-DD HH:mm'
                });
            });
        </script>
      </div>
      <div class="form-group row  @if ($errors->has('end_date')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="end_date">End Date:</label>
        <div class="col-sm-3">
          <input type="text"  class="form-control" name="end_date" id="end_date" @if(isset($message->id)) value="{{$message->end_date}}" @endif placeholder="End Date">
        </div>
        <script type="text/javascript">
            $(function () {
                $('#end_date').datetimepicker({
                  format: 'YYYY-MM-DD HH:mm'
                });
            });
        </script>
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
  function toggleType(type){
    if(1 == type){
      $('#yearDiv').removeClass('hide');
      $('#dateDiv').addClass('hide');
      $('#start_date').prop('required', false);
      $('#end_date').prop('required', false);
      $('#years').prop('required', true);
    } else {
      $('#yearDiv').addClass('hide');
      $('#dateDiv').removeClass('hide');
      $('#start_date').prop('required', true);
      $('#end_date').prop('required', true);
      $('#years').prop('required', false);
    }
  }
</script>
@stop