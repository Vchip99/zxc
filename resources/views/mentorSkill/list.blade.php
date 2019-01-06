@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Mentor Skill </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-graduation-cap"></i> Mentor </li>
      <li class="active"> Mentor Skill </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <div class="container">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="form-group row">
      <div>
        <a href="{{url('admin/createMentorSkill')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Skill">Add New Skill</a>&nbsp;&nbsp;
      </div>
    </div>
  <div >
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Skill </th>
          <th>area </th>
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
            <td>{{$areaNames[$skill->mentor_area_id]}}</td>
            <td>
              <a href="{{url('admin/mentorSkill')}}/{{$skill->id}}/edit"
                    ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$skill->name}}" />
                </a>
            </td>
            <td>
                <a id="{{$skill->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$skill->name}}" />
                <form id="deleteMentorSkill_{{$skill->id}}" action="{{url('admin/deleteMentorSkill')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="skill_id" value="{{$skill->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
            <tr><td colspan="5">No Skill is created.</td></tr>
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
        content: 'Are you sure, you want to delete this skill.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteMentorSkill_'+id;
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