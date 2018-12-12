@extends('layouts.master')
@section('header-title')
  <title>Hobby Projects in Electronics, IoT, VLSI and Vchip-kit |Vchip-edu</title>
@stop
@section('header-css')
@include('layouts.home-css')
  <link href="{{asset('css/solution.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/star-rating.css') }}" />
  <style type="text/css">
    .fa {
      font-size: medium !important;
    }
    .rating-container .filled-stars{
      color: #e7711b;
      border-color: #e7711b;
    }
    .rating-xs {
        font-size: 0em;
    }
    .download_iteam .fa{
      font-size: 20px;
      margin: 0px 5px;
    }
    .v_kit-img img{
          width: 100% ;
          height: auto;
          margin: 0px auto !important;
    }
    .project-media{margin-right: 200px;}
    .img-ckeditor p>img{width: 100% !important;
      height: auto !important;}
      @media(max-width: 1600px){
         .img-ckeditor p>img{
          width: 100% !important;
          height: auto !important;
          padding: 50px !important;
          margin: 0px auto !important;
           }
      }

    .message p>img{width: 50%;
      height: 400px !important;
    }
    @media(max-width: 768px){
      .img-ckeditor p>img{padding:10px!important;}
      .message p>img{width: 100%;height: auto !important;}
    }
        @media  (min-width: 350px) {
          .hidden-lg { display: none; }
        }
      @media  (max-width: 349px) {
          .hidden-sm { display: none; }
        }
    .fa-comment-o, .first-like i, .your-cmt{
      font-weight: bold;
      font-size: 18px;
      color: #555;
    }
    .first-like i{
      margin-right: 5px;
    }
    hr{
      margin-top: 5px;
      margin-bottom: 2px;
      border-bottom: 1px solid ;
    }
    .comment-meta{
      margin-left: 20px;
      margin-right: 20px;
      margin-top: 20px;
      margin-bottom: 20px;
    }
 </style>

@stop
@section('header-js')
  @include('layouts.home-js')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
@stop
@section('content')
@include('header.header_menu')
<section id="vchip-background" class="mrgn_60_btm">
  <div class="vchip-background-single">
    <div class="vchip-background-img">
      <figure>
        <img src="{{asset('images/projects.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Vchip Project Data"/>
      </figure>
    </div>
    <div class="vchip-background-content">
      <h2 class="animated bounceInLeft">Digital Education</h2>
    </div>
  </div>
