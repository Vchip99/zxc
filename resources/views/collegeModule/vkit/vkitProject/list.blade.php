@extends('dashboard.dashboard')
@section('dashboard_header')
  <style type="text/css">
    .btn-primary{
      width: 150px;
    }
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> Manage Projects </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-table"></i> Vkit </li>
      <li class="active"> Manage Projects </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="form-group row">
      <div >
        <a href="{{url('college/'.Session::get('college_user_url').'/createVkitProject')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Project">Add New Project</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table id="collegeVkitProject">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Project </th>
          <th>Category </th>
          <th>Created By </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($projects) > 0)
          @foreach($projects as $index => $project)
          <tr style="overflow: auto;">
            <td>{{$index + $projects->firstItem() }}</th>
            <td>{{$project->name}}</td>
            <td>{{$project->category}}</td>
            <td>{{$project->user}}</td>
            <td>
              @if($project->created_by == Auth::User()->id || (4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type))
              <a href="{{url('college/'.Session::get('college_user_url').'/vkitProject')}}/{{$project->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$project->name}}" />
                </a>
              @endif
            </td>
            <td>
              @if($project->created_by == Auth::User()->id || (4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type))
                <a id="{{$project->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$project->name}}" />
                </a>
                <form id="deleteProject_{{$project->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteVkitProject')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="project_id" value="{{$project->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="6">No project is created.</td></tr>
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
                Cancel: function () {
                }
            }
          });
    }

</script>
@stop