@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
&nbsp;
@section('module_title')
  <section class="content-header">
    <h1> Manage Sub Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses </li>
      <li class="active"> Manage Sub Category </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
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
        <a id="addSubCategory" href="{{url('createOnlineSubCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Sub Category">Add New Sub Category</a>&nbsp;&nbsp;
      </div>
    </div>
  <div >
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Sub Category </th>
          <th>Category </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($subCategories) > 0)
          @foreach($subCategories as $index => $subCategory)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + 1}}</th>
            <td>{{$subCategory->name}}</td>
            <td>{{$subCategory->category->name}}</td>
            <td>
              @if(($subCategory->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $subCategory->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $subCategory->client_id))
                <a href="{{url('onlinesubcategory')}}/{{$subCategory->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$subCategory->name}}" /></a>
              @endif
            </td>
            <td>
              @if(($subCategory->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $subCategory->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $subCategory->client_id))
                <a id="{{$subCategory->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$subCategory->name}}" />
                <form id="deleteSubCategory_{{$subCategory->id}}" action="{{url('deleteOnlineSubCategory')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="subCategory_id" value="{{$subCategory->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="6">No sub categories are created.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
</div>

  <script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this sub category, all associated courses and videos will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteSubCategory_'+id;
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