</section>
<section id="" class="v_container v_bg_grey">
  <div class="container ">
    <div class="row">
      @if(Session::has('message'))
        <div class="alert alert-success" id="message">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ Session::get('message') }}
        </div>
      @endif
      @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif
    </div>
    <div class="row">
      <div class="col-md-9">
       <div class="box-border">
       <img id="adImage" width="100%" style="max-width: 600px;" src="">
       <h2 class="v_h2_title text-center">{{$project->name}}</h2>
        <br>
        <div class="" style="border: 2px solid #ddd; padding: 10px;">
          <div class="v_kit-single">
              <figure class="v_kit-img">
               <a><img class="img-responsive" alt="img" src="{{asset($project->header_image_path)}}"/></a>
               <figcaption class="v_kit-imgcaption">
               <br/>
                <a>{{$project->author}}</a>
              </figcaption>
            </figure>
            <div class="v_kit-single-content">
              <h2><a>{{$project->name}} </a></h2>
              <h4>Course Information</h4>
              <div class="img-responsive img-ckeditor" >
                {!! $project->description !!}
              </div>
            </div>
          </div>
       </div>
     </div>
   </div>
  <div class="col-md-3">
    <div class="vchip-right-sidebar text-center">
      <label>Favourite : </label>&nbsp;
      <div class="btn-group" role="group" title="Favourite" style="cursor: pointer;">
        @if($registeredProjectIds && in_array($project->id, $registeredProjectIds))
          <a class=" voted-btn" id="favourite" data-favourite="true" onClick="registerProject(this);" data-project_id="{{$project->id}}" title="Favourite" style="color: #e91e63;"> <i class="fa fa-star " aria-hidden="true"></i> </a>
        @else
          <a class="voted-btn" id="favourite" data-favourite="false" onClick="registerProject(this);" data-project_id="{{$project->id}}" title="Un Favourite" style="color: #000;"> <i class="fa fa-star " aria-hidden="true"></i> </a>
        @endif
      </div>
    </div>
    @if($project->price > 0 && !empty($project->items))
      @if('false' == $isPurchasedProjectItems)
      <div class="vchip-right-sidebar text-center">
        <a class="text-center" data-toggle="modal" data-target="#model_{{$project->id}}">Purchase Vkit (Items)</a>
        @if(is_object(Auth::user()))
          <a data-project_id="{{$project->id}}" class="btn btn-sm btn-primary pay-width" style="cursor: pointer;" onClick="purchaseVkitComponents(this);">Pay Price: {{$project->price}} Rs.</a>
          <form id="purchaseVkitComponent_{{$project->id}}" method="POST" action="{{ url('purchaseVkitComponents')}}">
            {{ csrf_field() }}
            <input type="hidden" name="project_id" value="{{$project->id}}">
          </form>
        @else
          <a class="btn btn-sm btn-primary pay-width" style="cursor: pointer;" onClick="checkLogin();">Pay Price: {{$project->price}} Rs.</a>
        @endif
      </div>
      <div class="modal fade" id="model_{{$project->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-center" id="exampleModalLabel">Project Components</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" style="overflow-x: auto;">
              <ul class="custom-list-style" style="align-items: left;">
                @foreach(explode(',',$project->items) as $item)
                  <li>{{$item}}</li>
                @endforeach
              </ul>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
      @endif
    @endif
    <div class="vchip-right-sidebar">
      <h3 class="v_h3_title text-center">Study Material</h3>
      <div class="text-center download_iteam">
        <a href="{{asset($project->project_pdf_path)}}" download data-toggle="tooltip" data-placement="bottom" title="{{basename($project->project_pdf_path)}}" >
          <i class="fa fa-file-pdf-o tex" aria-hidden="true"></i>{{basename($project->project_pdf_path)}}
        </a>
      </div>
    </div>
    <div class="vchip-right-sidebar mrgn_10_top_btm text-center">
      <div style="display: inline-block;">
        @if(isset($reviewData[$project->id])) {{$reviewData[$project->id]['avg']}} @else 0 @endif
      </div>
      <div style="display: inline-block;">
        <input id="rating_input{{$project->id}}" name="input-{{$project->id}}" class="rating rating-loading" value="@if(isset($reviewData[$project->id])) {{$reviewData[$project->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
      </div>
      <div style="display: inline-block;">
        <a data-toggle="modal" data-target="#review-model-{{$project->id}}">
          @if(isset($reviewData[$project->id]))
            {{count($reviewData[$project->id]['rating'])}} <i class="fa fa-group"></i>
          @else
            0 <i class="fa fa-group"></i>
          @endif
        </a>
      </div>
    </div>
    <div id="review-model-{{$project->id}}" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            &nbsp;&nbsp;&nbsp;
            <button class="close" data-dismiss="modal">×</button>
            <div class="form-group row ">
              <div  style="display: inline-block;">
                @if(isset($reviewData[$project->id])) {{$reviewData[$project->id]['avg']}} @else 0 @endif
              </div>
              <div  style="display: inline-block;">
                <input name="input-{{$project->id}}" class="rating rating-loading" value="@if(isset($reviewData[$project->id])) {{$reviewData[$project->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
              </div>
              <div  style="display: inline-block;">
                @if(isset($reviewData[$project->id]))
                  {{count($reviewData[$project->id]['rating'])}} <i class="fa fa-group"></i>
                @else
                  0 <i class="fa fa-group"></i>
                @endif
              </div>
              @if(is_object(Auth::user()))
                <button class="pull-right" data-toggle="modal" data-target="#rating-model-{{$project->id}}">
                @if(isset($reviewData[$project->id]) && isset($reviewData[$project->id]['rating'][Auth::user()->id]))
                  Edit Rating
                @else
                  Give Rating
                @endif
                </button>
              @else
                <button class="pull-right" onClick="checkLogin();">Give Rating</button>
              @endif
            </div>
          </div>
          <div class="modal-body row">
            <div class="form-group row" style="overflow: auto;">
              @if(isset($reviewData[$project->id]))
                @foreach($reviewData[$project->id]['rating'] as $userId => $review)
                  {{$userNames[$userId]}}:
                  <input id="rating_input-{{$project->id}}-{{$userId}}" name="input-{{$project->id}}" class="rating rating-loading" value="{{$review['rating']}}" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                  {{$review['review']}}
                  <hr>
                @endforeach
              @else
                Please give ratings
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="rating-model-{{$project->id}}" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button class="close" data-dismiss="modal">×</button>
            Rate and Review
          </div>
          <div class="modal-body row">
            <form action="{{ url('giveRating')}}" method="POST">
              <div class="form-group row ">
                {{ csrf_field() }}
                @if(isset($reviewData[$project->id]) && is_object(Auth::user()) && isset($reviewData[$project->id]['rating'][Auth::user()->id]))
                  <input id="rating_input-{{$project->id}}" name="input-{{$project->id}}" class="rating rating-loading" value="{{$reviewData[$project->id]['rating'][Auth::user()->id]['rating']}}" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                @else
                  <input id="rating_input-{{$project->id}}" name="input-{{$project->id}}" class="rating rating-loading" value="0" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                @endif
                Review:<input type="text" name="review-text" class="form-control" value="@if(isset($reviewData[$project->id])  && is_object(Auth::user()) && isset($reviewData[$project->id]['rating'][Auth::user()->id])) {{trim($reviewData[$project->id]['rating'][Auth::user()->id]['review'])}} @endif">
                <br>
                <input type="hidden" name="module_id" value="{{$project->id}}">
                <input type="hidden" name="module_type" value="3">
                <input type="hidden" name="rating_id" value="@if(isset($reviewData[$project->id]) && is_object(Auth::user()) && isset($reviewData[$project->id]['rating'][Auth::user()->id])) {{$reviewData[$project->id]['rating'][Auth::user()->id]['review_id']}} @endif">
                <button type="submit" class="pull-right">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="vchip-right-sidebar mrgn_20_top_btm">
      <h3 class="v_h3_title text-center">Projects</h3>
      @if(count($projects) > 0)
        @foreach($projects as $vKitProject)
          <div class="right-sidebar" style="margin-bottom: 5px;">
            <div class="media project-media" style="border:none; box-shadow: none;" title="{{$vKitProject->name }}">
              <div class=" media-left">
                <a>
                @if(!empty($vKitProject->front_image_path))
                  <img class="media-object" src="{{ asset($vKitProject->front_image_path) }}" alt="vckits">
                @else
                  <img class="media-object" src="{{ asset('images/default_course_image.jpg') }}" alt="vckits">
                @endif
                </a>
              </div>
              <div class="media-body">
               <h4 class="" ><a href="{{ url('vkitproject')}}/{{$vKitProject->id }}">{{$vKitProject->name }}</a></h4>
             </div>
           </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
  </div>
