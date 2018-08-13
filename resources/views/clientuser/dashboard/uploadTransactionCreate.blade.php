@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('module_title')
  <section class="content-header">
    <h1> Uploaded Transactions </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Payments</li>
      <li class="active"> Uploaded Transactions </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
<div class="content-wrapper v-container tab-content" >
  <div class="container admin_div">
   <form action="{{url('createUploadTransaction')}}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="batch">Batch Name:</label>
      <div class="col-sm-3">
          <select class="form-control" name="batch" id="batch" required>
            <option value="">Select Batch</option>
            @if(count($batches) > 0)
              @foreach($batches as $batch)
                <option value="{{$batch->id}}">{{$batch->name}}</option>
              @endforeach
            @endif
          </select>
        @if($errors->has('batch')) <p class="help-block">{{ $errors->first('batch') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('photo')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="photo">Photo:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control" id="photo" name="photo" required>
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('comment')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="comment">Comment:</label>
      <div class="col-sm-6">
        <textarea class="form-control" id="comment" name="comment" required="true" rows="5"></textarea>
        @if($errors->has('comment')) <p class="help-block">{{ $errors->first('comment') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
    </form>
  </div>
</div>
@stop