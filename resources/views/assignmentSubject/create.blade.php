@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Subject  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Assignment </li>
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
    <div class="form-group row  @if ($errors->has('subject')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="subject">Subject Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="subject" name="subject" value="{{($subject)?$subject->name:null}}" required="true">
        @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('year')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="subject">Year:</label>
      <div class="col-sm-3">
         <select class="form-control" id="year" name="year" required title="year">
          <option value="">Select Year</option>
          <option value="1" @if(isset($subject->year) && 1 == $subject->year)) selected="selected" @endif>First Year</option>
          <option value="2" @if(isset($subject->year) && 2 == $subject->year)) selected="selected" @endif>Second Year</option>
          <option value="3" @if(isset($subject->year) && 3 == $subject->year)) selected="selected" @endif>Third Year</option>
          <option value="4" @if(isset($subject->year) && 4 == $subject->year)) selected="selected" @endif>Fourth Year</option>
        </select>
        @if($errors->has('year')) <p class="help-block">{{ $errors->first('year') }}</p> @endif
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