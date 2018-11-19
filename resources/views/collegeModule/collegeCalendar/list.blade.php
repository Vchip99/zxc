@extends('dashboard.dashboard')
@section('dashboard_header')
  <style type="text/css">
    .btn-primary{
      width: 150px;
    }
    .iframe-container {
      padding-bottom: 60%;
      padding-top: 30px; height: 0; overflow: hidden;
    }
    .iframe-container iframe,
    .iframe-container object,
    .iframe-container embed {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
    }
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> College Calendar </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-clock-o"></i> Time Table </li>
      <li class="active"> College Calendar </li>
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
        @if(!is_object($collegeCalendar))
        <a href="{{url('college/'.Session::get('college_user_url').'/createCollegeCalender')}}" type="button" class="btn btn-primary" style="float: right;" title="Add College Calender">Add College Calender</a>&nbsp;&nbsp;
        @endif
      </div>
    </div>
    <div>
      <table id="collegeCalendar">
        <thead >
          <tr>
            <th>#</th>
            <th>Image</th>
            <th>Created By</th>
            <th>Edit </th>
            <th>Delete </th>
          </tr>
        </thead>
        <tbody>
          @if(is_object($collegeCalendar))
            <tr style="overflow: auto;">
              <td>1</th>
              <td>
                <a href="#imageModal_{{$collegeCalendar->id}}" data-toggle="modal">{{basename($collegeCalendar->image_path)}}</a>
              </td>
              <td>{{$collegeCalendar->user->name}} </td>
              <td>
                @if(4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type || 6 ==  Auth::User()->user_type)
                <a href="{{url('college/'.Session::get('college_user_url').'/collegeCalender')}}/{{$collegeCalendar->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$collegeCalendar->name}}" />
                  </a>
                @endif
              </td>
              <td>
                @if(4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type || 6 ==  Auth::User()->user_type)
                  <a id="{{$collegeCalendar->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$collegeCalendar->name}}" />
                  </a>
                  <form id="deleteTt_{{$collegeCalendar->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCollegeCalender')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="time_table_id" value="{{$collegeCalendar->id}}">
                  </form>
                  @endif
              </td>
              <div class="modal fade in" id="imageModal_{{$collegeCalendar->id}}" role="dialog" style=" padding-right: 15px;">
                @if(preg_match('/(\.jpg|\.png|\.jpeg)$/', $collegeCalendar->image_path))
                  <div class="modal-dialog">
                  <div class="modal-content">
                @else
                  <div class="modal-dialog" style="width: 100%; padding-right: 20px;">
                  <div class="modal-content"  style="background-color: white;">
                @endif
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                      @if(preg_match('/(\.jpg|\.png|\.jpeg)$/', $collegeCalendar->image_path))
                        <img src="{{asset($collegeCalendar->image_path)}}"  width="100%" height="100%">
                      @else
                        <div class="iframe-container">
                           <object data="{{asset($collegeCalendar->image_path)}}" type="application/pdf" >
                            <a href="{{asset($collegeCalendar->image_path)}}"></a>
                           </object>
                        </div>
                      @endif
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
            </tr>
          @else
            <tr><td colspan="5">No record.</td></tr>
          @endif
        </tbody>
      </table>
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