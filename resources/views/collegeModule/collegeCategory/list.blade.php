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
    <h1> Manage Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Category </li>
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
      <div id="addCategoryDiv">
        <a id="addCategory" href="{{url('college/'.Session::get('college_user_url').'/createCollegeCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Category">Add New Category</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table id="collegeCourseCategory">
        <thead >
          <tr>
            <th>#</th>
            <th>Category</th>
            <th>Created By</th>
            <th>Edit </th>
            <th>Delete </th>
          </tr>
        </thead>
        <tbody>
          @if(count($collegeCategories) > 0)
            @foreach($collegeCategories as $index => $collegeCategory)
            <tr style="overflow: auto;">
              <td>{{$index + $collegeCategories->firstItem()}}</th>
              <td>{{$collegeCategory->name}} </td>
              <td>{{$collegeCategory->created_by_name}} </td>
              <td>
                @if($collegeCategory->user_id == Auth::User()->id || (4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type))
                <a href="{{url('college/'.Session::get('college_user_url').'/collegeCategory')}}/{{$collegeCategory->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$collegeCategory->name}}" />
                  </a>
                @endif
              </td>
              <td>
                @if($collegeCategory->user_id == Auth::User()->id || (4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type))
                  <a id="{{$collegeCategory->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$collegeCategory->name}}" />
                  </a>
                  <form id="deleteCategory_{{$collegeCategory->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCollegeCategory')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="category_id" value="{{$collegeCategory->id}}">
                  </form>
                  @endif
              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="5">No category is created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{ $collegeCategories->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
       $.confirm({
        title: 'Confirmation',
        content: 'If you delete this category, then all related to courses, tests and projects associated with this category will be deleted.',
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