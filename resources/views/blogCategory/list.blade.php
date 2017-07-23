@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Blog Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Blog </li>
      <li class="active"> Manage Blog Category</li>
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
    <div class="form-group row">
      <div id="addCategoryDiv">
        <a id="addCategory" href="{{url('admin/createBlogCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Blog Category">Add New Blog Category</a>&nbsp;&nbsp;
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
        @if(count($blogCategories) > 0)
        @foreach($blogCategories as $index => $blogCategory)
        <tr>
          <th scope="row">{{$index + 1}}</th>
          <td>{{$blogCategory->name}}</td>
          <td>
            <a href="{{url('admin/blogCategory')}}/{{$blogCategory->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$blogCategory->name}}" />
              </a>
          </td>
          <td>
          <a id="{{$blogCategory->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$blogCategory->name}}" />
              </a>
              <form id="deleteBlogCategory_{{$blogCategory->id}}" action="{{url('admin/deleteBlogCategory')}}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <input type="hidden" name="category_id" value="{{$blogCategory->id}}">
              </form>
          </td>
        </tr>
        @endforeach
        @endif
      </tbody>
    </table>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this blog category, all blogs associated with this category will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteBlogCategory_'+id;
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