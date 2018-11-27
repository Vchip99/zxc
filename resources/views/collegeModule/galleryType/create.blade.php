@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Gallery Types </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Gallery </li>
      <li class="active"> Manage Gallery Types </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container admin_div">
  @if(isset($galleryType->id))
    <form action="{{url('college/'.Session::get('college_user_url').'/updateCollegeGalleryType')}}" method="POST" id="submitForm">
    {{ method_field('PUT') }}
    <input type="hidden" id="gallery_type_id" name="gallery_type_id" value="{{$galleryType->id}}">
  @else
   <form action="{{url('college/'.Session::get('college_user_url').'/createCollegeGalleryType')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="name">Type Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="type_name" name="name" placeholder="name" value="{{$galleryType->name}}">
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      </div>
      <span class="hide" id="ttError" style="color: white;">Name already exist.Please enter another name.</span>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        @if(isset($galleryType->id) && $galleryType->created_by == Auth::user()->id)
          <button type="button" class="btn btn-primary" style="width: 90px !important;"  onclick="searchGalleryType();">Submit</button>
        @elseif(empty($galleryType->id))
          <button type="button" class="btn btn-primary" style="width: 90px !important;"  onclick="searchGalleryType();">Submit</button>
        @else
          <a href="{{url('college/'.Session::get('college_user_url').'/manageCollegeHoliday')}}" class="btn btn-primary" style="width: 90px !important;">Back</a>
        @endif
      </div>
    </div>
    </form>
  </div>
<script type="text/javascript">
  function searchGalleryType(){
    var name = document.getElementById('type_name').value;
    if(document.getElementById('gallery_type_id')){
      var galleryTypeId = document.getElementById('gallery_type_id').value;
    } else {
      var galleryTypeId = 0;
    }
    if(name){
      $.ajax({
        method:'POST',
        url: "{{url('isCollegeGalleryTypeExist')}}",
        data:{name:name,gallery_type_id:galleryTypeId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('ttError').classList.remove('hide');
          document.getElementById('ttError').classList.add('has-error');
        } else {
          document.getElementById('ttError').classList.add('hide');
          document.getElementById('ttError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    }else if(!name){
      alert('please enter name.');
    }
  }
</script>
@stop