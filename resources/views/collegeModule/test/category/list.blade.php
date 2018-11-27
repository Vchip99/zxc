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
      <li><i class="fa fa-files-o"></i> Online Test </li>
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
      <div >
        <a href="{{url('college/'.Session::get('college_user_url').'/createCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Category">Add New Category</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table id="collegeTestCategory">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Category </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody >
        @if(count($testCategories) > 0)
          @foreach($testCategories as $index => $testCategory)
          <tr style="overflow: auto;">
            <td>{{$index + $testCategories->firstItem()}}</th>
            <td>{{$testCategory->name}}</td>
            <td>
              <a href="{{url('college/'.Session::get('college_user_url').'/category')}}/{{$testCategory->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$testCategory->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$testCategory->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$testCategory->name}}" />
                </a>
                <form id="deleteCategory_{{$testCategory->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCategory')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="category_id" value="{{$testCategory->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No category is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $testCategories->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">
  function confirmDelete(ele){
    $.confirm({
      title: 'Confirmation',
      content: 'If you delete this category, all associated sub categories, subjects, papers and questions of this category will be deleted.',
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