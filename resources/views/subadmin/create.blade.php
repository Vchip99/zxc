@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Sub Admin </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-user"></i> Sub Admin </li>
      <li class="active"> Manage Sub Admin </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
  @if(isset($subadmin->id))
    <form action="{{url('admin/updateSubAdmin')}}" method="POST">
    {{method_field('PUT')}}
    <input type="hidden" name="subadmin_id" value="{{$subadmin->id}}">
  @else
    <form action="{{url('admin/createSubAdmin')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="name">Sub Admin Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="name" name="name" value="{{isset($subadmin->id)?$subadmin->name:NULL}}" required="true">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="email">Email:</label>
      <div class="col-sm-3">
        <input type="email" class="form-control" id="email" name="email" value="{{isset($subadmin->id)?$subadmin->email:NULL}}" required="true">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="password">Password:</label>
      <div class="col-sm-3">
        <input type="password" class="form-control" id="password" name="password" value="">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="confirm_password">Confirm Password:</label>
      <div class="col-sm-3">
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" value="">
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</form>
@stop