@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Sub Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-share"></i> Question Bank </li>
      <li class="active"> Manage Sub Category </li>
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
      <a id="addSubCategory" href="{{url('admin/createQuestionBankSubCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Sub Category">Add New Sub Category</a>&nbsp;&nbsp;
    </div>
  </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Sub Category Name</th>
          <th>Category Name</th>
          <th>Edit Sub Category</th>
          <th>Delete Sub Category</th>
        </tr>
      </thead>
      <tbody>
        @if(count($testSubCategories)>0)
          @foreach($testSubCategories as $index => $testSubCategory)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + $testSubCategories->firstItem()}}</th>
            <td>{{$testSubCategory->name}}</td>
            <td>{{$testSubCategory->category->name}}</td>
            <td>
              <a href="{{url('admin/questionBankSubCategory')}}/{{$testSubCategory->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$testSubCategory->name}}" />
                </a>
            </td>
            <td>
                <a id="{{$testSubCategory->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$testSubCategory->name}}" />
                <form id="deleteSubCategory_{{$testSubCategory->id}}" action="{{url('admin/deleteQuestionBankSubCategory')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="subcat_id" value="{{$testSubCategory->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td>No sub category is created.</td></tr>
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
          content: 'If you delete this sub category, all associated questions of this sub category will be deleted.',
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