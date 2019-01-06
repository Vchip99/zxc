@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Class Exam </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Class Exam </li>
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
      <div id="addTopicDiv">
        <a id="addTopic" href="{{url('college/'.Session::get('college_user_url').'/createCollegeClassExam')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add Class Exam">Add Class Exam</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="collegeExtraClass">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Subject </th>
          <th>Type </th>
          <th>Departments </th>
          <th>Years </th>
          <th>Topic </th>
          <th>Mark </th>
          <th>Date </th>
          <th>From Time </th>
          <th>To Time </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody >
        @if(count($collegeClassExams) > 0)
          @foreach($collegeClassExams as $index => $collegeClassExam)
          <tr style="overflow: auto;">
            <td>{{$index + $collegeClassExams->firstItem()}}</td>
            <td>{{$allSubjects[$collegeClassExam->college_subject_id]['name']}}</td>
            <td>
              @if(1 == $collegeClassExam->exam_type)
                Online
              @else
                Offline
              @endif
            </td>
            <td>
              @foreach(explode(',',$collegeClassExam->college_dept_ids) as $index => $collegeDeptId)
                @if(0 == $index)
                  {{$allCollegeDepts[$collegeDeptId]}}
                @else
                  ,{{$allCollegeDepts[$collegeDeptId]}}
                @endif
              @endforeach
            </td>
            <td>
              @foreach(explode(',',$collegeClassExam->years) as $index => $year)
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
            <td>{{$collegeClassExam->topic}}</td>
            <td>{{$collegeClassExam->marks}}</td>
            <td>{{$collegeClassExam->date}}</td>
            <td>{{$collegeClassExam->from_time}}</td>
            <td>{{$collegeClassExam->to_time}}</td>
            <td>
              <a href="{{url('college/'.Session::get('college_user_url').'/collegeClassExam')}}/{{$collegeClassExam->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit " />
                </a>
            </td>
            <td>
                <a id="{{$collegeClassExam->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete" />
                </a>
                <form id="deleteCollegeClassExam_{{$collegeClassExam->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCollegeClassExam')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="exam_id" value="{{$collegeClassExam->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="10">No exams are created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $collegeClassExams->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

  function confirmDelete(ele){
    $.confirm({
      title: 'Confirmation',
      content: 'Are you sure, you want to delete this class exam.',
      type: 'red',
      typeAnimated: true,
      buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-red',
                action: function(){
                  var id = $(ele).attr('id');
                  formId = 'deleteCollegeClassExam_'+id;
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