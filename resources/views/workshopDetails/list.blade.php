@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Workshop Details </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-link"></i> Workshop </li>
      <li class="active"> Manage Workshop Details </li>
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
        <a href="{{url('admin/createWorkshopDetails')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Workshop">Add New Workshop</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Workshop Name</th>
          <th>Category Name</th>
          <th>Edit Category</th>
          <th>Delete Workshop</th>
        </tr>
      </thead>
      <tbody>
        @if(count($workshopDetails) > 0)
          @foreach($workshopDetails as $index => $workshopDetail)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$workshopDetail->name}}</td>
            <td>{{$workshopDetail->category->name}}</td>
            <td>
              <a href="{{url('admin/workshopDetails')}}/{{$workshopDetail->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$workshopDetail->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$workshopDetail->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$workshopDetail->name}}" />
                </a>
                <form id="deleteWorkshopDetail_{{$workshopDetail->id}}" action="{{url('admin/deleteWorkshopDetails')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="workshop_id" value="{{$workshopDetail->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No workshop details is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;" id="pagination">
      {{ $workshopDetails->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'If you delete this workshop, all associated videos of this workshop will be deleted.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteWorkshopDetail_'+id;
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