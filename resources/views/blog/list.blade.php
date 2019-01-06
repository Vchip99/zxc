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
        <a id="addCategory" href="{{url('admin/createBlog')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Blog">Add New Blog</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Blog Name</th>
          <th>Category Name</th>
          <th>Edit Blog</th>
          <th>Delete Blog</th>
        </tr>
      </thead>
      <tbody>
        @if(count($blogs) > 0)
        @foreach($blogs as $index => $blog)
        <tr style="overflow: auto;">
          <th scope="row">{{$index + $blogs->firstItem()}}</th>
          <td>{{$blog->title}}</td>
          <td>{{ $blog->category->name }}</td>
          <td>
            <a href="{{url('admin/blog')}}/{{$blog->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$blog->title}}" />
              </a>
          </td>
          <td>
          <a id="{{$blog->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$blog->title}}" />
              </a>
              <form id="deleteBlog_{{$blog->id}}" action="{{url('admin/deleteBlog')}}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <input type="hidden" name="blog_id" value="{{$blog->id}}">
              </form>

          </td>
        </tr>
        @endforeach
        @endif
      </tbody>
    </table>
    <div style="float: right;" id="pagination">
        {{ $blogs->links() }}
      </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'You want to delete this blog?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteBlog_'+id;
                    document.getElementById(formId).submit();
                  }
              },
              Cancel: function () {
              }
          }
        });

    }
</script>
@stop