@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Workshop Video </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-link"></i> Workshop </li>
      <li class="active"> Manage Workshop Video </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  &nbsp;
  <div class="container admin_div">
  @if(isset($workshopVideo->id))
    <form action="{{url('admin/updateWorkshopVideo')}}" method="POST">
    {{method_field('PUT')}}
    <input type="hidden" name="video_id" value="{{$workshopVideo->id}}">
  @else
    <form action="{{url('admin/createWorkshopVideo')}}" method="POST">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('category')) has-error @endif">
    <label class="col-sm-2 col-form-label">Category Name:</label>
    <div class="col-sm-3">
      <select id="category" class="form-control" name="category" onChange="selectWorkshop(this);" required title="Category">
          <option value="">Select Category</option>
          @if(count($workshopCategories) > 0 && isset($workshopVideo->id)))
            @foreach($workshopCategories as $workshopCategory)
              @if( isset($workshopVideo->id) && $workshopVideo->workshop_category_id == $workshopCategory->id)
                <option value="{{$workshopCategory->id}}" selected="true">{{$workshopCategory->name}}</option>
              @else
                <option value="{{$workshopCategory->id}}">{{$workshopCategory->name}}</option>
              @endif
            @endforeach
          @else
            @foreach($workshopCategories as $workshopCategory)
                <option value="{{$workshopCategory->id}}">{{$workshopCategory->name}}</option>
            @endforeach
          @endif
      </select>
      @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('workshop')) has-error @endif">
    <label class="col-sm-2 col-form-label">Workshop Name:</label>
    <div class="col-sm-3">
      <select id="workshop" class="form-control" name="workshop" required title="Workshop">
        <option value="">Select Workshop</option>
        @if(count($workshopDetails) > 0 && isset($workshopVideo->id))
          @foreach($workshopDetails as $workshopDetail)
            @if($workshopVideo->workshop_details_id == $workshopDetail->id)
                <option value="{{$workshopDetail->id}}" selected="true">{{$workshopDetail->name}}</option>
              @else
                <option value="{{$workshopDetail->id}}">{{$workshopDetail->name}}</option>
            @endif
          @endforeach
        @endif
      </select>
      @if($errors->has('workshop')) <p class="help-block">{{ $errors->first('workshop') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('name')) has-error @endif">
    <label for="name" class="col-sm-2 col-form-label">Video Name:</label>
    <div class="col-sm-3">
      @if(isset($workshopVideo->id))
        <input type="text" class="form-control" name="name" value="{{$workshopVideo->name}}" placeholder="Video name" required="true">
      @else
        <input type="text" class="form-control" name="name" value="" placeholder="Video name" required="true">
      @endif
      @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('description')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Video Description:</label>
      <div class="col-sm-3">
        @if(isset($workshopVideo->id))
          <textarea type="text" class="form-control" name="description" required="true">{{$workshopVideo->description}}</textarea>
        @else
          <textarea type="text" class="form-control" name="description" required="true"></textarea>
        @endif
        @if($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
      </div>
    </div>
  <div class="form-group row @if ($errors->has('duration')) has-error @endif">
    <label for="name" class="col-sm-2 col-form-label">Total Time:</label>
    <div class="col-sm-3">
      @if(isset($workshopVideo->id))
        <input type="text" class="form-control" name="duration" value="{{$workshopVideo->duration}}"  placeholder="time in seconds" required="true">
      @else
        <input type="text" class="form-control" name="duration" value="" placeholder="duration in seconds" required="true">
      @endif
      @if($errors->has('duration')) <p class="help-block">{{ $errors->first('duration') }}</p> @endif
    </div>
  </div>
  <div class="form-group row">
      <label for="course" class="col-sm-2 col-form-label">Video Path</label>
      <div class="col-sm-3">
        <input type="text" class="form-control"  name="video_path" value="{{($workshopVideo->video_path)?$workshopVideo->video_path:NULL}}" placeholder="Please add video path with iframe" required="true">
      </div>
    </div>
  <div class="form-group row">
      <label for="date" class="col-sm-2 col-form-label">Start Time:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="date" id="date" @if(isset($workshopVideo->id)) value="{{$workshopVideo->date}}" @endif required="true" placeholder="date" required="true">
      </div>
      <script type="text/javascript">
          $(function () {
              $('#date').datetimepicker({format: 'YYYY-MM-DD hh:mm:ss'});
          });
      </script>
  </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</form>

<script type="text/javascript">
  function selectWorkshop(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('admin/getWorkshopsByCategory')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('workshop');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Workshop';
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
</script>
@stop