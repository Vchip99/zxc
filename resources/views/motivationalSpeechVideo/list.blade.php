@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Motivational Video </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-microphone"></i> Motivational Speech </li>
      <li class="active"> Manage Motivational Video </li>
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
        <a href="{{url('admin/createMotivationalSpeechVideo')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Motivational Video">Add New Motivational Video</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Video Name</th>
          <th>Motivational Speech Name</th>
          <th>Edit Video</th>
          <th>Delete Motivational Video</th>
        </tr>
      </thead>
      <tbody>
        @if(count($motivationalSpeechVideos) > 0)
          @foreach($motivationalSpeechVideos as $index => $motivationalSpeechVideo)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$motivationalSpeechVideo->name}}</td>
            <td>{{$motivationalSpeechVideo->motivationalspeech->name}}</td>
            <td>
              <a href="{{url('admin/motivationalSpeechVideo')}}/{{$motivationalSpeechVideo->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$motivationalSpeechVideo->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$motivationalSpeechVideo->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$motivationalSpeechVideo->name}}" />
                </a>
                <form id="deleteMotivationalSpeechVideo_{{$motivationalSpeechVideo->id}}" action="{{url('admin/deleteMotivationalSpeechVideo')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="video_id" value="{{$motivationalSpeechVideo->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No motivational videos is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;" id="pagination">
      {{ $motivationalSpeechVideos->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'Are you sure you want to delete this motivational speech video.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteMotivationalSpeechVideo_'+id;
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