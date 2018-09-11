@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
  <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
  <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Individual Messages </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-envelope"></i> Event/Message </li>
      <li class="active"> Individual Messages </li>
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
      <div class="col-md-3">
          <div style="margin-bottom: 10px">
            <input type="text"  class="form-control" name="date" id="date" value="{{$date}}" >
          </div>
      </div>
      <div>
        <a href="{{url('createIndividualMessage')}}" type="button" class="btn btn-primary" style="float: right; width: 170px !important;" title="Add New Message">Add Individual Messages</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table class="" id="clientBatchTable">
        <thead class="thead-inverse">
          <tr>
            <th>#</th>
            <th>Batch</th>
            <th>Date</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody id="individualMessageList">
          @if(count($messages) > 0)
            @foreach($messages as $index => $message)
              <tr>
                <td>{{$index + 1}}</td>
                <td>{{$message->batch->name}}</td>
                <td>{{date('Y-m-d h:i:s a', strtotime($message->created_at))}}</td>
                <td>
                  <a href="{{url('individualMessage')}}/{{$message->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit" /></a>
                </td>
                <td>
                  <a id="{{$message->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete" /></a>
                  <form id="deleteIndividualMessage_{{$message->id}}" action="{{url('deleteIndividualMessage')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="message_id" value="{{$message->id}}">
                  </form>
                </td>
              </tr>
            @endforeach
          @else
            <tr><td colspan="5">No individual messages</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
<script type="text/javascript">
  $(function () {
      var currentDate = "{{ date('Y-m-d')}}";
      $('#date').datetimepicker({
        defaultDate: currentDate,
        format: 'YYYY-MM-DD'
      }).on('dp.change', function (e) {
        showIndividualMessages($(e.target).val());
    });
  });

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
                  formId = 'deleteIndividualMessage_'+id;
                  document.getElementById(formId).submit();
                }
            },
            Cancle: function () {
            }
        }
      });
  }

  function showIndividualMessages(date){
    console.log(date);
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    if(date){
      $.ajax({
          method:'POST',
          url: "{{url('getIndividualMessagesByDate')}}",
          data:{_token:currentToken,date:date}
      }).done(function( result ) {
        var messages = document.getElementById('individualMessageList');
        messages.innerHTML = '';
        if(result.length){
          var editImage = "{{asset('images/edit1.png')}}";
          var deleteImage = "{{asset('images/delete2.png')}}";
          var csrfField = '{{ csrf_field() }}';
          var deleteMethod = '{{ method_field('DELETE') }}';
          var deleteUrl = "{{url('deleteIndividualMessage')}}";
          $.each(result, function(idx, obj) {
            var editUrl = '{{url('individualMessage')}}/'+ obj.id +'/edit';
            messagesInnerHTML ='<tr><td>'+(idx +1)+'</td><td>'+obj.batch+'</td><td>'+obj.date+'</td><td ><a href="'+editUrl+'" ><img src="'+editImage+'" width="30" height="30" title="Edit" /></a></td><td><a id="'+ obj.id +'" onclick="confirmDelete(this);"><img src="'+deleteImage+'" width="30" height="30" title="Delete" /></a><form id="deleteIndividualMessage_'+ obj.id +'" action="'+deleteUrl+'" method="POST" style="display: none;">'+csrfField+''+deleteMethod+'<input type="hidden" name="message_id" value="'+ obj.id +'"></form></td></tr>';
            messages.innerHTML += messagesInnerHTML;
          });
        } else {
          messages.innerHTML = '<tr><td colspan="5">No Result!</td></tr>';
        }
      });
    }
  }

</script>
@stop