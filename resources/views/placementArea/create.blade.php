@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Placement Area </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-gift"></i> Placement </li>
      <li class="active"> Manage Placement Area </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($placementArea->id))
    <form action="{{url('admin/updatePlacementArea')}}" method="POST" id="submitForm">
    {{ method_field('PUT') }}
    <input type="hidden" name="area_id" id="area_id" value="{{$placementArea->id}}">
  @else
   <form action="{{url('admin/createPlacementArea')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('area')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="area">Placement Area Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="area" name="area" value="{{($placementArea)?$placementArea->name:null}}" required="true">
        @if($errors->has('area')) <p class="help-block">{{ $errors->first('area') }}</p> @endif
        <span class="hide" id="areaError" style="color: white;">Given name is already exist.Please enter another name.</span>
      </div>
    </div>

    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" onclick="searchArea();">Submit</button>
      </div>
    </div>
    </form>
  </div>
<script type="text/javascript">
  function searchArea(){
    var area = document.getElementById('area').value;
    if(document.getElementById('area_id')){
      var areaId = document.getElementById('area_id').value;
    } else {
      var areaId = 0;
    }
    if(area){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isPlacementAreaExist')}}",
        data:{area:area,area_id:areaId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('areaError').classList.remove('hide');
          document.getElementById('areaError').classList.add('has-error');
        } else {
          document.getElementById('areaError').classList.add('hide');
          document.getElementById('areaError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else {
      alert('please enter name.');
    }
  }
</script>
@stop