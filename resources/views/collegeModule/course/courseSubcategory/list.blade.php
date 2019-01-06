@extends('dashboard.dashboard')
@section('dashboard_header')
  <style type="text/css">
    .btn-primary{
      width: 150px;
    }
  </style>
@stop
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
  <div class="container">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="form-group row">
      <div id="addSubCategoryDiv">
        <a id="addSubCategory" href="{{url('college/'.Session::get('college_user_url').'/createCourseSubCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Sub Category">Add New Sub Category</a>&nbsp;&nbsp;
      </div>
    </div>
  <div >
    <table id="collegeCourseSubCategory">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Sub Category </th>
          <th>Category </th>
          <th>Created By </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($courseSubCategories) > 0)
          @foreach($courseSubCategories as $index => $courseSubCategory)
          <tr style="overflow: auto;">
            <td>{{$index + $courseSubCategories->firstItem()}}</th>
            <td>{{$courseSubCategory->name}}</td>
            <td>{{$courseSubCategory->category}}</td>
            <td>{{$courseSubCategory->created_by_name}}</td>
            <td>
              @if($courseSubCategory->created_by == Auth::User()->id || (4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type))
              <a href="{{url('college/'.Session::get('college_user_url').'/coursesubcategory')}}/{{$courseSubCategory->id}}/edit"
                    ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$courseSubCategory->name}}" />
                </a>
              @endif
            </td>
            <td>
              @if($courseSubCategory->created_by == Auth::User()->id || (4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type))
                <a id="{{$courseSubCategory->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$courseSubCategory->name}}" />
                <form id="deleteCourseSubCategory_{{$courseSubCategory->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCourseSubCategory')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="subCategory_id" value="{{$courseSubCategory->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
            <tr><td colspan="6">No sub category is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $courseSubCategories->links() }}
    </div>
  </div>
</div>

  <script type="text/javascript">

    function confirmDelete(ele){
       $.confirm({
        title: 'Confirmation',
        content: 'If you delete this sub category, then all courses and videos associated with this sub category will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteCourseSubCategory_'+id;
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