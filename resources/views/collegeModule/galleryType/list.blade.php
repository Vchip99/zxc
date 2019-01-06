@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Gallery Types </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Gallery </li>
      <li class="active"> Manage Gallery Types </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="form-group row">
      <div>
        <a href="{{url('college/'.Session::get('college_user_url').'/createCollegeGalleryType')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add Gallery Type">Add Gallery Type</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table class="" id="collegeGalleryType">
        <thead class="thead-inverse">
          <tr>
            <th>#</th>
            <th>Type</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody id="">
          @if(count($galleryTypes) > 0)
            @foreach($galleryTypes as $index => $galleryType)
            <tr style="overflow: auto;">
              <td>{{$index + 1}}</td>
              <td>
                  {{$galleryType->name}}
              </td>
              <td>
                <a href="{{url('college/'.Session::get('college_user_url').'/collegeGalleryType')}}/{{$galleryType->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$galleryType->name}}" />
                  </a>
              </td>
              <td>
              <a id="{{$galleryType->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$galleryType->name}}" />
                  </a>
                  <form id="deleteGalleryType_{{$galleryType->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCollegeGalleryType')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="gallery_type_id" value="{{$galleryType->id}}">
                  </form>
              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="4">No gallery types.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{ $galleryTypes->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">
    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this gallery type.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteGalleryType_'+id;
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