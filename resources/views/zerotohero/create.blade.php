@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Zero To Hero </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-asterisk"></i> Zero To Hero </li>
      <li class="active"> Manage Zero To Hero </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
  <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
  <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  &nbsp;
  <div class="container admin_div">
  @if(isset($hero->id))
    <form action="{{url('admin/updateZeroToHero')}}" method="POST" id="submitForm">
    {{ method_field('PUT') }}
    <input type="hidden" name="hero_id" id="hero_id" value="{{$hero->id}}">
  @else
    <form action="{{url('admin/createZeroToHero')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('designation')) has-error @endif">
      <label class="col-sm-2 col-form-label">Designation Name:</label>
      <div class="col-sm-3">
        <select class="form-control" id="designation" name="designation" required title="Designation"  onChange="selectArea();">
            <option value="">Select Designation</option>
            @if(count($designations) > 0)
              @foreach($designations as $designation)
                @if( $hero->designation_id == $designation->id)
                  <option value="{{$designation->id}}" selected="true">{{$designation->name}}</option>
                @else
                  <option value="{{$designation->id}}">{{$designation->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
          @if($errors->has('designation')) <p class="help-block">{{ $errors->first('designation') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('area')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="area">Area Name:</label>
      <div class="col-sm-3">
        <select class="form-control" id="area" name="area" required title="Area">
          <option value="">Select Area</option>
          @if(count($areas) > 0)
            @foreach($areas as $area)
              @if( $hero->area_id == $area->id)
                <option value="{{$area->id}}" selected="true">{{$area->name}}</option>
              @else
                <option value="{{$area->id}}">{{$area->name}}</option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('area')) <p class="help-block">{{ $errors->first('area') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('hero')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="hero">Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="hero" name="hero" value="{{($hero)?$hero->name:null}}" required="true">
        @if($errors->has('hero')) <p class="help-block">{{ $errors->first('hero') }}</p> @endif
        <span class="hide" id="heroError" style="color: white;">Given name is already exist with selected designation and area.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('url')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="url">Url:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="url" name="url" value="{{($hero)?$hero->url:null}}" required="true">
        @if($errors->has('url')) <p class="help-block">{{ $errors->first('url') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label for="release_date" class="col-sm-2 col-form-label">Release Date:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="release_date" id="release_date" @if(isset($hero->id)) value="{{$hero->release_date}}" @endif required="true">
      </div>
      <script type="text/javascript">
          $(function () {
              $('#release_date').datetimepicker({format: 'YYYY-MM-DD hh:mm'});
          });
      </script>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" onclick="searchHero();">Submit</button>
      </div>
    </div>
    </form>
  </div>
<script type="text/javascript">
  function selectArea(){
    var designationId = parseInt(document.getElementById('designation').value);
    if( 0 < designationId ){
      $.ajax({
          method: "POST",
          url: "{{url('admin/getAreasByDesignation')}}",
          data: {designation_id:designationId}
      })
      .done(function( msg ) {
        select = document.getElementById('area');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '0';
        opt.innerHTML = 'Select Area';
        select.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
      });
    }
  }

  function searchHero(){
    var designation = document.getElementById('designation').value;
    var area = document.getElementById('area').value;
    var hero = document.getElementById('hero').value;
    if(document.getElementById('hero_id')){
      var heroId = document.getElementById('hero_id').value;
    } else {
      var heroId = 0;
    }
    if(area && designation && hero){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isHeroExist')}}",
        data:{area:area,designation:designation,hero:hero,hero_id:heroId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('heroError').classList.remove('hide');
          document.getElementById('heroError').classList.add('has-error');
        } else {
          document.getElementById('heroError').classList.add('hide');
          document.getElementById('heroError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else if(!designation){
      alert('please select designation.');
    } else if(!area){
      alert('please select area.');
    } else {
      alert('please enter name.');
    }
  }
</script>
@stop