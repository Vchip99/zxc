@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Class </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Calendar Management </li>
      <li class="active"> Manage Class </li>
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
        <a href="{{url('createClientClass')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Class">Add New Class</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table class="" id="clientClasses">
        <thead class="thead-inverse">
          <tr>
            <th>#</th>
            <th>Batch</th>
            <th>Subject</th>
            <th>Topic</th>
            <th>Teacher</th>
            <th>Date</th>
            <th>From</th>
            <th>To</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody id="">
          @if(count($classes) > 0)
            @foreach($classes as $index => $class)
            <tr style="overflow: auto;">
              <td>{{$index + 1}}</td>
              <td>
                @if(0 == $class->client_batch_id)
                  All
                @else
                  {{$class->batch->name}}
                @endif
              </td>
              <td>{{$class->subject}}</td>
              <td>{{$class->topic}}</td>
              <td>
                @if(0 == $class->clientuser_id)
                  Admin
                @else
                  {{$class->user->name}}
                @endif
              </td>
              <td>{{$class->date}}</td>
              <td>{{$class->from_time}}</td>
              <td>{{$class->to_time}}</td>
              <td>
                <a href="{{url('class')}}/{{$class->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$class->name}}" />
                  </a>
              </td>
              <td>
              <a id="{{$class->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$class->name}}" />
                  </a>
                  <form id="deleteClass_{{$class->id}}" action="{{url('deleteClass')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="class_id" value="{{$class->id}}">
                  </form>
              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="10">No classes are created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{ $classes->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">
    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this class.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteClass_'+id;
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