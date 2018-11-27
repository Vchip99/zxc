@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Subject  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-tasks"></i> Assignment </li>
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
    <table class="" id="clientAssignmentSubjectTable">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Subject</th>
          <th>Batch</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody id="clientAssignmentSubjects">
        @if(count($subjects) > 0)
          @foreach($subjects as $index => $subject)
          <tr style="overflow: auto;">
            <td>{{$index + 1}}</td>
            <td>{{$subject->name}}</td>
            <td>
              @if(0 == $subject->client_batch_id || empty($subject->client_batch_id))
                All
              @else
                {{$subject->batch->name}}
              @endif
            </td>
            <td>
              @if(($subject->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $subject->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $subject->client_id))
                <a href="{{url('assignmentSubject')}}/{{$subject->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$subject->name}}" /></a>
              @endif
            </td>
            <td>
              @if(($subject->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $subject->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $subject->client_id))
                <a id="{{$subject->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$subject->name}}" /></a>
                <form id="deleteSubject_{{$subject->id}}" action="{{url('deleteAssignmentSubject')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="subject_id" value="{{$subject->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No subjects are created.</td></tr>
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
              Cancle: function () {
              }
          }
        });
    }

</script>
@stop