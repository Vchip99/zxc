@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Offline Paper  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Batch </li>
      <li class="active"> Manage  Offline Paper </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($paper->id))
    <form action="{{url('updateOfflinePaper')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="paper_id" value="{{$paper->id}}">
  @else
   <form action="{{url('createOfflinePaper')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="batch">Batch Name:</label>
      <div class="col-sm-3">
        @if(isset($paper->id))
          @if(count($batches) > 0)
            @foreach($batches as $batch)
              @if($batch->id == $paper->client_batch_id)
                <input type="text" class="form-control" name="batch_text" id="batch" value="{{$batch->name}}" readonly>
                <input type="hidden" name="batch" value="{{$batch->id}}">
              @endif
            @endforeach
          @endif
        @else
          <select class="form-control" name="batch" id="batch" required>
            <option value="">Select Batch</option>
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
    <div class="form-group row  @if ($errors->has('name')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="name">Paper Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="name" name="name" value="{{($paper)?$paper->name:null}}" required="true">
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('marks')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="marks">Marks:</label>
      <div class="col-sm-3">
        @if(isset($paper->id))
          <input type="text" class="form-control" id="marks" name="marks" value="{{($paper)?$paper->marks:null}}" required="true" readonly>
        @else
          <input type="text" class="form-control" id="marks" name="marks" value="{{($paper)?$paper->marks:null}}" required="true">
        @endif
        @if($errors->has('marks')) <p class="help-block">{{ $errors->first('marks') }}</p> @endif
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
    $('#batch').focus();
  </script>
@stop