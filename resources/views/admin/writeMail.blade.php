@extends('admin.master')
@section('module_title')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  <section class="content-header">
    <h1> Manage Send Mails </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Send Mails </li>
      <li class="active"> Manage Send Mails </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
    <div class="container admin_div">

       <form action="{{url('admin/sendSubscribedMails')}}" method="POST">
        {{ csrf_field() }}
        <div class="form-group row ">
          <label class="col-sm-2 col-form-label" for="category">Mail Content:</label>
          <div class="col-sm-9">
            <textarea class="form-control" name="mail_content" id="mail_content" rows="20" required="true"></textarea>
            <script type="text/javascript">
              CKEDITOR.replace( 'mail_content');
              CKEDITOR.config.width="100%";
              CKEDITOR.config.height="auto";
              CKEDITOR.on('dialogDefinition', function (ev) {
                  var dialogName = ev.data.name,
                      dialogDefinition = ev.data.definition;
                  if (dialogName == 'image') {
                      var onOk = dialogDefinition.onOk;
                      dialogDefinition.onOk = function (e) {
                          var width = this.getContentElement('info', 'txtWidth');
                          width.setValue('100%');
                          var height = this.getContentElement('info', 'txtHeight');
                          height.setValue('400');
                          onOk && onOk.apply(this, e);
                      };
                  }
              });
            </script>
          </div>
        </div>

        <div class="form-group row">
          <div class="offset-sm-2 col-sm-3" title="Submit">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
      </form>
  </div>
  </div>
@stop