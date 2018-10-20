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
    <h1> Manage Course Video </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses </li>
      <li class="active"> Manage Course Video </li>
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
        <a href="{{url('college/'.Session::get('college_user_url').'/createCourseVideo')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Video">Add New Video</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table id="collegeCourseVideo">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Video </th>
          <th>Category </th>
          <th>Sub Category </th>
          <th>Course </th>
          <th>Created By </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($courseVideos) > 0)
          @foreach($courseVideos as $index => $courseVideo)
          <tr style="overflow: auto;">
            <td>{{$index + $courseVideos->firstItem()}}</th>
            <td>{{$courseVideo->name}}</td>
            <td>{{$courseVideo->category}}</td>
            <td>{{$courseVideo->subcategory}}</td>
            <td>{{$courseVideo->course}}</td>
            <td>{{$courseVideo->user}}</td>
            <td>
              @if($courseVideo->created_by == Auth::User()->id || (4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type))
              <a href="{{url('college/'.Session::get('college_user_url').'/courseVideo')}}/{{$courseVideo->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$courseVideo->name}}" />
                </a>
              @endif
            </td>
            <td>
              @if($courseVideo->created_by == Auth::User()->id || (4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type))
                <a id="{{$courseVideo->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$courseVideo->name}}" />
                </a>
                <form id="deleteVideo_{{$courseVideo->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCourseVideo')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="video_id" value="{{$courseVideo->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
            <tr><td colspan="8">No video is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{$courseVideos->links()}}
    </div>
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
              Cancle: function () {
              }
          }
        });
    }

</script>
@stop