@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses </li>
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
        <a id="addCategory" href="{{url('admin/createCourseCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Category">Add New Category</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table class="table admin_table">
        <thead >
          <tr>
            <th>#</th>
            <th>Category</th>
            <th>Edit </th>
            <th>Delete </th>
          </tr>
        </thead>
        <tbody>
          @if(count($courseCategories) > 0)
            @foreach($courseCategories as $index => $courseCategory)
            <tr style="overflow: auto;">
              <th scope="row">{{$index + $courseCategories->firstItem()}}</th>
              <td>{{$courseCategory->name}}</td>
              <td>
                <a href="{{url('admin/coursecategory')}}/{{$courseCategory->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$courseCategory->name}}" />
                  </a>
              </td>
              <td>
              <a id="{{$courseCategory->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$courseCategory->name}}" />
                  </a>
                  <form id="deleteCategory_{{$courseCategory->id}}" action="{{url('admin/deleteCourseCategory')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="category_id" value="{{$courseCategory->id}}">
                  </form>
              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="4">No category is created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{ $courseCategories->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
       $.confirm({
        title: 'Confirmation',
        content: 'If you delete this category, then all sub categories, courses and videos associated with this category will be deleted.',
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