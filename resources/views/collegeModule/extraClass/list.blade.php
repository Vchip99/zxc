@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Extra Class  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Extra Class </li>
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
        <a id="addTopic" href="{{url('college/'.Session::get('college_user_url').'/createCollegeExtraClass')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add Extra Class">Add Extra Class</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="collegeExtraClass">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Subject </th>
          <th>Departments </th>
          <th>Years </th>
          <th>Topic </th>
          <th>Date </th>
          <th>From Time </th>
          <th>To Time </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($collegeClasses) > 0)
          @foreach($collegeClasses as $index => $collegeClass)
          <tr style="overflow: auto;">
            <td>{{$index + $collegeClasses->firstItem()}}</td>
            <td>{{$allSubjects[$collegeClass->college_subject_id]['name']}}</td>
            <td>
              @foreach(explode(',',$collegeClass->college_dept_ids) as $index => $subjectDept)
                @if(0 == $index)
                  {{$allCollegeDepts[$subjectDept]}}
                @else
                  ,{{$allCollegeDepts[$subjectDept]}}
                @endif
              @endforeach
            </td>
            <td>
              @foreach(explode(',',$collegeClass->years) as $index => $year)
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
            <td>{{$collegeClass->topic}}</td>
            <td>{{$collegeClass->date}}</td>
            <td>{{$collegeClass->from_time}}</td>
            <td>{{$collegeClass->to_time}}</td>
            <td>
              <a href="{{url('college/'.Session::get('college_user_url').'/collegeExtraClass')}}/{{$collegeClass->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit " />
                </a>
            </td>
            <td>
                <a id="{{$collegeClass->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete" />
                </a>
                <form id="deleteCollegeExtraClass_{{$collegeClass->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCollegeExtraClass')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="class_id" value="{{$collegeClass->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="10">No Classes are created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $collegeClasses->links() }}
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
                  formId = 'deleteCollegeExtraClass_'+id;
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