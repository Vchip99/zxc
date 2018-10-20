@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage College Info </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-university"></i> Add College Info </li>
      <li class="active"> Manage College Info </li>
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
      <div id="addCollegeDiv">
        <a id="addCollege" href="{{url('admin/createCollege')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New College">Add New College</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>College</th>
          <th>Url/Extention</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>
        @if(count($colleges) > 0)
          @foreach($colleges as $index => $college)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$college->name}}</td>
            <td>{{$college->url}}</td>
            <td>
              <a href="{{url('admin/college')}}/{{$college->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$college->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$college->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$college->name}}" />
                </a>
                <form id="deleteCollege_{{$college->id}}" action="{{url('admin/deleteCollege')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="college_id" value="{{$college->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="3">No college is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $colleges->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this college, all associated information of this college will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteCollege_'+id;
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