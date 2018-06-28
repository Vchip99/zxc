@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Discussion Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-comments-o"></i> Discussion </li>
      <li class="active"> Manage Discussion Category</li>
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
        <a id="addCategory" href="{{url('admin/createDiscussionCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Discussion Category">Add New Discussion Category</a>&nbsp;&nbsp;
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
        @if(count($discussionCategories) > 0)
        @foreach($discussionCategories as $index => $discussionCategory)
        <tr>
          <th scope="row">{{$index + $discussionCategories->firstItem()}}</th>
          <td>{{$discussionCategory->name}}</td>
          <td>
            <a href="{{url('admin/discussionCategory')}}/{{$discussionCategory->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$discussionCategory->name}}" />
              </a>
          </td>
          <td>
          <a id="{{$discussionCategory->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$discussionCategory->name}}" />
              </a>
              <form id="deleteDiscussionCategory_{{$discussionCategory->id}}" action="{{url('admin/deleteDiscussionCategory')}}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <input type="hidden" name="category_id" value="{{$discussionCategory->id}}">
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
        content: 'If you delete this discussion category, all discussions associated with this category will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteDiscussionCategory_'+id;
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