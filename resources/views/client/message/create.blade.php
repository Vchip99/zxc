@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Message </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-envelope"></i> Message </li>
      <li class="active"> Manage Message </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($message->id))
    <form action="{{url('updateMessage')}}" method="POST" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    <input type="hidden" name="message_id" value="{{$message->id}}">
  @else
   <form action="{{url('createMessage')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="batch">Batch Name:</label>
      <div class="col-sm-3">
        @if(isset($message->id))
          @if(0 == $message->client_batch_id || empty($message->client_batch_id))
            <input type="text" class="form-control" name="batch_text" value="All" readonly>
            <input type="hidden" name="batch" value="0">
          @else
            @if(count($batches) > 0)
              @foreach($batches as $batch)
                @if($batch->id == $message->client_batch_id)
                  <input type="text" class="form-control" name="batch_text" value="{{$batch->name}}" readonly>
                  <input type="hidden" name="batch" value="{{$batch->id}}">
                @endif
              @endforeach
            @endif
          @endif
        @else
          <select class="form-control" name="batch" id="batch" required>
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
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
    </form>
  </div>
@stop