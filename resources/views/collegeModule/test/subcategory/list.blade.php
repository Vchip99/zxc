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
      <li><i class="fa fa-files-o"></i> Online Test </li>
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
    <div>
      <a href="{{url('college/'.Session::get('college_user_url').'/createSubCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Sub Category">Add New Sub Category</a>&nbsp;&nbsp;
    </div>
  </div>
  <div>
    <table id="collegeTestSubCategory">
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
        @if(count($testSubCategories)>0)
          @foreach($testSubCategories as $index => $testSubCategory)
          <tr style="overflow: auto;">
            <td>{{$index + $testSubCategories->firstItem()}}</th>
            <td>{{$testSubCategory->name}}</td>
            <td>{{$testSubCategory->category}}</td>
            <td>{{$testSubCategory->created_by_name}}</td>
            <td>
              @if($testSubCategory->created_by == Auth::User()->id || (4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type))
              <a href="{{url('college/'.Session::get('college_user_url').'/subCategory')}}/{{$testSubCategory->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$testSubCategory->name}}" />
                </a>
              @endif
            </td>
            <td>
              @if($testSubCategory->created_by == Auth::User()->id || (4 ==  Auth::User()->user_type || 5 ==  Auth::User()->user_type))
                <a id="{{$testSubCategory->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$testSubCategory->name}}" />
                <form id="deleteSubCategory_{{$testSubCategory->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteSubCategory')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="subcat_id" value="{{$testSubCategory->id}}">
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
      {{ $testSubCategories->links() }}
    </div>
  </div>
  </div>

  <script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'If you delete this sub category, all associated subjects, papers and questions of this sub category will be deleted.',
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