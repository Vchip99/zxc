@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Batch </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Batch </li>
      <li class="active"> Manage Batch </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($batch->id))
    <form action="{{url('updateBatch')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="batch_id" value="{{$batch->id}}">
  @else
   <form action="{{url('createBatch')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('name')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="name">Batch Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="name" name="name" value="{{($batch)?$batch->name:null}}" required="true">
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
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
  $('#name').focus();
</script>
@stop