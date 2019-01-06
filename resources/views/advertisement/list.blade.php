@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Advertisement </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-television"></i> Advertisement Page </li>
      <li class="active"> Manage Advertisement </li>
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
        <a id="" href="{{url('admin/createAdvertisement')}}" type="button" class="btn btn-primary" style="float: right;" title="Add Advertisement ">Add Advertisement </a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Image Name</th>
          <th>Url</th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($advertisements) > 0)
          @foreach($advertisements as $index => $advertisement)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + $advertisements->firstItem()}}</th>
            <td>{{ basename($advertisement->image)}}</td>
            <td>{{ $advertisement->url }}</td>
            <td>
              <a href="{{url('admin/advertisement')}}/{{$advertisement->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit" />
                </a>
            </td>
            <td>
              @if($advertisement->admin_id == Auth::guard('admin')->user()->id)
                <a id="{{$advertisement->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete" />
                </a>
                <form id="deleteAdvertisement_{{$advertisement->id}}" action="{{url('admin/deleteAdvertisement')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="advertisement_id" value="{{$advertisement->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No advertisement is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $advertisements->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this page?.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteAdvertisement_'+id;
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