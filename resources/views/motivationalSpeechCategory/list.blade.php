@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Speaker </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-microphone"></i> Motivational Speech </li>
      <li class="active"> Manage Speaker </li>
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
      <div >
        <a href="{{url('admin/createMotivationalSpeechCategory')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Speaker">Add New Speaker</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Speaker Name</th>
          <th>Edit Speaker</th>
          <th>Delete Speaker</th>
        </tr>
      </thead>
      <tbody>
        @if(count($motivationalSpeechCategories) > 0)
          @foreach($motivationalSpeechCategories as $index => $motivationalSpeechCategory)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$motivationalSpeechCategory->name}}</td>
            <td>
              <a href="{{url('admin/motivationalSpeechCategory')}}/{{$motivationalSpeechCategory->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$motivationalSpeechCategory->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$motivationalSpeechCategory->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$motivationalSpeechCategory->name}}" />
                </a>
                <form id="deleteWorkshopCategory_{{$motivationalSpeechCategory->id}}" action="{{url('admin/deleteMotivationalSpeechCategory')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="category_id" value="{{$motivationalSpeechCategory->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No speaker is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;" id="pagination">
      {{ $motivationalSpeechCategories->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'If you delete this speaker, all associated motivational speeches will be deleted.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteWorkshopCategory_'+id;
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