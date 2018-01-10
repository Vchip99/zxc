@extends('layouts.master')
@section('header-title')
  <title>Live Online video Courses episod by Industrial Expert |V-edu</title>
@stop
@section('header-css')
@include('layouts.home-css')
  <link href="{{ asset('css/episode.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
    div.scroll {
      background-color: #00FFFF;
      width: 100%;
      height: 480px;
      overflow: scroll;
    }

    .list-group {
      list-style: decimal inside;
    }

    .list-group-item {
      display: list-item;
    }
    .list-group  li {

      color: #00868B;
      padding:16px;
      margin:12px;
    }
    .list-group li>a {
      color: #000;
      margin-left: 20px;
      margin-right: 20px;
      font-size: 15px;
    }
    .list-group li>span{
      color: #00868B;
      float: right;
    }

    @media (max-width: 768px) {
      div.scroll {
        margin-top: 30px;
        background-color: #00FFFF;
        width: 100%;
        height: 170px;
        overflow: scroll;
      }
    }
    @media (max-width: 1200px) {
      div.scroll {
        margin-top: 30px;
        background-color: #00FFFF;
        width: 100%;
        height: 325px;
        overflow: scroll;
      }
    }
    .download_iteam{
      border: 1px solid #ddd;
      width: 100px;
      border-radius: 20px;
      padding: 10px;
      margin-top: 10px;
    }
    .download_iteam .fa{
      font-size: 20px;
      margin: 0px 5px;
    }
   .list-group-item .ellipsis{
      display:inline-block;
      width:60px;
      white-space: nowrap;
      overflow:hidden !important;
      text-overflow: ellipsis;
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
  margin-left: 30px;
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
<section id="" class="v_container " style="background: #3A5894;">
  <div class="container text-center">
    <div class="row mrgn_60_top">
      <div class="col-md-9">
        <div class="embed-responsive embed-responsive-16by9" width="854" height="480">
          {!! $video->video_path !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="scroll">
          <ol class="list-group">
            @if(count($courseVideos)>0)
              @foreach($courseVideos as $courseVideo)
                <li class="list-group-item">
                  <a class="ellipsis" href="{{url('episode')}}/{{$courseVideo->id}}" data-toggle="tooltip" title="{{$courseVideo->name}}">{{$courseVideo->name}} </a>
                  <span class="running-time"> {{ gmdate('H:i:s', $courseVideo->duration)}} </span>
                </li>
              @endforeach
            @endif
          </ol>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="v_container ">
  <div class="container">
    <div class="row">
      <div class="col-md-6 ">
        <span class="divider">&#9679;</span>
        <span class="running-time">Run Time- {{ gmdate('H:i:s', $video->duration)}}</span>
        <h4 class="v_h4_subtitle">
        <a >{{$video->name}}</a>
         </h4>
         <p class="more">{{$video->description}}</p>
         <!-- <span class="v_download" title="Download">
          <a class="btn btn-primary is-bold" role="button" data-toggle="collapse" href="#download_link" aria-expanded="false" aria-controls="collapseExample">
          Download</a></span> -->
          <div class="collapse" id="download_link">
            <div class="download_iteam">
              <a download data-toggle="tooltip" data-placement="bottom" title="Pdf">
                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
              </a>
              <a download data-toggle="tooltip" data-placement="bottom" title="Video">
                <i class="fa fa-video-camera" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-6 mrgn_10_tops">
        </div>
      </div>
    </div>
  </section>

<!-- <span>&nbsp;</span> -->
  <section class="">
    <div class="container">
      <div class=" with-nav-tabs">
        <div class="">
          <div class="comment-meta">
            <span id="like_{{$video->id}}" class="first-like">
              @if( isset($likesCount[$video->id]) && isset($likesCount[$video->id]['user_id'][$currentUser]))
                   <i id="video_like_{{$video->id}}" data-video_id="{{$video->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"> Like </i>
                   <span id="like1-bs3">{{count($likesCount[$video->id]['like_id'])}}</span>
              @else
                   <i id="video_like_{{$video->id}}" data-video_id="{{$video->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"> Like </i>
                   <span id="like1-bs3">@if( isset($likesCount[$video->id])) {{count($likesCount[$video->id]['like_id'])}} @endif</span>
              @endif
            </span>

            <span class="mrgn_5_left">
              <i class="fa fa-comment-o" aria-hidden="true"></i>
              @if(is_object(Auth::user()))
                <a class="your-cmt" role="button" data-toggle="collapse" href="#replyToEpisode{{$video->id}}" aria-expanded="false" aria-controls="collapseExample">Comment</a>
              @else
                <a class="your-cmt" role="button" data-toggle="modal" data-placement="bottom" href="#loginUserModel">Comment</a>
              @endif
            </span>
             <hr />
            <div class="collapse replyComment" id="replyToEpisode{{$video->id}}" >
                <div class="form-group">
                  <textarea name="comment" id="comment" placeholder="Comment here.." class="form-control" rows="7"></textarea>
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
                                height.setValue('400');
                                onOk && onOk.apply(this, e);
                            };
                        }
                    });
                  </script>
                </div>
                <input type="hidden" id="video_id" name="video_id" value="{{$video->id}}">
                <button type="button" class="btn btn-default" onclick="confirmSubmit(this);" title="Send" >
                  <span class="hidden-lg fa fa-share" aria-hidden="true"></span>
                  <div class="hidden-sm">Send</div>
                </button>
                <button type="button" class="btn btn-default" data-id="replyToEpisode{{$video->id}}" onclick="cancleReply(this);" title="Cancle">
                  <span class="hidden-lg fa fa-times-circle" aria-hidden="true"></span>
                  <div class="hidden-sm">Cancle</div>
                </button>
            </div>
          </div>

        </div>
        <div class="panel-body">
          <div class="tab-content">
            <div class="tab-pane fade in active" id="questions" style="padding: 15px !important;">
              <div class="post-comments ">
                <div class="row">
                   <div class=" ">
                    <div class="box-body chat " id="chat-box">
                      @if(count( $comments) > 0)
                        @foreach($comments as $comment)
                          <div class="item" id="showComment_{{$comment->id}}">
                            @if(is_file($comment->user->photo))
                              <img src="{{ asset($comment->user->photo)}} " class="img-circle" alt="User Image">
                            @else
                              <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
                            @endif
                            <div class="message">
                              @if(is_object(Auth::user()) && (Auth::user()->id == $comment->user_id))
                              <div class="dropdown pull-right">
                                <button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                  @if(Auth::user()->id == $comment->user_id)
                                    <li><a id="{{$comment->id}}" data-comment_id="{{$comment->id}}" data-video_id="{{$video->id}}" onclick="confirmCommentDelete(this);">Delete</a></li>
                                  @endif
                                  @if(Auth::user()->id == $comment->user_id)
                                    <li><a id="{{$comment->id}}" onclick="editComment(this);">Edit</a></li>
                                  @endif
                                </ul>
                              </div>
                              @endif
                                <a class="SubCommentName">{{ $user->find($comment->user_id)->name }}</a>
                                <div class="more" id="editCommentHide_{{$comment->id}}">{!! $comment->body !!}</div>
                                  <div class="form-group hide" id="editCommentShow_{{$comment->id}}" >
                                    <textarea class="form-control" name="comment{{$comment->id}}" id="comment{{$comment->id}}" rows="3">{!! $comment->body !!}</textarea>
                                    <script type="text/javascript">
                                      CKEDITOR.replace( 'comment{{$comment->id}}');
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
                                    </script>
                                    <button class="btn btn-primary" data-comment_id="{{$comment->id}}" data-video_id="{{$video->id}}" onclick="updateComment(this);">Update</button>
                                    <button type="button" class="btn btn-default" id="{{$comment->id}}" onclick="cancleComment(this);">Cancle</button>
                                  </div>
                              </div>
                              <div class="comment-meta reply-1">
                                <span id="cmt_like_{{$comment->id}}" >
                                  @if( isset($commentLikesCount[$comment->id]) && isset($commentLikesCount[$comment->id]['user_id'][$currentUser]))
                                       <i id="comment_like_{{$comment->id}}" data-video_id="{{$video->id}}" data-comment_id="{{$comment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                       <span id="like1-bs3">{{count($commentLikesCount[$comment->id]['like_id'])}}</span>
                                  @else
                                       <i id="comment_like_{{$comment->id}}" data-video_id="{{$video->id}}" data-comment_id="{{$comment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                                       <span id="like1-bs3">@if( isset($commentLikesCount[$comment->id])) {{count($commentLikesCount[$comment->id]['like_id'])}} @endif</span>
                                  @endif
                                </span>
                               <span class="mrgn_5_left">
                                @if(is_object(Auth::user()))
                                  <a class="" role="button" data-toggle="collapse" href="#replyToComment{{$comment->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
                                @else
                                  <a role="button" data-toggle="modal" data-placement="bottom" href="#loginUserModel">reply</a>
                                @endif
                              </span>
                              <span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> {{$comment->updated_at->diffForHumans()}}</span>
                              <div class="collapse replyComment" id="replyToComment{{$comment->id}}">
                                  <div class="form-group">
                                    <label for="subcomment">Your Sub Comment</label>
                                      <textarea name="subcomment" id="subcomment_{{$video->id}}_{{$comment->id}}" class="form-control" rows="3"></textarea>
                                  </div>
                                  <button type="button" class="btn btn-default" onclick="confirmSubmitReplytoComment(this);" data-comment_id="{{$comment->id}}" data-video_id="{{$video->id}}" >Send</button>
                                  <button type="button" class="btn btn-default" data-id="replyToComment{{$comment->id}}" onclick="cancleReply(this);">Cancle</button>
                              </div>
                            </div>
                          </div>
                          @if(count( $comment->children ) > 0)
                            @include('courses.comments', ['comments' => $comment->children, 'parent' => $comment->id, 'user' => $user, 'videoId' => $video->id])
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
    </div>
  </section>
