@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Placement Company </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-gift"></i> Placement </li>
      <li class="active"> Manage Placement Company </li>
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
      <a id="addSubCategory" href="{{url('admin/createPlacementCompany')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Placement Company">Add New Placement Company</a>&nbsp;&nbsp;
    </div>
  </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Company Name</th>
          <th>Area Name</th>
          <th>Edit Company</th>
          <th>Delete Company</th>
        </tr>
      </thead>
      <tbody>
        @if(count($placementCompanies)>0)
          @foreach($placementCompanies as $index => $placementCompany)
          <tr style="overflow: auto;">
            <td scope="row">{{$index + $placementCompanies->firstItem()}}</td>
            <td>{{$placementCompany->name}}</td>
            <td>{{$placementCompany->area->name}}</td>
            <td>
              <a href="{{url('admin/placementCompany')}}/{{$placementCompany->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$placementCompany->name}}" />
                </a>
            </td>
            <td>
                <a id="{{$placementCompany->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$placementCompany->name}}" />
                <form id="deletePlacementCompany_{{$placementCompany->id}}" action="{{url('admin/deletePlacementCompany')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="company_id" value="{{$placementCompany->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No placement company is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $placementCompanies->links() }}
    </div>
  </div>
  </div>

  <script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'If you delete this company, its details, process and all associated faq will be deleted.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deletePlacementCompany_'+id;
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