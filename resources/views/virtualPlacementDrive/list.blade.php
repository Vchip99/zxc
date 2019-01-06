@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Virtual Placement Drive </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-handshake-o"></i> Virtual Placement Drive </li>
      <li class="active"> Virtual Placement Drive </li>
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
        <a href="{{url('admin/createVirtualPlacementDrive')}}" type="button" class="btn btn-primary" style="float: right;" title="Add Virtual Placement Drive">Add Virtual Placement Drive</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Virtual Placement Drive</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>
        @if(count($virtualPlacementDrives) > 0)
          @foreach($virtualPlacementDrives as $index => $virtualPlacementDrive)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + 1}}</th>
            <td>{{$virtualPlacementDrive->name}}</td>
            <td>
              <a href="{{url('admin/virtualPlacementDrive')}}/{{$virtualPlacementDrive->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$virtualPlacementDrive->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$virtualPlacementDrive->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$virtualPlacementDrive->name}}" />
                </a>
                <form id="deleteVirtualPlacementDrive_{{$virtualPlacementDrive->id}}" action="{{url('admin/deleteVirtualPlacementDrive')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="placement_id" value="{{$virtualPlacementDrive->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No virtual placement drive is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;" id="pagination">
      {{ $virtualPlacementDrives->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'Are you sure , you want to delete this virtual placement drive.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteVirtualPlacementDrive_'+id;
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