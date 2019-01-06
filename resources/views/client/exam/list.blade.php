@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Exam </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Calendar Management </li>
      <li class="active"> Manage Exam </li>
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
      <div>
        <a href="{{url('createClientExam')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Exam">Add New Exam</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table class="" id="clientExams">
        <thead class="thead-inverse">
          <tr>
            <th>#</th>
            <th>Batch</th>
            <th>Type</th>
            <th>Exam</th>
            <th>Subject</th>
            <th>Topic</th>
            <th>Mark</th>
            <th>Date</th>
            <th>From</th>
            <th>To</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody id="">
          @if(count($exams) > 0)
            @foreach($exams as $index => $exam)
            <tr style="overflow: auto;">
              <td>{{$index + 1}}</td>
              <td>
                @if(0 == $exam->client_batch_id)
                  All
                @else
                  {{$exam->batch->name}}
                @endif
              </td>
              <td>
                @if(1 == $exam->exam_type)
                  Online
                @else
                  Offline
                @endif
              </td>
              <td>{{$exam->name}}</td>
              <td>{{$exam->subject}}</td>
              <td>{{$exam->topic}}</td>
              <td>{{$exam->marks}}</td>
              <td>{{$exam->date}}</td>
              <td>{{$exam->from_time}}</td>
              <td>{{$exam->to_time}}</td>
              <td>
                <a href="{{url('exam')}}/{{$exam->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$exam->name}}" />
                  </a>
              </td>
              <td>
              <a id="{{$exam->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$exam->name}}" />
                  </a>
                  <form id="deleteExam_{{$exam->id}}" action="{{url('deleteExam')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="exam_id" value="{{$exam->id}}">
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
        {{ $exams->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">
    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this exam.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteExam_'+id;
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