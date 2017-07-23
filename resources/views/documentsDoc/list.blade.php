@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Document </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Documents </li>
      <li class="active"> Manage Document </li>
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
      <div id="addCategoryDiv">
        <a id="addCategory" href="{{url('admin/createDocumentsDoc')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Document">Add New Document</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Document Name</th>
          <th>Category Name</th>
          <th>Edit Document</th>
          <th>Delete Document</th>
        </tr>
      </thead>
      <tbody>
        @if(count($documentsDocs) > 0)
          @foreach($documentsDocs as $index => $documentsDoc)
          <tr>
            <th scope="row">{{$index+1}}</th>
            <td>{{$documentsDoc->name}}</td>
            <td>{{$documentsDoc->category->name}}</td>
            <td>
              <a href="{{url('admin/documentDoc')}}/{{$documentsDoc->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$documentsDoc->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$documentsDoc->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$documentsDoc->name}}" />
                </a>
                <form id="deleteDocument_{{$documentsDoc->id}}" action="{{url('admin/deleteDocumentsDoc')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="document_id" value="{{$documentsDoc->id}}">
                </form>

            </td>
          </tr>
          @endforeach
        @else
          <tr><td>No document is created.</td></tr>
        @endif
      </tbody>
    </table>
     <div style="float: right;">
      {{ $documentsDocs->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'You want to delete this document?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteDocument_'+id;
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