@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Holiday  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Holiday </li>
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
        <a href="{{url('college/'.Session::get('college_user_url').'/createCollegeHoliday')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add Holiday">Add Holiday</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="collegeHoliday">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Holiday Message </th>
          <th>Created By </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody id="collegeOfflinePaperTable">
        @if(count($collegeHolidays) > 0)
          @foreach($collegeHolidays as $index => $collegeHoliday)
          <tr style="overflow: auto;">
            <td>{{$index + $collegeHolidays->firstItem()}}</td>
            <td>{{$collegeHoliday->date}}</td>
            <td>{!! mb_strimwidth($collegeHoliday->note, 0, 100, "...") !!}</td>
            <td>{{$teacherNames[$collegeHoliday->created_by]}}</td>
            <td>
              <a href="{{url('college/'.Session::get('college_user_url').'/collegeHoliday')}}/{{$collegeHoliday->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit " />
                </a>
            </td>
            <td>
              @if($collegeHoliday->created_by == Auth::user()->id)
                <a id="{{$collegeHoliday->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete" />
                </a>
                <form id="deleteCollegeHoliday_{{$collegeHoliday->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCollegeHoliday')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="holiday_id" value="{{$collegeHoliday->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="10">No Holiday.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $collegeHolidays->links() }}
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
                  formId = 'deleteCollegeHoliday_'+id;
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