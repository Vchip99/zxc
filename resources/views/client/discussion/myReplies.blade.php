@extends(is_object(Auth::guard('client')->user())?'client.dashboard':'clientuser.dashboard.dashboard')
@section('dashboard_header')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Replies </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-comments-o"></i> Discussion </li>
      <li class="active"> My Replies </li>
    </ol>
  </section>
  <style type="text/css">
    .ask-qst button{
      border-radius: 0px;
      margin-bottom: 20px;
      margin-left: -13px;
    }
    @media(max-width: 768px){
      .ask-qst {
        text-align: center !important;
      }
    }
    .cmt-left-margin{
      margin-left: 20px;
    }
    .red-color{
      color: red;
    }
  </style>
@stop
@section('dashboard_content')
  <section   class="v_container">
    <div class="container ">
      <div class="row">
        <div class="col-sm-9">
            <div class="ask-qst row">
              @if(is_object(Auth::guard('client')->user()))
                <a href="{{ url('manageDiscussion')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="Discussion"><i class="fa fa-comments"></i></a>&nbsp;
                <a href="{{ url('manageQuestions')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="My Questions"><i class="fa fa-question-circle"></i></a>&nbsp;
                <a href="{{ url('manageReplies')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="My Replies"><i class="fa fa-reply"></i></a>&nbsp;
              @else
                <a href="{{ url('myDiscussion')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="My Discussion"><i class="fa fa-comments"></i></a>&nbsp;
                <a href="{{ url('myQuestions')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="My Questions"><i class="fa fa-question-circle"></i></a>&nbsp;
                <a href="{{ url('myReplies')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="My Replies"><i class="fa fa-reply"></i></a>&nbsp;
              @endif
             </div>
                <div class="post-comments ">
                  <div class="row" id="showAllPosts">
                    @if(count($posts) > 0)
                      @foreach($posts as $post)
                       <div class="media" id="showPost_{{$post->id}}">
                          <div class="media-heading" >
                          <div class="user-block ">
                            <span class="tital">{{$post->title}} </span>
                          </div>
                          <div class="box-tools ">
                            <button type="button" data-toggle="collapse" data-target="#post{{$post->id}}" aria-expanded="false" aria-controls="collapseExample" class="btn btn-box-tool clickable-btn" ><i class="fa fa-chevron-up"></i>
                            </button>
                          </div>
                          </div>
                          <div class="cmt-parent panel-collapse collapse in" id="post{{$post->id}}">
                          <div class="user-block cmt-left-margin">
                          @if($post->clientuser_id > 0 && (is_file($post->getUser($post->clientuser_id)->photo) || (!empty($post->getUser($post->clientuser_id)->photo) && false == preg_match('/clientUserStorage/',$post->getUser($post->clientuser_id)->photo))))
                            <img src="{{ asset($post->getUser($post->clientuser_id)->photo)}} " class="img-circle" alt="User Image">
                          @elseif(0 == $post->clientuser_id && (is_file($post->getClient($post->client_id)->photo) || (!empty($post->getClient($post->client_id)->photo) && false == preg_match('/client_images/',$post->getClient($post->client_id)->photo))))
                            <img src="{{ asset($post->getClient($post->client_id)->photo)}} " class="img-circle" alt="User Image">
                          @else
                            <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
                          @endif
                            <span class="username">
                              @if($post->clientuser_id > 0)
                                {{ $post->getUser($post->clientuser_id)->name }}
                              @else
                                {{ $post->getClient($post->client_id)->name }}
                              @endif
                            </span>
                            <span class="description">Shared publicly - {{$post->updated_at->diffForHumans()}}</span>
                          </div>
                          <div  class="media-body" data-toggle="lightbox">
                            <br/>
                            <div class="more img-ckeditor img-responsive cmt-left-margin" id="editPostHide_{{$post->id}}">{!! $post->body !!}
                            </div>
                            <br/>
                            @if($post->answer1 && $post->answer2 && $post->answer && $post->solution)
                              <div class="cmt-left-margin">
                                <p id="1" role="button" data-post_id="{{$post->id}}" onClick="checkAnswer(this)">
                                  1. {!! $post->answer1 !!}
                                  @if(1 == $post->answer)
                                    <span class="hide" id="right_answer_image_{{$post->id}}"> <img src="{{ url('images/accept.png')}}"></span>
                                  @endif
                                </p>
                                <p id="2" role="button" data-post_id="{{$post->id}}" onClick="checkAnswer(this)">
                                  2. {!! $post->answer2 !!}
                                  @if(2 == $post->answer)
                                    <span class="hide" id="right_answer_image_{{$post->id}}"> <img src="{{ url('images/accept.png')}}"></span>
                                  @endif
                                </p>
                                @if($post->answer3)
                                <p id="3" role="button" data-post_id="{{$post->id}}" onClick="checkAnswer(this)">
                                  3. {!! $post->answer3 !!}
                                  @if(3 == $post->answer)
                                    <span class="hide" id="right_answer_image_{{$post->id}}"> <img src="{{ url('images/accept.png')}}"></span>
                                  @endif
                                </p>
                                @endif
                                @if($post->answer4)
                                <p id="4" role="button" data-post_id="{{$post->id}}" onClick="checkAnswer(this)">
                                  4. {!! $post->answer4 !!}
                                  @if(4 == $post->answer)
                                    <span class="hide" id="right_answer_image_{{$post->id}}"> <img src="{{ url('images/accept.png')}}"></span>
                                  @endif
                                </p>
                                @endif
                                <p class="hide" id="answer_{{$post->id}}"><b>Answer:</b> Option {{ $post->answer }}</p>
                                <p class="hide" id="solution_{{$post->id}}"><b>Solution:</b><br/> {!! $post->solution !!}</p>
                                <input type="hidden" id="right_answer_{{$post->id}}" value="{{$post->answer}}">
                              </div>
                            @endif
                             <br/>
                            <div class="border-bottom"></div>
                            <div class="comment-meta main-reply-box cmt-left-margin">
                                <span id="like_{{$post->id}}" >
                                  @if( isset($likesCount[$post->id]) && is_object($currentUser) && isset($likesCount[$post->id]['user_id'][$currentUser->id]))
                                       <i id="post_like_{{$post->id}}" data-post_id="{{$post->id}}" data-episode_id="{{$post->episode_id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                       <span id="like1-bs3">{{count($likesCount[$post->id]['like_id'])}}</span>
                                  @else
                                       <i id="post_like_{{$post->id}}" data-post_id="{{$post->id}}" data-episode_id="{{$post->episode_id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                                       <span id="like1-bs3">@if( isset($likesCount[$post->id])) {{count($likesCount[$post->id]['like_id'])}} @endif</span>
                                  @endif
                                </span>
                               <span class="mrgn_5_left">
                                  @if($post->answer1 && $post->answer2 && $post->answer && $post->solution)
                                    | <a id="{{$post->id}}" onClick="toggleSolution(this);">Solution</a>
                                  @endif
                                </span>
                            </div>
                            <div class="cmt-bg">
                              <div class="box-body chat" id="chat-box">
                                @if(count( $post->descComments) > 0)
                                  @foreach($post->descComments as $comment)
                                    @if(is_object($comment))
                                      <div class="item cmt-left-margin-10" id="showComment_{{$comment->id}}">
                                        @if($comment->clientuser_id > 0 && (is_file($comment->getUser($comment->clientuser_id)->photo) || (!empty($comment->getUser($comment->clientuser_id)->photo) && false == preg_match('/clientUserStorage/',$comment->getUser($comment->clientuser_id)->photo))))
                                          <img src="{{ asset($comment->getUser($comment->clientuser_id)->photo)}} " class="img-circle" alt="User Image">
                                        @elseif(0 == $comment->clientuser_id && (is_file($comment->getClient($comment->client_id)->photo) || (!empty($comment->getClient($comment->client_id)->photo) && false == preg_match('/client_images/',$comment->getClient($comment->client_id)->photo))))
                                          <img src="{{ asset($comment->getClient($comment->client_id)->photo)}} " class="img-circle" alt="User Image">
                                        @else
                                          <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
                                        @endif

                                        <div class="message">
                                          @if(is_object($currentUser))
                                            @if((0 != $comment->clientuser_id && $currentUser->id == $comment->clientuser_id) || (0 == $comment->clientuser_id && $currentUser->id == $comment->client_id && 1 == $currentUser->admin_approve))
                                            <div class="dropdown pull-right">
                                              <button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                              </button>
                                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                @if(($currentUser->id == $comment->clientuser_id || $currentUser->id == $post->clientuser_id) ||($currentUser->id == $comment->client_id || $currentUser->id == $post->client_id))
                                                  <li><a id="{{$comment->id}}" onclick="confirmCommentDelete(this);">Delete</a></li>
                                                @endif
                                                @if(($currentUser->id == $comment->clientuser_id) || ($currentUser->id == $comment->client_id))
                                                  <li><a id="{{$comment->id}}" onclick="editComment(this);">Edit</a></li>
                                                @endif
                                              </ul>
                                            </div>
                                            @endif
                                          @endif
                                            <a class="SubCommentName">
                                              @if(0 != $comment->clientuser_id)
                                                {{ $comment->getUser($comment->clientuser_id)->name }}
                                              @else
                                                {{ $comment->getClient($comment->client_id)->name }}
                                              @endif
                                            </a>
                                            <p class="more" id="editCommentHide_{{$comment->id}}">{!! $comment->body !!}</p>
                                              <div class="form-group hide" id="editCommentShow_{{$comment->id}}" >
                                                <textarea class="form-control" name="comment" id="comment_{{$post->id}}_{{$comment->id}}" rows="3">{!! $comment->body !!}</textarea>
                                                <button class="btn btn-primary" data-post_id="{{$post->id}}" data-comment_id="{{$comment->id}}" onclick="updateComment(this);">Update</button>
                                                <button type="button" class="btn btn-default" id="{{$comment->id}}" onclick="cancleComment(this);">Cancle</button>
                                              </div>
                                          </div>
                                          <div class="comment-meta reply-1 cmt-left-margin">
                                            <span id="cmt_like_{{$comment->id}}" >
                                              @if( isset($commentLikesCount[$comment->id]) &&  is_object($currentUser) && isset($commentLikesCount[$comment->id]['user_id'][$currentUser->id]))
                                                   <i id="comment_like_{{$comment->id}}" data-post_id="{{$comment->client_discussion_post_id}}" data-comment_id="{{$comment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                                   <span id="like1-bs3">{{count($commentLikesCount[$comment->id]['like_id'])}}</span>
                                              @else
                                                   <i id="comment_like_{{$comment->id}}" data-post_id="{{$comment->client_discussion_post_id}}" data-comment_id="{{$comment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                                                   <span id="like1-bs3">@if( isset($commentLikesCount[$comment->id])) {{count($commentLikesCount[$comment->id]['like_id'])}} @endif</span>
                                              @endif
                                            </span>
                                           <span class="mrgn_5_left">
                                            @if(is_object($currentUser))
                                              <a class="" role="button" data-toggle="collapse" href="#replyToComment{{$post->id}}-{{$comment->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
                                            @else
                                              <a role="button" data-toggle="modal" data-placement="bottom" href="#loginUserModel">reply</a>
                                            @endif
                                          </span>
                                          <span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> {{$comment->updated_at->diffForHumans()}}</span>
                                          <div class="collapse replyComment" id="replyToComment{{$post->id}}-{{$comment->id}}">
                                              <div class="form-group">
                                                <label for="subcomment">Your Sub Comment</label>
                                                  <textarea name="subcomment" id="subcomment_{{$post->id}}_{{$comment->id}}" class="form-control" rows="3"></textarea>
                                              </div>
                                              <button class="btn btn-default" data-post_id="{{$post->id}}" data-comment_id="{{$comment->id}}" onclick="confirmSubmitReplytoComment(this);">Send</button>
                                              <button type="button" class="btn btn-default" data-id="replyToComment{{$post->id}}-{{$comment->id}}" onclick="cancleReply(this);">Cancle</button>
                                          </div>
                                        </div>
                                      </div>
                                      @if(count( $comment->children ) > 0)
                                        @include('client.discussion.comments', ['comments' => $comment->children, 'parent' => $comment->id, 'currentUser' => $currentUser])
                                      @endif
                                    @endif
                                  @endforeach
                                @endif
                              </div>
                            </div>
                          </div>
                          </div>
                        </div>
                      @endforeach
                    @else
                      No discussion questions
                    @endif
                  </div>
                </div>
        </div>
      </div>
    </div>
  </section>
  <script type="text/javascript">
  function toggleType(type){
    if('mcq' == type){
      $('#mcs_options').removeClass('hide');
    } else {
      $('#mcs_options').addClass('hide');
    }
  }

  function toggleSolution(ele){
    var solId = $(ele).attr('id');
    if($('#answer_'+solId).hasClass('hide')){
      $('#answer_'+solId).removeClass('hide');
    } else {
      $('#answer_'+solId).addClass('hide');
    }
    if($('#solution_'+solId).hasClass('hide')){
      $('#solution_'+solId).removeClass('hide');
    } else {
      $('#solution_'+solId).addClass('hide');
    }
  }

  function checkAnswer(ele){
    var answer = $(ele).attr('id');
    var postId = $(ele).data('post_id');
    var rightAnswer = $('#right_answer_'+postId).val();
    if(answer == rightAnswer){
      $(ele).prop('style', 'color:green;');
      $('#answer_'+postId).removeClass('hide');
      $('#solution_'+postId).removeClass('hide');
      $('#right_answer_image_'+postId).removeClass('hide');
    } else {
      $(ele).prop('style', 'color:grey;');
    }
  }

  $(document).on("click", "i[id^=post_like_]", function(e) {
        var postId = $(this).data('post_id');
        var dislike = $(this).data('dislike');
        var userId = parseInt(document.getElementById('user_id').value);
        if( isNaN(userId)) {
          $('#loginUserModel').modal();
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('discussionLikePost')}}",
              data: {post_id:postId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
              var likeSpan = document.getElementById('like_'+postId);
              likeSpan.innerHTML = '';
              if( 1 == dislike ){
                likeSpan.innerHTML +='<i id="post_like_'+postId+'" data-post_id="'+postId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                if(msg.length > 0){
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                }
              } else {
                likeSpan.innerHTML +='<i id="post_like_'+postId+'" data-post_id="'+postId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                if(msg.length > 0){
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                }
              }
            }
          });
        }
    });

   $(document).on("click", "i[id^=comment_like_]", function(e) {
        var postId = $(this).data('post_id');
        var commentId = $(this).data('comment_id');
        var dislike = $(this).data('dislike');
        var userId = parseInt(document.getElementById('user_id').value);
        if( isNaN(userId)) {
          $('#loginUserModel').modal();
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('discussionLikeComment')}}",
              data: {post_id:postId, comment_id:commentId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
                var likeSpan = document.getElementById('cmt_like_'+commentId);
                likeSpan.innerHTML = '';
                if( 1 == dislike ){
                  likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-post_id="'+postId+'" data-comment_id="'+commentId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                  if(msg.length > 0){
                    likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                  }
                } else {
                  likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-post_id="'+postId+'" data-comment_id="'+commentId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                  if(msg.length > 0){
                    likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                  }
                }
          }
          });
        }
    });

  $(document).on("click", "i[id^=sub_comment_like_]", function(e) {
        var postId = $(this).data('post_id');
        var commentId = $(this).data('comment_id');
        var subCommentId = $(this).data('sub_comment_id');
        var dislike = $(this).data('dislike');
        var userId = parseInt(document.getElementById('user_id').value);
        if( isNaN(userId)) {
          $('#loginUserModel').modal();
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('discussionLikeSubComment')}}",
              data: {post_id:postId, comment_id:commentId, sub_comment_id:subCommentId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
                var likeSpan = document.getElementById('sub_cmt_like_'+subCommentId);
                likeSpan.innerHTML = '';
                if( 1 == dislike ){
                  likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-post_id="'+postId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                  if(msg.length > 0){
                    likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                  }
                } else {
                  likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-post_id="'+postId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                  if(msg.length > 0){
                    likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                  }
                }
          }
          });
        }
    });

  $( document ).ready(function() {
      showMore();
  });

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