@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Course Video </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses </li>
      <li class="active"> Manage Course Video </li>
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
      <div >
        <a href="{{url('createOnlineVideo')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Video">Add New Video</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr style="overflow: auto;">
          <th>#</th>
          <th>Video </th>
          <th>Category </th>
          <th>Sub Category </th>
          <th>Course </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($videos) > 0)
          @foreach($videos as $index => $video)
          <tr>
            <th scope="row">{{ $index + 1}}</th>
            <td>{{$video->name}}</td>
            <td>{{$video->category()}}</td>
            <td>{{$video->subcategory()}}</td>
            <td>{{$video->course->name}}</td>
            <td>
              @if(($video->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $video->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $video->client_id))
                <a href="{{url('onlinevideo')}}/{{$video->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$video->name}}"/></a>
              @endif
            </td>
            <td>
              @if(($video->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $video->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $video->client_id))
                <a id="{{$video->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$video->name}}" /></a>
                <form id="deleteVideo_{{$video->id}}" action="{{url('deleteOnlineVideo')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="video_id" value="{{$video->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No videos are created.</td></tr>
        @endif
      </tbody>
    </table>

  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
       $.confirm({
        title: 'Confirmation',
        content: 'You want to delete this video?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteVideo_'+id;
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