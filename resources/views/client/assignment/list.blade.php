@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Assignment  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-tasks"></i> Assignment </li>
      <li class="active"> Manage Assignment </li>
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
      <div id="addSubjectDiv">
        <a id="addSubject" href="{{url('createAssignment')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Subject">Add New Assignment</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="clientAssignmentTable">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th style="max-width: 800px;">Question</th>
          <th>Subject Name</th>
          <th>Topic Name</th>
          <th>Edit Subject</th>
          <th>Delete Subject</th>
        </tr>
      </thead>
      <tbody id="assignmentSubjects">
        @if(count($assignments) > 0)
          @foreach($assignments as $index => $assignment)
          <tr>
            <td>{{$index + 1}}</td>
            <td style="max-width: 800px;">{!! mb_strimwidth($assignment->question, 0, 400, "...") !!}</td>
            <td>{{$assignment->subject->name}}</td>
            <td>{{$assignment->topic->name}}</td>
            <td>
              <a href="{{url('assignment')}}/{{$assignment->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$assignment->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$assignment->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$assignment->name}}" />
                </a>
                <form id="delete_{{$assignment->id}}" action="{{url('deleteAssignment')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="assignment_id" value="{{$assignment->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="7">No assignments are created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $assignments->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this assignment, all associated answers will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'delete_'+id;
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