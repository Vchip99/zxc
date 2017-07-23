@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Sub Admin </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Sub Admin </li>
      <li class="active"> Manage Sub Admin </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
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
    <!-- <div class="form-group row @if ($errors->has('is_subadmin')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Sub Admin:</label>
      <div class="col-sm-3">
          @if(isset($course->id))
          <label class="radio-inline"><input type="radio" name="is_subadmin" value="1" @if(1 == $isSubadmin) checked="true" @endif> Yes</label>
          <label class="radio-inline"><input type="radio" name="is_subadmin" value="0" @if(0 == $isSubadmin) checked="true" @endif> No</label>
          @else
            <label class="radio-inline"><input type="radio" name="is_subadmin" value="1"> Yes</label>
          <label class="radio-inline"><input type="radio" name="is_subadmin" value="0" checked> No</label>
          @endif
        @if($errors->has('is_subadmin')) <p class="help-block">{{ $errors->first('is_subadmin') }}</p> @endif
      </div>
    </div> -->
    @if(count($allPermissions))
      @foreach($allPermissions as $Permission)
        <div class="form-group row">
          <label class="col-sm-2 col-form-label" for="confirm_password">{{$Permission->description}}:</label>
          <div class="col-sm-3" title="{{$Permission->description}}">
            @if(in_array($Permission->id, $subadminPermissions))
              <input class="form-check-input" name="permissions[]" type="checkbox" value="{{$Permission->id}}" checked>
            @else
              <input class="form-check-input" name="permissions[]" type="checkbox" value="{{$Permission->id}}">
            @endif
          </div>
        </div>
      @endforeach
    @endif
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</form>
@stop