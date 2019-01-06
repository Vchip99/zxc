@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Workshop Video </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-link"></i> Workshop </li>
      <li class="active"> Manage Workshop Video </li>
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
        <a href="{{url('admin/createWorkshopVideo')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Workshop">Add New Workshop Video</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Video Name</th>
          <th>Workshop Name</th>
          <th>Category Name</th>
          <th>Edit Category</th>
          <th>Delete Workshop</th>
        </tr>
      </thead>
      <tbody>
        @if(count($workshopVideos) > 0)
          @foreach($workshopVideos as $index => $workshopVideo)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + 1}}</th>
            <td>{{$workshopVideo->name}}</td>
            <td>{{$workshopVideo->workshop->name}}</td>
            <td>{{$workshopVideo->category->name}}</td>
            <td>
              <a href="{{url('admin/workshopVideo')}}/{{$workshopVideo->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$workshopVideo->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$workshopVideo->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$workshopVideo->name}}" />
                </a>
                <form id="deleteWorkshopVideo_{{$workshopVideo->id}}" action="{{url('admin/deleteWorkshopVideo')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="video_id" value="{{$workshopVideo->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No workshop video is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;" id="pagination">
      {{ $workshopVideos->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'You want to delete this Workshop Video?',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteWorkshopVideo_'+id;
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