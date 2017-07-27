@extends('layouts.master')
@section('header-title')
  <title>V-edu – Technical Discussion Forum |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('header-js')
  @include('layouts.home-js')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
@stop
@section('content')
  @include('header.header_menu')
  <section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single">
      <div class="vchip-background-img ">
        <figure class="">
          <img src="{{ asset('images/discussion.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed"/ alt="vchip discussion Forum">
        </figure>
      </div>
      <div class="overlay"></div>
      <div class="vchip-background-content">
      <h2 class="animated bounceInLeft">Digital Education</h2>
        </div>
    </div>
  </section>
  <section   class="v_container">
    <div class="container ">
      <div class="row">
        <div class="col-sm-3" id="sidemenuindex">
          <h4 class="v_h4_subtitle"> Sorted By</h4>
          <div class="dropdown mrgn_20_top_btm" id="cat">
            <select id="category" class="form-control" name="category" title="Category" onChange="showPosts(this);" required>
              <option value = "0"> Select Category ...</option>
              @if(count($discussionCategories) > 0)
                @foreach($discussionCategories as $discussionCategory)
                  <option value = "{{$discussionCategory->id}}"> {{$discussionCategory->name}} </option>
                @endforeach
              @endif
            </select>
          </div>
          <h4 class="v_h4_subtitle mrgn_20_top_btm"> Filter By</h4>
          <div class="panel"></div>
          <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" title="Others"> Others</p>
          <div class="panel">
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="1" data-filter="recent" onclick="searchDuscussionPosts();">Recent</label>
            </div>
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="1" data-filter="mostpopular" onclick="searchDuscussionPosts();">Most popular</label>
            </div>
          </div>
        </div>
        <div class="col-sm-9">
          <div class="panel with-nav-tabs panel-info">
            <div class="panel-heading">
              <ul class="nav nav-tabs">
                <li class="active"><a href="#questions" data-toggle="tab" title="Questions">Questions</a></li>
                <li><a href="#askQuestion" data-toggle="tab" title="Ask Question">Ask Question</a></li>
              </ul>
            </div>
            <div class="panel-body">
              <div class="tab-content">
                <div class="tab-pane fade in active" id="questions" style="padding: 15px !important;">
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
                              @if(is_object(Auth::user()) && Auth::user()->id == $post->user_id)
                              <button type="button" class="btn btn-box-tool toggle-dropdown" data-toggle="dropdown"><i class="fa fa-cog"></i></button>
                              <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                <li><a id="{{$post->id}}" onclick="confirmPostDelete(this);">Delete</a></li>
                                <form id="deletePost_{{$post->id}}" action="{{ url('deletePost')}}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <input type="hidden" name="post_id" value="{{$post->id}}">
                                </form>
                                <li><a id="{{$post->id}}" onclick="editPost(this);">Edit</a></li>
                              </ul>
                              @endif
                            </div>
                            </div>
                            <div class="cmt-parent panel-collapse collapse in" id="post{{$post->id}}">
                            <div class="user-block">
                              <img class="img-circle" src="{{ asset('images/user1.png') }}" alt="User Image" />
                              <span class="username">{{ $user->find($post->user_id)->name }} </span>
                              <span class="description">Shared publicly - {{$post->updated_at->diffForHumans()}}</span>
                            </div>
                            <div  class="media-body" data-toggle="lightbox">
                              <br/>
                              <div class="more bold img-ckeditor img-responsive" id="editPostHide_{{$post->id}}">{!! $post->body !!}</div>
                              <form action="{{ url('updatePost')}}" method="POST" id="formUpdatePost{{$post->id}}">
                                    {{csrf_field()}}
                                    {{ method_field('PUT') }}
                                <div class="form-group hide" id="editPostShow_{{$post->id}}" >
                                  <textarea name="update_question" placeholder="Answer 1" type="text" id="updatequestion_{{$post->id}}" required>{!! $post->body !!}</textarea>
                                    <script type="text/javascript">
                                      CKEDITOR.replace( 'updatequestion_{{$post->id}}', { enterMode: CKEDITOR.ENTER_BR } );
                                      CKEDITOR.config.width="100%";
                                      CKEDITOR.config.height="auto";
                                      CKEDITOR.on('dialogDefinition', function (ev) {

                                          var dialogName = ev.data.name,
                                              dialogDefinition = ev.data.definition;

                                          if (dialogName == 'image') {
                                              var onOk = dialogDefinition.onOk;

                                              dialogDefinition.onOk = function (e) {
                                                  var width = this.getContentElement('info', 'txtWidth');
                                                  width.setValue('100%');//Set Default Width

                                                  var height = this.getContentElement('info', 'txtHeight');
                                                  height.setValue('auto');////Set Default height

                                                  onOk && onOk.apply(this, e);
                                              };
                                          }
                                      });
                                    </script>
                                  <input type="hidden" name="post_id" value="{{$post->id}}">
                                  <button type="submit" class="btn btn-primary">Update</button>
                                  <button type="button" class="btn btn-default" id="{{$post->id}}" onclick="canclePost(this);">Cancle</button>
                                </div>
                              </form>
                              <div class="border-bottom"></div>
                              <div class="comment-meta main-reply-box">
                                  <span id="like_{{$post->id}}" >
                                    @if( isset($likesCount[$post->id]) && isset($likesCount[$post->id]['user_id'][$currentUser]))
                                         <i id="post_like_{{$post->id}}" data-post_id="{{$post->id}}" data-episode_id="{{$post->episode_id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                         <span id="like1-bs3">{{count($likesCount[$post->id]['like_id'])}}</span>
                                    @else
                                         <i id="post_like_{{$post->id}}" data-post_id="{{$post->id}}" data-episode_id="{{$post->episode_id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                                         <span id="like1-bs3">@if( isset($likesCount[$post->id])) {{count($likesCount[$post->id]['like_id'])}} @endif</span>
                                    @endif
                                  </span>
                                 <span class="mrgn_5_left">
                                  <a class="" role="button" data-toggle="collapse" href="#replyToPost{{$post->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
                                </span>
                                <div class="collapse replyComment" id="replyToPost{{$post->id}}">
                                  <form action="{{ url('createComment')}}" method="POST" id="formReplyToPost{{$post->id}}">
                                    {{csrf_field()}}
                                    <div class="form-group">
                                      <label for="comment">Your Comment</label>
                                      <textarea name="comment" class="form-control" ></textarea>
                                    </div>
                                    <input type="hidden" name="discussion_post_id" value="{{$post->id}}">
                                    <button type="button" class="btn btn-default" onclick="confirmSubmitReplytoPost(this);" id="formReplyToPost{{$post->id}}">Send</button>
                                  </form>
                                </div>
                              </div>
                              <div class="cmt-bg">
                                <div class="box-body chat" id="chat-box">
                                  @if(count( $post->comments) > 0)
                                    @foreach($post->comments as $comment)
                                      <div class="item" id="showComment_{{$comment->id}}">
                                        <img src="{{ asset('images/user1.png') }}" alt="User Image" />
                                        <div class="message">
                                          @if(is_object(Auth::user()) && (Auth::user()->id == $comment->user_id || Auth::user()->id == $post->user_id))
                                          <div class="dropdown pull-right">
                                            <button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                              <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                              @if(Auth::user()->id == $comment->user_id || Auth::user()->id == $post->user_id)
                                                <li><a id="{{$comment->id}}" onclick="confirmCommentDelete(this);">Delete</a></li>
                                                <form id="deleteComment_{{$comment->id}}" action="{{ url('deleteComment')}}" method="POST" style="display: none;">
                                                  {{ csrf_field() }}
                                                  {{ method_field('DELETE') }}
                                                  <input type="hidden" name="comment_id" value="{{$comment->id}}">
                                                </form>
                                              @endif
                                              @if(Auth::user()->id == $comment->user_id)
                                                <li><a id="{{$comment->id}}" onclick="editComment(this);">Edit</a></li>
                                              @endif
                                            </ul>
                                          </div>
                                          @endif
                                            <a class="SubCommentName">{{ $user->find($comment->user_id)->name }}</a>
                                            <p class="more" id="editCommentHide_{{$comment->id}}">{!! $comment->body !!}</p>
                                            <form action="{{ url('updateComment')}}" method="POST" id="formUpdateComment{{$comment->id}}">
                                                  {{csrf_field()}}
                                                  {{ method_field('PUT') }}
                                              <div class="form-group hide" id="editCommentShow_{{$comment->id}}" >
                                                <textarea class="form-control" name="comment" rows="3">{!! $comment->body !!}</textarea>
                                                <input type="hidden" name="comment_id" value="{{$comment->id}}">
                                                <input type="hidden" name="post_id" value="{{$post->id}}">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                                <button type="button" class="btn btn-default" id="{{$comment->id}}" onclick="cancleComment(this);">Cancle</button>
                                              </div>
                                            </form>
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
                                           <span class="mrgn_5_left">
                                            <a class="" role="button" data-toggle="collapse" href="#replyToComment{{$post->id}}-{{$comment->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
                                          </span>
                                          <span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> {{$comment->updated_at->diffForHumans()}}</span>
                                          <div class="collapse replyComment" id="replyToComment{{$post->id}}-{{$comment->id}}">
                                            <form action="{{ url('createSubComment')}}" method="POST" id="formReplyToComment{{$post->id}}{{$comment->id}}">
                                               {{csrf_field()}}
                                              <div class="form-group">
                                                <label for="subcomment">Your Sub Comment</label>
                                                  <textarea name="subcomment" class="form-control" rows="3"></textarea>
                                              </div>
                                              <input type="hidden" name="comment_id" value="{{$comment->id}}">
                                              <input type="hidden" name="discussion_post_id" value="{{$post->id}}">
                                              <button type="button" class="btn btn-default" onclick="confirmSubmitReplytoComment(this);" data-id="formReplyToComment{{$post->id}}{{$comment->id}}">Send</button>
                                            </form>
                                          </div>
                                        </div>
                                      </div>
                                      @if(count( $comment->children ) > 0)
                                        @include('discussion.comments', ['comments' => $comment->children, 'parent' => $comment->id, 'user' => $user])
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
                <div class="tab-pane fade" id="askQuestion">
                  <div class="">
                    <div class="dropdown selectCategory col-md-6">
                      <select id="post_category" class="form-control" name="post_category" required>
                        <option value = ""> Select Category ...</option>
                        @if(count($discussionCategories) > 0)
                          @foreach($discussionCategories as $discussionCategory)
                            <option value = "{{$discussionCategory->id}}"> {{$discussionCategory->name}} </option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="widget-area ">
                      <div class="status-upload">
                        <form action="{{url('createPost')}}" method="POST" id="createPost">
                          {{csrf_field()}}
                           <div class="input-group">
                              <span class="input-group-addon">Title</span>
                              <input id="title" type="text" class="form-control" name="title" placeholder="Add Title Here">
                            </div>
                             <textarea name="question" placeholder="post here" type="text" id="question" required></textarea>
                            <script type="text/javascript">
                              CKEDITOR.replace( 'question', { enterMode: CKEDITOR.ENTER_BR } );
                              CKEDITOR.config.width="100%";
                              CKEDITOR.config.height="auto";
                              CKEDITOR.on('dialogDefinition', function (ev) {

                                  var dialogName = ev.data.name,
                                      dialogDefinition = ev.data.definition;

                                  if (dialogName == 'image') {
                                      var onOk = dialogDefinition.onOk;

                                      dialogDefinition.onOk = function (e) {
                                          var width = this.getContentElement('info', 'txtWidth');
                                          width.setValue('100%');//Set Default Width

                                          var height = this.getContentElement('info', 'txtHeight');
                                          height.setValue('auto');////Set Default height

                                          onOk && onOk.apply(this, e);
                                      };
                                  }
                              });
                            </script>
                            <ul>
                              <li><a title="" data-toggle="tooltip" data-placement="bottom" data-original-title="Audio"><i class="fa fa-music"></i></a></li>
                              <li><a title="" data-toggle="tooltip" data-placement="bottom" data-original-title="Video"><i class="fa fa-video-camera"></i></a></li>
                              <li><a title="" data-toggle="tooltip" data-placement="bottom" data-original-title="Sound Record"><i class="fa fa-microphone"></i></a></li>
                              <li><a title="" data-toggle="tooltip" data-placement="bottom" data-original-title="Picture"><i class="fa fa-picture-o"></i></a></li>
                            </ul>
                            <input type="hidden" name="post_category_id" value="" id="post_category_id">
                            <button type="button" class="btn btn-success btn-circle text-uppercase" onclick=" confirmSubmit(this);" id="createPost" title="Share"><i class="fa fa-share"></i> Share</button>
                        </form>
                      </div>
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
@stop
@section('footer')
  @include('footer.footer')
  <script type="text/javascript" src="{{ asset('js/togleForFilterBy.js') }}"></script>

