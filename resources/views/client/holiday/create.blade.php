@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Holidays </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Calendar Management </li>
      <li class="active"> Manage Holidays </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container admin_div">
    <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  @if(isset($holiday->id))
    <form action="{{url('updateClientHoliday')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="holiday_id" value="{{$holiday->id}}">
  @else
   <form action="{{url('createClientHoliday')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="batch">Batch:</label>
      <div class="col-md-3">
        <div style="margin-bottom: 10px">
          @if(isset($holiday->id))
            @if(0 == $holiday->client_batch_id)
              <input type="text" class="form-control" name="batch_text" value="All" readonly>
              <input type="hidden" class="form-control" name="batch" value="0">
            @endif
            @if(count($batches) > 0)
              @foreach($batches as $batch)
                @if($holiday->client_batch_id ==$batch->id)
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
    <div class="form-group row">
      <label for="" class="col-sm-2 col-form-label">Date:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="date" id="date" @if(isset($holiday->id)) value="{{$holiday->date}}" @endif placeholder="Date" required>
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
      <label class="col-sm-2 col-form-label" for="note">Note:</label>
      <div class="col-sm-3">
        <textarea type="text" class="form-control" id="note" name="note" placeholder="note">{{($holiday->note)?$holiday->note:null}}</textarea>
        @if($errors->has('note')) <p class="help-block">{{ $errors->first('note') }}</p> @endif
      </div>
      <div class="col-sm-6">* Only first 120 alphabets/letters will be send as sms if setting is on</div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
    </form>
  </div>
@stop