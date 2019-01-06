@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Subject  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Assignment </li>
      <li class="active"> Manage Subject </li>
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
        <a id="addSubject" href="{{url('createAssignmentSubject')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Subject">Add New Subject</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="assignmentSubjects">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Subject Name</th>
          <th>Year</th>
          <th>Edit Subject</th>
          <th>Delete Subject</th>
        </tr>
      </thead>
      <tbody id="assignmentSubject">
        @if(count($subjects) > 0)
          @foreach($subjects as $index => $subject)
          <tr style="overflow: auto;">
            <td>{{$index + $subjects->firstItem()}}</td>
            <td>{{$subject->name}}</td>
            <td>
                @if( 1 == $subject->year)
                  First Year
                @elseif( 2 == $subject->year)
                  Second Year
                @elseif( 3 == $subject->year)
                  Third Year
                @else
                  Fourth Year
                @endif
            </td>
            <td>
              <a href="{{url('assignmentSubject')}}/{{$subject->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$subject->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$subject->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$subject->name}}" />
                </a>
                <form id="deleteSubject_{{$subject->id}}" action="{{url('deleteAssignmentSubject')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="subject_id" value="{{$subject->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No subject is created by you.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $subjects->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this subject, all associated topics, assignments and its answers will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteSubject_'+id;
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