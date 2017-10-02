@extends('dashboard.dashboard')
@section('mytest_header')
  <link href="{{ asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Replies </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-comments"></i> Discussion</li>
      <li class="active">My Replies</li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <section   class="v_container">
    <div class="container ">
      <div class="row">
        <div class="col-sm-9">
          <div class="post-comments ">
            <div class="" id="showAllPosts">
              @if(count($posts) > 0)
                @foreach($posts as $post)
                 <div class="media" id="showPost_{{$post->id}}">
                    <div class="media-heading" >
                      <div class="user-block ">
                        <a id="{{$post->id}}" class="tital" onClick="goToPost(this);" style="cursor: pointer;">{{$post->title}} </a>
                          <form id="goToPost_{{$post->id}}" action="{{ url('goToPost')}}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                            <input type="hidden" name="post_id" value="{{$post->id}}">
                          </form>
                      </div>
                      <div class="box-tools ">
                        <button type="button" data-toggle="collapse" data-target="#post{{$post->id}}" aria-expanded="false" aria-controls="collapseExample" class="btn btn-box-tool clickable-btn" ><i class="fa fa-chevron-up"></i>
                        </button>
                      </div>
                    </div>
                    <div class="cmt-parent panel-collapse collapse in" id="post{{$post->id}}">
                    <div class="user-block cmt-left-margin">
                      <img class="img-circle" src="{{ asset('images/user1.png') }}" alt="User Image" />
                      <span class="username">{{ $user->find($post->user_id)->name }} </span>
                      <span class="description">Shared publicly - {{$post->updated_at->diffForHumans()}}</span>
                    </div>
                    <div  class="media-body" data-toggle="lightbox">
                      <br/>
                      <div class="more bold cmt-left-margin" id="editPostHide_{{$post->id}}">{!! $post->body !!}</div>
                      <br/>
                      <div class="border-bottom"></div>
                      <div class="comment-meta main-reply-box cmt-left-margin">
                          <span id="like_{{$post->id}}" >
                            @if( isset($likesCount[$post->id]) && isset($likesCount[$post->id]['user_id'][$currentUser]))
                                 <i id="post_like_{{$post->id}}" data-post_id="{{$post->id}}" data-episode_id="{{$post->episode_id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                 <span id="like1-bs3">{{count($likesCount[$post->id]['like_id'])}}</span>
                            @else
                                 <i id="post_like_{{$post->id}}" data-post_id="{{$post->id}}" data-episode_id="{{$post->episode_id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                                 <span id="like1-bs3">@if( isset($likesCount[$post->id])) {{count($likesCount[$post->id]['like_id'])}} @endif</span>
                            @endif
                          </span>
                        </div>
                      </div>
                      <div class="cmt-bg">
                        <div class="box-body chat" id="chat-box">
                          @if(count( $post->comments) > 0)
                            @foreach($post->comments as $comment)
                              <div class="item cmt-left-margin-10" id="showComment_{{$comment->id}}">
                                <img src="{{ asset('images/user1.png') }}" alt="User Image" />
                                <div class="message">
                                    <a class="SubCommentName" id="{{$comment->id}}" onClick="goToComment(this);" style="cursor: pointer;">{{ $user->find($comment->user_id)->name }}</a>
                                    <form id="goToComment_{{$comment->id}}" action="{{ url('goToComment')}}" method="POST" style="display: none;">
                                      {{ csrf_field() }}
                                      <input type="hidden" name="comment_id" value="{{$comment->id}}">
                                    </form>
                                    <div class="more" id="editCommentHide_{{$comment->id}}">{!! $comment->body !!}</div>
                                  </div>
                                  <div class="comment-meta reply-1">
                                    <span id="cmt_like_{{$comment->id}}" >
                                      @if( isset($commentLikesCount[$comment->id]) && isset($commentLikesCount[$comment->id]['user_id'][$currentUser]))
                                           <i id="comment_like_{{$comment->id}}" data-post_id="{{$comment->discussion_post_id}}" data-comment_id="{{$comment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                           <span id="like1-bs3">{{count($commentLikesCount[$comment->id]['like_id'])}}</span>
                                      @else
                                           <i id="comment_like_{{$comment->id}}" data-post_id="{{$comment->discussion_post_id}}" data-comment_id="{{$comment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                                           <span id="like1-bs3">@if( isset($commentLikesCount[$comment->id])) {{count($commentLikesCount[$comment->id]['like_id'])}} @endif</span>
                                      @endif
                                    </span>
                                  <span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> {{$comment->updated_at->diffForHumans()}}</span>
                                </div>
                              </div>
                              @if(count( $comment->children ) > 0)
                                @include('dashboard.myRepliesComments', ['comments' => $comment->children, 'parent' => $comment->id, 'user' => $user])
                              @endif
                            @endforeach
                          @endif
                        </div>
                      </div>
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
  <script type="text/javascript">
    function confirmSubmit(ele){
      var userId = parseInt(document.getElementById('user_id').value);
      var categoryId = parseInt(document.getElementById('post_category').value);
      var question = document.getElementById('question_text').value;

      if(0 < userId && 0 < categoryId && question){
          var category = document.getElementById('post_category_id');
          category.value= categoryId;
          formId = $(ele).attr('id');
          form = document.getElementById(formId);
          form.submit();
      } else if( isNaN(userId)) {
        $.confirm({
        title: 'Confirmation',
        content: 'Please login first. Click "Ok" button to login.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    window.location="{{url('/home')}}";
                  }
              },
              Cancle: function () {
              }
          }
        });
      }else if( isNaN(categoryId)) {
        $.alert({
          title: 'Alert!',
          content: 'Please select post category.',
        });
      } else if( !question){
        $.alert({
          title: 'Alert!',
          content: 'Please enter something in a question. ',
        });
      }
    }

    function confirmSubmitReply(ele){
      var userId = parseInt(document.getElementById('user_id').value);
      if(0 < userId){
          formId = $(ele).attr('id');
          form = document.getElementById(formId);
          form.submit();
      } else if( isNaN(userId)) {
        $.confirm({
        title: 'Confirmation',
        content: 'Please login first. Click "Ok" button to login.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    window.location="{{url('/home')}}";
                  }
              },
              Cancle: function () {
              }
          }
        });
      }
    }

    function goToPost(ele){
      formId ='goToPost_'+ $(ele).attr('id');
      form = document.getElementById(formId);
      form.submit();
    }

    function goToComment(ele){
      formId ='goToComment_'+ $(ele).attr('id');
      form = document.getElementById(formId);
      form.submit();
    }

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

  $(document).on("click", "i[id^=post_like_]", function(e) {
        var postId = $(this).data('post_id');
        var dislike = $(this).data('dislike');
        var userId = parseInt(document.getElementById('user_id').value);
         if( isNaN(userId)) {
          $.confirm({
          title: 'Confirmation',
          content: 'Please login first. Click "Ok" button to login.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      window.location="{{url('/home')}}";
                    }
                },
                Cancle: function () {
                }
            }
          });
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
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              } else {
                likeSpan.innerHTML +='<i id="post_like_'+postId+'" data-post_id="'+postId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
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
          $.confirm({
          title: 'Confirmation',
          content: 'Please login first. Click "Ok" button to login.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      window.location="{{url('/home')}}";
                    }
                },
                Cancle: function () {
                }
            }
          });
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
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                } else {
                  likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-post_id="'+postId+'" data-comment_id="'+commentId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
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
          $.confirm({
          title: 'Confirmation',
          content: 'Please login first. Click "Ok" button to login.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      window.location="{{url('/home')}}";
                    }
                },
                Cancle: function () {
                }
            }
          });
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
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                } else {
                  likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-post_id="'+postId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                }
          }
          });
        }
    });
  </script>
@stop