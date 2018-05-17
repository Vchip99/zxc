@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Advertisement Page </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-television"></i> Advertisement Page </li>
      <li class="active"> Manage Advertisement Page </li>
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
      <div id="addCategoryDiv">
        <a id="addCategory" href="{{url('admin/createAdvertisementPage')}}" type="button" class="btn btn-primary" style="float: right;" title="Add Advertisement Page">Add Advertisement Page</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Page Name</th>
          <th>Edit Page</th>
          <th>Delete Page</th>
        </tr>
      </thead>
      <tbody>
        @if(count($advertisementPages) > 0)
          @foreach($advertisementPages as $index => $advertisementPage)
          <tr>
            <th scope="row">{{$index + 1}}</th>
            <td>{{$advertisementPage->name}}</td>
            <td>
              <a href="{{url('admin/advertisementPage')}}/{{$advertisementPage->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$advertisementPage->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$advertisementPage->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$advertisementPage->name}}" />
                </a>
                <form id="deleteAdvertisementPage_{{$advertisementPage->id}}" action="{{url('admin/deleteAdvertisementPage')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="page_id" value="{{$advertisementPage->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No advertisement page is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $advertisementPages->links() }}
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
                    formId = 'deleteAdvertisementPage_'+id;
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