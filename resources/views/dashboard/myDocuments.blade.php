@extends('dashboard.dashboard')
@section('dashboard_header')
  <link href="{{ asset('css/sidemenuindex.css')}}" rel="stylesheet"/>
  <link href="{{ asset('css/v_courses.css')}}" rel="stylesheet"/>
  <style type="text/css">
    .inline_btn{
      display:inline;
    }
    .voted-btn{
      background:#e91e63
    }
    .vote-btn{
      background:#337ab7
    }
    .modal-header h2{font-size: 15px; font-weight: bold; color:#e91e63; }
  .iframe-container {
    padding-bottom: 60%;
    padding-top: 30px; height: 0; overflow: hidden;
  }
  .modal-footer{margin:   0px auto;
    text-align: center;}
  .iframe-container iframe,
  .iframe-container object,
  .iframe-container embed {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
  .btn-primary {
    width: 45px;
  }
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> All Articles </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-book"></i> Documents</li>
      <li class="active">All Articles</li>
    </ol>
  </section>
@stop
@section('dashboard_content')
<div class="container">
  <div class="row ">
    <div class="col-sm-4 mrgn_10_btm">
      <select class="form-control" id="category" name="category" onchange="showDocuments(this);">
        <option>Select Category</option>
        @if(count($categories) > 0)
          @foreach($categories as $category)
            <option value="{{$category->id}}">{{$category->name}}</option>
          @endforeach
        @endif
      </select>
    </div>
    <a class="btn btn-default" id="favourite" data-favourite="false" title="Favourite" onClick="myFavouriteDocs(this);" style="border-radius: 2px;"> <i class="fa fa-star " aria-hidden="true"></i> </a>
  </div>
  <br/>
  <div class="row" id="render_documents">
  	@if(count($documents)>0)
      @foreach($documents as $document)
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
          <div class="course-box">
            <a class="img-course-box">
              @if(!empty($document->doc_image_path))
                <img class="img-responsive " src="{{ asset($document->doc_image_path) }}" alt="document">
              @else
                <img class="img-responsive " src="{{ asset('images/default_course_image.jpg') }}" alt="document">
              @endif
            </a>
            <div class="topleft">@if( 1 == $document->type_of_document )Research Paper @else Documentary @endif</div>
            <div class="topright">@if($document->price > 0) Paid @else Free @endif</div>
            <div class="course-box-content" >
               <h4 class="course-box-title " title="{{$document->name}}" data-toggle="tooltip" data-placement="bottom"> <p class="block-with-text"><a >{{$document->name}}</a></p></h4>
               <br/>
                <p class="text-center ">
                  <a data-path="{{asset($document->doc_pdf_path)}}" class="btn btn-primary" data-toggle="modal" data-target="#dynamic-modal-{{$document->id}}" data-document_id="{{$document->id}}"> <i class="fa fa-book" aria-hidden="true"></i> </a>
                  <a href="{{asset($document->doc_pdf_path)}}" download class="btn btn-primary download" id="myBtn"><i class="fa fa-download" aria-hidden="true"></i></a>
                  @if(in_array($document->id, $favouriteDocIds))
                    <a class="btn btn-primary voted-btn" id="favourite-{{$document->id}}" data-favourite="true" onClick="registerFavouriteDocuments(this);" data-document_id="{{$document->id}}" title="Favourite Document"> <i class="fa fa-star " aria-hidden="true"></i> </a>
                  @else
                    <a class="btn btn-primary vote-btn" id="favourite-{{$document->id}}" data-favourite="false" onClick="registerFavouriteDocuments(this);" data-document_id="{{$document->id}}" title="Favourite Document"> <i class="fa fa-star " aria-hidden="true"></i> </a>
                  @endif
                </p>
            </div>
            <div class="course-auther">
              <a ><i class="fa fa-long-arrow-right block-with-text" aria-hidden="true" title="{{$document->author}}"> {{$document->author}}</i>
              </a>
            </div>
          </div>
        </div>
        <div id="dynamic-modal-{{$document->id}}" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h2  class="modal-title">{{$document->name}}</h2>
              </div>
              <div class="modal-body">
                  <div class="iframe-container">
                    <iframe src="{{asset($document->doc_pdf_path)}}" frameborder="0"></iframe>
                  </div>
              </div>
              <div class="modal-footer ">
                <a href="{{asset($document->doc_pdf_path)}}" download class="btn btn-primary download" id="myBtn"><i class="fa fa-download" aria-hidden="true"></i></a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    @else
      No articlies are read.
    @endif
  </div>
</div>
<script type="text/javascript">
  function renderDocuments(msg){
    documents = document.getElementById('render_documents');
    documents.innerHTML = '';
    if(undefined !== msg['documents'] && 0 < msg['documents'].length) {
      $.each(msg['documents'], function(idx, obj) {
        var firstDiv = document.createElement('div');
        firstDiv.className = "col-lg-4 col-md-4 col-sm-6 col-xs-12";
        var secondDiv = document.createElement('div');
        secondDiv.className = "course-box";
        var anc = document.createElement('a');
        anc.className = 'img-course-box';
        var img = document.createElement('img');
        img.className = "img-responsive";
        if(obj.doc_image_path){
          img.src = "{{ asset('') }}" + obj.doc_image_path;
        } else {
          img.src = "{{ asset('images/default_course_image.jpg') }}";
        }
        anc.appendChild(img);
        secondDiv.appendChild(anc);
        var topleftEle = document.createElement('div');
        topleftEle.className = "topleft";
        if(1 == obj.type_of_document ){ certifiedVal = 'Research Paper';} else {certifiedVal='Documentary'}

        topleftEle.innerHTML = certifiedVal;
        secondDiv.appendChild(topleftEle);
        var toprightEle = document.createElement('div');
        toprightEle.className = "topright";
        if( obj.price > 0 ){ price = 'Paid';} else { price='Free';}
        toprightEle.innerHTML = price;
        secondDiv.appendChild(toprightEle);

        var thirdDiv = document.createElement('div');
        thirdDiv.className = "course-box-content";

        var courseContent = '<h4 class="course-box-title" title="'+ obj.name +'" data-toggle="tooltip" data-placement="bottom"><p class="block-with-text"><a>'+ obj.name +'</a></p></h4>';

        courseContent += '<p class="text-center">';
        var docPath = "{{asset('')}}" +obj.doc_pdf_path;
        courseContent += '<a data-path="'+ docPath +'" class="btn btn-primary" data-toggle="modal" data-target="#dynamic-modal-'+ obj.id +'" data-document_id="'+ obj.id +'"> <i class="fa fa-book" aria-hidden="true"></i> </a>';
        courseContent += '&nbsp;<a href="'+ docPath +'" download class="btn btn-primary download" id="myBtn"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;';
        if(false == (msg['favouriteDocIds'].indexOf(obj.id) > -1)){
          courseContent += '<a class="btn btn-primary vote-btn" id="favourite-'+ obj.id +'" data-favourite="true" onClick="registerFavouriteDocuments(this);" data-document_id="'+ obj.id +'" title="Favourite Document"> <i class="fa fa-star " aria-hidden="true"></i> </a>';
        } else {
          courseContent += '<a class="btn btn-primary voted-btn" id="favourite-'+ obj.id +'" data-favourite="true" onClick="registerFavouriteDocuments(this);" data-document_id="'+ obj.id +'" title="Favourite Document"> <i class="fa fa-star " aria-hidden="true"></i> </a>';
        }
        courseContent += '</p>';
        thirdDiv.innerHTML = courseContent;
        secondDiv.appendChild(thirdDiv);

        var authorDiv = document.createElement('div');
        authorDiv.className = "course-auther";
        authorDiv.innerHTML = '<a ><i class="fa fa-long-arrow-right block-with-text" aria-hidden="true" title="'+ obj.author +'">'+ obj.author +'</i></a>';
        secondDiv.appendChild(authorDiv);
        firstDiv.appendChild(secondDiv);
        documents.appendChild(firstDiv);

       var modelDivInnerHtml = '';
       var modelDiv = document.createElement('div');
       modelDiv.className="modal fade";
       modelDiv.id = "dynamic-modal-"+ obj.id;

       var modelDialogDiv = document.createElement('div');
       modelDialogDiv.className = 'modal-dialog';

        var modelContentDiv = document.createElement('div');
        modelContentDiv.className = 'modal-content';
        modelDivInnerHtml += '<div class="modal-header"><button class="close" data-dismiss="modal">×</button><h2  class="modal-title">'+ obj.name+'</h2></div>';
        modelDivInnerHtml += '<div class="modal-body"><div class="iframe-container"><iframe src="'+ docPath +'" frameborder="0"></iframe></div></div>';
        modelDivInnerHtml += '<div class="modal-footer "><a href="'+ docPath +'" download class="btn btn-primary download" id="myBtn"><i class="fa fa-download" aria-hidden="true"></i></a></div>';
        modelContentDiv.innerHTML = modelDivInnerHtml;
        modelDialogDiv.appendChild(modelContentDiv);
        modelDiv.appendChild(modelDialogDiv);
       documents.appendChild(modelDiv);
     });
    } else {
      documents.innerHTML = 'No result found.';
    }
  }

  function showDocuments(ele){
    id = parseInt($(ele).val());
    var userId = parseInt(document.getElementById('user_id').value);
    document.getElementById('render_documents').innerHTML = '';
    if( 0 < id && 0 < userId ){
      $.ajax({
        method: "POST",
        url: "{{url('getDocumentsByCategoryId')}}",
        data: {id:id, user_id:userId}
      })
      .done(function( msg ) {
        renderDocuments(msg);
      });
    }
  }

  function registerFavouriteDocuments(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var documentId = parseInt($(ele).data('document_id'));
    if( true == isNaN(userId)){
      $('#loginUserModel').modal();
    } else {
      $.ajax({
        method: "POST",
        url: "{{url('registerFavouriteDocuments')}}",
        data: {user_id:userId, document_id:documentId}
      })
      .done(function( msg ) {
        var id = 'favourite-'+documentId;
        var favEle = document.getElementById(id);
        if('false' == msg){
          favEle.classList.add("vote-btn");
          favEle.classList.remove("voted-btn");
        } else {
          favEle.classList.add("voted-btn");
          favEle.classList.remove("vote-btn");
        }
      });
    }
  }

  function myFavouriteDocs(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if( true == isNaN(userId)){
      $('#loginUserModel').modal();
    } else {
      if(false == $(ele).data('favourite')){
        $(ele).data('favourite',true);
        $(ele).prop('style','color: rgb(233, 30, 99);');
        $(ele).prop('title','All');
        $.ajax({
          method: "POST",
          url: "{{url('getFavouriteDocumentsByUserId')}}",
          data: {user_id:userId}
        })
        .done(function( msg ) {
          renderDocuments(msg);
        });
      } else {
        window.location.reload();
      }
    }
  }

</script>
<style type="text/css">
  p.ellipsis{
    display:inline-block;
    width:180px;
    white-space: nowrap;
    overflow:hidden !important;
    text-overflow: ellipsis;
  }
</style>
@stop