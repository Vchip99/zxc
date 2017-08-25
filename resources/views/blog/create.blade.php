@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Blog </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-newspaper-o"></i> Blog </li>
      <li class="active"> Manage Blog </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  &nbsp;
  <div class="container admin_div">
  @if(isset($blog->id))
    <form action="{{url('admin/updateBlog')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="blog_id" value="{{$blog->id}}">
  @else
   <form action="{{url('admin/createBlog')}}" method="POST">
    @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('title')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="title">Blog Title:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="title" name="title" value="{{ old('title',$blog->title) }}" required="true">
        @if($errors->has('title')) <p class="help-block">{{ $errors->first('title') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('author')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="author">Author:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="author" name="author" value="{{ old('author',$blog->author) }}" required="true">
        @if($errors->has('author')) <p class="has-error">{{ $errors->first('author') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('tags')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="tags">Tags:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags',$tags) }}" required="true">
        @if($errors->has('tags')) <p class="has-error">{{ $errors->first('tags') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('category_id')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="category_id">Category:</label>
      <div class="col-sm-3">
        <select class="form-control" id="category_id" name="category_id" required="true" title="Category">
          <option value="">Select Category ...</option>
          @if(count($blogCategories) > 0)
            @foreach($blogCategories as $blogCategory)
              @if( old('category_id',$blog->blog_category_id) ==  $blogCategory->id )
                <option value="{{$blogCategory->id}}" selected="true">{{$blogCategory->name}}</option>
              @else
                <option value="{{$blogCategory->id}}">{{$blogCategory->name}}</option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('category_id')) <p class="has-error">{{ $errors->first('category_id') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('content')) has-error @endif">
      <label class="col-sm-2 col-form-label">Blog Content:</label>
      <div class="col-sm-10">
        <textarea name="content" placeholder="Blog here" type="text" id="content" equired>{{ old('content', $blog->content) }}
        </textarea>
        @if($errors->has('content')) <p class="has-error">{{ $errors->first('content') }}</p> @endif
       <script type="text/javascript">
          CKEDITOR.replace( 'content', { enterMode: CKEDITOR.ENTER_BR } );
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
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</form>
@stop