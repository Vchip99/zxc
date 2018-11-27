@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Skills </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Users Info </li>
      <li class="active"> Manage Skills </li>
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
        <a href="{{url('admin/createSkill')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Skill">Add New Skill</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Skill </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($skills) > 0)
          @foreach($skills as $index => $skill)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + $skills->firstItem()}}</th>
            <td>{{$skill->name}}</td>
            <td>
              <a href="{{url('admin/skill')}}/{{$skill->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$skill->name}}" />
                </a>
            </td>
            <td>
              <a id="{{$skill->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$skill->name}}" />
                </a>
                <form id="deleteSkill_{{$skill->id}}" action="{{url('admin/deleteSkill')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="skill_id" value="{{$skill->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td>No skill is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $skills->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'You want to delete this skill.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteSkill_'+id;
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