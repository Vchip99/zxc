@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Topic  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-tasks"></i> Assignment </li>
      <li class="active"> Manage Topic </li>
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
      <div id="addTopicDiv">
        <a id="addTopic" href="{{url('createAssignmentTopic')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Topic">Add New Topic</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="clientAssignmentTopicTable">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Topic Name</th>
          <th>Subject Name</th>
          <th>Edit Topic</th>
          <th>Delete Topic</th>
        </tr>
      </thead>
      <tbody id="assignmentTopics">
        @if(count($topics) > 0)
          @foreach($topics as $index => $topic)
          <tr>
            <td>{{$index + 1}}</td>
            <td>{{$topic->name}}</td>
            <td>{{$topic->subject->name}}</td>
            <td>
              <a href="{{url('assignmentTopic')}}/{{$topic->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$topic->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$topic->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$topic->name}}" />
                </a>
                <form id="deleteTopic_{{$topic->id}}" action="{{url('deleteAssignmentTopic')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="topic_id" value="{{$topic->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="6">No topics are created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $topics->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this topic, all associated assignments and its answers will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteTopic_'+id;
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