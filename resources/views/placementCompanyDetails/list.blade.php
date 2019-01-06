@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Placement Company Details</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-gift"></i> Placement </li>
      <li class="active"> Manage Placement Company Details</li>
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
      <a id="addSubCategory" href="{{url('admin/createPlacementCompanyDetails')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Company Details">Add New Company Details</a>&nbsp;&nbsp;
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
          <th>Delete Details</th>
        </tr>
      </thead>
      <tbody>
        @if(count($companyDetails)>0)
          @foreach($companyDetails as $index => $companyDetail)
          <tr style="overflow: auto;">
            <td scope="row">{{$index + $companyDetails->firstItem()}}</td>
            <td>{{$companyDetail->area->name}}</td>
            <td>{{$companyDetail->company->name}}</td>
            <td>
              <a href="{{url('admin/placementCompanyDetail')}}/{{$companyDetail->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$companyDetail->name}}" />
                </a>
            </td>
            <td>
                <a id="{{$companyDetail->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$companyDetail->name}}" />
                <form id="deleteCompanyDetail_{{$companyDetail->id}}" action="{{url('admin/deleteCompanyDetails')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="details_id" value="{{$companyDetail->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No company details is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $companyDetails->links() }}
    </div>
  </div>
  </div>

  <script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'If you delete this company details, its process and all associated faq will be deleted.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteCompanyDetail_'+id;
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