@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Manage Category </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
    <div class="form-group row">
      <div id="addCategoryDiv">
        <a id="addCategory" href="{{url('createOnlineTestCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Category">Add New Category</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Category Name</th>
          <th>Edit Category</th>
          <th>Delete Category</th>
        </tr>
      </thead>
      <tbody>
        @if(count($testCategories) > 0)
          @foreach($testCategories as $index => $testCategory)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            @if(in_array($testCategory->id, $isPurchasedSubCategories))
              <td>{{$testCategory->name}}*</td>
            @else
              <td>{{$testCategory->name}}</td>
            @endif
            <td>
              <a href="{{url('onlinetestcategory')}}/{{$testCategory->id}}/edit"
                    ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$testCategory->name}}" />
                </a>
            </td>
            <td>
            @if(in_array($testCategory->id, $isPurchasedSubCategories))
              <a id="{{$testCategory->id}}" onclick="confirmDelete(this, true);">
            @else
              <a id="{{$testCategory->id}}" onclick="confirmDelete(this, false);">
            @endif

              <img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$testCategory->name}}" />
            </a>
                <form id="deleteCategory_{{$testCategory->id}}" action="{{url('deleteOnlineTestCategory')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="category_id" value="{{$testCategory->id}}">
                </form>

            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No categories are created.</td></tr>
        @endif
      </tbody>
    </table>
    <p><b>Note: * means Category have purchased sub category.</b> </p>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele, isPurchasedSubCategory){
      if(isPurchasedSubCategory){
        message = 'This Category have purchase sub category and if you delete this category, all associated sub categories and purchased subcategory, subjects, papers and questions will be deleted.'
      } else {
        message = 'If you delete this category, all associated sub categories, subjects, papers and questions will be deleted.'
      }
      $.confirm({
        title: 'Confirmation',
        content: message,
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteCategory_'+id;
                    document.getElementById(formId).submit();
                  }
              },
              Cancle: function () {
              }
          }
        });
    }

</script>
@stop