<script type="text/javascript">
  function confirmSubmit(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var categoryId = parseInt(document.getElementById('post_category').value);
    var questionLength = CKEDITOR.instances.question.getData().length;

    if(0 < userId && 0 < categoryId && questionLength > 0){
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
    } else if( questionLength < 0){
      $.alert({
        title: 'Alert!',
        content: 'Please enter something in a question. ',
      });
    }
  }

  function editPost(ele){
    var id = $(ele).attr('id');
    document.getElementById('editPostHide_'+id).classList.add("hide");
    document.getElementById('editPostShow_'+id).classList.remove("hide");
  }

  function canclePost(ele){
    var id = $(ele).attr('id');
    document.getElementById('editPostHide_'+id).classList.remove("hide");
    document.getElementById('editPostShow_'+id).classList.add("hide");
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
                  var id = $(ele).attr('id');
                  formId = 'deleteSubComment_'+id;
                  document.getElementById(formId).submit();
                }
            },
            Cancle: function () {
            }
        }
      });
    }

  function confirmSubmitReplytoComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
        formId = $(ele).data('id');
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
  function confirmSubmitReplytoPost(ele){
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
                    formId = 'deleteComment_'+id;
                    document.getElementById(formId).submit();
                  }
          },
          Cancle: function () {
          }
      }
    });
  }

  function confirmPostDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this post, all comments and sub comments of this post will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deletePost_'+id;
                    document.getElementById(formId).submit();
                  }
              },
              Cancle: function () {
              }
          }
        });
  }

  function renderPosts(msg){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 > userId){
      userId = 0;
    }
    showPostsDiv = document.getElementById('showAllPosts');
    showPostsDiv.innerHTML = '';
      $.each(msg['posts'], function(idx, obj) {
        var divMedia = document.createElement('div');
        divMedia.className = 'media';
        divMedia.id = 'showPosts_'+obj.id;

        var divMediaHeading = document.createElement('div');
        divMediaHeading.className = 'media-heading';

        var titleDiv = document.createElement('div');
        titleDiv.className = 'user-block';
        titleDiv.innerHTML = '<span class="tital">'+ obj.title +'</span>'
        divMediaHeading.appendChild(titleDiv);


        var boxDiv = document.createElement('div');
        boxDiv.className = 'box-tools';
        boxDivInnerHtml = '<button type="button" data-toggle="collapse" data-target="#post'+ obj.id +'" aria-expanded="false" aria-controls="collapseExample" class="btn btn-box-tool clickable-btn" ><i class="fa fa-chevron-up"></i></button>';
        if(userId == obj.user_id){
          var url = "{{ url('deletePost')}}";
          var csrfField = '{{ csrf_field() }}';
          var methodField = '{{ method_field('DELETE') }}';
          boxDivInnerHtml += '<button type="button" class="btn btn-box-tool toggle-dropdown" data-toggle="dropdown"><i class="fa fa-cog"></i></button><ul role="menu" class="dropdown-menu dropdown-menu-right"><li><a id="'+obj.id+'" onclick="confirmPostDelete(this);">Delete</a></li><form id="deletePost_'+obj.id+'" action="'+ url +'" method="POST" style="display: none;">"'+csrfField+''+methodField+'"<input type="hidden" name="post_id" value="'+obj.id+'"></form><li><a id="'+obj.id+'" onclick="editPost(this);">Edit</a></li></ul>'
        }
        boxDiv.innerHTML = boxDivInnerHtml;
        divMediaHeading.appendChild(boxDiv);
        divMedia.appendChild(divMediaHeading);

        var divPanel = document.createElement('div');
        divPanel.className = 'cmt-parent panel-collapse collapse in';
        divPanel.id = 'post'+obj.id;

        var commentBlockDiv = document.createElement('div');
        commentBlockDiv.className = 'user-block';
        var userImage = "{{ asset('images/user1.png') }}";
        commentBlockDiv.innerHTML = '<img class="img-circle" src="'+userImage+'" alt="User Image" /><span class="username">'+ obj.user_name +'</span><span class="description">Shared publicly - '+ obj.updated_at+'</span>';
        divPanel.appendChild(commentBlockDiv);

        var divMediaBody = document.createElement('div');
        divMediaBody.className = 'media-body';
        divMediaBody.setAttribute('data-toggle', 'lightbox');
        var pBody = document.createElement('p');
        pBody.className = 'more bold';
        pBody.id ='editPostHide_'+ obj.id;
        pBody.innerHTML = obj.body;
        divMediaBody.appendChild(pBody);

        var spanEle = document.createElement('span');
        var formCsrfField = '{{ csrf_field() }}';
        var putMethodField = '{{ method_field('PUT') }}';
        var formUrl ="{{ url('updatePost')}}";

        spanInnerHTML = '<form action="'+formUrl+'" method="POST" id="formUpdatePost'+ obj.id +'">'+formCsrfField+''+putMethodField+'<div class="form-group hide" id="editPostShow_'+ obj.id +'" ><textarea name="update_question" placeholder="update here" type="text" id="updatequestion_'+ obj.id +'" required>"'+ obj.body +'"</textarea>';
          var formUpdateId = 'updatequestion_'+ obj.id;
          $( document ).ready(function() {
            CKEDITOR.replace( formUpdateId, { enterMode: CKEDITOR.ENTER_BR } );
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
                        height.setValue('500');
                        onOk && onOk.apply(this, e);
                    };
                }
            });
          });
        spanInnerHTML += '<input type="hidden" name="post_id" value="'+ obj.id +'"><button type="submit" class="btn btn-primary">Update</button><button type="button" class="btn btn-default" id="'+ obj.id +'" onclick="canclePost(this);">Cancle</button></div></form>';
        spanEle.innerHTML = spanInnerHTML;
        divMediaBody.appendChild(spanEle);

        var borderDiv = document.createElement('div');
        borderDiv.className = 'border-bottom';
        divMediaBody.appendChild(borderDiv);

        var divComment = document.createElement('div');
        divComment.className = 'comment-meta main-reply-box';
        commentInnerHtml = '<span id="like_'+obj.id+'">';

          if( msg['likesCount'][obj.id] && msg['likesCount'][obj.id]['user_id'][userId]){
            commentInnerHtml +='<i id="post_like_'+obj.id+'" data-post_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
            commentInnerHtml +='<span id="like1-bs3">'+ Object.keys(msg['likesCount'][obj.id]['like_id']).length +'</span>';
          } else {
            commentInnerHtml +='<i id="post_like_'+obj.id+'" data-post_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
            if(msg['likesCount'][obj.id]){
              commentInnerHtml +='<span id="like1-bs3">'+ Object.keys(msg['likesCount'][obj.id]['like_id']).length +'</span>';
            }
          }
        commentInnerHtml +='</span>';
        commentInnerHtml += '<span class="mrgn_5_left"><a class="" role="button" data-toggle="collapse" href="#replyToPost'+obj.id+'" aria-expanded="false" aria-controls="collapseExample">reply</a></span>';
        var commentCsrfField = '{{ csrf_field() }}';
        var commentFormUrl ="{{ url('createComment')}}";

        commentInnerHtml += '<div class="collapse replyComment" id="replyToPost'+obj.id+'"><form action="'+commentFormUrl+'" method="POST" id="formReplyToPost'+obj.id+'">'+commentCsrfField+'<div class="form-group"><label for="comment">Your Comment</label><textarea name="comment" class="form-control" ></textarea></div><input type="hidden" name="discussion_post_id" value="'+obj.id+'"><button type="button" class="btn btn-default" onclick="confirmSubmitReplytoPost(this);" id="formReplyToPost'+obj.id+'">Send</button></form></div>';
        divComment.innerHTML = commentInnerHtml;

        var divPostReply = document.createElement('div');
        divPostReply.className = 'collapse';
        divPostReply.id = 'replyToPost'+obj.id;
        var postReplyForm = ''
        var postReplyurl ="{{ url('createComment')}}";
        var csrfField = '{{csrf_field()}}';
        postReplyForm += '<form action='+ postReplyurl +' method="POST" id="formReplyToPost'+ obj.id +'">'+ csrfField +'<div class="form-group"><label for="comment">Your Comment</label><textarea name="comment" class="form-control" ></textarea></div><input type="hidden" name="discussion_post_id" value="'+ obj.id +'"><button type="button" class="btn btn-default" onclick="confirmSubmitReply(this);" id="formReplyToPost'+ obj.id +'">Send</button></form>';

        divPostReply.innerHTML = postReplyForm;
        divComment.appendChild(divPostReply);
        divMediaBody.appendChild(divComment);

        var commentBgDiv = document.createElement('div');
        commentBgDiv.className = 'cmt-bg';

        var commentchatDiv = document.createElement('div');
        commentchatDiv.className = 'box-body chat';
        commentchatDiv.id = 'chat-box';
        var postId = obj.id;
        var comments = obj.comments;
        var commentLikesCount = msg['commentLikesCount'];
        var subcommentLikesCount = msg['subcommentLikesCount'];
        var postUserId = obj.user_id;
        if(Object.keys(comments).length > 0){
          if(false == $.isEmptyObject(comments)){
            $.each(comments, function(idx, obj) {
              var mainCommentDiv = document.createElement('div');
              mainCommentDiv.className = 'item';
              mainCommentDiv.id = 'showComment_'+obj.id;

              var commentImage = document.createElement('img');
              var imageUrl = "{{ asset('images/user1.png') }}";
              commentImage.setAttribute('src',imageUrl);
              mainCommentDiv.appendChild(commentImage);

              var commentMessageDiv = document.createElement('div');
              commentMessageDiv.className = 'message';
              if( userId == obj.user_id || userId == postUserId ){
                var commentEditDeleteDiv = document.createElement('div');
                commentEditDeleteDiv.className = 'dropdown pull-right';
                editDeleteInnerHtml = '<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>';
                editDeleteInnerHtml += '<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';
                if( userId == obj.user_id || userId == postUserId ){
                  var deleteComment = "{{ url('deleteComment')}}";
                  var cmtCsrfField = '{{csrf_field()}}';
                  var cmtDeleteField = '{{ method_field('DELETE') }}';
                  editDeleteInnerHtml += '<li><a id="'+obj.id+'" onclick="confirmCommentDelete(this);">Delete</a></li><form id="deleteComment_'+obj.id+'" action="'+deleteComment+'" method="POST" style="display: none;">'+cmtCsrfField+''+cmtDeleteField;
                  editDeleteInnerHtml += '<input type="hidden" name="comment_id" value="'+obj.id+'"></form>';
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

              var spanUpdateComment = document.createElement('span');
              var updateCommentCsrfField = '{{ csrf_field() }}';
              var updateCommentMethodField = '{{ method_field("PUT") }}';
              var updateCommentUrl ="{{ url('updateComment')}}";

              spanUpdateComment.innerHTML = '<form action="'+updateCommentUrl+'" method="POST" id="formUpdateComment'+ obj.id +'">'+updateCommentCsrfField+''+updateCommentMethodField+'<div class="form-group hide" id="editCommentShow_'+ obj.id +'" ><textarea class="form-control" name="comment" rows="3">'+ obj.body+'</textarea><input type="hidden" name="comment_id" value="'+ obj.id +'"><input type="hidden" name="post_id" value="'+ postId +'"><button type="submit" class="btn btn-primary">Update</button><button type="button" class="btn btn-default" id="'+ obj.id +'" onclick="cancleComment(this);">Cancle</button></div></form>';
              commentMessageDiv.appendChild(spanUpdateComment);
              mainCommentDiv.appendChild(commentMessageDiv);

              var commentReplyDiv = document.createElement('div');
              commentReplyDiv.className = 'comment-meta reply-1';

              var spanCommenReply = document.createElement('span');
              spanCommenReply.id = 'cmt_like_'+obj.id;
              var spanCommenInnerHtml = '';
              if( commentLikesCount[obj.id] && commentLikesCount[obj.id]['user_id'][userId]){
                spanCommenInnerHtml +='<i id="comment_like_'+obj.id+'" data-post_id="'+obj.discussion_post_id+'" data-comment_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                spanCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(commentLikesCount[obj.id]['like_id']).length +'</span>';
              } else {
                spanCommenInnerHtml +='<i id="comment_like_'+obj.id+'" data-post_id="'+obj.discussion_post_id+'" data-comment_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                if(commentLikesCount[obj.id]){
                  spanCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(commentLikesCount[obj.id]['like_id']).length +'</span>';
                }
              }
              spanCommenReply.innerHTML = spanCommenInnerHtml;
              commentReplyDiv.appendChild(spanCommenReply);

              var spanCommenReplyButton = document.createElement('span');
              spanCommenReplyButton.className = 'mrgn_5_left';
              spanCommenReplyButton.innerHTML = '<a class="" role="button" data-toggle="collapse" href="#replyToComment'+postId+'-'+obj.id+'" aria-expanded="false" aria-controls="collapseExample">reply</a>';
              commentReplyDiv.appendChild(spanCommenReplyButton);

              var spanCommenReplyDate = document.createElement('span');
              spanCommenReplyDate.className = 'text-muted time-of-reply';
              spanCommenReplyDate.innerHTML = '<i class="fa fa-clock-o"></i>'+ obj.updated_at;
              commentReplyDiv.appendChild(spanCommenReplyDate);

              var subCommenDiv = document.createElement('div');
              subCommenDiv.className = 'collapse replyComment';
              subCommenDiv.id = 'replyToComment'+postId+'-'+obj.id;
              var urlCreateSubComment = "{{ url('createSubComment')}}";
              var csrfCreateSubComment = '{{csrf_field()}}';
              subCommenDiv.innerHTML = '<form action="'+urlCreateSubComment+'" method="POST" id="formReplyToComment'+postId+obj.id+'">'+csrfCreateSubComment+'<div class="form-group"><label for="subcomment">Your Sub Comment</label><textarea name="subcomment" class="form-control" rows="3"></textarea></div><input type="hidden" name="comment_id" value="'+ obj.id +'"><input type="hidden" name="discussion_post_id" value="'+postId+'"><button type="button" class="btn btn-default" onclick="confirmSubmitReplytoComment(this);" data-id="formReplyToComment'+postId+obj.id+'">Send</button></form>';
              commentReplyDiv.appendChild(subCommenDiv);
              mainCommentDiv.appendChild(commentReplyDiv);
              commentchatDiv.appendChild(mainCommentDiv);
              if( obj.subcomments ){
                if(false == $.isEmptyObject(obj.subcomments)){
                  var commentUserId = obj.user_id;
                  showSubComments(obj.subcomments, commentchatDiv, subcommentLikesCount, userId, commentUserId, postUserId);
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
        showMore();
      });
  }

  function showPosts( ele ){
    var id = parseInt($(ele).val());
    if( 0 < id ){
       $.ajax({
          method: "POST",
          url: "{{url('getDiscussionPostsByCategoryId')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        renderPosts(msg);
      });
    }
  }

  function showSubComments(subcomments, commentchatDiv, subcommentLikesCount, userId, commentUserId, postUserId){
    if(false == $.isEmptyObject(subcomments)){
      $.each(subcomments, function(idx, obj) {
        var mainSubCommentDiv = document.createElement('div');
        mainSubCommentDiv.className = 'item replySubComment-1';

        var subcommentImage = document.createElement('img');
        var subcommentImageUrl = "{{ asset('images/user1.png') }}";
        subcommentImage.setAttribute('src',subcommentImageUrl);
        mainSubCommentDiv.appendChild(subcommentImage);

        var subCommentMessageDiv = document.createElement('div');
        subCommentMessageDiv.className = 'message';
        if( userId == obj.user_id || userId == commentUserId || userId == postUserId ){
          var subcommentEditDeleteDiv = document.createElement('div');
          subcommentEditDeleteDiv.className = 'dropdown pull-right';
          editDeleteInnerHtml = '<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>';
          editDeleteInnerHtml += '<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">';
          if(  userId == obj.user_id || userId == commentUserId || userId == postUserId ){
            var deleteComment = "{{ url('deleteSubComment')}}";
            var cmtCsrfField = '{{csrf_field()}}';
            var cmtDeleteField = '{{ method_field('DELETE') }}';
            editDeleteInnerHtml += '<li><a id="'+obj.discussion_comment_id+'_'+obj.id+'" onclick="confirmSubCommentDelete(this);">Delete</a></li><form id="deleteSubComment_'+obj.discussion_comment_id+'_'+obj.id+'" action="'+deleteComment+'" method="POST" style="display: none;">'+cmtCsrfField+''+cmtDeleteField;
            editDeleteInnerHtml += '<input type="hidden" name="subcomment_id" value="'+obj.id+'"></form>';
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
        ancUserNameDiv.innerHTML = obj.user_name;
        pSubcommentBodyDiv.appendChild(ancUserNameDiv);

        var spanSubCommentBodyDiv = document.createElement('span');
        spanSubCommentBodyDiv.className = 'more';
        spanSubCommentBodyDiv.id = 'editSubCommentHide_'+obj.id;
        spanSubCommentBodyDiv.innerHTML = obj.body; //'{!! '+obj.body+' !!}';
        pSubcommentBodyDiv.appendChild(spanSubCommentBodyDiv);
        subCommentMessageDiv.appendChild(pSubcommentBodyDiv);

        var spanUpdateSubComment = document.createElement('span');
        var updateSubCommentCsrfField = '{{ csrf_field() }}';
        var updateSubCommentMethodField = '{{ method_field("PUT") }}';
        var updateSubCommentUrl ="{{ url('updateSubComment')}}";

        spanUpdateSubComment.innerHTML = '<form action="'+updateSubCommentUrl+'" method="POST" id="formUpdateSubComment'+ obj.id +'">'+updateSubCommentCsrfField+''+updateSubCommentMethodField+'<div class="form-group hide" id="editSubCommentShow_'+ obj.id +'" ><textarea class="form-control" name="comment" rows="3">'+ obj.body+'</textarea><input type="hidden" name="sub_comment_id" value="'+ obj.id +'"><input type="hidden" name="comment_id" value="'+ obj.discussion_comment_id +'"><input type="hidden" name="post_id" value="'+ obj.discussion_post_id +'"><button type="submit" class="btn btn-primary">Update</button><button type="button" class="btn btn-default" id="'+ obj.id +'" onclick="cancleSubComment(this);">Cancle</button></div></form>';
        subCommentMessageDiv.appendChild(spanUpdateSubComment);
        mainSubCommentDiv.appendChild(subCommentMessageDiv);

        var subcommentReplyDiv = document.createElement('div');
        subcommentReplyDiv.className = 'comment-meta reply-1';

        var spanCommenReply = document.createElement('span');
        spanCommenReply.id = 'sub_cmt_like_'+obj.id;
        var spanSubCommenInnerHtml = '';
        if( subcommentLikesCount[obj.id] && subcommentLikesCount[obj.id]['user_id'][userId]){
          spanSubCommenInnerHtml +='<i id="sub_comment_like_'+obj.id+'" data-post_id="'+obj.discussion_post_id+'" data-comment_id="'+obj.discussion_comment_id+'"  data-sub_comment_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
          spanSubCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(subcommentLikesCount[obj.id]['like_id']).length +'</span>';
        } else {
          spanSubCommenInnerHtml +='<i id="sub_comment_like_'+obj.id+'" data-post_id="'+obj.discussion_post_id+'" data-comment_id="'+obj.discussion_comment_id+'" data-sub_comment_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
          if(subcommentLikesCount[obj.id]){
            spanSubCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(subcommentLikesCount[obj.id]['like_id']).length +'</span>';
          }
        }
        spanCommenReply.innerHTML = spanSubCommenInnerHtml;
        subcommentReplyDiv.appendChild(spanCommenReply);

        var spanSubCommenReplyButton = document.createElement('span');
        spanSubCommenReplyButton.className = 'mrgn_5_left';
        spanSubCommenReplyButton.innerHTML = '<a class="" role="button" data-toggle="collapse" href="#replySubComment'+obj.discussion_comment_id+'-'+obj.id+'" aria-expanded="false" aria-controls="collapseExample">reply</a>';
        subcommentReplyDiv.appendChild(spanSubCommenReplyButton);

        var spanSubCommenReplyDate = document.createElement('span');
        spanSubCommenReplyDate.className = 'text-muted time-of-reply';
        spanSubCommenReplyDate.innerHTML = '<i class="fa fa-clock-o"></i>'+ obj.updated_at;
        subcommentReplyDiv.appendChild(spanSubCommenReplyDate);

        var createSubCommenDiv = document.createElement('div');
        createSubCommenDiv.className = 'collapse replyComment';
        createSubCommenDiv.id = 'replySubComment'+obj.discussion_comment_id+'-'+obj.id;
        var urlCreateSubComment = "{{ url('createSubComment')}}";
        var createSubCommentCsrf = '{{csrf_field()}}';
        createSubCommenDivInnerHTML = '<form action="'+urlCreateSubComment+'" method="POST" id="formReplyToSubComment'+obj.discussion_comment_id+obj.id+'">'+createSubCommentCsrf+'<div class="form-group"><label for="subcomment">Your Sub Comment</label>';
        if( userId != obj.user_id ){
          createSubCommenDivInnerHTML += '<textarea name="subcomment" class="form-control" rows="3">'+obj.user_name+'</textarea>';
        } else {
          createSubCommenDivInnerHTML += '<textarea name="subcomment" class="form-control" rows="3"></textarea>';
        }
        createSubCommenDivInnerHTML += '</div><input type="hidden" name="comment_id" value="'+ obj.id +'"><input type="hidden" name="comment_id" value="'+obj.discussion_comment_id+'"><input type="hidden" name="parent_id" value="'+obj.id+'"><input type="hidden" name="discussion_post_id" value="'+obj.discussion_post_id+'"><button type="button" class="btn btn-default" onclick="confirmSubmitReplytoComment(this);" data-id="formReplyToSubComment'+obj.discussion_comment_id+obj.id+'">Send</button></form>';
        subcommentReplyDiv.appendChild(createSubCommenDiv);
        mainSubCommentDiv.appendChild(subcommentReplyDiv);
        commentchatDiv.appendChild(mainSubCommentDiv);
      });
    }
  }

  function searchDuscussionPosts(){
    var searches = document.getElementsByClassName('search');
    var arr = [];
    var recent = 0;
    var mostpopular = 0;
    $.each(searches, function(ind, obj){
      if(true == $(obj).is(':checked')){
        var filter = $(obj).data('filter');
        var filterVal = $(obj).val();
        if(false == (arr.indexOf(filter) > -1)){
          if('recent' == filter) {
            recent = filterVal;
            arr.push(filterVal);
          }
          if('mostpopular' == filter) {
            mostpopular = filterVal;
            arr.push(filterVal);
          }
        }
      }
    });
    if(arr instanceof Array ){
      var arrJson = {'recent' : recent, 'mostpopular' : mostpopular};
      $.ajax({
        method: "POST",
        url: "{{url('getDuscussionPostsBySearchArray')}}",
        data: {arr:JSON.stringify(arrJson)}
      })
      .done(function( msg ) {
        renderPosts(msg);
      });
    }
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

  $( document ).ready(function() {
      showCommentEle = "{{ Session::get('show_comment_area')}}";
      showPostEle = "{{ Session::get('show_post_area')}}";
      if(showCommentEle > 0 &&  showPostEle == 0){
        window.location.hash = '#showComment_'+showCommentEle;
      } else if(showCommentEle == 0 &&  showPostEle > 0){
        window.location.hash = '#showPost_'+showPostEle;
      }
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
  // $(".panel-collapse").on("hide.bs.collapse", function (event) {
  //    $(".clickable-btn").find('i').addClass("fa-chevron-up").removeClass("fa-chevron-down");
  // });
  // $(".panel-collapse").on("show.bs.collapse", function () {
  //    $(".clickable-btn .collapsed").find('i').addClass("fa-chevron-down").removeClass("fa-chevron-up");
  // });

</script>
@stop