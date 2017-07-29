@extends('client.dashboard')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Course </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses </li>
      <li class="active"> Manage Course </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container ">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="form-group row">
      <div >
        <a href="{{url('createOnlineCourse')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Course">Add New Course</a>&nbsp;&nbsp;&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Course Name</th>
          <th>Category Name</th>
          <th>Sub Category Name</th>
          <th>Institute Course Name</th>
          <th>Edit Course</th>
          <th>Delete Course</th>
        </tr>
      </thead>
      <tbody>
        @if(count($courses) > 0)
          @foreach($courses as $index => $course)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$course->name}}</td>
            <td>{{$course->category}}</td>
            <td>{{$course->subcategory}}</td>
            <td>{{$course->instituteCourse->name}}</td>
            <td>
              <a href="{{url('onlinecourse')}}/{{$course->id}}/edit"
                    ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$course->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$course->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$course->name}}" />
                </a>
                <form id="deleteCourse_{{$course->id}}" action="{{url('deleteOnlineCourse')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="course_id" value="{{$course->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
            <tr><td colspan="7">No courses are created.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this course, all associated videos of this course will be deleted.',
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