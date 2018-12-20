@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu – Be partner with Vchip Technology</title>
@stop
@section('header-css')
	@include('layouts.home-css')
<link id="cpswitch" href="{{ asset('css/hover.css?ver=1.0')}}" rel="stylesheet" />
<link href="{{ asset('css/v_career.css?ver=1.0')}}" rel="stylesheet"/>
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
    .user-block img {
      width: 40px;
      height: 40px;
      float: left;
      border: 2px solid #d2d6de;
      padding: 1px;
    }
    .img-circle {
      border-radius: 50%;
    }
    .user-block .username, .user-block .description{
        display: block;
        margin-left: 50px;
    }
    a.list-group-item:hover{
      color: #f4645f;
    }
    h1 {
      font-size: 48px;
      font-weight: 200;
    }
</style>
@stop
@section('header-js')
	@include('layouts.home-js')
@stop
@section('content')
@include('header.study_material_menu',compact('categories','subcategories'))
<div class="container_fluid" style="padding-top: 100px; padding-bottom: 50px;">
  <div class="row ">
    <div style="margin-left: 35px;">
      <a data-toggle="modal" data-target="#review-model-{{$subcategoryId}}" style="cursor: pointer;">
        <div style="display: inline-block;">
          @if(isset($reviewData[$subcategoryId])) {{$reviewData[$subcategoryId]['avg']}} @else 0 @endif
        </div>
        <div style="display: inline-block;">
          <input id="rating_input{{$subcategoryId}}" name="input-{{$subcategoryId}}" class="rating rating-loading" value="@if(isset($reviewData[$subcategoryId])) {{$reviewData[$subcategoryId]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
        </div>
        <div style="display: inline-block;">
          @if(isset($reviewData[$subcategoryId]))
            {{count($reviewData[$subcategoryId]['rating'])}} <i class="fa fa-group"></i>
          @else
            0 <i class="fa fa-group"></i>
          @endif
        </div>
      </a>
    </div>
    <div id="review-model-{{$subcategoryId}}" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            &nbsp;&nbsp;&nbsp;
            <button class="close" data-dismiss="modal">×</button>
            <div class="form-group row ">
              <div  style="display: inline-block;">
                @if(isset($reviewData[$subcategoryId])) {{$reviewData[$subcategoryId]['avg']}} @else 0 @endif
              </div>
              <div  style="display: inline-block;">
                <input name="input-{{$subcategoryId}}" class="rating rating-loading" value="@if(isset($reviewData[$subcategoryId])) {{$reviewData[$subcategoryId]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
              </div>
              <div  style="display: inline-block;">
                @if(isset($reviewData[$subcategoryId]))
                  {{count($reviewData[$subcategoryId]['rating'])}} <i class="fa fa-group"></i>
                @else
                  0 <i class="fa fa-group"></i>
                @endif
              </div>
              @if(is_object(Auth::user()))
                <button class="pull-right" data-toggle="modal" data-target="#rating-model-{{$subcategoryId}}">
                @if(isset($reviewData[$subcategoryId]) && isset($reviewData[$subcategoryId]['rating'][Auth::user()->id]))
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
              @if(isset($reviewData[$subcategoryId]))
                @foreach($reviewData[$subcategoryId]['rating'] as $userId => $review)
                  <div class="user-block cmt-left-margin">
                    @if(is_file($userNames[$userId]['photo']) || (!empty($userNames[$userId]['photo']) && false == preg_match('/userStorage/',$userNames[$userId]['photo'])))
                      <img src="{{ asset($userNames[$userId]['photo'])}} " class="img-circle" alt="User Image">
                    @else
                      <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
                    @endif
                    <span class="username">{{ $userNames[$userId]['name'] }} </span>
                    <span class="description">Shared publicly - {{$review['updated_at']}}</span>
                  </div>
                  <br>
                  <input id="rating_input-{{$subcategoryId}}-{{$userId}}" name="input-{{$subcategoryId}}" class="rating rating-loading" value="{{$review['rating']}}" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
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
    <div id="rating-model-{{$subcategoryId}}" class="modal fade" role="dialog">
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
                @if(isset($reviewData[$subcategoryId]) && is_object(Auth::user()) && isset($reviewData[$subcategoryId]['rating'][Auth::user()->id]))
                  <input id="rating_input-{{$subcategoryId}}" name="input-{{$subcategoryId}}" class="rating rating-loading" value="{{$reviewData[$subcategoryId]['rating'][Auth::user()->id]['rating']}}" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                @else
                  <input id="rating_input-{{$subcategoryId}}" name="input-{{$subcategoryId}}" class="rating rating-loading" value="0" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                @endif
                Review:<input type="text" name="review-text" class="form-control" value="@if(isset($reviewData[$subcategoryId])  && is_object(Auth::user()) && isset($reviewData[$subcategoryId]['rating'][Auth::user()->id])) {{trim($reviewData[$subcategoryId]['rating'][Auth::user()->id]['review'])}} @endif">
                <br>
                <input type="hidden" name="module_id" value="{{$subcategoryId}}">
                <input type="hidden" name="module_type" value="4">
                <input type="hidden" name="rating_id" value="@if(isset($reviewData[$subcategoryId]) && is_object(Auth::user()) && isset($reviewData[$subcategoryId]['rating'][Auth::user()->id])) {{$reviewData[$subcategoryId]['rating'][Auth::user()->id]['review_id']}} @endif">
                <button type="submit" class="pull-right">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-2">
      <div id="MainMenu">
        <div class="list-group panel">
          @if(count($subjects) > 0)
            <b> {{$subcategoryName}}</b>
            @foreach($subjects as $subjectId => $subject)
              <a href="#{{$subjectId}}" class="list-group-item" data-toggle="collapse" data-parent="#MainMenu"><b>{{$subject}}</b>  <i class="fa fa-caret-down"></i></a>
              @if(count($topics) > 0)
                @if($selectedSubjectId == $subjectId)
                  <div class="collapse in" id="{{$subjectId}}">
                @else
                  <div class="collapse" id="{{$subjectId}}">
                @endif
                  @foreach($topics[$subjectId] as $intTopicId => $topic)
                    <a href="{{ url('study-material')}}/{{$subcategoryId}}/{{$subject}}/{{$intTopicId}}" class="list-group-item" style="color: #f4645f;">{{$topic}}</a>
                  @endforeach
                </div>
              @endif
            @endforeach
          @endif
        </div>
      </div>
    </div>
    <div class="col-md-10">
      <div align="center"><img id="adImage" width="100%" style="max-width: 600px;" src=""></div>
      <h1 align="center">{{$topicName}}</h1>
      <hr>
      {!! $topicContent !!}
      <hr>
      <div class="post-comments ">
        <div class="row" id="showAllPosts">
          @if(count($posts) > 0)
            @foreach($posts as $index => $post)
             <div class="media" id="showPost_{{$post->id}}">
                <div class="cmt-parent panel-collapse collapse in" id="post{{$post->id}}">
                <div  class="media-body" data-toggle="lightbox">
                  <div class="more img-ckeditor img-responsive" id="editPostHide_{{$post->id}}">{{$index + 1}}. {!! $post->body !!}
                  </div>
                  <br/>
                  @if($post->answer1 && $post->answer2 && $post->answer && $post->solution)
                    <div class="cmt-left-margin">
                      <p id="1" role="button" data-post_id="{{$post->id}}" onClick="checkAnswer(this)">
                        1. {!! $post->answer1 !!}
                        @if(1 == $post->answer)
                          <span class="hide" id="right_answer_image_{{$post->id}}_1"> <img src="{{ url('images/accept.png')}}"></span>
                        @else
                          <span class="hide" id="wrong_answer_image_{{$post->id}}_1"> <img src="{{ url('images/delete1.png')}}"></span>
                        @endif
                      </p>
                      <p id="2" role="button" data-post_id="{{$post->id}}" onClick="checkAnswer(this)">
                        2. {!! $post->answer2 !!}
                        @if(2 == $post->answer)
                          <span class="hide" id="right_answer_image_{{$post->id}}_2"> <img src="{{ url('images/accept.png')}}"></span>
                        @else
                          <span class="hide" id="wrong_answer_image_{{$post->id}}_2"> <img src="{{ url('images/delete1.png')}}"></span>
                        @endif
                      </p>
                      @if($post->answer3)
                      <p id="3" role="button" data-post_id="{{$post->id}}" onClick="checkAnswer(this)">
                        3. {!! $post->answer3 !!}
                        @if(3 == $post->answer)
                          <span class="hide" id="right_answer_image_{{$post->id}}_3"> <img src="{{ url('images/accept.png')}}"></span>
                        @else
                          <span class="hide" id="wrong_answer_image_{{$post->id}}_3"> <img src="{{ url('images/delete1.png')}}"></span>
                        @endif
                      </p>
                      @endif
                      @if($post->answer4)
                      <p id="4" role="button" data-post_id="{{$post->id}}" onClick="checkAnswer(this)">
                        4. {!! $post->answer4 !!}
                        @if(4 == $post->answer)
                          <span class="hide" id="right_answer_image_{{$post->id}}_4"> <img src="{{ url('images/accept.png')}}"></span>
                        @else
                          <span class="hide" id="wrong_answer_image_{{$post->id}}_4"> <img src="{{ url('images/delete1.png')}}"></span>
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
                        @if(is_object($currentUser))
                          | <a class="" role="button" data-toggle="collapse" href="#replyToPost{{$post->id}}" aria-expanded="false" aria-controls="collapseExample">Comment</a>
                        @else
                          | <a role="button" data-toggle="modal" data-placement="bottom" href="#loginUserModel">Comment</a>
                        @endif
                        @if($post->answer1 && $post->answer2 && $post->answer && $post->solution)
                          | <a id="{{$post->id}}" onClick="toggleSolution(this);">Solution</a>
                        @endif
                      </span>
                    <div class="collapse replyComment" id="replyToPost{{$post->id}}">
                        <div class="form-group">
                          <label for="comment">Your Comment</label>
                          <textarea name="comment" id="comment_{{$post->id}}" class="form-control" ></textarea>
                        </div>
                        <button class="btn btn-default" onclick="confirmSubmitReplytoPost(this);" data-post_id="{{$post->id}}">Send</button>
                        <button type="button" class="btn btn-default" data-id="replyToPost{{$post->id}}" onclick="cancleReply(this);">Cancle</button>
                    </div>
                  </div>
                  <div class="cmt-bg">
                    <div class="box-body chat" id="chat-box">
                      @if(count( $post->descComments) > 0)
                        @foreach($post->descComments as $comment)
                          @if(is_object($comment))
                            <div class="item cmt-left-margin-10" id="showComment_{{$comment->id}}">
                              @if(is_file($comment->getUser($comment->user_id)->photo) || (!empty($comment->getUser($comment->user_id)->photo) && false == preg_match('/userStorage/',$comment->getUser($comment->user_id)->photo)))
                                <img src="{{ asset($comment->getUser($comment->user_id)->photo)}} " class="img-circle" alt="User Image">
                              @else
                                <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
                              @endif
                              <div class="message">
                                @if(is_object($currentUser) && ($currentUser->id == $comment->user_id || $currentUser->id == $post->user_id))
                                <div class="dropdown pull-right">
                                  <button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                  </button>
                                  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    @if($currentUser->id == $comment->user_id || $currentUser->id == $post->user_id)
                                      <li><a id="{{$comment->id}}" onclick="confirmCommentDelete(this);">Delete</a></li>
                                    @endif
                                    @if($currentUser->id == $comment->user_id)
                                      <li><a id="{{$comment->id}}" onclick="editComment(this);">Edit</a></li>
                                    @endif
                                  </ul>
                                </div>
                                @endif
                                  <a class="SubCommentName">{{ $comment->getUser($comment->user_id)->name }}</a>
                                  <p class="more" id="editCommentHide_{{$comment->id}}">{!! $comment->body !!}</p>
                                    <div class="form-group hide" id="editCommentShow_{{$comment->id}}" >
                                      <textarea class="form-control" name="comment" id="comment_{{$post->id}}_{{$comment->id}}" rows="3">{!! $comment->body !!}</textarea>
                                      <button class="btn btn-primary" data-post_id="{{$post->id}}" data-comment_id="{{$comment->id}}" style="width: 100px;" onclick="updateComment(this);">Update</button>
                                      <button type="button" class="btn btn-default" id="{{$comment->id}}" onclick="cancleComment(this);">Cancle</button>
                                    </div>
                                </div>
                                <div class="comment-meta reply-1 cmt-left-margin">
                                  <span id="cmt_like_{{$comment->id}}" >
                                    @if( isset($commentLikesCount[$comment->id]) &&  is_object($currentUser) && isset($commentLikesCount[$comment->id]['user_id'][$currentUser->id]))
                                         <i id="comment_like_{{$comment->id}}" data-post_id="{{$comment->study_material_post_id}}" data-comment_id="{{$comment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                         <span id="like1-bs3">{{count($commentLikesCount[$comment->id]['like_id'])}}</span>
                                    @else
                                         <i id="comment_like_{{$comment->id}}" data-post_id="{{$comment->study_material_post_id}}" data-comment_id="{{$comment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
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
                              @include('studyMaterial.comments', ['comments' => $comment->children, 'parent' => $comment->id, 'currentUser' => $currentUser])
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
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="images" value="{{$images}}">
<input type="hidden" id="topic_id" value="{{$topicId}}">
<input type="hidden" id="user_id" value="@if(is_object(Auth::user())) {{Auth::user()->id}} @endif">
@stop
@section('footer')
  @include('footer.footer')
  <script src="{{ asset('js/star-rating.js') }}"></script>
  <script type="text/javascript">
  $(document).ready(function() {
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
      $('#right_answer_image_'+postId+'_'+answer).removeClass('hide');
      $('#wrong_answer_image_'+postId+'_'+answer).addClass('hide');
    } else {
      $(ele).prop('style', 'color:grey;');
      $('#right_answer_image_'+postId+'_'+answer).addClass('hide');
      $('#wrong_answer_image_'+postId+'_'+answer).removeClass('hide');
    }
  }
  function confirmSubmitReplytoPost(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
      var postId = $(ele).data('post_id');
      var topicId = parseInt(document.getElementById('topic_id').value);
      var comment = document.getElementById('comment_'+postId).value
      $.ajax({
          method: "POST",
          url: "{{url('createStudyMaterialComment')}}",
          data: {topic_id:topicId,post_id:postId, comment:comment}
      })
      .done(function( msg ) {
        renderPosts(msg);
      });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }

  function confirmSubmitReplytoComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
        var postId = $(ele).data('post_id');
        var commentId = $(ele).data('comment_id');
        var subcomment = document.getElementById('subcomment_'+postId+'_'+commentId).value;
        var topicId = parseInt(document.getElementById('topic_id').value);
        $.ajax({
            method: "POST",
            url: "{{url('createStudyMaterialSubComment')}}",
            data: {topic_id:topicId,post_id:postId,comment_id:commentId,subcomment:subcomment}
        })
        .done(function( msg ) {
          renderPosts(msg);
        });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }

  function confirmSubmitReplytoSubComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
        var postId = $(ele).data('post_id');
        var commentId = $(ele).data('comment_id');
        var parentId = $(ele).data('parent_id');
        var subcomment = document.getElementById('create_subcomment_'+parentId).value;
        var topicId = parseInt(document.getElementById('topic_id').value);
        $.ajax({
            method: "POST",
            url: "{{url('createStudyMaterialSubComment')}}",
            data: {topic_id:topicId,post_id:postId,comment_id:commentId,parent_id:parentId,subcomment:subcomment}
        })
        .done(function( msg ) {
          renderPosts(msg);
        });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }

  function cancleReply(ele){
    var id = $(ele).data('id');
    document.getElementById(id).classList.remove("in");
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

  function updateComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
      var postId = $(ele).data('post_id');
      var commentId = $(ele).data('comment_id');
      var comment = document.getElementById('comment_'+postId+'_'+commentId).value;
      var topicId = parseInt(document.getElementById('topic_id').value);
      $.ajax({
          method: "POST",
          url: "{{url('updateStudyMaterialComment')}}",
          data: {topic_id:topicId,post_id:postId,comment_id:commentId,comment:comment}
      })
      .done(function( msg ) {
        renderPosts(msg);
      });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }

  function updateSubComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
      var postId = $(ele).data('post_id');
      var commentId = $(ele).data('comment_id');
      var subcommentId = $(ele).data('subcomment_id');
      var comment = document.getElementById('update_subcomment_'+commentId+'_'+subcommentId).value;
      var topicId = parseInt(document.getElementById('topic_id').value);
      $.ajax({
          method: "POST",
          url: "{{url('updateStudyMaterialSubComment')}}",
          data: {topic_id:topicId,post_id:postId,comment_id:commentId,subcomment_id:subcommentId,comment:comment}
      })
      .done(function( msg ) {
        renderPosts(msg);
      });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
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
                var subcommentId = $(ele).data('subcomment_id');
                var topicId = parseInt(document.getElementById('topic_id').value);
                $.ajax({
                    method: "POST",
                    url: "{{url('deleteStudyMaterialSubComment')}}",
                    data: {topic_id:topicId,subcomment_id:subcommentId}
                })
                .done(function( msg ) {
                  renderPosts(msg);
                });
              }
          },
          Cancle: function () {
          }
      }
    });
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
                    var id = $(ele).attr('id');
                    var topicId = parseInt(document.getElementById('topic_id').value);
                    $.ajax({
                        method: "POST",
                        url: "{{url('deleteStudyMaterialComment')}}",
                        data: {topic_id:topicId,comment_id:id}
                    })
                    .done(function( msg ) {
                      renderPosts(msg);
                    });
                  }
          },
          Cancle: function () {
          }
      }
    });
  }

  $(document).on("click", "i[id^=post_like_]", function(e) {
    var postId = $(this).data('post_id');
    var dislike = $(this).data('dislike');
    var userId = parseInt(document.getElementById('user_id').value);
    var topicId = parseInt(document.getElementById('topic_id').value);

    if( isNaN(userId)) {
      $('#loginUserModel').modal();
    } else {
      $.ajax({
          method: "POST",
          url: "{{url('studyMaterialLikePost')}}",
          data: {topic_id:topicId,post_id:postId, dis_like:dislike}
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
      var topicId = parseInt(document.getElementById('topic_id').value);
      if( isNaN(userId)) {
        $('#loginUserModel').modal();
      } else {
        $.ajax({
            method: "POST",
            url: "{{url('studyMaterialLikeComment')}}",
            data: {topic_id:topicId,post_id:postId,comment_id:commentId,dis_like:dislike}
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
    var topicId = parseInt(document.getElementById('topic_id').value);
    if( isNaN(userId)) {
      $('#loginUserModel').modal();
    } else {
      $.ajax({
          method: "POST",
          url: "{{url('studyMaterialLikeSubComment')}}",
          data: {topic_id:topicId,post_id:postId, comment_id:commentId, sub_comment_id:subCommentId, dis_like:dislike}
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

  function renderPosts(msg){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 > userId){
      userId = 0;
    }
    showPostsDiv = document.getElementById('showAllPosts');
    showPostsDiv.innerHTML = '';
    var arrayPosts = [];

    $.each(msg['posts'], function(idx, obj) {
      arrayPosts[idx] = obj;
    });
    var sortedPostArray = arrayPosts;
      $.each(sortedPostArray, function(idx, obj) {
        if(false == $.isEmptyObject(obj)){
        var divMedia = document.createElement('div');
        divMedia.className = 'media';
        divMedia.id = 'showPosts_'+obj.id;

        var divMediaBody = document.createElement('div');
        divMediaBody.className = 'media-body';
        divMediaBody.setAttribute('data-toggle', 'lightbox');
        divMediaBody.innerHTML = '<br/>';
        var pBody = document.createElement('div');
        pBody.className = 'more img-ckeditor img-responsive cmt-left-margin';
        pBody.id ='editPostHide_'+ obj.id;
        pBody.innerHTML = (idx)+'. '+ obj.body + ' <br/>';
        divMediaBody.appendChild(pBody);

        var divPanel = document.createElement('div');
        divPanel.className = 'cmt-parent panel-collapse collapse in';
        divPanel.id = 'post'+obj.id;

        if(obj.answer1 && obj.answer2 && obj.answer && obj.solution){
          var solutionBody = document.createElement('div');
          solutionBody.className = 'cmt-left-margin';
          solutionInnerHtml = '<br/>';
          successImage = '{{ url('images/accept.png')}}';
          solutionInnerHtml += '<p id="1" role="button" data-post_id="'+obj.id+'" onClick="checkAnswer(this)">1. '+obj.answer1;
          if(1 == obj.answer){
            solutionInnerHtml += '<span class="hide" id="right_answer_image_'+obj.id+'"> <img src="'+successImage+'"></span>';
          }
          solutionInnerHtml += '</p>';
          solutionInnerHtml += '<p id="2" role="button" data-post_id="'+obj.id+'" onClick="checkAnswer(this)">2. '+obj.answer2;
          if(2 == obj.answer){
            solutionInnerHtml += '<span class="hide" id="right_answer_image_'+obj.id+'"> <img src="'+successImage+'"></span>';
          }
          solutionInnerHtml += '</p>';
          if(obj.answer3){
            solutionInnerHtml += '<p id="3" role="button" data-post_id="'+obj.id+'" onClick="checkAnswer(this)">3. '+obj.answer3;
            if(3 == obj.answer){
              solutionInnerHtml += '<span class="hide" id="right_answer_image_'+obj.id+'"> <img src="'+successImage+'"></span>';
            }
            solutionInnerHtml += '</p>';
          }
          if(obj.answer4){
            solutionInnerHtml += '<p id="4" role="button" data-post_id="'+obj.id+'" onClick="checkAnswer(this)">4. '+obj.answer4;
            if(4 == obj.answer){
              solutionInnerHtml += '<span class="hide" id="right_answer_image_'+obj.id+'"> <img src="'+successImage+'"></span>';
            }
            solutionInnerHtml += '</p>';
          }
          solutionInnerHtml += '<p class="hide" id="answer_'+obj.id+'"><b>Answer:</b> Option '+obj.answer+'</p><p class="hide" id="solution_'+obj.id+'"><b>Solution:</b><br/> '+obj.solution+'</p><input type="hidden" id="right_answer_'+obj.id+'" value="'+obj.answer+'">';
          solutionBody.innerHTML = solutionInnerHtml;
          divMediaBody.appendChild(solutionBody);
        }

        var borderDiv = document.createElement('div');
        borderDiv.className = 'border-bottom';
        divMediaBody.appendChild(borderDiv);

        var divComment = document.createElement('div');
        divComment.className = 'comment-meta main-reply-box cmt-left-margin';
        commentInnerHtml = '<span id="like_'+obj.id+'">';

          if( msg['likesCount'][obj.id] && msg['likesCount'][obj.id]['user_id'][userId]){
            commentInnerHtml +='<i id="post_like_'+obj.id+'" data-post_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
            commentInnerHtml +='<span id="like1-bs3">';
              if(Object.keys(msg['likesCount'][obj.id]['like_id']).length > 0){
                commentInnerHtml += Object.keys(msg['likesCount'][obj.id]['like_id']).length;
              }
            commentInnerHtml +='</span>';
          } else {
            commentInnerHtml +='<i id="post_like_'+obj.id+'" data-post_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
            if(msg['likesCount'][obj.id]){
              commentInnerHtml +='<span id="like1-bs3">';
              if(Object.keys(msg['likesCount'][obj.id]['like_id']).length > 0){
                commentInnerHtml += Object.keys(msg['likesCount'][obj.id]['like_id']).length;
              }
              commentInnerHtml +='</span>';
            }
          }
        commentInnerHtml +='</span><span class="mrgn_5_left">| ';
        if(userId > 0){
          commentInnerHtml += '<a class="" role="button" data-toggle="collapse" href="#replyToPost'+obj.id+'" aria-expanded="false" aria-controls="collapseExample">Comment</a>';
        } else {
          commentInnerHtml += '<a role="button" data-toggle="modal" data-placement="bottom" href="#loginUserModel">Comment</a>';
        }
        if(obj.answer1 && obj.answer2 && obj.answer && obj.solution){
          commentInnerHtml += '| <a id="'+obj.id+'" onClick="toggleSolution(this);">Solution</a>';
        }
        commentInnerHtml += '</span><div class="collapse replyComment" id="replyToPost'+obj.id+'"><div class="form-group"><label for="comment">Your Comment</label><textarea name="comment" id="comment_'+obj.id+'"  class="form-control" ></textarea></div><button class="btn btn-default" onclick="confirmSubmitReplytoPost(this);" data-post_id="'+obj.id+'">Send</button><button type="button" class="btn btn-default" data-id="replyToPost'+obj.id+'" onclick="cancleReply(this);">Cancle</button></div>';
        divComment.innerHTML = commentInnerHtml;
        divMediaBody.appendChild(divComment);

        var commentBgDiv = document.createElement('div');
        commentBgDiv.className = 'cmt-bg';

        var commentchatDiv = document.createElement('div');
        commentchatDiv.className = 'box-body chat';
        commentchatDiv.id = 'chat-box';
        var postId = obj.id;
        // var comments = obj.comments;
        var commentsArr = [];
        var arrayRevComments = [];
        $.each(obj.comments, function(idx, obj) {
          arrayRevComments[idx] = obj;
        });
        commentsArr = arrayRevComments.reverse();

        var commentLikesCount = msg['commentLikesCount'];
        var subcommentLikesCount = msg['subcommentLikesCount'];

        if(Object.keys(commentsArr).length > 0){
          if(false == $.isEmptyObject(commentsArr)){
            $.each(commentsArr, function(idx, obj) {
              if(false == $.isEmptyObject(obj)){
              var mainCommentDiv = document.createElement('div');
              mainCommentDiv.className = 'item cmt-left-margin-10';
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
              if( userId == obj.user_id){
                var commentEditDeleteDiv = document.createElement('div');
                commentEditDeleteDiv.className = 'dropdown pull-right';
                editDeleteInnerHtml = '<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>';
                editDeleteInnerHtml += '<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';
                if( userId == obj.user_id){
                  editDeleteInnerHtml += '<li><a id="'+obj.id+'" onclick="confirmCommentDelete(this);">Delete</a></li>';
                }
                if( userId == obj.user_id ){
                  editDeleteInnerHtml += '<li><a id="'+obj.id+'" onclick="editComment(this);">Edit</a></li>';
                }
                commentEditDeleteDiv.innerHTML = editDeleteInnerHtml;
                commentMessageDiv.appendChild(commentEditDeleteDiv);
              }

              var ancUserNameDiv = document.createElement('a');
              ancUserNameDiv.className = 'SubCommentName';
              ancUserNameDiv.innerHTML = obj.user_name;
              commentMessageDiv.appendChild(ancUserNameDiv);

              var pCommentBodyDiv = document.createElement('p');
              pCommentBodyDiv.className = 'more';
              pCommentBodyDiv.id = 'editCommentHide_'+obj.id;
              pCommentBodyDiv.innerHTML = obj.body; //'{!! '+obj.body+' !!}';
              commentMessageDiv.appendChild(pCommentBodyDiv);

              var spanUpdateComment = document.createElement('div');
              spanUpdateComment.className = 'form-group hide';
              spanUpdateComment.id = 'editCommentShow_'+obj.id;

              spanUpdateComment.innerHTML = '<textarea class="form-control" name="comment" id="comment_'+ postId +'_'+ obj.id +'" rows="3">'+ obj.body+'</textarea><button class="btn btn-primary" style="width: 100px;" data-post_id="'+ postId +'" data-comment_id="'+ obj.id +'" onclick="updateComment(this);">Update</button><button class="btn btn-default" id="'+ obj.id +'" onclick="cancleComment(this);">Cancle</button>';
              commentMessageDiv.appendChild(spanUpdateComment);
              mainCommentDiv.appendChild(commentMessageDiv);

              var commentReplyDiv = document.createElement('div');
              commentReplyDiv.className = 'comment-meta reply-1 cmt-left-margin';

              var spanCommenReply = document.createElement('span');
              spanCommenReply.id = 'cmt_like_'+obj.id;
              var spanCommenInnerHtml = '';
              if( commentLikesCount[obj.id] && commentLikesCount[obj.id]['user_id'][userId]){
                spanCommenInnerHtml +='<i id="comment_like_'+obj.id+'" data-post_id="'+obj.study_material_post_id+'" data-comment_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                spanCommenInnerHtml +='<span id="like1-bs3">';
                if(Object.keys(commentLikesCount[obj.id]['like_id']).length > 0){
                  spanCommenInnerHtml += Object.keys(commentLikesCount[obj.id]['like_id']).length;
                }
                spanCommenInnerHtml +='</span>';
              } else {
                spanCommenInnerHtml +='<i id="comment_like_'+obj.id+'" data-post_id="'+obj.study_material_post_id+'" data-comment_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                if(commentLikesCount[obj.id]){
                  spanCommenInnerHtml +='<span id="like1-bs3">';
                  if(Object.keys(commentLikesCount[obj.id]['like_id']).length > 0){
                    spanCommenInnerHtml += Object.keys(commentLikesCount[obj.id]['like_id']).length;
                  }
                  spanCommenInnerHtml +='</span>';
                }
              }
              spanCommenReply.innerHTML = spanCommenInnerHtml;
              commentReplyDiv.appendChild(spanCommenReply);

              var spanCommenReplyButton = document.createElement('span');
              spanCommenReplyButton.className = 'mrgn_5_left';
              if(userId > 0){
                spanCommenReplyButton.innerHTML = '<a class="" role="button" data-toggle="collapse" href="#replyToComment'+postId+'-'+obj.id+'" aria-expanded="false" aria-controls="collapseExample">reply</a>';
              } else {
                spanCommenReplyButton.innerHTML = '<a role="button" data-toggle="modal" data-placement="bottom" href="#loginUserModel">reply</a>';
              }
              commentReplyDiv.appendChild(spanCommenReplyButton);

              var spanCommenReplyDate = document.createElement('span');
              spanCommenReplyDate.className = 'text-muted time-of-reply';
              spanCommenReplyDate.innerHTML = '<i class="fa fa-clock-o"></i>'+ obj.updated_at;
              commentReplyDiv.appendChild(spanCommenReplyDate);

              var subCommenDiv = document.createElement('div');
              subCommenDiv.className = 'collapse replyComment';
              subCommenDiv.id = 'replyToComment'+postId+'-'+obj.id;
              subCommenDiv.innerHTML = '<div class="form-group"><label for="subcomment">Your Sub Comment</label><textarea name="subcomment" id="subcomment_'+postId+'_'+obj.id+'" class="form-control" rows="3"></textarea></div><input type="hidden" name="comment_id" value="'+ obj.id +'"><input type="hidden" name="post_id" value="'+postId+'"><button class="btn btn-default" data-post_id="'+postId+'" data-comment_id="'+obj.id+'"  onclick="confirmSubmitReplytoComment(this);">Send</button><button type="button" class="btn btn-default" data-id="replyToComment'+postId+'-'+obj.id+'" onclick="cancleReply(this);">Cancle</button>';
              commentReplyDiv.appendChild(subCommenDiv);
              mainCommentDiv.appendChild(commentReplyDiv);
              commentchatDiv.appendChild(mainCommentDiv);
              if( obj.subcomments ){
                if(false == $.isEmptyObject(obj.subcomments)){
                  var commentUserId = obj.user_id;
                  showSubComments(obj.subcomments, commentchatDiv, subcommentLikesCount, userId, commentUserId);
                }
              }
            }
            });
          }
        }

        commentBgDiv.appendChild(commentchatDiv);
        divMediaBody.appendChild(commentBgDiv);

        divPanel.appendChild(divMediaBody);
        divMedia.appendChild(divPanel);
        showPostsDiv.appendChild(divMedia);
        }
      });
      showMore();
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
        if( userId == obj.user_id || userId == commentUserId){
          var subcommentEditDeleteDiv = document.createElement('div');
          subcommentEditDeleteDiv.className = 'dropdown pull-right';
          editDeleteInnerHtml = '<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>';
          editDeleteInnerHtml += '<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">';
          if(  userId == obj.user_id || userId == commentUserId){
            editDeleteInnerHtml += '<li><a id="'+obj.study_material_comment_id+'_'+obj.id+'" data-subcomment_id="'+ obj.id +'" onclick="confirmSubCommentDelete(this);">Delete</a></li>';
          }
          if( userId == obj.user_id ){
            editDeleteInnerHtml += '<li><a id="'+obj.id+'" onclick="editSubComment(this);">Edit</a></li>';
          }
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
        spanSubCommentBodyDiv.innerHTML = ' '+ obj.body; //'{!! '+obj.body+' !!}';
        pSubcommentBodyDiv.appendChild(spanSubCommentBodyDiv);
        subCommentMessageDiv.appendChild(pSubcommentBodyDiv);

        var spanUpdateSubComment = document.createElement('div');
        spanUpdateSubComment.className = 'form-group hide';
        spanUpdateSubComment.id = 'editSubCommentShow_'+obj.id;

        spanUpdateSubComment.innerHTML = '<textarea class="form-control" name="comment" id="update_subcomment_'+ obj.study_material_comment_id +'_'+ obj.id +'" rows="3">'+ obj.body+'</textarea><button class="btn btn-primary" data-post_id="'+ obj.study_material_post_id+'" data-comment_id="'+ obj.study_material_comment_id +'" data-subcomment_id="'+ obj.id +'" style="width: 100px;" onclick="updateSubComment(this);">Update</button><button type="button" class="btn btn-default" id="'+ obj.id +'" onclick="cancleSubComment(this);">Cancle</button>';
        subCommentMessageDiv.appendChild(spanUpdateSubComment);
        mainSubCommentDiv.appendChild(subCommentMessageDiv);

        var subcommentReplyDiv = document.createElement('div');
        subcommentReplyDiv.className = 'comment-meta reply-1';

        var spanCommenReply = document.createElement('span');
        spanCommenReply.id = 'sub_cmt_like_'+obj.id;
        var spanSubCommenInnerHtml = '';
        if( subcommentLikesCount[obj.id] && subcommentLikesCount[obj.id]['user_id'][userId]){
          spanSubCommenInnerHtml +='<i id="sub_comment_like_'+obj.id+'" data-post_id="'+obj.study_material_post_id+'" data-comment_id="'+obj.study_material_comment_id+'"  data-sub_comment_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
          spanSubCommenInnerHtml +='<span id="like1-bs3">';
          if(Object.keys(subcommentLikesCount[obj.id]['like_id']).length > 0){
            spanSubCommenInnerHtml += Object.keys(subcommentLikesCount[obj.id]['like_id']).length;
          }
          spanSubCommenInnerHtml +='</span>';

        } else {
          spanSubCommenInnerHtml +='<i id="sub_comment_like_'+obj.id+'" data-post_id="'+obj.study_material_post_id+'" data-comment_id="'+obj.study_material_comment_id+'" data-sub_comment_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
          if(subcommentLikesCount[obj.id]){
            spanSubCommenInnerHtml +='<span id="like1-bs3">';
            if(Object.keys(subcommentLikesCount[obj.id]['like_id']).length > 0){
              spanSubCommenInnerHtml += Object.keys(subcommentLikesCount[obj.id]['like_id']).length;
            }
            spanSubCommenInnerHtml +='</span>';
          }
        }
        spanCommenReply.innerHTML = spanSubCommenInnerHtml;
        subcommentReplyDiv.appendChild(spanCommenReply);

        var spanSubCommenReplyButton = document.createElement('span');
        spanSubCommenReplyButton.className = 'mrgn_5_left';
        if(userId > 0){
          spanSubCommenReplyButton.innerHTML = '<a class="" role="button" data-toggle="collapse" href="#replySubComment'+obj.study_material_comment_id+'-'+obj.id+'" aria-expanded="false" aria-controls="collapseExample">reply</a>';
        } else {
          spanSubCommenReplyButton.innerHTML = '<a role="button" data-toggle="modal" data-placement="bottom" href="#loginUserModel">reply</a>';
        }
        subcommentReplyDiv.appendChild(spanSubCommenReplyButton);

        var spanSubCommenReplyDate = document.createElement('span');
        spanSubCommenReplyDate.className = 'text-muted time-of-reply';
        spanSubCommenReplyDate.innerHTML = '<i class="fa fa-clock-o"></i>'+ obj.updated_at;
        subcommentReplyDiv.appendChild(spanSubCommenReplyDate);

        var createSubCommenDiv = document.createElement('div');
        createSubCommenDiv.className = 'collapse replyComment';
        createSubCommenDiv.id = 'replySubComment'+obj.study_material_comment_id+'-'+obj.id;
        createSubCommenDivInnerHTML = '<div class="form-group"><label for="subcomment">Your Sub Comment</label>';
        if( userId != obj.user_id ){
          createSubCommenDivInnerHTML += '<textarea name="subcomment" class="form-control" id="create_subcomment_'+ obj.id +'"  rows="3">'+obj.user_name+'</textarea>';
        } else {
          createSubCommenDivInnerHTML += '<textarea name="subcomment" id="create_subcomment_'+ obj.id +'" class="form-control" rows="3"></textarea>';
        }
        createSubCommenDivInnerHTML += '</div><button class="btn btn-default"  data-post_id="'+ obj.study_material_post_id+'" data-comment_id="'+ obj.study_material_comment_id+'" data-parent_id="'+ obj.id+'" onclick="confirmSubmitReplytoSubComment(this);">Send</button><button type="button" class="btn btn-default" data-id="replySubComment'+obj.study_material_comment_id+'-'+obj.id+'" onclick="cancleReply(this);">Cancle</button>';
        createSubCommenDiv.innerHTML = createSubCommenDivInnerHTML;
        subcommentReplyDiv.appendChild(createSubCommenDiv);
        mainSubCommentDiv.appendChild(subcommentReplyDiv);
        commentchatDiv.appendChild(mainSubCommentDiv);
      });
    }
  }
</script>
@stop