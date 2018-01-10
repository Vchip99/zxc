@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Projects </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-table"></i> Vkit </li>
      <li class="active"> Manage Projects </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  &nbsp;
  <div class="container admin_div">
  @if(isset($project->id))
    <form action="{{url('admin/updateVkitProject')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" name="project_id" id="project_id" value="{{$project->id}}">
  @else
    <form action="{{url('admin/createVkitProject')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('category_id')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="category_id">Category:</label>
      <div class="col-sm-3">
        <select class="form-control" id="category_id" name="category_id" required="true" title="Category">
          <option value="">Select Category ...</option>
          @if(count($vkitCategories) > 0)
            @foreach($vkitCategories as $vkitCategory)
              @if(isset($project->category_id) && $project->category_id == $vkitCategory->id)
                <option value="{{$vkitCategory->id}}" selected="true">{{$vkitCategory->name}}</option>
              @else
                <option value="{{$vkitCategory->id}}">{{$vkitCategory->name}}</option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('category_id')) <p class="has-error">{{ $errors->first('category_id') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('project')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="project">Project Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="project" name="project" value="{{($project->name)?$project->name:NULL}}" required="true">
        @if($errors->has('project')) <p class="has-error">{{ $errors->first('project') }}</p> @endif
        <span class="hide" id="projectError" style="color: white;">Given name is already exist with selected category.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('author')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="author">Author:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="author" name="author" value="{{($project->author)?$project->author:NULL}}" required="true">
        @if($errors->has('author')) <p class="has-error">{{ $errors->first('author') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="introduction">Introduction:</label>
      <div class="col-sm-3">
        <textarea class="form-control" id="introduction" name="introduction" required="true">{{($project->introduction)?$project->introduction:NULL}}</textarea>
        @if($errors->has('introduction')) <p class="has-error">{{ $errors->first('introduction') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('gateway')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="gateway">Gateway:</label>
      <div class="col-sm-3">
        <select class="form-control" id="gateway" name="gateway" required="true" title="Gateway">
          <option  value="">Select Gateway ...</option>
          <option value="1" @if(1 == $project->gateway) selected @endif >Android</option>
          <option value="2" @if(2 == $project->gateway) selected @endif >Raspberry-pi</option>
          <option value="3" @if(3 == $project->gateway) selected @endif >Intel galileo</option>
        </select>
        @if($errors->has('gateway')) <p class="has-error">{{ $errors->first('gateway') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('microcontroller')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="microcontroller">Micro Controller:</label>
      <div class="col-sm-3">
        <select class="form-control" id="microcontroller" name="microcontroller" required="true" title="Micro Controller">
          <option  value="">Select Micro Controller ...</option>
          <option value="1" @if(1 == $project->microcontroller) selected @endif >AVR</option>
          <option value="2" @if(2 == $project->microcontroller) selected @endif >Atmega328</option>
          <option value="3" @if(3 == $project->microcontroller) selected @endif >8051/8052</option>
        </select>
        @if($errors->has('microcontroller')) <p class="has-error">{{ $errors->first('microcontroller') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('front_image')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="front_image">Project Front Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="front_image" id="front_image" >
        @if($errors->has('front_image')) <p class="has-error">{{ $errors->first('front_image') }}</p> @endif
        @if(isset($project->front_image_path))
          <b><span>Existing Image: {!! basename($project->front_image_path) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('header_image')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="header_image">Header Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="header_image" id="header_image" >
        @if($errors->has('header_image')) <p class="has-error">{{ $errors->first('header_image') }}</p> @endif
        @if(isset($project->header_image_path))
          <b><span>Existing Image: {!! basename($project->header_image_path) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('pdf')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="pdf">Project Pdf:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="pdf" id="pdf" >
        @if(isset($project->project_pdf_path))
          <b><span>Existing Pdf: {!! basename($project->project_pdf_path) !!}</span></b>
        @endif
        @if($errors->has('pdf')) <p class="has-error">{{ $errors->first('pdf') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('date')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="date">Date:</label>
      <div class="col-sm-3">
        <input type="date" class="form-control"  name="date" id="date" value="{{($project->date)?$project->date:NULL}}" required="true">
        @if($errors->has('date')) <p class="has-error">{{ $errors->first('date') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('description')) has-error @endif">
      <label class="col-sm-2 col-form-label">Description:</label>
      <div class="col-sm-10">
        <textarea name="description" placeholder="Answer 1" type="text" id="description" required>{{($project->description)?$project->description:NULL}}
        </textarea>
        @if($errors->has('description')) <p class="has-error">{{ $errors->first('description') }}</p> @endif
        <script type="text/javascript">
          CKEDITOR.replace( 'description' );
          CKEDITOR.config.width="100%";
          CKEDITOR.config.height="auto";
          CKEDITOR.on('dialogDefinition', function (ev) {

              var dialogName = ev.data.name,
                  dialogDefinition = ev.data.definition;

              if (dialogName == 'image') {
                  var onOk = dialogDefinition.onOk;

                  dialogDefinition.onOk = function (e) {
                      var width = this.getContentElement('info', 'txtWidth');
                      width.setValue('100%');//Set Default Width

                      var height = this.getContentElement('info', 'txtHeight');
                      height.setValue('400');////Set Default height

                      onOk && onOk.apply(this, e);
                  };
              }
          });
        </script>
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" onclick="searchProject();">Submit</button>
      </div>
    </div>
  </div>
</form>
<script type="text/javascript">
  function searchProject(){
    var category = document.getElementById('category_id').value;
    var project = document.getElementById('project').value;
    if(document.getElementById('project_id')){
      var projectId = document.getElementById('project_id').value;
    } else {
      var projectId = 0;
    }
    if(category && project){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isVkitProjectExist')}}",
        data:{category:category,project:project,project_id:projectId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('projectError').classList.remove('hide');
          document.getElementById('projectError').classList.add('has-error');
        } else {
          document.getElementById('projectError').classList.add('hide');
          document.getElementById('projectError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else if(!category){
      alert('please select category.');
    } else if(!project){
      alert('please enter name.');
    }
  }
</script>
@stop