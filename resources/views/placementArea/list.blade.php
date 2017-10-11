@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Placement Area </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-link"></i> Placement </li>
      <li class="active"> Manage Placement Area </li>
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
      <div id="addCategoryDiv">
        <a id="addCategory" href="{{url('admin/createPlacementArea')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Placement Area">Add New Placement Area</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Area Name</th>
          <th>Edit Area</th>
          <th>Delete Area</th>
        </tr>
      </thead>
      <tbody>
        @if(count($placementAreas) > 0)
          @foreach($placementAreas as $index => $placementArea)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$placementArea->name}}</td>
            <td>
              <a href="{{url('admin/placementArea')}}/{{$placementArea->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$placementArea->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$placementArea->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$placementArea->name}}" />
                </a>
                <form id="deletePlacementArea_{{$placementArea->id}}" action="{{url('admin/deletePlacementArea')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="area_id" value="{{$placementArea->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No placement area is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $placementAreas->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this area, all companies associated with this area, companies details and its process, its associated faq will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deletePlacementArea_'+id;
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