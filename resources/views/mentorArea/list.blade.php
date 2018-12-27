@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Mentor Area </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-graduation-cap"></i> Mentor </li>
      <li class="active"> Mentor Area </li>
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
      <div>
        <a href="{{url('admin/createMentorArea')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Area">Add New Area</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table class="table admin_table">
        <thead >
          <tr>
            <th>#</th>
            <th>Area</th>
            <th>Edit </th>
            <th>Delete </th>
          </tr>
        </thead>
        <tbody>
          @if(count($areas) > 0)
            @foreach($areas as $index => $area)
            <tr style="overflow: auto;">
              <th scope="row">{{$index + $areas->firstItem()}}</th>
              <td>{{$area->name}}</td>
              <td>
                <a href="{{url('admin/mentorArea')}}/{{$area->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$area->name}}" />
                  </a>
              </td>
              <td>
              <a id="{{$area->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$area->name}}" />
                  </a>
                  <form id="deleteArea_{{$area->id}}" action="{{url('admin/deleteMentorArea')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="area_id" value="{{$area->id}}">
                  </form>
              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="4">No area is created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{ $areas->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
       $.confirm({
        title: 'Confirmation',
        content: 'If you delete this area, then all skills associated with this area will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteArea_'+id;
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