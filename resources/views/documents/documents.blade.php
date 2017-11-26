@extends('layouts.master')
@section('header-title')
  <title>Document – Technical Document, Research Paper, Study Material |Vchip-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
    <link href="{{ asset('css/sidemenuindex.css')}}" rel="stylesheet"/>
    <link href="{{ asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
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
    .modal-header button{font-size: 15px; font-weight: bold; color:#e91e63; }
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
  </style>
@stop
@section('header-js')
  @include('layouts.home-js')
@stop
@section('content')
  @include('header.header_menu')
<section id="vchip-background" class="mrgn_60_btm">
  <div class="vchip-background-single">
    <div class="vchip-background-img">
      <figure>
        <img src="{{asset('images/document.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip documents" />
      </figure>
    </div>
    <div class="vchip-background-content">
      <h2 class="animated bounceInLeft">Digital Education</h2>
    </div>
  </div>
</section>
<section id="sidemenuindex" class="v_container">
  <div class="container ">
    <div class="row">
      <div class="col-sm-3 ">
        <h4 class="v_h4_subtitle"> Sorted By</h4>
        <div class="dropdown mrgn_20_top_btm" id="cat">
          <select class="form-control" id="category" name="category" onchange="showDocuments(this);">
             <option>Select Category ...</option>
              @if(count($documentsCategories) > 0)
                @foreach($documentsCategories as $index => $documentsCategory)
                  <option value="{{$documentsCategory->id}}">{{$documentsCategory->name}}</option>
                @endforeach
              @endif
          </select>
        </div>
        <h4 class="v_h4_subtitle mrgn_20_top_btm"> Filter By</h4>
        <div class="panel"></div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" title="Difficulty"> Difficulty</p>
        <div class="panel">
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="difficulty" onclick="searchDocuments();"> Beginner</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="2" data-filter="difficulty" onclick="searchDocuments();">Intermediate</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="3" data-filter="difficulty" onclick="searchDocuments();"> Advanced</label>
          </div>
        </div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" title="Type of Document"> Type of Document </p>
        <div class="panel">
           <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="typeOfDoc" onclick="searchDocuments();">Reasearch Paper</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="2" data-filter="typeOfDoc" onclick="searchDocuments();">Documentry</label>
          </div>
        </div>
      </div>
      <div class="col-sm-9 ">
        <div class="row info" id="documents">
          @if(count($documents)>0)
            @foreach($documents as $document)
              @if($id == $document->id)
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 slideanim" style="border-style: dotted;border-color: red;">
              @else
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 slideanim">
              @endif
                <div class="course-box">
                  <a class="img-course-box">
                    @if(!empty($document->doc_image_path))
                      <img class="img-responsive " src="{{ asset($document->doc_image_path) }}" alt="document">
                    @else
                      <img class="img-responsive " src="{{ asset('images/default_course_image.jpg') }}" alt="document">
                    @endif
                  </a>
                  <div class="topleft">@if( 1 == $document->type_of_document )Research Paper @else Documentary @endif</div>
                  <div class="topright">@if($document->is_paid) Paid @else Free @endif</div>
                  <div class="course-box-content" >
                     <h4 class="course-box-title " title="{{$document->name}}" data-toggle="tooltip" data-placement="bottom"> <p class="block-with-text"><a >{{$document->name}}</a></p></h4>
                     <br/>
                      <p class="text-center ">
                       @if(in_array($document->id, $registeredDocIds))
                        <a data-path="{{asset($document->doc_pdf_path)}}" class="btn btn-primary" data-toggle="modal" data-target="#dynamic-modal-{{$document->id}}" title="Read Document"> <i class="fa fa-book" aria-hidden="true"></i> </a>
                        @else
                          <a data-path="{{asset($document->doc_pdf_path)}}" class="btn btn-primary" data-toggle="modal" data-target="#dynamic-modal-{{$document->id}}" onClick="registerDocuments(this);" data-document_id="{{$document->id}}"  title="Read Document"> <i class="fa fa-book" aria-hidden="true"></i> </a>
                        @endif
                        <a href="{{asset($document->doc_pdf_path)}}" download class="btn btn-primary download" id="myBtn"><i class="fa fa-download" aria-hidden="true" title="Download Document"></i></a>
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
                  <div class="modal-content"  style="background-color: white;">
                    <div class="modal-header">
                      <button class="close" data-dismiss="modal">×</button>
                      <h2  class="modal-title">{{$document->name}}</h2>
                    </div>
                    <div class="modal-body">
                        <div class="iframe-container">
                           <object data="{{asset($document->doc_pdf_path)}}" type="application/pdf" >
                            <a href="{{asset($document->doc_pdf_path)}}"></a>
                           </object>
                        </div>
                    </div>
                    <div class="modal-footer ">
                      <a href="{{asset($document->doc_pdf_path)}}" download class="btn btn-primary download" id="myBtn" title="Download Document"><i class="fa fa-download" aria-hidden="true"></i></a>
                      @if(in_array($document->id, $favouriteDocIds))
                        <a class="btn btn-primary voted-btn" id="favourite-{{$document->id}}" data-favourite="true" onClick="registerFavouriteDocuments(this);" data-document_id="{{$document->id}}" title="Favourite Document"> <i class="fa fa-star " aria-hidden="true"></i> </a>
                      @else
                        <a class="btn btn-primary vote-btn" id="favourite-{{$document->id}}" data-favourite="false" onClick="registerFavouriteDocuments(this);" data-document_id="{{$document->id}}" title="Favourite Document"> <i class="fa fa-star " aria-hidden="true"></i> </a>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          @else
            No documents are available.
          @endif
        </div>
          <div style="float: right;" id="pagination">
            {{ $documents->links() }}
          </div>
      </div>
    </div>
  </div>
</section>
@stop
@section('footer')
  @include('footer.footer')
  <script type="text/javascript" src="{{ asset('js/togleForFilterBy.js')}}"></script>
  <script type="text/javascript">
  function renderDocuments(msg){
    documents = document.getElementById('documents');
    documents.innerHTML = '';
    document.getElementById('pagination').innerHTML = '';
    if(undefined !== msg['documents'] && 0 < msg['documents'].length){
      $.each(msg['documents'], function(idx, obj) {
        var firstDiv = document.createElement('div');
        firstDiv.className = "col-lg-4 col-md-4 col-sm-6 col-xs-6";
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
        if(false == (msg['registeredDocuments'].indexOf(obj.id) > -1)){
          courseContent += '<a data-path="'+ docPath +'" class="btn btn-primary" data-toggle="modal" data-target="#dynamic-modal-'+ obj.id +'" title="Read Document"> <i class="fa fa-book" aria-hidden="true"></i> </a>';
        } else {
          courseContent += '<a data-path="'+ docPath +'" class="btn btn-primary" data-toggle="modal" data-target="#dynamic-modal-'+ obj.id +'" onClick="registerDocuments(this);" data-document_id="'+ obj.id +'" title="Read Document"> <i class="fa fa-book" aria-hidden="true"></i> </a>';
        }
        courseContent += '&nbsp;<a href="'+ docPath +'" download class="btn btn-primary download" id="myBtn" title="Download Document"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;';
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
          modelContentDiv.setAttribute('style','background-color: white;');
          modelContentDiv.className = 'modal-content';
          modelDivInnerHtml += '<div class="modal-header"><button class="close" data-dismiss="modal">×</button><h2  class="modal-title">'+ obj.name+'</h2></div>';
          modelDivInnerHtml += '<div class="modal-body"><div class="iframe-container"><object data="'+ docPath +'" type="application/pdf" ><a href="'+ docPath +'"></a></object></div></div>';
          modelDivInnerHtml += '<div class="modal-footer "><a href="'+ docPath +'" download class="btn btn-primary download" id="myBtn" title="Download Document"><i class="fa fa-download" aria-hidden="true"></i></a></div>';
          modelContentDiv.innerHTML = modelDivInnerHtml;
          modelDialogDiv.appendChild(modelContentDiv);
          modelDiv.appendChild(modelDialogDiv);
         documents.appendChild(modelDiv);
     });
    } else {
      documents.innerHTML = 'No result found.';
    }
  }
  function searchDocuments(){
    var searches = document.getElementsByClassName('search');
    var arrDifficulty = [];
    var arrTypeOfDoc = [];
    var arr = [];
    var arrFees = [];
    $.each(searches, function(ind, obj){
      if(true == $(obj).is(':checked')){
        var filter = $(obj).data('filter');
        var filterVal = $(obj).val();
        if(false == (arrDifficulty.indexOf(filter) > -1)){
          if('difficulty' == filter) {
            arrDifficulty.push(filterVal);
            arr.push(filterVal);
          }
          if('typeOfDoc' == filter) {
            arrTypeOfDoc.push(filterVal);
            arr.push(filterVal);
          }
          if('fees' == filter) {
            arrFees.push(filterVal);
            arr.push(filterVal);
          }
        }
      }
    });
    if(arr instanceof Array ){
      categoryId = document.getElementById('category').value;
      userId = document.getElementById('user_id').value;
      var arrJson = {'difficulty' : arrDifficulty, 'typeOfDoc' : arrTypeOfDoc, 'fees' : arrFees,'categoryId' : categoryId, 'userId' : userId };
      $.ajax({
        method: "POST",
        url: "{{url('getDocumentsBySearchArray')}}",
        data: {arr:JSON.stringify(arrJson)}
      })
      .done(function( msg ) {
        renderDocuments(msg);
      });
    }
  }

  function showDocuments(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
        method: "POST",
        url: "{{url('getDocumentsByCategoryId')}}",
        data: {id:id}
      })
      .done(function( msg ) {
        renderDocuments(msg);
        var searches = document.getElementsByClassName('search');
        $.each(searches, function(ind, obj){
          $(obj).attr('checked', false);
        });
      });
    }
  }

  function registerDocuments(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var documentId = parseInt($(ele).data('document_id'));
    if( false == isNaN(userId)){
      $.ajax({
        method: "POST",
        url: "{{url('registerDocuments')}}",
        data: {user_id:userId, document_id:documentId}
      })
      .done(function( msg ) {
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