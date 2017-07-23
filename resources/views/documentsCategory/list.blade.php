@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Documents </li>
      <li class="active"> Manage Category </li>
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
        <a id="addCategory" href="{{url('admin/createDocumentsCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Category">Add New Category</a>&nbsp;&nbsp;
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
        @if(count($documentsCategories) > 0)
          @foreach($documentsCategories as $index => $documentsCategory)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$documentsCategory->name}}</td>
            <td>
              <a href="{{url('admin/documentCategory')}}/{{$documentsCategory->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$documentsCategory->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$documentsCategory->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$documentsCategory->name}}" />
                </a>
                <form id="deleteCategory_{{$documentsCategory->id}}" action="{{url('admin/deleteDocumentsCategory')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="category_id" value="{{$documentsCategory->id}}">
                </form>

            </td>
          </tr>
          @endforeach
        @else
          <tr><td>No category is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $documentsCategories->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this category, all associated documents of this category will be deleted.',
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