@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Document </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Documents </li>
      <li class="active"> Manage Document </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($documentsDoc->id))
    <form action="{{url('admin/updateDocumentsDoc')}}" method="POST" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    <input type="hidden" name="document_id" value="{{$documentsDoc->id}}"/>
  @else
   <form action="{{url('admin/createDocumentsDoc')}}" method="POST" enctype="multipart/form-data">
    @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('name')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="category">Document Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="name" name="name" value="{{($documentsDoc)?$documentsDoc->name:null}}" required="true">
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="author">Author:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="author" name="author" value="{{($documentsDoc->author)?$documentsDoc->author:NULL}}" required="true">
        @if($errors->has('author')) <p class="help-block">{{ $errors->first('author') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="introduction">Introduction:</label>
      <div class="col-sm-3">
        <textarea class="form-control" id="introduction" name="introduction" required="true">{{($documentsDoc->introduction)?$documentsDoc->introduction:NULL}}</textarea>
        @if($errors->has('introduction')) <p class="help-block">{{ $errors->first('introduction') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="doc_category_id">Category:</label>
      <div class="col-sm-3">
        <select class="form-control" id="doc_category_id" name="doc_category_id" required="true" title="Category">
          <option value="">Select Category ...</option>
          @if(count($documentsCategories) > 0)
            @foreach($documentsCategories as $documentsCategory)
              @if(isset($documentsDoc->doc_category_id) && $documentsDoc->doc_category_id == $documentsCategory->id)
                <option value="{{$documentsCategory->id}}" selected="true">{{$documentsCategory->name}}</option>
              @else
                <option value="{{$documentsCategory->id}}">{{$documentsCategory->name}}</option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('doc_category_id')) <p class="help-block">{{ $errors->first('doc_category_id') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('is_paid')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Is Paid:</label>
      <div class="col-sm-3">
          @if(isset($documentsDoc->id))
          <label class="radio-inline"><input type="radio" name="is_paid" value="1" @if(1 == $documentsDoc->is_paid) checked="true" @endif> Yes</label>
          <label class="radio-inline"><input type="radio" name="is_paid" value="0" @if(0 == $documentsDoc->is_paid) checked="true" @endif> No</label>
          @else
            <label class="radio-inline"><input type="radio" name="is_paid" value="1"> Yes</label>
            <label class="radio-inline"><input type="radio" name="is_paid" value="0" checked> No</label>
          @endif
        @if($errors->has('is_paid')) <p class="help-block">{{ $errors->first('is_paid') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('price')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Price:</label>
      <div class="col-sm-3">
        @if(isset($documentsDoc->id))
          <input type="text" class="form-control" name="price" value="{{$documentsDoc->price}}" required="true">
        @else
          <input type="text" class="form-control" name="price" value="" placeholder="Price" required="true">
        @endif
        @if($errors->has('price')) <p class="help-block">{{ $errors->first('price') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('difficulty_level')) has-error @endif">
      <label class="col-sm-2 col-form-label">Difficult Level:</label>
      <div class="col-sm-3">
        <select id="difficulty_level" class="form-control" name="difficulty_level" required title="Difficult Level">
            <option value="">Select Difficult Level ...</option>
            <option value="1" @if(1 == $documentsDoc->difficulty_level) Selected @endif>Beginner</option>
            <option value="2" @if(2 == $documentsDoc->difficulty_level) Selected @endif>Intermediate</option>
            <option value="3" @if(3 == $documentsDoc->difficulty_level) Selected @endif>Advanced</option>
        </select>
        @if($errors->has('difficulty_level')) <p class="help-block">{{ $errors->first('difficulty_level') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('type_of_document')) has-error @endif">
      <label class="col-sm-2 col-form-label">Type of Document:</label>
      <div class="col-sm-3">
        <select id="type_of_document" class="form-control" name="type_of_document" required title="Type of Document">
            <option value="">Select Type of Document ...</option>
            <option value="1" @if(1 == $documentsDoc->type_of_document) Selected @endif>Reasearch paper</option>
            <option value="2" @if(2 == $documentsDoc->type_of_document) Selected @endif>Documentry</option>
        </select>
        @if($errors->has('type_of_document')) <p class="help-block">{{ $errors->first('type_of_document') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label for="course" class="col-sm-2 col-form-label">Date of Update:</label>
      <div class="col-sm-3">
        <input type="Date" name="date_of_update" @if(isset($documentsDoc->id)) value="{{$documentsDoc->date_of_update}}" @endif required="true">
        @if($errors->has('date_of_update')) <p class="help-block">{{ $errors->first('date_of_update') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="doc_image">Document Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="doc_image" id="doc_image">
        @if(isset($documentsDoc->doc_image_path))
          <b><span>Existing Image: {!! basename($documentsDoc->doc_image_path) !!}</span></b>
        @endif
      </div>
    </div>
     <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="doc_pdf">Document PDF:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="doc_pdf" id="doc_pdf">
        @if(isset($documentsDoc->doc_pdf_path))
          <b><span>Existing Pdf: {!! basename($documentsDoc->doc_pdf_path) !!}</span></b>
        @endif
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