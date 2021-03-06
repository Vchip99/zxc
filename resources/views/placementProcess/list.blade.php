@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Placement Process</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-gift"></i> Placement </li>
      <li class="active"> Manage Placement Process</li>
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
    <div id="addSubCategoryDiv">
      <a id="addSubCategory" href="{{url('admin/createPlacementProcess')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Placement Process">Add New Placement Process</a>&nbsp;&nbsp;
    </div>
  </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Company Name</th>
          <th>Area Name</th>
          <th>Edit Placement Process</th>
          <th>Delete Process</th>
        </tr>
      </thead>
      <tbody>
        @if(count($placementProcesses)>0)
          @foreach($placementProcesses as $index => $placementProcess)
          <tr style="overflow: auto;">
            <td scope="row">{{$index + $placementProcesses->firstItem()}}</td>
            <td>{{$placementProcess->area->name}}</td>
            <td>{{$placementProcess->company->name}}</td>
            <td>
              <a href="{{url('admin/placementCompanyProcess')}}/{{$placementProcess->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$placementProcess->name}}" />
                </a>
            </td>
            <td>
                <a id="{{$placementProcess->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$placementProcess->name}}" />
                <form id="deletePlacementProcess_{{$placementProcess->id}}" action="{{url('admin/deletePlacementProcess')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="process_id" value="{{$placementProcess->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No placement process is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $placementProcesses->links() }}
    </div>
  </div>
  </div>

  <script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'If you delete this placement process, all associated faq will be deleted.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deletePlacementProcess_'+id;
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