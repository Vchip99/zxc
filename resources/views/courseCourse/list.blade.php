@extends('admin.master')
@section('admin_content')
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
  <div class="container ">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="form-group row">
      <div >
        <a href="{{url('admin/createCourseCourse')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Course"> Add New Course</a>&nbsp;&nbsp;&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Course </th>
          <th>Category </th>
          <th>Sub Category </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($courseCourses) > 0)
          @foreach($courseCourses as $index => $courseCourse)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + $courseCourses->firstItem()}}</th>
            <td>{{$courseCourse->name}}</td>
            <td>{{$courseCourse->category}}</td>
            <td>{{$courseCourse->subcategory}}</td>
            <td>
              <a href="{{url('admin/courseCourse')}}/{{$courseCourse->id}}/edit"
                    ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$courseCourse->name}}" />
                </a>
            </td>
            <td>
              @if($courseCourse->admin_id == Auth::guard('admin')->user()->id)
                <a id="{{$courseCourse->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$courseCourse->name}}" />
                </a>
                <form id="deleteCourse_{{$courseCourse->id}}" action="{{url('admin/deleteCourseCourse')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="course_id" value="{{$courseCourse->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
            <tr><td colspan="6">No course is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{$courseCourses->links()}}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
       $.confirm({
        title: 'Confirmation',
        content: 'If you delete this course, then all videos associated with this course will be deleted.',
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