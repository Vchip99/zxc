@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Designation </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-asterisk"></i> Zero To Hero </li>
      <li class="active"> Manage Designation </li>
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
      <div id="">
        <a href="{{url('admin/createDesignation')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Designation">Add New Designation</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Designation Name</th>
          <th>Edit Designation</th>
          <th>Delete Designation</th>
        </tr>
      </thead>
      <tbody>
        @if(count($designations) > 0)
          @foreach($designations as $index => $designation)
          <tr>
            <th scope="row">{{$index + $designations->firstItem()}}</th>
            <td>{{$designation->name}}</td>
            <td>
              <a href="{{url('admin/designation')}}/{{$designation->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$designation->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$designation->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$designation->name}}" />
                </a>
                <form id="deleteDesignation_{{$designation->id}}" action="{{url('admin/deleteDesignation')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="designation_id" value="{{$designation->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="3">No designation is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $designations->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this designation, all associated areas and records of zero to hero will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteDesignation_'+id;
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