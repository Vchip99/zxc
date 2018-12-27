@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Mentors </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-graduation-cap"></i> Mentor </li>
      <li class="active"> Mentors </li>
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
    <div>
      <table class="table admin_table">
        <thead >
          <tr>
            <th>#</th>
            <th>Mentor</th>
            <th>Delete </th>
          </tr>
        </thead>
        <tbody>
          @if(count($mentors) > 0)
            @foreach($mentors as $index => $mentor)
            <tr style="overflow: auto;">
              <th scope="row">{{$index + $mentors->firstItem()}}</th>
              <td>{{$mentor->name}}</td>
              <td>
              <a id="{{$mentor->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$mentor->name}}" />
                  </a>
                  <form id="deleteMentor_{{$mentor->id}}" action="{{url('admin/deleteMentor')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="mentor_id" value="{{$mentor->id}}">
                  </form>
              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="4">No mentor is created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{ $mentors->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
       $.confirm({
        title: 'Confirmation',
        content: 'If you delete mentor, then all info associated with this mentor will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteMentor_'+id;
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