@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Placement Company </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-gift"></i> Placement </li>
      <li class="active"> Manage Placement Company </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($placementCompany->id))
    <form action="{{url('admin/updatePlacementCompany')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" name="company_id" id="company_id" value="{{$placementCompany->id}}">
  @else
    <form action="{{url('admin/createPlacementCompany')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('area')) has-error @endif">
    <label class="col-sm-2 col-form-label">Placement Area:</label>
    <div class="col-sm-3">
      <select class="form-control" name="area" id="area" required title="Placement Area">
          <option value="">Select Placement Area</option>
          @if(count($placementAreas) > 0)
            @foreach($placementAreas as $placementArea)
              @if( $placementCompany->placement_area_id == $placementArea->id)
                <option value="{{$placementArea->id}}" selected="true">{{$placementArea->name}}</option>
              @else
                <option value="{{$placementArea->id}}">{{$placementArea->name}}</option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('area')) <p class="help-block">{{ $errors->first('area') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('company')) has-error @endif">
    <label for="company" class="col-sm-2 col-form-label">Placement Company Name:</label>
    <div class="col-sm-3">
      @if(isset($placementCompany))
        <input type="text" class="form-control" name="company" id="company" value="{{$placementCompany->name}}" required="true">
      @else
        <input type="text" class="form-control" name="company" id="company" value="" required="true">
      @endif
      @if($errors->has('company')) <p class="help-block">{{ $errors->first('company') }}</p> @endif
      <span class="hide" id="companyError" style="color: white;">Given company is already exist with selected  area.Please enter another name.</span>
    </div>
  </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" onclick="searchCompany();">Submit</button>
      </div>
    </div>
  </div>
</form>
<script type="text/javascript">
  function searchCompany(){
    var area = document.getElementById('area').value;
    var company = document.getElementById('company').value;
    if(document.getElementById('company_id')){
      var companyId = document.getElementById('company_id').value;
    } else {
      var companyId = 0;
    }
    if(area && company){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isPlacementCompanyExist')}}",
        data:{area:area,company:company,company_id:companyId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('companyError').classList.remove('hide');
          document.getElementById('companyError').classList.add('has-error');
        } else {
          document.getElementById('companyError').classList.add('hide');
          document.getElementById('companyError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else {
      alert('please enter name.');
    }
  }
</script>
@stop