@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Manage Category </li>
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
      <div id="addCategoryDiv">
        <b>Note: * means Category have purchased sub category.</b>
        <a id="addCategory" href="{{url('createOnlineTestCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Category">Add New Category</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Category </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($testCategories) > 0)
          @foreach($testCategories as $index => $testCategory)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            @if(in_array($testCategory->id, $isPurchasedSubCategories))
              <td>{{$testCategory->name}}*</td>
            @else
              <td>{{$testCategory->name}}</td>
            @endif
            <td>
              @if(($testCategory->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $testCategory->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $testCategory->client_id))
                <a href="{{url('onlinetestcategory')}}/{{$testCategory->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$testCategory->name}}" /></a>
              @endif
            </td>
            <td>
              @if(($testCategory->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $testCategory->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $testCategory->client_id))
                @if(in_array($testCategory->id, $isPurchasedSubCategories))
                  <a id="{{$testCategory->id}}" onclick="confirmDelete(this, true);">
                @else
                  <a id="{{$testCategory->id}}" onclick="confirmDelete(this, false);">
                @endif
                  <img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$testCategory->name}}" />
                </a>
                <form id="deleteCategory_{{$testCategory->id}}" action="{{url('deleteOnlineTestCategory')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="category_id" value="{{$testCategory->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No categories are created.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele, isPurchasedSubCategory){
      if(isPurchasedSubCategory){
        message = 'This Category have purchase sub category and if you delete this category, all associated sub categories and purchased subcategory, subjects, papers and questions will be deleted.'
      } else {
        message = 'If you delete this category, all associated sub categories, subjects, papers and questions will be deleted.'
      }
      $.confirm({
        title: 'Confirmation',
        content: message,
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteCategory_'+id;
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