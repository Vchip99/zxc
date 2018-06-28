@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Live Video </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Live Courses </li>
      <li class="active"> Manage Live Video </li>
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
        <a href="{{url('admin/createLiveVideo')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Live Course Video">Add New Live Course Video</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Video Name</th>
          <th>Course Name</th>
          <th>Edit Live  Course</th>
          <th>Delete Live Course</th>
        </tr>
      </thead>
      <tbody>
        @if(count($liveVideos) > 0)
        @foreach($liveVideos as $index => $liveVideo)
        <tr>
          <th scope="row">{{$index + $liveVideos->firstItem()}}</th>
          <td>{{$liveVideo->name}}</td>
          <td>{{$liveVideo->course->name}}</td>
          <td>
            <a href="{{url('admin/liveVideo')}}/{{$liveVideo->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$liveVideo->name}}" />
              </a>
          </td>
          <td>
          <a id="{{$liveVideo->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$liveVideo->name}}" />
              </a>
              <form id="deleteLiveVideo_{{$liveVideo->id}}" action="{{url('admin/deleteLiveVideo')}}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <input type="hidden" name="live_video_id" value="{{$liveVideo->id}}">
              </form>
          </td>
        </tr>
        @endforeach
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $liveVideos->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'You want to delete this live video?',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteLiveVideo_'+id;
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