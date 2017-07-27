@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Courses </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Institute Courses </li>
      <li class="active"> Manage Courses </li>
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
      <div id="addCourseDiv">
        <a id="addCourse" href="{{url('createClientInstituteCourse')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Category">Add New Institute Course</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table class="table admin_table">
        <thead >
          <tr>
            <th>#</th>
            <th>Courses Name</th>
            <th>Edit Courses</th>
            <th>Delete Courses</th>
          </tr>
        </thead>
        <tbody>
          @if(count($instituteCourses) > 0)
            @foreach($instituteCourses as $index => $course)
            <tr>
              <th scope="row">{{ $index + 1 }}</th>
              <td>{{$course->name}}</td>
              <td>
                <a href="{{url('clientInstituteCourse')}}/{{$course->id}}/edit"
                      ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$course->name}}" />
                  </a>
              </td>
              <td>
              <a id="{{$course->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$course->name}}"/>
                  </a>
                  <form id="deleteCourse_{{$course->id}}" action="{{url('deleteClientInstituteCourse')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="course_id" value="{{$course->id}}">
                  </form>

              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="4">No institute Courses are created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{ $instituteCourses->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
        $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this course.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteCourse_'+id;
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