@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses </li>
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
        <a id="addCategory" href="{{url('createOnlineCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Category">Add New Category</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table class="table admin_table">
        <thead >
          <tr>
            <th>#</th>
            <th>Category Name</th>
            <th>Institute Course Name</th>
            <th>Edit Category</th>
            <th>Delete Category</th>
          </tr>
        </thead>
        <tbody>
          @if(count($categories) > 0)
            @foreach($categories as $index => $category)
            <tr>
              <th scope="row">{{ $index + 1 }}</th>
              <td>{{$category->name}}</td>
              <td>{{$category->instituteCourse->name}}</td>
              <td>
                <a href="{{url('onlinecategory')}}/{{$category->id}}/edit"
                      ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$category->name}}" />
                  </a>
              </td>
              <td>
              <a id="{{$category->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$category->name}}"/>
                  </a>
                  <form id="deleteCategory_{{$category->id}}" action="{{url('deleteOnlineCategory')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="category_id" value="{{$category->id}}">
                  </form>

              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="5">No categories are created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
      </div>
    </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
        $.confirm({
        title: 'Confirmation',
        content: 'If you delete this category, all associated sub categories, courses and videos will be deleted.',
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