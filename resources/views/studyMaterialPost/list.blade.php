@extends('admin.master')
@section('admin_content')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Post </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-file-pdf-o"></i> Study Material </li>
      <li class="active"> Manage Post </li>
    </ol>
  </section>
@stop
  <div class="container ">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="form-group row">
      <div >
        <a href="{{url('admin/createStudyMaterialPost')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Post"> Add New Post</a>&nbsp;&nbsp;&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Post</th>
          <th>Category </th>
          <th>Sub Category </th>
          <th>Subject </th>
          <th>Topic</th>
          <th>Created_by</th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($posts) > 0)
          @foreach($posts as $index => $post)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + $posts->firstItem()}}</th>
            <td>{!! mb_strimwidth($post->body, 0, 400, "...") !!}</td>
            <td>{{$post->category}}</td>
            <td>{{$post->subcategory}}</td>
            <td>{{$post->subject}}</td>
            <td>{{$post->topic}}</td>
            <td>{{$adminNames[$post->admin_id]}}</td>
            <td>
              <a href="{{url('admin/studyMaterialPost')}}/{{$post->id}}/edit"
                    ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$post->name}}" />
                </a>
            </td>
            <td>
                <a id="{{$post->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$post->name}}" />
                </a>
                <form id="deleteStudyMaterialPost_{{$post->id}}" action="{{url('admin/deleteStudyMaterialPost')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="post_id" value="{{$post->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
            <tr><td colspan="6">No Post is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{$posts->links()}}
    </div>
  </div>
  </div>
<script type="text/javascript">
  function confirmDelete(ele){
     $.confirm({
      title: 'Confirmation',
      content: 'Are you sure, you want to delete this post.',
      type: 'red',
      typeAnimated: true,
      buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-red',
                action: function(){
                  var id = $(ele).attr('id');
                  formId = 'deleteStudyMaterialPost_'+id;
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