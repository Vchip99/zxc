@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Message </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-envelope"></i> Message </li>
      <li class="active"> Manage Message </li>
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
      <div>
        <a href="{{url('createMessage')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Message">Add New Message</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="clientBatchTable">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Message</th>
          <th>Batch</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody id="batches">
        @if(count($messages) > 0)
          @foreach($messages as $index => $message)
          <tr>
            <td>{{$index + 1}}</td>
            <td>{!! mb_strimwidth($message->message, 0, 400, "...") !!}</td>
            <td>
              @if(0 == $message->client_batch_id || empty($message->client_batch_id))
                All
              @else
                {{$message->batch->name}}
              @endif
            </td>
            <td>
              <a href="{{url('message')}}/{{$message->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$message->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$message->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$message->name}}" />
                </a>
                <form id="deleteMessage_{{$message->id}}" action="{{url('deleteMessage')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="message_id" value="{{$message->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No messages are created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $messages->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">
    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this message?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteMessage_'+id;
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