<span>&nbsp;</span>
@stop
@section('footer')
  @include('footer.footer')
  <script type="text/javascript">
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
        if(obj.image_exist){
          var imageUrl =  "{{ asset('') }}"+obj.user_image;
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
            editDeleteInnerHtml += '<li><a id="'+obj.id+'" data-comment_id="'+obj.id+'" data-video_id="'+obj.course_video_id+'" onclick="confirmCommentDelete(this);">Delete</a></li>';
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
        divUpdateComment.innerHTML = '<textarea class="form-control" name="comment" id="comment'+ obj.id +'" rows="3">'+ obj.body+'</textarea><button class="btn btn-primary" data-comment_id="'+ obj.id +'" data-video_id="'+ obj.course_video_id +'" onclick="updateComment(this);">Update</button><button type="button" class="btn btn-default" id="'+ obj.id +'" onclick="cancleComment(this);">Cancle</button>';
        commentMessageDiv.appendChild(divUpdateComment);
        mainCommentDiv.appendChild(commentMessageDiv);
        $( document ).ready(function() {
          CKEDITOR.replace('comment'+ obj.id);
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
          spanCommenInnerHtml +='<i id="comment_like_'+obj.id+'" data-video_id="'+obj.course_video_id+'" data-comment_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
          spanCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(commentLikesCount[obj.id]['like_id']).length +'</span>';
        } else {
          spanCommenInnerHtml +='<i id="comment_like_'+obj.id+'" data-video_id="'+obj.course_video_id+'" data-comment_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
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
        subCommenDiv.innerHTML = '<div class="form-group"><label for="subcomment">Your Sub Comment</label><textarea name="subcomment" class="form-control" rows="3"  id="subcomment_'+obj.course_video_id+'_'+obj.id+'" ></textarea></div><button type="button" class="btn btn-default" onclick="confirmSubmitReplytoComment(this);" data-comment_id="'+obj.id+'" data-video_id="'+obj.course_video_id+'" >Send</button><button type="button" class="btn btn-default" data-id="replyToComment'+obj.id+'" onclick="cancleReply(this);">Cancle</button>';
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

  function confirmSubmit(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(userId > 0){
      var comment = CKEDITOR.instances.comment.getData();
      var videoId = parseInt(document.getElementById('video_id').value);
      document.getElementById('replyToEpisode'+videoId).classList.remove("in");
      CKEDITOR.instances.comment.setData('');
      $.ajax({
              method: "POST",
              url: "{{url('createCourseComment')}}",
              data: {video_id:videoId, comment:comment}
          })
          .done(function( msg ) {
            renderComments(msg, userId);
          });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }

  function showSubComments(subcomments, commentchatDiv, subcommentLikesCount, userId, commentUserId){
    if(false == $.isEmptyObject(subcomments)){
      $.each(subcomments, function(idx, obj) {
        var mainSubCommentDiv = document.createElement('div');
        mainSubCommentDiv.className = 'item replySubComment-1';

        var subcommentImage = document.createElement('img');
        if(obj.image_exist){
          var subcommentImageUrl = "{{ asset('') }}"+obj.user_image;
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
            editDeleteInnerHtml += '<li><a id="'+obj.course_comment_id+'_'+obj.id+'" onclick="confirmSubCommentDelete(this);"  data-subcomment_id="'+obj.id+'" data-comment_id="'+obj.course_comment_id+'" data-video_id="'+obj.course_video_id+'">Delete</a></li>';
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
        ancUserNameDiv.innerHTML = '<i>'+obj.user_name+'</i>';
        pSubcommentBodyDiv.appendChild(ancUserNameDiv);

        var spanSubCommentBodyDiv = document.createElement('span');
        spanSubCommentBodyDiv.className = 'more';
        spanSubCommentBodyDiv.id = 'editSubCommentHide_'+obj.id;
        spanSubCommentBodyDiv.innerHTML = ' '+obj.body; //'{!! '+obj.body+' !!}';
        pSubcommentBodyDiv.appendChild(spanSubCommentBodyDiv);
        subCommentMessageDiv.appendChild(pSubcommentBodyDiv);

        var divUpdateSubComment = document.createElement('div');
        divUpdateSubComment.className = 'form-group hide';
        divUpdateSubComment.id = 'editSubCommentShow_'+obj.id;

        divUpdateSubComment.innerHTML = '<textarea class="form-control" name="comment" id="updateSubComment_'+ obj.id +'" rows="3">'+ obj.body+'</textarea><button class="btn btn-primary"  data-subcomment_id="'+ obj.id +'" data-comment_id="'+ obj.course_comment_id +'" data-video_id="'+ obj.course_video_id +'" onclick="updateCourseSubComment(this);">Update</button><button type="button" class="btn btn-default" id="'+ obj.id +'" onclick="cancleSubComment(this);">Cancle</button></div></form>';
        subCommentMessageDiv.appendChild(divUpdateSubComment);
        mainSubCommentDiv.appendChild(subCommentMessageDiv);

        var subcommentReplyDiv = document.createElement('div');
        subcommentReplyDiv.className = 'comment-meta reply-1';

        var spanCommenReply = document.createElement('span');
        spanCommenReply.id = 'sub_cmt_like_'+obj.id;
        var spanSubCommenInnerHtml = '';
        if( subcommentLikesCount[obj.id] && subcommentLikesCount[obj.id]['user_id'][userId]){
          spanSubCommenInnerHtml +='<i id="sub_comment_like_'+obj.id+'" data-video_id="'+obj.course_video_id+'" data-comment_id="'+obj.course_comment_id+'"  data-sub_comment_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
          spanSubCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(subcommentLikesCount[obj.id]['like_id']).length +'</span>';
        } else {
          spanSubCommenInnerHtml +='<i id="sub_comment_like_'+obj.id+'" data-video_id="'+obj.course_video_id+'" data-comment_id="'+obj.course_comment_id+'" data-sub_comment_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
          if(subcommentLikesCount[obj.id]){
            spanSubCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(subcommentLikesCount[obj.id]['like_id']).length +'</span>';
          }
        }
        spanCommenReply.innerHTML = spanSubCommenInnerHtml;
        subcommentReplyDiv.appendChild(spanCommenReply);

        var spanSubCommenReplyButton = document.createElement('span');
        spanSubCommenReplyButton.className = 'mrgn_5_left';
        spanSubCommenReplyButton.innerHTML = '<a class="" role="button" data-toggle="collapse" href="#replySubComment'+obj.course_comment_id+'-'+obj.id+'" aria-expanded="false" aria-controls="collapseExample">reply</a>';
        subcommentReplyDiv.appendChild(spanSubCommenReplyButton);

        var spanSubCommenReplyDate = document.createElement('span');
        spanSubCommenReplyDate.className = 'text-muted time-of-reply';
        spanSubCommenReplyDate.innerHTML = '<i class="fa fa-clock-o"></i>'+ obj.updated_at;
        subcommentReplyDiv.appendChild(spanSubCommenReplyDate);

        var createSubCommenDiv = document.createElement('div');
        createSubCommenDiv.className = 'collapse replyComment';
        createSubCommenDiv.id = 'replySubComment'+obj.course_comment_id+'-'+obj.id;
        createSubCommenDivInnerHTML = '<div class="form-group"><label for="subcomment">Your Sub Comment</label>';
        if( userId != obj.user_id ){
          createSubCommenDivInnerHTML += '<textarea name="subcomment" class="form-control" rows="3" id="createSubComment_'+ obj.id +'" >'+obj.user_name+'</textarea>';
        } else {
          createSubCommenDivInnerHTML += '<textarea name="subcomment" class="form-control" rows="3" id="createSubComment_'+ obj.id +'"></textarea>';
        }
        createSubCommenDivInnerHTML += '</div><button class="btn btn-default" onclick="confirmSubmitReplytoSubComment(this);" data-subcomment_id="'+ obj.id +'" data-comment_id="'+ obj.course_comment_id +'" data-video_id="'+ obj.course_video_id +'" >Send</button><button class="btn btn-default" data-id="replySubComment'+ obj.course_comment_id +'-'+ obj.id +'" onclick="cancleReply(this);">Cancle</button>';
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
        var commentId = $(ele).data('comment_id');
        var videoId = $(ele).data('video_id');
        var subcommentDataId = 'subcomment_'+videoId+'_'+commentId;
        var subcomment = document.getElementById(subcommentDataId).value;

        $.ajax({
            method: "POST",
            url: "{{url('createCourseSubComment')}}",
            data: {video_id:videoId, comment_id:commentId, subcomment:subcomment}
        })
        .done(function( msg ) {
          renderComments(msg, userId);
        });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }

  function confirmSubmitReplytoSubComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
        var commentId = $(ele).data('comment_id');
        var videoId = $(ele).data('video_id');
        var subcommentId = $(ele).data('subcomment_id');
        var subcomment = document.getElementById('createSubComment_'+subcommentId).value;

        $.ajax({
            method: "POST",
            url: "{{url('createCourseSubComment')}}",
            data: {video_id:videoId, comment_id:commentId, subcomment:subcomment, subcomment_id:subcommentId}
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
                    var commentId = $(ele).data('comment_id');
                    var videoId = $(ele).data('video_id');
                    var userId = parseInt(document.getElementById('user_id').value);
                    $.ajax({
                        method: "POST",
                        url: "{{url('deleteCourseComment')}}",
                        data: {video_id:videoId, comment_id:commentId}
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

  function updateComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var commentId = $(ele).data('comment_id');
    var videoId = $(ele).data('video_id');
    commentid = 'comment'+commentId;
    var comment = CKEDITOR.instances[commentid].getData();
    $.ajax({
        method: "POST",
        url: "{{url('updateCourseComment')}}",
        data: {video_id:videoId, comment_id:commentId, comment:comment}
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
        content: 'You want to delete this comment?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var commentId = $(ele).data('comment_id');
                    var videoId = $(ele).data('video_id');
                    var subcommentId = $(ele).data('subcomment_id');
                    var userId = parseInt(document.getElementById('user_id').value);
                    $.ajax({
                        method: "POST",
                        url: "{{url('deleteCourseSubComment')}}",
                        data: {video_id:videoId, comment_id:commentId, subcomment_id:subcommentId}
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

  function updateCourseSubComment(ele){
      var commentId = $(ele).data('comment_id');
      var videoId = $(ele).data('video_id');
      var subcommentId = $(ele).data('subcomment_id');
      var subcomment = document.getElementById('updateSubComment_'+subcommentId).value;
      var userId = parseInt(document.getElementById('user_id').value);
      $.ajax({
          method: "POST",
          url: "{{url('updateCourseSubComment')}}",
          data: {video_id:videoId, comment_id:commentId, subcomment_id:subcommentId, subcomment:subcomment}
      })
      .done(function( msg ) {
        renderComments(msg, userId);
      });
  }

  function cancleReply(ele){
    var id = $(ele).data('id');
    document.getElementById(id).classList.remove("in");
  }

    $(document).on("click", "i[id^=video_like_]", function(e) {
        var videoId = $(this).data('video_id');
        var dislike = $(this).data('dislike');
        var userId = parseInt(document.getElementById('user_id').value);
        if( isNaN(userId)) {
          $('#loginUserModel').modal();
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('likeCourseVideo')}}",
              data: {video_id:videoId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
              var likeSpan = document.getElementById('like_'+videoId);
              likeSpan.innerHTML = '';
              if( 1 == dislike ){
                likeSpan.innerHTML +='<i id="video_like_'+videoId+'" data-video_id="'+videoId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"> Like </i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              } else {
                likeSpan.innerHTML +='<i id="video_like_'+videoId+'" data-video_id="'+videoId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"> Like </i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              }
            }
          });
        }
    });

    $(document).on("click", "i[id^=comment_like_]", function(e) {
        var videoId = $(this).data('video_id');
        var commentId = $(this).data('comment_id');
        var dislike = $(this).data('dislike');
        var userId = parseInt(document.getElementById('user_id').value);
        if( isNaN(userId)) {
          $('#loginUserModel').modal();
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('likeCourseVideoComment')}}",
              data: {video_id:videoId, comment_id:commentId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
                var likeSpan = document.getElementById('cmt_like_'+commentId);
                likeSpan.innerHTML = '';
                if( 1 == dislike ){
                  likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-video_id="'+videoId+'" data-comment_id="'+commentId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like" style= "margin-right:5px;"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                } else {
                  likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-video_id="'+videoId+'" data-comment_id="'+commentId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like" style= "margin-right:5px;"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                }
          }
          });
        }
    });

    $(document).on("click", "i[id^=sub_comment_like_]", function(e) {
        var videoId = $(this).data('video_id');
        var commentId = $(this).data('comment_id');
        var subCommentId = $(this).data('sub_comment_id');
        var dislike = $(this).data('dislike');
        var userId = parseInt(document.getElementById('user_id').value);
        if( isNaN(userId)) {
          $('#loginUserModel').modal();
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('likeCourseVideoSubComment')}}",
              data: {video_id:videoId, comment_id:commentId, sub_comment_id:subCommentId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
                var likeSpan = document.getElementById('sub_cmt_like_'+subCommentId);
                likeSpan.innerHTML = '';
                if( 1 == dislike ){
                  likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-video_id="'+videoId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                } else {
                  likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-video_id="'+videoId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                }
          }
          });
        }
    });
</script>
<script type="text/javascript">
  $( document ).ready(function() {
    showCommentEle = "{{ Session::get('course_comment_area')}}";
    showsubCommentEle = "{{ Session::get('show_subcomment_area')}}";

     if(showCommentEle > 0){
        window.location.hash = '#showComment_'+showCommentEle;
      } else if(showsubCommentEle > 0){
        window.location.hash = '#subcomment_'+showsubCommentEle;
      }
      showMore();
  });
</script>
<script type="text/javascript">
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