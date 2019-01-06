@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Event/Message </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-envelope"></i> Event/Message </li>
      <li class="active"> Manage Event/Message </li>
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
        <a href="{{url('college/'.Session::get('college_user_url').'/createMessage')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Message">Add New Message</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="collegeMessages">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Departments</th>
          <th>Years</th>
          <th>Message</th>
          <th>Event/Message</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody >
        @if(count($messages) > 0)
          @foreach($messages as $index => $message)
          <tr style="overflow: auto;">
            <td>{{$index + 1}}</td>
            <td>
              @if(!empty($message->college_dept_ids))
                @foreach(explode(',',$message->college_dept_ids) as $index => $deptId)
                  @if(0 == $index)
                    {{$departments[$deptId]}}
                  @else
                    ,{{$departments[$deptId]}}
                  @endif
                @endforeach
              @endif
            </td>
            <td>
              @if(!empty($message->years))
                @foreach(explode(',',$message->years) as $index => $year)
                  @if(0 == $index)
                    {{$collegeYears[$year]}}
                  @else
                    ,{{$collegeYears[$year]}}
                  @endif
                @endforeach
              @endif
            </td>
            <td>{!! mb_strimwidth($message->message, 0, 400, "...") !!}</td>
            <td>
              @if(!empty($message->start_date) && !empty($message->end_date))
                Event
              @else
                Message
              @endif
            </td>
            <td>
              @if(Auth::user()->id == $message->created_by || 4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
                <a href="{{url('college/'.Session::get('college_user_url').'/message')}}/{{$message->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit " />
                </a>
              @endif
            </td>
            <td>
              @if(Auth::user()->id == $message->created_by || 4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
                <a id="{{$message->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$message->name}}" />
                </a>
                <form id="deleteMessage_{{$message->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteMessage')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="message_id" value="{{$message->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="6">No messages are created.</td></tr>
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
        Cancel: function () {
        }
      }
    });
  }
</script>
@stop