</div>
</section>
<section class="">
    <div class="container">
      <div class="with-nav-tabs">
        <div class="">
          <div class="comment-meta">
            <span id="like_{{$project->id}}"  class="first-like">
              @if( isset($likesCount[$project->id]) && is_object($currentUser) && isset($likesCount[$project->id]['user_id'][$currentUser->id]))
                   <i id="project_like_{{$project->id}}" data-project_id="{{$project->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                   <span id="like1-bs3">{{count($likesCount[$project->id]['like_id'])}}</span>
              @else
                   <i id="project_like_{{$project->id}}" data-project_id="{{$project->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                   <span id="like1-bs3">@if( isset($likesCount[$project->id])) {{count($likesCount[$project->id]['like_id'])}} @endif</span>
              @endif
            </span>

            <span class="mrgn_5_left">
              <i class="fa fa-comment-o" aria-hidden="true"></i>
                @if(is_object($currentUser))
                  <a class="your-cmt" role="button" data-toggle="collapse" href="#replyToProject{{$project->id}}" aria-expanded="false" aria-controls="collapseExample">Comment</a>
                @else
                  <a class="your-cmt" role="button" data-toggle="modal" data-placement="bottom" href="#loginUserModel">Comment</a>
                @endif
            </span>
            <hr />
            <div class="collapse replyComment" id="replyToProject{{$project->id}}">
                <div class="form-group">
                  <textarea name="comment" id="comment" placeholder="Comment here.." class="form-control"></textarea>
                  <script type="text/javascript">
                    CKEDITOR.replace('comment');
                    CKEDITOR.config.width="100%";
                    CKEDITOR.config.height="auto";
                    CKEDITOR.on('dialogDefinition', function (ev) {
                        var dialogName = ev.data.name,
                            dialogDefinition = ev.data.definition;
                        if (dialogName == 'image') {
                            var onOk = dialogDefinition.onOk;
                            dialogDefinition.onOk = function (e) {
                                var width = this.getContentElement('info', 'txtWidth');
                                width.setValue('100%');
                                var height = this.getContentElement('info', 'txtHeight');
                                height.setValue('auto');
                                onOk && onOk.apply(this, e);
                            };
                        }
                    });
                  </script>
                </div>
                <input type="hidden" id="project_id" name="project_id" value="{{$project->id}}">
                <button class="btn btn-default" onclick="confirmSubmit(this);" >Send</button>
                <button class="btn btn-default" data-id="replyToProject{{$project->id}}" onclick="cancleReply(this);">Cancle</button>
            </div>
          </div>
        </div>
        <div class="panel-body">
          <div class="tab-content">
            <div class="tab-pane fade in active" id="questions" style="padding: 15px !important;">
              <div class="post-comments ">
                <div class="row">
                  <div class="box-body chat " id="chat-box">
                    @if(count( $comments) > 0)
                      @foreach($comments as $comment)
                        <div class="item" id="showComment_{{$comment->id}}">
                          @if(is_file($comment->getUser($comment->user_id)->photo) || (!empty($comment->getUser($comment->user_id)->photo) && false == preg_match('/userStorage/',$comment->getUser($comment->user_id)->photo)))
                            <img src="{{ asset($comment->getUser($comment->user_id)->photo)}} " class="img-circle" alt="User Image">
                          @else
                            <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
                          @endif
                          <div class="message">
                            @if(is_object($currentUser) && ($currentUser->id == $comment->user_id))
                            <div class="dropdown pull-right">
                              <button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                              </button>
                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                @if($currentUser->id == $comment->user_id)
                                  <li><a id="{{$comment->id}}" data-comment_id="{{$comment->id}}" data-project_id="{{$project->id}}"onclick="confirmCommentDelete(this);">Delete</a></li>
                                @endif
                                @if($currentUser->id == $comment->user_id)
                                  <li><a id="{{$comment->id}}" onclick="editComment(this);">Edit</a></li>
                                @endif
                              </ul>
                            </div>
                            @endif
                              <a class="SubCommentName">{{ $comment->getUser($comment->user_id)->name }}</a>
                              <div class="more img-responsive img-ckeditor " id="editCommentHide_{{$comment->id}}">{!! $comment->body !!}</div>
                                <div class="form-group hide" id="editCommentShow_{{$comment->id}}" >
                                   <textarea class="form-control" name="comment" id="comment_{{$comment->id}}" rows="3">{!! $comment->body !!}</textarea>
                                  <script type="text/javascript">
                                    CKEDITOR.replace('comment_{{$comment->id}}');
                                    CKEDITOR.config.width="100%";
                                    CKEDITOR.config.height="auto";
                                    CKEDITOR.on('dialogDefinition', function (ev) {
                                        var dialogName = ev.data.name,
                                            dialogDefinition = ev.data.definition;
                                        if (dialogName == 'image') {
                                            var onOk = dialogDefinition.onOk;
                                            dialogDefinition.onOk = function (e) {
                                                var width = this.getContentElement('info', 'txtWidth');
                                                width.setValue('100%');
                                                var height = this.getContentElement('info', 'txtHeight');
                                                height.setValue('auto');
                                                onOk && onOk.apply(this, e);
                                            };
                                        }
                                    });
                                  </script>
                                  <button class="btn btn-primary" data-comment_id="{{$comment->id}}" data-project_id="{{$project->id}}" onclick="updateComment(this);">Update</button>
                                  <button class="btn btn-default" id="{{$comment->id}}" onclick="cancleComment(this);">Cancle</button>
                                </div>
                            </div>
                            <div class="comment-meta reply-1">
                              <span id="cmt_like_{{$comment->id}}" >
                                @if( isset($commentLikesCount[$comment->id]) &&  is_object($currentUser) && isset($commentLikesCount[$comment->id]['user_id'][$currentUser->id]))
                                     <i id="comment_like_{{$comment->id}}" data-project_id="{{$project->id}}" data-comment_id="{{$comment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                     <span id="like1-bs3">{{count($commentLikesCount[$comment->id]['like_id'])}}</span>
                                @else
                                     <i id="comment_like_{{$comment->id}}" data-project_id="{{$project->id}}" data-comment_id="{{$comment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                                     <span id="like1-bs3">@if( isset($commentLikesCount[$comment->id])) {{count($commentLikesCount[$comment->id]['like_id'])}} @endif</span>
                                @endif
                              </span>
                             <span class="mrgn_5_left">
                              @if(is_object($currentUser))
                                <a class="" role="button" data-toggle="collapse" href="#replyToComment{{$comment->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
                              @else
                                <a role="button" data-toggle="modal" data-placement="bottom" href="#loginUserModel">reply</a>
                              @endif
                            </span>
                            <span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> {{$comment->updated_at->diffForHumans()}}</span>
                            <div class="collapse replyComment" id="replyToComment{{$comment->id}}">
                                <div class="form-group">
                                  <label for="subcomment">Your Sub Comment</label>
                                    <textarea name="subcomment" id="subcomment_{{$project->id}}_{{$comment->id}}" class="form-control" rows="3"></textarea>
                                </div>
                                <button class="btn btn-default" data-comment_id="{{$comment->id}}" data-project_id="{{$project->id}}"  onclick="confirmSubmitReplytoComment(this);" >Send</button>
                                <button class="btn btn-default" data-id="replyToComment{{$comment->id}}" onclick="cancleReply(this);">Cancle</button>
                            </div>
                          </div>
                        </div>
                        @if(count( $comment->children ) > 0)
                          @include('vkits.comments', ['comments' => $comment->children, 'parent' => $comment->id, 'projectId' => $project->id])
                        @endif
                      @endforeach
                    @endif
                  </div>
              </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
