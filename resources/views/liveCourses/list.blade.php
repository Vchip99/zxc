@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Live Courses </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-pie-chart"></i> Live Courses </li>
      <li class="active"> Manage Live Courses </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="form-group row">
      <div >
        <a href="{{url('admin/createLiveCourse')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Live Course">Add New Live Course</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Live Course Name</th>
          <th>Category Name</th>
          <th>Edit Live  Course</th>
          <th>Delete Live Course</th>
        </tr>
      </thead>
      <tbody>
        @if(count($liveCourses) > 0)
        @foreach($liveCourses as $index => $liveCourse)
        <tr>
          <th scope="row">{{$index + 1}}</th>
          <td>{{$liveCourse->name}}</td>
          <td>@if( 1 == $liveCourse->category_id) Technology @else Science @endif</td>
          <td>
            <a href="{{url('admin/liveCourse')}}/{{$liveCourse->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$liveCourse->name}}" />
              </a>
          </td>
          <td>
          <a id="{{$liveCourse->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$liveCourse->name}}" />
              </a>
              <form id="deleteLiveCourses_{{$liveCourse->id}}" action="{{url('admin/deleteLiveCourses')}}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <input type="hidden" name="live_course_id" value="{{$liveCourse->id}}">
              </form>
          </td>
        </tr>
        @endforeach
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $liveCourses->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this live course, all associated videos of this live course will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteLiveCourses_'+id;
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