@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Advertisement Page </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-television"></i> Advertisement Page </li>
      <li class="active"> Manage Advertisement Page </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($advertisementPage->id))
    <form action="{{url('admin/updateAdvertisementPage')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="page_id" value="{{$advertisementPage->id}}">
  @else
   <form action="{{url('admin/createAdvertisementPage')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('page_type')) has-error @endif ">
      <label for="paper" class="col-sm-2 col-form-label">Page Type:</label>
      <div class="col-sm-3">
        @if(isset($advertisementPage->id))
          <label class="radio-inline"><input type="radio" name="page_type" value="1" onClick="showMainPages(this);"
          @if($advertisementPage->parent_page == 0) checked @endif> Main Page </label>
          <label class="radio-inline"><input type="radio" name="page_type" value="0" onClick="showMainPages(this);" @if($advertisementPage->parent_page > 0) checked @endif>  Sub Page </label>
        @else
          <label class="radio-inline"><input type="radio" name="page_type" value="1" onClick="showMainPages(this);" checked> Main Page </label>
          <label class="radio-inline"><input type="radio" name="page_type" value="0" onClick="showMainPages(this);">  Sub Page </label>
        @endif
        @if($errors->has('page_type')) <p class="help-block">{{ $errors->first('page_type') }}</p> @endif
      </div>
    </div>
    @if($advertisementPage->parent_page > 0)
      <div class="form-group row @if ($errors->has('parent_page')) has-error @endif " id="main_page">
    @else
      <div class="form-group row hide @if ($errors->has('parent_page')) has-error @endif " id="main_page">
    @endif
      <label for="paper" class="col-sm-2 col-form-label">Main Page:</label>
      <div class="col-sm-3">
        <select id="subject" class="form-control" name="parent_page" required title="Parent Page">
        <option value="">Select Page</option>
          @if(count($mainPages) > 0  && isset($advertisementPage->id))
            @foreach($mainPages as $mainPage)
              @if($advertisementPage->parent_page == $mainPage->id )
                <option value="{{$mainPage->id}}" selected>{{$mainPage->name}}</option>
              @else
                <option value="{{$mainPage->id}}">{{$mainPage->name}}</option>
              @endif
            @endforeach
          @elseif(count($mainPages) > 0 )
            @foreach($mainPages as $mainPage)
              <option value="{{$mainPage->id}}">{{$mainPage->name}}</option>
            @endforeach
          @endif
        </select>
        @if($errors->has('parent_page')) <p class="help-block">{{ $errors->first('parent_page') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('name')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="name">Page Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="name" name="name" value="{{($advertisementPage)?$advertisementPage->name:null}}" required="true">
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('url')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="url">Page Url:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="url" name="url" value="{{($advertisementPage)?$advertisementPage->url:null}}" required="true">
        @if($errors->has('url')) <p class="help-block">{{ $errors->first('url') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('price')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="price">Page Price:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="price" name="price" value="{{($advertisementPage)?$advertisementPage->price:null}}" required="true">
        @if($errors->has('price')) <p class="help-block">{{ $errors->first('price') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
    </form>
  </div>

<script type="text/javascript">
  function showMainPages(ele){
      if(0 == $(ele).val()){
        $('#main_page').removeClass('hide');;
      } else {
        $('#main_page').addClass('hide');
      }
    }
</script>
@stop