<span>&nbsp;</span>
<input type="hidden" id="images" value="{{$images}}">
@stop
@section('footer')
@include('footer.footer')
 <script src="{{ asset('js/star-rating.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
      showCommentEle = "{{ Session::get('project_comment_area')}}";
      showsubCommentEle = "{{ Session::get('show_subcomment_area')}}";
      if(showCommentEle > 0){
        window.location.hash = '#showComment_'+showCommentEle;
      } else if(showsubCommentEle > 0){
        window.location.hash = '#subcomment_'+showsubCommentEle;
      }
      if($('#images').val()){
        // change image after 10 sec
        setInterval(imageCycle, 10000);
        var count = 0;
        var images = $('#images').val().split(',');
        var imagePath = "{{ url('') }}/";
        function imageCycle(){
          if((count + 1) == images.length){
            count = 0;
          } else {
            count = count + 1;
          }
          $('#adImage').prop('src',imagePath+images[count]);
        }
      }
  });
</script>
<script type="text/javascript">
  function purchaseVkitComponents(ele){
    $.confirm({
      title: 'Confirmation',
      content: 'Do you want to purchase vkit items?',
      type: 'red',
      typeAnimated: true,
      buttons: {
        Ok: {
          text: 'Ok',
          btnClass: 'btn-red',
          action: function(){
            var projectId = parseInt($(ele).data('project_id'));
            document.getElementById('purchaseVkitComponent_'+projectId).submit();
          }
        },
        Cancle: function () {
        }
      }
    });
  }

  function registerProject(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var projectId = parseInt($(ele).data('project_id'));
    if( true == isNaN(userId)){
      $('#loginUserModel').modal();
    } else {
      $.ajax({
        method: "POST",
        url: "{{url('registerProject')}}",
        data: {user_id:userId, project_id:projectId}
      })
      .done(function( msg ) {
        if('true' == msg){
          $(ele).css({'color':'#e91e63'})
        } else {
          $(ele).css({'color':'#000'})
        }
      });
    }
  }

    $(document).on("click", "i[id^=project_like_]", function(e) {
        var projectId = $(this).data('project_id');
        var dislike = $(this).data('dislike');
        var userId = parseInt(document.getElementById('user_id').value);
        if( isNaN(userId)) {
          $('#loginUserModel').modal();
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('likeVkitProject')}}",
              data: {dis_like:dislike, project_id:projectId}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
              var likeSpan = document.getElementById('like_'+projectId);
              likeSpan.innerHTML = '';
              if( 1 == dislike ){
                likeSpan.innerHTML +='<i id="project_like_'+projectId+'" data-project_id="'+projectId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              } else {
                likeSpan.innerHTML +='<i id="project_like_'+projectId+'" data-project_id="'+projectId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              }
            }
          });
        }
    });
    $(document).on("click", "i[id^=comment_like_]", function(e) {
        var projectId = $(this).data('project_id');
        var commentId = $(this).data('comment_id');
        var dislike = $(this).data('dislike');
        var userId = parseInt(document.getElementById('user_id').value);
        if( isNaN(userId)) {
          $('#loginUserModel').modal();
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('likeVkitProjectComment')}}",
              data: {project_id:projectId, comment_id:commentId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
                var likeSpan = document.getElementById('cmt_like_'+commentId);
                likeSpan.innerHTML = '';
                if( 1 == dislike ){
                  likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-project_id="'+projectId+'" data-comment_id="'+commentId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                } else {
                  likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-project_id="'+projectId+'" data-comment_id="'+commentId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                }
            }
          });
        }
    });

    $(document).on("click", "i[id^=sub_comment_like_]", function(e) {
        var projectId = $(this).data('project_id');
        var commentId = $(this).data('comment_id');
        var subCommentId = $(this).data('sub_comment_id');
        var dislike = $(this).data('dislike');
        var userId = parseInt(document.getElementById('user_id').value);
        if( isNaN(userId)) {
          $('#loginUserModel').modal();
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('likekitProjectSubComment')}}",
              data: {project_id:projectId, comment_id:commentId, sub_comment_id:subCommentId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
                var likeSpan = document.getElementById('sub_cmt_like_'+subCommentId);
                likeSpan.innerHTML = '';
                if( 1 == dislike ){
                  likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-project_id="'+projectId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                } else {
                  likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-project_id="'+projectId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                }
            }
          });
        }
    });

  function confirmSubmit(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
      var comment = CKEDITOR.instances.comment.getData();
      var projectId = parseInt(document.getElementById('project_id').value);
      document.getElementById('replyToProject'+projectId).classList.remove("in");
      CKEDITOR.instances.comment.setData('');
      $.ajax({
              method: "POST",
              url: "{{url('createProjectComment')}}",
              data: {project_id:projectId, comment:comment}
          })
          .done(function( msg ) {
            renderComments(msg, userId);
          });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }

  function renderComments(msg, userId){
    var chatDiv = document.getElementById('chat-box');
    chatDiv.innerHTML = '';
    var commentLikesCount = msg['commentLikesCount'];
    var subcommentLikesCount = msg['subcommentLikesCount'];
    arrayComments = [];
    $.each(msg['comments'], function(idx, obj) {
      arrayComments[idx] = obj;
    });
    var sortedArray = arrayComments.reverse();
    $.each(sortedArray, function(idx, obj) {
      if(false == $.isEmptyObject(obj)){
        var mainCommentDiv = document.createElement('div');
        mainCommentDiv.className = 'item';
        mainCommentDiv.id = 'showComment_'+obj.id;

        var commentImage = document.createElement('img');
        if('system' == obj.image_exist){
          var imageUrl =  "{{ asset('') }}"+obj.user_image;
        } else if('other' == obj.image_exist){
          var imageUrl =  obj.user_image;
        } else {
          var imageUrl = "{{ asset('images/user1.png') }}";
        }
        commentImage.setAttribute('src',imageUrl);
        mainCommentDiv.appendChild(commentImage);

        var commentMessageDiv = document.createElement('div');
        commentMessageDiv.className = 'message';
        if( userId == obj.user_id ){
          var commentEditDeleteDiv = document.createElement('div');
          commentEditDeleteDiv.className = 'dropdown pull-right';
          editDeleteInnerHtml = '<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>';
          editDeleteInnerHtml += '<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';
          if( userId == obj.user_id ){
            editDeleteInnerHtml += '<li><a id="'+obj.id+'" data-comment_id="'+obj.id+'" data-project_id="'+obj.vkit_project_id+'" onclick="confirmCommentDelete(this);">Delete</a></li>';
          }
          if( userId == obj.user_id ){
            editDeleteInnerHtml += '<li><a id="'+obj.id+'" onclick="editComment(this);">Edit</a></li>';
          }
          editDeleteInnerHtml += '</ul>';
          commentEditDeleteDiv.innerHTML = editDeleteInnerHtml;
          commentMessageDiv.appendChild(commentEditDeleteDiv);
        }

        var ancUserNameDiv = document.createElement('a');
        ancUserNameDiv.className = 'SubCommentName';
        ancUserNameDiv.innerHTML = obj.user_name + ' ';
        commentMessageDiv.appendChild(ancUserNameDiv);

        var pCommentBodyDiv = document.createElement('p');
        pCommentBodyDiv.className = 'more';
        pCommentBodyDiv.id = 'editCommentHide_'+obj.id;
        pCommentBodyDiv.innerHTML = obj.body; //'{!! '+obj.body+' !!}';
        commentMessageDiv.appendChild(pCommentBodyDiv);

        var divUpdateComment = document.createElement('div');
        divUpdateComment.className = 'form-group hide';
        divUpdateComment.id = 'editCommentShow_'+obj.id;
        divUpdateComment.innerHTML = '<textarea class="form-control" name="comment" id="comment_'+ obj.id +'" rows="3">'+ obj.body+'</textarea><button class="btn btn-primary" data-comment_id="'+ obj.id +'" data-project_id="'+ obj.vkit_project_id +'" onclick="updateComment(this);">Update</button><button type="button" class="btn btn-default" id="'+ obj.id +'" onclick="cancleComment(this);">Cancle</button>';
        commentMessageDiv.appendChild(divUpdateComment);
        mainCommentDiv.appendChild(commentMessageDiv);
        $( document ).ready(function() {
          CKEDITOR.replace('comment_'+ obj.id);
          CKEDITOR.config.width="100%";
          CKEDITOR.config.height="auto";
          CKEDITOR.on('dialogDefinition', function (ev) {
              var dialogName = ev.data.name,
                  dialogDefinition = ev.data.definition;
              if (dialogName == 'image') {
                  var onOk = dialogDefinition.onOk;
                  dialogDefinition.onOk = function (e) {
                      var width = this.getContentElement('info', 'txtWidth');
                      width.setValue('100%');
                      var height = this.getContentElement('info', 'txtHeight');
                      height.setValue('400');
                      onOk && onOk.apply(this, e);
                  };
              }
          });
        });

        var commentReplyDiv = document.createElement('div');
        commentReplyDiv.className = 'comment-meta reply-1';

        var spanCommenReply = document.createElement('span');
        spanCommenReply.id = 'cmt_like_'+obj.id;
        var spanCommenInnerHtml = '';
        if( commentLikesCount[obj.id] && commentLikesCount[obj.id]['user_id'][userId]){
          spanCommenInnerHtml +='<i id="comment_like_'+obj.id+'" data-project_id="'+obj.vkit_project_id+'" data-comment_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
          spanCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(commentLikesCount[obj.id]['like_id']).length +'</span>';
        } else {
          spanCommenInnerHtml +='<i id="comment_like_'+obj.id+'" data-project_id="'+obj.vkit_project_id+'" data-comment_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
          if(commentLikesCount[obj.id]){
            spanCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(commentLikesCount[obj.id]['like_id']).length +'</span>';
          }
        }
        spanCommenReply.innerHTML = spanCommenInnerHtml;
        commentReplyDiv.appendChild(spanCommenReply);

        var spanCommenReplyButton = document.createElement('span');
        spanCommenReplyButton.className = 'mrgn_5_left';
        spanCommenReplyButton.innerHTML = '<a class="" role="button" data-toggle="collapse" href="#replyToComment'+obj.id+'" aria-expanded="false" aria-controls="collapseExample">reply</a>';
        commentReplyDiv.appendChild(spanCommenReplyButton);

        var spanCommenReplyDate = document.createElement('span');
        spanCommenReplyDate.className = 'text-muted time-of-reply';
        spanCommenReplyDate.innerHTML = '<i class="fa fa-clock-o"></i>'+ obj.updated_at;
        commentReplyDiv.appendChild(spanCommenReplyDate);

        var subCommenDiv = document.createElement('div');
        subCommenDiv.className = 'collapse replyComment';
        subCommenDiv.id = 'replyToComment'+obj.id;
        subCommenDiv.innerHTML = '<div class="form-group"><label for="subcomment">Your Sub Comment</label><textarea name="subcomment" class="form-control" rows="3"  id="subcomment_'+obj.vkit_project_id+'_'+obj.id+'" ></textarea></div><button type="button" class="btn btn-default" onclick="confirmSubmitReplytoComment(this);" data-comment_id="'+obj.id+'" data-project_id="'+obj.vkit_project_id+'" >Send</button><button type="button" class="btn btn-default" data-id="replyToComment'+obj.id+'" onclick="cancleReply(this);">Cancle</button>';
        commentReplyDiv.appendChild(subCommenDiv);
        mainCommentDiv.appendChild(commentReplyDiv);
        chatDiv.appendChild(mainCommentDiv);
        if( obj['subcomments'] ){
          if(false == $.isEmptyObject(obj['subcomments'])){
            var commentUserId = obj.user_id;
            showSubComments(obj['subcomments'], chatDiv, subcommentLikesCount, userId, commentUserId);
          }
        }
      }
    });
    showMore();
  }

  function showSubComments(subcomments, commentchatDiv, subcommentLikesCount, userId, commentUserId){
    if(false == $.isEmptyObject(subcomments)){
      $.each(subcomments, function(idx, obj) {
        var mainSubCommentDiv = document.createElement('div');
        mainSubCommentDiv.className = 'item replySubComment-1';

        var subcommentImage = document.createElement('img');
        if('system' == obj.image_exist){
          var subcommentImageUrl =  "{{ asset('') }}"+obj.user_image;
        } else if('other' == obj.image_exist){
          var subcommentImageUrl =  obj.user_image;
        } else {
          var subcommentImageUrl = "{{ asset('images/user1.png') }}";
        }

        subcommentImage.setAttribute('src',subcommentImageUrl);
        mainSubCommentDiv.appendChild(subcommentImage);

        var subCommentMessageDiv = document.createElement('div');
        subCommentMessageDiv.className = 'message';
        subCommentMessageDiv.id = 'subcomment_'+obj.id;
        if( userId == obj.user_id || userId == commentUserId){
          var subcommentEditDeleteDiv = document.createElement('div');
          subcommentEditDeleteDiv.className = 'dropdown pull-right';
          editDeleteInnerHtml = '<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>';
          editDeleteInnerHtml += '<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">';
          if(  userId == obj.user_id || userId == commentUserId){
            editDeleteInnerHtml += '<li><a id="'+obj.vkit_project_comment_id+'_'+obj.id+'" onclick="confirmSubCommentDelete(this);"  data-subcomment_id="'+obj.id+'" data-comment_id="'+obj.vkit_project_comment_id+'" data-project_id="'+obj.vkit_project_id+'">Delete</a></li>';
          }
          if( userId == obj.user_id ){
            editDeleteInnerHtml += '<li><a id="'+obj.id+'" onclick="editSubComment(this);">Edit</a></li>';
          }
          editDeleteInnerHtml += '</ul>';
          subcommentEditDeleteDiv.innerHTML = editDeleteInnerHtml;
          subCommentMessageDiv.appendChild(subcommentEditDeleteDiv);
        }

        var pSubcommentBodyDiv = document.createElement('p');
        var ancUserNameDiv = document.createElement('a');
        ancUserNameDiv.className = 'SubCommentName';
        ancUserNameDiv.innerHTML = '<i>' + obj.user_name+ '</i>' + ' ';
        pSubcommentBodyDiv.appendChild(ancUserNameDiv);

        var spanSubCommentBodyDiv = document.createElement('span');
        spanSubCommentBodyDiv.className = 'more';
        spanSubCommentBodyDiv.id = 'editSubCommentHide_'+obj.id;
        spanSubCommentBodyDiv.innerHTML = obj.body; //'{!! '+obj.body+' !!}';
        pSubcommentBodyDiv.appendChild(spanSubCommentBodyDiv);
        subCommentMessageDiv.appendChild(pSubcommentBodyDiv);

        var divUpdateSubComment = document.createElement('div');
        divUpdateSubComment.className = 'form-group hide';
        divUpdateSubComment.id = 'editSubCommentShow_'+obj.id;

        divUpdateSubComment.innerHTML = '<textarea class="form-control" name="comment" id="updateSubComment_'+ obj.id +'" rows="3">'+ obj.body+'</textarea><button class="btn btn-primary"  data-subcomment_id="'+ obj.id +'" data-comment_id="'+ obj.vkit_project_comment_id +'" data-project_id="'+ obj.vkit_project_id +'" onclick="updateSubComment(this);">Update</button><button type="button" class="btn btn-default" id="'+ obj.id +'" onclick="cancleSubComment(this);">Cancle</button></div></form>';
        subCommentMessageDiv.appendChild(divUpdateSubComment);
        mainSubCommentDiv.appendChild(subCommentMessageDiv);

        var subcommentReplyDiv = document.createElement('div');
        subcommentReplyDiv.className = 'comment-meta reply-1';

        var spanCommenReply = document.createElement('span');
        spanCommenReply.id = 'sub_cmt_like_'+obj.id;
        var spanSubCommenInnerHtml = '';
        if( subcommentLikesCount[obj.id] && subcommentLikesCount[obj.id]['user_id'][userId]){
          spanSubCommenInnerHtml +='<i id="sub_comment_like_'+obj.id+'" data-project_id="'+obj.vkit_project_id+'" data-comment_id="'+obj.vkit_project_comment_id+'"  data-sub_comment_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
          spanSubCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(subcommentLikesCount[obj.id]['like_id']).length +'</span>';
        } else {
          spanSubCommenInnerHtml +='<i id="sub_comment_like_'+obj.id+'" data-project_id="'+obj.vkit_project_id+'" data-comment_id="'+obj.vkit_project_comment_id+'" data-sub_comment_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
          if(subcommentLikesCount[obj.id]){
            spanSubCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(subcommentLikesCount[obj.id]['like_id']).length +'</span>';
          }
        }
        spanCommenReply.innerHTML = spanSubCommenInnerHtml;
        subcommentReplyDiv.appendChild(spanCommenReply);

        var spanSubCommenReplyButton = document.createElement('span');
        spanSubCommenReplyButton.className = 'mrgn_5_left';
        spanSubCommenReplyButton.innerHTML = '<a class="" role="button" data-toggle="collapse" href="#replySubComment'+obj.vkit_project_comment_id+'-'+obj.id+'" aria-expanded="false" aria-controls="collapseExample">reply</a>';
        subcommentReplyDiv.appendChild(spanSubCommenReplyButton);

        var spanSubCommenReplyDate = document.createElement('span');
        spanSubCommenReplyDate.className = 'text-muted time-of-reply';
        spanSubCommenReplyDate.innerHTML = '<i class="fa fa-clock-o"></i>'+ obj.updated_at;
        subcommentReplyDiv.appendChild(spanSubCommenReplyDate);

        var createSubCommenDiv = document.createElement('div');
        createSubCommenDiv.className = 'collapse replyComment';
        createSubCommenDiv.id = 'replySubComment'+obj.vkit_project_comment_id+'-'+obj.id;
        createSubCommenDivInnerHTML = '<div class="form-group"><label for="subcomment">Your Sub Comment</label>';
        if( userId != obj.user_id ){
          createSubCommenDivInnerHTML += '<textarea name="subcomment" class="form-control" rows="3" id="createSubComment_'+ obj.id +'" >'+obj.user_name+'</textarea>';
        } else {
          createSubCommenDivInnerHTML += '<textarea name="subcomment" class="form-control" rows="3" id="createSubComment_'+ obj.id +'"></textarea>';
        }
        createSubCommenDivInnerHTML += '</div><button class="btn btn-default" onclick="confirmSubmitReplytoSubComment(this);" data-subcomment_id="'+ obj.id +'" data-comment_id="'+ obj.vkit_project_comment_id +'" data-project_id="'+ obj.vkit_project_id +'" >Send</button><button class="btn btn-default" data-id="replySubComment'+ obj.vkit_project_comment_id +'-'+ obj.id +'" onclick="cancleReply(this);">Cancle</button>';
        createSubCommenDiv.innerHTML = createSubCommenDivInnerHTML;
        subcommentReplyDiv.appendChild(createSubCommenDiv);
        mainSubCommentDiv.appendChild(subcommentReplyDiv);
        commentchatDiv.appendChild(mainSubCommentDiv);
      });
    }
  }

  function confirmSubmitReplytoComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
        var userId = parseInt(document.getElementById('user_id').value);
        var commentId = $(ele).data('comment_id');
        var projectId = $(ele).data('project_id');
        commentid = 'subcomment_'+projectId+'_'+commentId;
        var subcomment = document.getElementById(commentid).value;
        $.ajax({
            method: "POST",
            url: "{{url('createVkitProjectSubComment')}}",
            data: {project_id:projectId, comment_id:commentId, subcomment:subcomment}
        })
        .done(function( msg ) {
          renderComments(msg, userId);
        });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }

  function editComment(ele){
    var id = $(ele).attr('id');
    document.getElementById('editCommentHide_'+id).classList.add("hide");
    document.getElementById('editCommentShow_'+id).classList.remove("hide");
  }

  function cancleComment(ele){
    var id = $(ele).attr('id');
    document.getElementById('editCommentHide_'+id).classList.remove("hide");
    document.getElementById('editCommentShow_'+id).classList.add("hide");
  }
  function confirmCommentDelete(ele){
    $.confirm({
        title: 'Confirmation',
        content: 'If you delete this comment, all sub comments of this comment will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-red',
                action: function(){
                  var userId = parseInt(document.getElementById('user_id').value);
                  var commentId = $(ele).data('comment_id');
                  var projectId = $(ele).data('project_id');
                  $.ajax({
                      method: "POST",
                      url: "{{url('deleteVkitProjectComment')}}",
                      data: {project_id:projectId, comment_id:commentId}
                  })
                  .done(function( msg ) {
                    renderComments(msg, userId);
                  });
                }
            },
            Cancle: function () {
            }
        }
    });
  }

  function confirmSubmitReplytoSubComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
        var commentId = $(ele).data('comment_id');
        var subcommentId = $(ele).data('subcomment_id');
        var projectId = $(ele).data('project_id');
        var subcomment = document.getElementById('createSubComment_'+subcommentId).value
        $.ajax({
            method: "POST",
            url: "{{url('createVkitProjectSubComment')}}",
            data: {project_id:projectId, comment_id:commentId, subcomment_id:subcommentId, subcomment:subcomment}
        })
        .done(function( msg ) {
          renderComments(msg, userId);
        });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }

  function updateComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var commentId = $(ele).data('comment_id');
    var projectId = $(ele).data('project_id');
    commentid = 'comment_'+commentId;
    var comment = CKEDITOR.instances[commentid].getData();
    $.ajax({
        method: "POST",
        url: "{{url('updateVkitProjectComment')}}",
        data: {project_id:projectId, comment_id:commentId, comment:comment}
    })
    .done(function( msg ) {
      renderComments(msg, userId);
    });
  }
  function editSubComment(ele){
    var id = $(ele).attr('id');
    document.getElementById('editSubCommentHide_'+id).classList.add("hide");
    document.getElementById('editSubCommentShow_'+id).classList.remove("hide");
  }

  function cancleSubComment(ele){
    var id = $(ele).attr('id');
    document.getElementById('editSubCommentHide_'+id).classList.remove("hide");
    document.getElementById('editSubCommentShow_'+id).classList.add("hide");
  }

  function confirmSubCommentDelete(ele){
    $.confirm({
        title: 'Confirmation',
        content: 'You want to delete this sub comment?',
        type: 'red',
        typeAnimated: true,
        buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-red',
                action: function(){
                  var userId = parseInt(document.getElementById('user_id').value);
                  var commentId = $(ele).data('comment_id');
                  var subcommentId = $(ele).data('subcomment_id');
                  var projectId = $(ele).data('project_id');
                  $.ajax({
                      method: "POST",
                      url: "{{url('deleteVkitProjectSubComment')}}",
                      data: {project_id:projectId, comment_id:commentId, subcomment_id:subcommentId}
                  })
                  .done(function( msg ) {
                    renderComments(msg, userId);
                  });
                }
            },
            Cancle: function () {
            }
        }
    });
  }
  function updateSubComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var commentId = $(ele).data('comment_id');
    var subcommentId = $(ele).data('subcomment_id');
    var projectId = $(ele).data('project_id');
    var subcomment = document.getElementById('updateSubComment_'+subcommentId).value
    $.ajax({
        method: "POST",
        url: "{{url('updateVkitProjectSubComment')}}",
        data: {project_id:projectId, comment_id:commentId, subcomment_id:subcommentId, subcomment:subcomment}
    })
    .done(function( msg ) {
      renderComments(msg, userId);
    });
  }

  function cancleReply(ele){
    var id = $(ele).data('id');
    document.getElementById(id).classList.remove("in");
  }
  function showMore(){
     var showChar = 400;
      var ellipsestext = "...";
      var moretext = "Read more";
      var lesstext = "less";
      $('.more').each(function() {
        var content = $(this).html();

        if(content.length > showChar) {

          var c = content.substr(0, showChar);
          var h = content.substr(0, content.length);
          var html = '<div class="zxc">'+ c + '<span style="color:#01bafd; margin-left:5px;">' + ellipsestext+ '</span><br /><a href="" class="morelink" style="color:#01bafd";>' + moretext + '</a></div><div class="zxc1" style="display:none;">'+ h + '<br /><a href="" class="morelink1" style="color:#01bafd";>' + lesstext + '</a></div>';

          $(this).html(html);
        }

      });

      $(".morelink").click(function(){
        $(this).closest('.zxc').toggle();
        $(this).closest('.zxc').siblings('.zxc1').toggle();
        return false;
      });
      $(".morelink1").click(function(){
        $(this).closest('.zxc1').toggle();
        $(this).closest('.zxc1').siblings('.zxc').toggle();
        return false;
      });
  }
</script>
@stop