@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Notice  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Notice </li>
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
        <a href="{{url('college/'.Session::get('college_user_url').'/createCollegeNotice')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add Notice">Add Notice</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="collegeNotice">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Departments </th>
          <th>Years </th>
          <th>Date </th>
          <th>Notice </th>
          <th>Created By </th>
          <th>Emergency Notice</th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody >
        @if(count($collegeNotices) > 0)
          @foreach($collegeNotices as $index => $collegeNotice)
          <tr style="overflow: auto;">
            <td>{{$index + $collegeNotices->firstItem()}}</td>
            <td>
              @foreach(explode(',',$collegeNotice->college_dept_ids) as $index => $collegeDeptId)
                @if(0 == $index)
                  {{$allCollegeDepts[$collegeDeptId]}}
                @else
                  ,{{$allCollegeDepts[$collegeDeptId]}}
                @endif
              @endforeach
            </td>
            <td>
              @foreach(explode(',',$collegeNotice->years) as $index => $year)
                @if(0 == $index)
                  @if(1 == $year)
                    First
                  @elseif(2 == $year)
                    Second
                  @elseif(3 == $year)
                    Third
                  @elseif(4 == $year)
                    Fourth
                  @endif
                @else
                  @if(1 == $year)
                    ,First
                  @elseif(2 == $year)
                    ,Second
                  @elseif(3 == $year)
                    ,Third
                  @elseif(4 == $year)
                    ,Fourth
                  @endif
                @endif
              @endforeach
            </td>
            <td>{{$collegeNotice->date}}</td>
            <td>{!! mb_strimwidth($collegeNotice->notice, 0, 80, "...") !!}</td>
            <td>{{$teacherNames[$collegeNotice->created_by]}}</td>
            <td>
              @if(0 == $collegeNotice->is_emergency)
                No
              @else
                Yes
              @endif
            </td>
            <td>
              <a href="{{url('college/'.Session::get('college_user_url').'/collegeNotice')}}/{{$collegeNotice->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit " />
                </a>
            </td>
            <td>
              @if($collegeNotice->created_by == Auth::user()->id)
                <a id="{{$collegeNotice->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete" />
                </a>
                <form id="deleteCollegeNotice_{{$collegeNotice->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCollegeNotice')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="notice_id" value="{{$collegeNotice->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="10">No Notice.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $collegeNotices->links() }}
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
                  formId = 'deleteCollegeNotice_'+id;
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