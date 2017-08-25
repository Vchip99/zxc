@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Sub Admin </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-user"></i> Sub Admin </li>
      <li class="active"> Manage Sub Admin </li>
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
      <div id="addUserDiv">
        <a id="addUser" href="{{url('admin/createSubAdmin')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Sub Admin">Add New Sub Admin</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Sub Admin Name</th>
          <th>Edit Sub Admin</th>
          <!-- <th>Delete Sub Admin</th> -->
        </tr>
      </thead>
      <tbody>
        @if(count($subadmins) > 0)
        @foreach($subadmins as $index => $subadmin)
        <tr>
          <th scope="row">{{$subadmin->id}}</th>
          <td>{{$subadmin->name}}</td>
          <td>
            <a href="{{url('admin/subadmin')}}/{{$subadmin->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$subadmin->name}}" />
              </a>
          </td>
         <!--  <td>
          <a id="{{$subadmin->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30'/>
              </a>
              <form id="deleteSubadmin_{{$subadmin->id}}" action="{{url('admin/deleteSubadmin')}}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <input type="hidden" name="subadmin_id" value="{{$subadmin->id}}">
              </form>

          </td> -->
        </tr>
        @endforeach
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $subadmins->links()}}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'You want to delete this subadmin?',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteSubadmin_'+id;
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