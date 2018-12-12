@extends('admin.master')
@section('admin_content')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Topic </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-file-pdf-o"></i> Study Material </li>
      <li class="active"> Manage Topic </li>
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
        <a href="{{url('admin/createStudyMaterialTopic')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Topic"> Add New Topic</a>&nbsp;&nbsp;&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Topic</th>
          <th>Category </th>
          <th>Sub Category </th>
          <th>Subject </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($topics) > 0)
          @foreach($topics as $index => $topic)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + $topics->firstItem()}}</th>
            <td>{{$topic->name}}</td>
            <td>{{$topic->category->name}}</td>
            <td>{{$topic->subcategory->name}}</td>
            <td>{{$topic->subject->name}}</td>
            <td>
              <a href="{{url('admin/studyMaterialTopic')}}/{{$topic->id}}/edit"
                    ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$topic->name}}" />
                </a>
            </td>
            <td>
              @if($topic->admin_id == Auth::guard('admin')->user()->id)
                <a id="{{$topic->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$topic->name}}" />
                </a>
                <form id="deleteStudyMaterialTopic_{{$topic->id}}" action="{{url('admin/deleteStudyMaterialTopic')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="topic_id" value="{{$topic->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
            <tr><td colspan="6">No topic is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{$topics->links()}}
    </div>
  </div>
  </div>
<script type="text/javascript">
  function confirmDelete(ele){
     $.confirm({
      title: 'Confirmation',
      content: 'Are you sure, you want to delete this topic.',
      type: 'red',
      typeAnimated: true,
      buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-red',
                action: function(){
                  var id = $(ele).attr('id');
                  formId = 'deleteStudyMaterialTopic_'+id;
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