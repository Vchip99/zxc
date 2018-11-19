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
    <h1> College Time Table </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-clock-o"></i> Time Table </li>
      <li class="active"> College Time Table </li>
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
      <div>
        <a href="{{url('college/'.Session::get('college_user_url').'/createCollegeTimeTable')}}" type="button" class="btn btn-primary" style="float: right;" title="Add Time Table">Add Time Table</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table id="collegeTimeTable">
        <thead >
          <tr>
            <th>#</th>
            <th>Department</th>
            <th>Year</th>
            <th>Image</th>
            <th>Created By</th>
            <th>Edit </th>
            <th>Delete </th>
          </tr>
        </thead>
        <tbody>
          @if(count($timeTables) > 0)
            @foreach($timeTables as $index => $timeTable)
            <tr style="overflow: auto;">
              <td>{{$index + $timeTables->firstItem()}}</th>
              <td>{{$timeTable->department->name}} </td>
              <td>
                @if(1 == $timeTable->year)
                  First
                @elseif(2 == $timeTable->year)
                  Second
                @elseif(3 == $timeTable->year)
                  Third
                @else
                  Fourth
                @endif
              </td>
              <td>
                <a href="#imageModal_{{$timeTable->id}}" data-toggle="modal">{{basename($timeTable->image_path)}}</a>
              </td>
              <td>{{$timeTable->user->name}} </td>
              <td>
                @if(4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type || 6 ==  Auth::User()->user_type)
                <a href="{{url('college/'.Session::get('college_user_url').'/collegeTimeTable')}}/{{$timeTable->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$timeTable->name}}" />
                  </a>
                @endif
              </td>
              <td>
                @if(4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type || 6 ==  Auth::User()->user_type)
                  <a id="{{$timeTable->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$timeTable->name}}" />
                  </a>
                  <form id="deleteTt_{{$timeTable->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCollegeTimeTable')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="time_table_id" value="{{$timeTable->id}}">
                  </form>
                  @endif
              </td>
              <div class="modal fade in" id="imageModal_{{$timeTable->id}}" role="dialog" style=" padding-right: 15px;">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                      <img src="{{asset($timeTable->image_path)}}" width="100%" height="100%">
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
            </tr>
            @endforeach
          @else
            <tr><td colspan="7">No records.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{ $timeTables->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
       $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this record?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteTt_'+id;
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