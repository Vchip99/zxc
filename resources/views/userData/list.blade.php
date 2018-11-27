@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> User Data </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Users Info </li>
      <li class="active"> User Data </li>
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
      <div>
        <a href="{{url('admin/createUserData')}}" type="button" class="btn btn-primary" style="float: right;" title="Add User Data">Add User Data</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>User </th>
          <th>Paper </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($userDatas) > 0)
          @foreach($userDatas as $index => $userData)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + $userDatas->firstItem()}}</th>
            <td>{{$userData->user->name}}</td>
            <td>{{$userData->paper->name}}</td>
            <td>
              <a href="{{url('admin/userData')}}/{{$userData->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$userData->name}}" />
                </a>
            </td>
            <td>
              <a id="{{$userData->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$userData->name}}" />
                </a>
                <form id="deleteUserData_{{$userData->id}}" action="{{url('admin/deleteUserData')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="user_data_id" value="{{$userData->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td>No User Data is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $userDatas->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'You want to delete this user data.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteUserData_'+id;
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