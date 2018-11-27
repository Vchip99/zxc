@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Notices </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Calendar Management </li>
      <li class="active"> Manage Notices </li>
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
        <a href="{{url('createClientNotice')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Notice">Add New Notice</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table class="" id="clientHolidays">
        <thead class="thead-inverse">
          <tr>
            <th>#</th>
            <th>Batch</th>
            <th>Date</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody id="">
          @if(count($notices) > 0)
            @foreach($notices as $index => $notice)
            <tr style="overflow: auto;">
              <td>{{$index + 1}}</td>
              <td>
                @if(0 == $notice->client_batch_id)
                  All
                @else
                  {{$notice->batch->name}}
                @endif
              </td>
              <td>{{$notice->date}}</td>
              <td>
                <a href="{{url('notice')}}/{{$notice->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$notice->name}}" />
                  </a>
              </td>
              <td>
              <a id="{{$notice->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$notice->name}}" />
                  </a>
                  <form id="deleteNotice_{{$notice->id}}" action="{{url('deleteNotice')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="notice_id" value="{{$notice->id}}">
                  </form>
              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="5">No notices are created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{ $notices->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">
    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this notice.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteNotice_'+id;
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