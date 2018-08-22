@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Holidays </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Syllabus Management </li>
      <li class="active"> Manage Holidays </li>
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
        <a href="{{url('createClientHoliday')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Holiday">Add New Holiday</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table class="" id="">
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
          @if(count($holidays) > 0)
            @foreach($holidays as $index => $holiday)
            <tr>
              <td>{{$index + 1}}</td>
              <td>
                @if(0 == $holiday->client_batch_id)
                  All
                @else
                  {{$holiday->batch->name}}
                @endif
              </td>
              <td>{{$holiday->date}}</td>
              <td>
                <a href="{{url('holiday')}}/{{$holiday->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$holiday->name}}" />
                  </a>
              </td>
              <td>
              <a id="{{$holiday->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$holiday->name}}" />
                  </a>
                  <form id="deleteHoliday_{{$holiday->id}}" action="{{url('deleteHoliday')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="holiday_id" value="{{$holiday->id}}">
                  </form>
              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="5">No holidays are created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{ $holidays->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">
    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this holiday.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteHoliday_'+id;
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