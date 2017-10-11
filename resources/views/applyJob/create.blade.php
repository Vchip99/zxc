@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Apply Job</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-link"></i> Placement </li>
      <li class="active"> Apply Job</li>
    </ol>
  </section>
@stop
@section('admin_content')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  &nbsp;
  <div class="container admin_div">
  @if(isset($applyJob->id))
    <form action="{{url('admin/updateApplyJob')}}" method="POST" enctype="multipart/form-data">
      {{method_field('PUT')}}
      <input type="hidden" name="apply_job_id" value="{{$applyJob->id}}">
  @else
      <form action="{{url('admin/createApplyJob')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('company')) has-error @endif">
    <label for="company" class="col-sm-2 col-form-label">Company Name:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" id="company" name="company" value="{{$applyJob->company}}" required="true">
      @if($errors->has('company')) <p class="help-block">{{ $errors->first('company') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('job_description')) has-error @endif">
    <label for="job_description" class="col-sm-2 col-form-label">Job Description:</label>
    <div class="col-sm-10">
      @if(isset($applyJob))
        <textarea class="form-control" id="job_description" name="job_description" required="true">{{$applyJob->job_description}}</textarea>
      @else
        <textarea class="form-control" id="job_description" name="job_description" required="true">
        </textarea>
      @endif
       <script type="text/javascript">
          CKEDITOR.replace('job_description');
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
      @if($errors->has('about_company')) <p class="help-block">{{ $errors->first('about_company') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('mock_test')) has-error @endif">
    <label for="mock_test" class="col-sm-2 col-form-label">Mock Test Url:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" id="mock_test" name="mock_test" value="{{$applyJob->mock_test}}" required="true">
      @if($errors->has('mock_test')) <p class="help-block">{{ $errors->first('mock_test') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('job_url')) has-error @endif">
    <label for="job_url" class="col-sm-2 col-form-label">Apply Job Url:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" id="job_url" name="job_url" value="{{$applyJob->job_url}}" required="true">
      @if($errors->has('job_url')) <p class="help-block">{{ $errors->first('job_url') }}</p> @endif
    </div>
  </div>
  <div class="form-group row"  id="submit">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</form>
@stop