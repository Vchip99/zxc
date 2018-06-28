@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Motivational Speech Details </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-microphone"></i> Motivational Speech </li>
      <li class="active"> Manage Motivational Speech Details </li>
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
        <a href="{{url('admin/createMotivationalSpeechDetails')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Motivational Speech">Add New Motivational Speech</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Motivational Speech Name</th>
          <th>Category Name</th>
          <th>Edit Category</th>
          <th>Delete Motivational Speech</th>
        </tr>
      </thead>
      <tbody>
        @if(count($motivationalSpeechDetails) > 0)
          @foreach($motivationalSpeechDetails as $index => $motivationalSpeechDetail)
          <tr>
            <th scope="row">{{$index + $motivationalSpeechDetails->firstItem()}}</th>
            <td>{{$motivationalSpeechDetail->name}}</td>
            <td>{{$motivationalSpeechDetail->category->name}}</td>
            <td>
              <a href="{{url('admin/motivationalSpeechDetails')}}/{{$motivationalSpeechDetail->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$motivationalSpeechDetail->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$motivationalSpeechDetail->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$motivationalSpeechDetail->name}}" />
                </a>
                <form id="deleteMotivationalSpeechDetail_{{$motivationalSpeechDetail->id}}" action="{{url('admin/deleteMotivationalSpeechDetails')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="motivational_speech_details_id" value="{{$motivationalSpeechDetail->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No motivational speeches is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;" id="pagination">
      {{ $motivationalSpeechDetails->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'Are you sure you want to delete this motivational speech detail.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteMotivationalSpeechDetail_'+id;
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