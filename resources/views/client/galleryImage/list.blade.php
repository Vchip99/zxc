@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Gallery Images </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-picture-o"></i> Gallery </li>
      <li class="active"> Manage Gallery Images </li>
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
        <a href="{{url('createClientGalleryImage')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add Gallery Image">Add Gallery Image</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table class="" id="collegeGalleryImage">
        <thead class="thead-inverse">
          <tr>
            <th>#</th>
            <th>Type</th>
            <th>Images</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody id="">
          @if(count($galleryImages) > 0)
            @foreach($galleryImages as $index => $galleryImage)
            <tr style="overflow: auto;">
              <td>{{$index + 1}}</td>
              <td>{{$galleryTypes[$galleryImage->client_gallery_type_id]}}</td>
              <td>
                @foreach(explode(',',$galleryImage->images) as $index => $image)
                  @if(0 == $index)
                    {{ basename($image) }}
                  @else
                    ,{{ basename($image) }}
                  @endif
                @endforeach
              </td>
              <td>
                <a href="{{url('galleryImage')}}/{{$galleryImage->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$galleryImage->name}}" />
                  </a>
              </td>
              <td>
              <a id="{{$galleryImage->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$galleryImage->name}}" />
                  </a>
                  <form id="deleteGalleryImage_{{$galleryImage->id}}" action="{{url('deleteGalleryImage')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="gallery_image_id" value="{{$galleryImage->id}}">
                  </form>
              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="5">No gallery images.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{ $galleryImages->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">
    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this gallery images.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteGalleryImage_'+id;
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