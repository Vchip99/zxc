@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Projects </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-table"></i> Vkit </li>
      <li class="active"> Manage Projects </li>
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
      <div >
        <a href="{{url('admin/createVkitProject')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Project">Add New Project</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Project </th>
          <th>Category </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($projects) > 0)
          @foreach($projects as $index => $project)
          <tr>
            <th scope="row">{{$index + $projects->firstItem() }}</th>
            <td>{{$project->name}}</td>
            <td>{{$project->category}}</td>
            <td>
              <a href="{{url('admin/vkitProject')}}/{{$project->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$project->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$project->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$project->name}}" />
                </a>
                <form id="deleteProject_{{$project->id}}" action="{{url('admin/deleteVkitProject')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="project_id" value="{{$project->id}}">
                </form>

            </td>
          </tr>
          @endforeach
        @else
          <tr><td>No project is created.</td></tr>
        @endif
      </tbody>
    </table>
      <div style="float: right;" id="pagination">
        {{ $projects->links() }}
      </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'You want to delete this project?',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteProject_'+id;
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