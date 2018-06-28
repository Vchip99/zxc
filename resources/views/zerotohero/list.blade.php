@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Zero To Hero </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-asterisk"></i> Zero To Hero </li>
      <li class="active"> Manage Zero To Hero </li>
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
        <a href="{{url('admin/createZeroToHero')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Zero To Hero">Add New Zero To Hero</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Zero To Hero Name</th>
          <th>Designation</th>
          <th>Area</th>
          <th>Edit Zero To Hero</th>
          <th>Delete Zero To Hero</th>
        </tr>
      </thead>
      <tbody>
        @if(count($heros) > 0)
          @foreach($heros as $index => $hero)
          <tr>
            <th scope="row">{{$index + $heros->firstItem()}}</th>
            <td>{{$hero->name}}</td>
            <td>{{$hero->designation->name}}</td>
            <td>{{$hero->area->name}}</td>
            <td>
              <a href="{{url('admin/herotozero')}}/{{$hero->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$hero->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$hero->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$hero->name}}" />
                </a>
                <form id="deleteHero_{{$hero->id}}" action="{{url('admin/deleteHero')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="hero_id" value="{{$hero->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="5">No Zero To Hero is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $heros->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this Zero To Hero?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteHero_'+id;
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