@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu – Technical Discussion Forum |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
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
          <img src="{{ asset('images/discussion.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed"/ alt="Vchip Discussion Forum">
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
        <div class="col-sm-3 hidden-div" id="sidemenuindex">
          <h4 class="v_h4_subtitle"> Sort By</h4>
          <div class="dropdown mrgn_20_top_btm" id="cat">
            <select id="category" class="form-control" name="category" title="Category" onchange="location = this.value;" required>
              <option value = "0"> Select Category</option>
              @if(count($discussionCategories) > 0)
                @foreach($discussionCategories as $discussionCategory)
                  @if($selectedCategoryId == $discussionCategory->id)
                    <option value ="{{ url($discussionCategory->id.'/discussion')}}/{{$discussionCategory->name}}" selected data-id="{{$discussionCategory->id}}">{{$discussionCategory->name}}</option>
                  @else
                    <option value ="{{ url($discussionCategory->id.'/discussion')}}/{{$discussionCategory->name}}" data-id="{{$discussionCategory->id}}">{{$discussionCategory->name}}</option>
                  @endif
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
        <div class="col-sm-9 col-sm-push-3">
          <div class="ask-qst">
              @if(is_object($currentUser))
                <a class="btn btn-primary" data-toggle="modal" data-target="#askQuestion">Ask Question</a>
              @else
                <a class="btn btn-primary" data-toggle="modal" data-target="#loginUserModel">Ask Question</a>
              @endif
              <input type="hidden" name="user_id" id="user_id" value="{{ (is_object($currentUser))?$currentUser->id:NULL}}">
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
                      @if(is_object($currentUser) && $currentUser->id == $post->user_id)
                      <button type="button" class="btn btn-box-tool toggle-dropdown" data-toggle="dropdown"><i class="fa fa-cog"></i></button>
                      <ul role="menu" class="dropdown-menu dropdown-menu-right">
                        <li><a id="{{$post->id}}" onclick="confirmPostDelete(this);">Delete</a></li>
                        <li><a id="{{$post->id}}" onclick="editPost(this);">Edit</a></li>
                      </ul>
                      @endif
                    </div>
                    </div>
                    <div class="cmt-parent panel-collapse collapse in" id="post{{$post->id}}">
                    <div class="user-block cmt-left-margin">
                      @if(is_file($post->getUser($post->user_id)->photo) || (!empty($post->getUser($post->user_id)->photo) && false == preg_match('/userStorage/',$post->getUser($post->user_id)->photo)))
                        <img src="{{ asset($post->getUser($post->user_id)->photo)}} " class="img-circle" alt="User Image">
                      @else
                        <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
                      @endif
                      <span class="username">{{ $post->getUser($post->user_id)->name }} </span>
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
                        <div class="form-group hide" id="editPostShow_{{$post->id}}" >
                          <textarea name="update_question" placeholder="Answer 1" type="text" id="updatequestion_{{$post->id}}" required>{!! $post->body !!}</textarea>
                            <script type="text/javascript">
                              CKEDITOR.replace('updatequestion_{{$post->id}}', { enterMode: CKEDITOR.ENTER_BR } );
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
                          @if($post->answer1 && $post->answer2 && $post->answer && $post->solution)
                            @if(!empty($post->answer1))
                            <div class="form-group row">
                              <label class="col-sm-3 col-form-label">Option 1:<span class="red-color">*</span></label>
                              <div class="col-sm-3">
                                <input type="text" name="answer1" id="updated_answer1_{{$post->id}}" value="{{$post->answer1}}" required>
                              </div>
                            </div>
                            @endif
                            @if(!empty($post->answer2))
                            <div class="form-group row">
                              <label class="col-sm-3 col-form-label">Option 2:<span class="red-color">*</span></label>
                              <div class="col-sm-3">
                                <input type="text" name="answer2" id="updated_answer2_{{$post->id}}" value="{{$post->answer2}}" required>
                              </div>
                            </div>
                            @endif
                            @if(!empty($post->answer3))
                            <div class="form-group row">
                              <label class="col-sm-3 col-form-label">Option 3:</label>
                              <div class="col-sm-3">
                                <input type="text" name="answer3" id="updated_answer3_{{$post->id}}" value="{{$post->answer3}}">
                              </div>
                            </div>
                            @endif
                            @if(!empty($post->answer4))
                            <div class="form-group row">
                              <label class="col-sm-3 col-form-label">Option 4:</label>
                              <div class="col-sm-3">
                                <input type="text" name="answer4" id="updated_answer4_{{$post->id}}" value="{{$post->answer4}}">
                              </div>
                            </div>
                            @endif
                            <div class="form-group row">
                              <label class="col-sm-3 col-form-label">Right Answer:<span class="red-color">*</span></label>
                              <div class="col-sm-3">
                                <input type="number" name="answer" id="updated_answer_{{$post->id}}" min="1" max="4" step="1" value="{{$post->answer}}" pattern="[1-4]{1}">
                              </div>
                            </div>
                            <div class="form-group row">
                              <label class="col-sm-3 col-form-label">Solution:<span class="red-color">*</span></label>
                              <div class="col-sm-9">
                                <textarea name="solution" id="updated_solution_{{$post->id}}" required cols="40" rows="5">{{$post->solution}}</textarea>
                                <script type="text/javascript">
                                  CKEDITOR.replace( 'updated_solution_{{$post->id}}', { enterMode: CKEDITOR.ENTER_BR } );
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
                              </div>
                            </div>
                          @endif
                          <button class="btn btn-primary" data-post_id="{{$post->id}}" style="width: 100px;" onclick="updatePost(this);">Update</button>
                          <button type="button" class="btn btn-default" id="{{$post->id}}" onclick="canclePost(this);">Cancle</button>
                        </div>
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
                                             <i id="comment_like_{{$comment->id}}" data-post_id="{{$comment->discussion_post_id}}" data-comment_id="{{$comment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                             <span id="like1-bs3">{{count($commentLikesCount[$comment->id]['like_id'])}}</span>
                                        @else
                                             <i id="comment_like_{{$comment->id}}" data-post_id="{{$comment->discussion_post_id}}" data-comment_id="{{$comment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
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
                                  @include('discussion.comments', ['comments' => $comment->children, 'parent' => $comment->id, 'currentUser' => $currentUser])
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
          <div class="modal fade" id="askQuestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <select id="post_category" class="form-control" name="post_category" required>
                      <option value = ""> Select Category</option>
                      @if(count($discussionCategories) > 0)
                        @foreach($discussionCategories as $discussionCategory)
                          <option value = "{{$discussionCategory->id}}"> {{$discussionCategory->name}} </option>
                        @endforeach
                      @endif
                  </select>
                  <input type="radio" name="type" checked="true" value="discussion" onClick="toggleType('discussion');">Discussion
                  <input type="radio" name="type" value="mcq" onClick="toggleType('mcq');">MCQ
                </div>
                <div class="modal-body" style="padding: 0px;">
                  <div class="widget-area no-padding blank">
                    <div class="status-upload">
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
                      <div id="mcs_options" class="hide">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Option 1:<span class="red-color">*</span></label>
                          <div class="col-sm-3">
                            <input type="text" name="answer1" id="answer1" value="" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Option 2:<span class="red-color">*</span></label>
                          <div class="col-sm-3">
                            <input type="text" name="answer2" id="answer2" value="" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Option 3:</label>
                          <div class="col-sm-3">
                            <input type="text" name="answer3" id="answer3" value="">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Option 4:</label>
                          <div class="col-sm-3">
                            <input type="text" name="answer4" id="answer4" value="">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Right Answer:<span class="red-color">*</span></label>
                          <div class="col-sm-3">
                            <input type="number" name="answer" id="answer" min="1" max="4" step="1" value="1" pattern="[1-4]{1}">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Solution:<span class="red-color">*</span></label>
                          <div class="col-sm-9">
                            <textarea name="solution" id="solution" required cols="40" rows="5"></textarea>
                            <script type="text/javascript">
                              CKEDITOR.replace( 'solution', { enterMode: CKEDITOR.ENTER_BR } );
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
                          </div>
                        </div>
                      </div>
                      <button type="button" class="btn btn-success btn-circle text-uppercase" onclick="confirmSubmit(this);" id="createPost" title="Share"><i class="fa fa-share"></i> Share</button>
                    </div><!-- Status Upload  -->
                  </div>
                </div>
                <div class="modal-footer ">
                  <button type="button" class="btn btn-default " data-dismiss="modal" style="margin-top: 10px;">close</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-3 col-sm-pull-9">
          <div class="hidden-div1" id="sidemenuindex">
            <h4 class="v_h4_subtitle"> Sort By</h4>
            <div class="dropdown mrgn_20_top_btm" id="cat">
              <select id="category" class="form-control" name="category" title="Category" onchange="location = this.value;" required>
                <option value = "0"> Select Category</option>
                @if(count($discussionCategories) > 0)
                  @foreach($discussionCategories as $discussionCategory)
                    @if($selectedCategoryId == $discussionCategory->id)
                      <option value = "{{ url($discussionCategory->id.'/discussion')}}/{{$discussionCategory->name}}" selected data-id="{{$discussionCategory->id}}">{{$discussionCategory->name}}</option>
                    @else
                      <option value ="{{ url($discussionCategory->id.'/discussion')}}/{{$discussionCategory->name}}" data-id="{{$discussionCategory->id}}">{{$discussionCategory->name}}</option>
                    @endif
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
          <div class="advertisement-area">
            <span class="pull-right create-add"><a href="{{ url('createAd') }}"> Create Ad</a></span>
          </div>
            <br/>
            @if(count($ads) > 0)
              @foreach($ads as $ad)
                <div class="add-1">
                  <div class="course-box">
                    <a class="img-course-box" href="{{ $ad->website_url }}" target="_blank">
                      <img src="{{asset($ad->logo)}}" alt="{{ $ad->company }}"  class="img-responsive" />
                    </a>
                    <div class="course-box-content">
                      <h4 class="course-box-title" title="{{ $ad->company }}" data-toggle="tooltip" data-placement="bottom">
                        <a href="{{ $ad->website_url }}" target="_blank">{{ $ad->company }}</a>
                      </h4>
                      <p class="more"> {{ $ad->tag_line }}</p>
                    </div>
                  </div>
                </div>
              @endforeach
            @endif
            @if(count($ads) < 3)
              @for($i = count($ads)+1; $i <=3; $i++)
                @if(1 == $i)
                  <div class="add-1">
                    <div class="course-box">
                      <a class="img-course-box" href="http://www.ssgmce.org" target="_blank">
                        <img src="{{ asset('images/logo/ssgmce-logo.jpg') }}" alt="Shri Sant Gajanan Maharaj College of Engineering"  class="img-responsive" />
                      </a>
                      <div class="course-box-content">
                        <h4 class="course-box-title" title="Shri Sant Gajanan Maharaj College of Engineering" data-toggle="tooltip" data-placement="bottom">
                          <a href="http://www.ssgmce.org/" target="_blank">Shri Sant Gajanan Maharaj College of Engineering</a>
                        </h4>
                        <p class="more"> Shri Sant Gajanan Maharaj College of Engineering</p>
                      </div>
                    </div>
                  </div>
                @elseif(2 == $i)
                  <div class="add-1">
                    <div class="course-box">
                      <a class="img-course-box" href="http://ghrcema.raisoni.net/" target="_blank">
                        <img src="{{ asset('images/logo/ghrcema_logo.png') }}" alt="G H RISONI"  class="img-responsive" />
                      </a>
                      <div class="course-box-content">
                        <h4 class="course-box-title" title="G H RISONI" data-toggle="tooltip" data-placement="bottom">
                          <a href="http://ghrcema.raisoni.net/" target="_blank">G H RISONI</a>
                        </h4>
                        <p class="more"> G H RISONI</p>
                      </div>
                    </div>
                  </div>
                @elseif(3 == $i)
                  <div class="add-1">
                    <div class="course-box">
                      <a class="img-course-box" href="http://hvpmcoet.in/" target="_blank">
                        <img src="{{ asset('images/logo/hvpm.jpg') }}" alt="HVPM"  class="img-responsive" />
                      </a>
                      <div class="course-box-content">
                        <h4 class="course-box-title" title="HVPM" data-toggle="tooltip" data-placement="bottom">
                          <a href="http://hvpmcoet.in/" target="_blank">HVPM College of Engineer And Technology</a>
                        </h4>
                        <p class="more"> HVPM College of Engineer And Technology</p>
                      </div>
                    </div>
                  </div>
                @endif
              @endfor
            @endif
        </div>
      </div>
    </div>
  </section>
@stop
@section('footer')
  @include('footer.footer')
  <script type="text/javascript" src="{{ asset('js/togleForFilterBy.js') }}"></script>

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
      $('#right_answer_image_'+postId+'_'+answer).removeClass('hide');
      $('#wrong_answer_image_'+postId+'_'+answer).addClass('hide');
    } else {
      $(ele).prop('style', 'color:grey;');
      $('#right_answer_image_'+postId+'_'+answer).addClass('hide');
      $('#wrong_answer_image_'+postId+'_'+answer).removeClass('hide');
    }
  }

  function confirmSubmit(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var categoryId = parseInt(document.getElementById('post_category').value);
    var questionLength = CKEDITOR.instances.question.getData().length;
    var title = document.getElementById('title').value;
    var type = $('input[name="type"]:checked').val();
    var answer1 = document.getElementById('answer1').value;
    var answer2 = document.getElementById('answer2').value;
    var answer3 = document.getElementById('answer3').value;
    var answer4 = document.getElementById('answer4').value;
    var answer = document.getElementById('answer').value;
    var solutionLength = CKEDITOR.instances.solution.getData().length;

    if(isNaN(userId)) {
      $('#loginUserModel').modal();
      return false;
    }else if( isNaN(categoryId)) {
      $.alert({
        title: 'Alert!',
        content: 'Please select post category.',
      });
      return false;
    }else if(!title) {
      $.alert({
        title: 'Alert!',
        content: 'Please enter title.',
      });
      return false;
    } else if( 0 == questionLength){
      $.alert({
        title: 'Alert!',
        content: 'Please enter something in a question. ',
      });
      return false;
    } else if('mcq' == type){
      if(!answer1){
        $.alert({
          title: 'Alert!',
          content: 'Please Enter Option 1.',
        });
        return false;
      } else if(!answer2){
        $.alert({
          title: 'Alert!',
          content: 'Please Enter Option 2.',
        });
        return false;
      } else if(!answer){
        $.alert({
          title: 'Alert!',
          content: 'Please Enter Right Answer.',
        });
        return false;
      } else if(0 == answer){
        $.alert({
          title: 'Alert!',
          content: 'Please enter right answer in between no of entered options.',
        });
        return false;
      } else if(0 == solutionLength){
        $.alert({
          title: 'Alert!',
          content: 'Please Enter Solution.',
        });
        return false;
      }
      var optionCount = 0;
      if(answer1){
        optionCount += 1;
      }
      if(answer2){
        optionCount += 1;
      }
      if(answer3){
        optionCount += 1;
      }
      if(answer4){
        optionCount += 1;
      }
      if(answer > optionCount || answer < 1){
        $.alert({
          title: 'Alert!',
          content: 'Please enter right answer in between no of entered options.',
        });
        return false;
      }
      if(userId > 0 && categoryId > 0 && questionLength > 0 && title && answer1 && answer2 && solutionLength > 0){
        var question = CKEDITOR.instances.question.getData();
        var solution = CKEDITOR.instances.solution.getData();
        $.ajax({
            method: "POST",
            url: "{{url('createPost')}}",
            data: {title:title,post_category_id:categoryId,question:question,answer1:answer1,answer2:answer2,answer3:answer3,answer4:answer4,answer:answer,solution:solution}
        })
        .done(function( msg ) {
          $('#askQuestion').modal('hide');
          document.getElementById('post_category').value = 0;
          document.getElementById('title').value = '';
          CKEDITOR.instances.question.setData('');
          CKEDITOR.instances.solution.setData('');
          renderPosts(msg);
        });
      }
    } else {
      if(userId > 0 && categoryId > 0 && questionLength > 0 && title){
        var question = CKEDITOR.instances.question.getData();
        var solution = CKEDITOR.instances.solution.getData();
        $.ajax({
            method: "POST",
            url: "{{url('createPost')}}",
            data: {title:title,post_category_id:categoryId,question:question,answer1:answer1,answer2:answer2,answer3:answer3,answer4:answer4,answer:answer,solution:solution}
        })
        .done(function( msg ) {
          $('#askQuestion').modal('hide');
          document.getElementById('post_category').value = 0;
          document.getElementById('title').value = '';
          CKEDITOR.instances.question.setData('');
          CKEDITOR.instances.solution.setData('');
          renderPosts(msg);
        });
      }
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
                var subcommentId = $(ele).data('subcomment_id');
                var categoryId = $('#category option:selected').data('id');
                $.ajax({
                    method: "POST",
                    url: "{{url('deleteSubComment')}}",
                    data: {subcomment_id:subcommentId,category_id:categoryId}
                })
                .done(function( msg ) {
                  renderPosts(msg);
                });
              }
          },
          Cancel: function () {
          }
      }
    });
  }

  function confirmSubmitReplytoComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
        var categoryId = $('#category option:selected').data('id');
        var postId = $(ele).data('post_id');
        var commentId = $(ele).data('comment_id');
        var subcomment = document.getElementById('subcomment_'+postId+'_'+commentId).value;
        $.ajax({
            method: "POST",
            url: "{{url('createSubComment')}}",
            data: {discussion_post_id:postId,comment_id:commentId,subcomment:subcomment,category_id:categoryId}
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
        var categoryId = $('#category option:selected').data('id');
        var postId = $(ele).data('post_id');
        var commentId = $(ele).data('comment_id');
        var parentId = $(ele).data('parent_id');
        var subcomment = document.getElementById('create_subcomment_'+parentId).value;
        $.ajax({
            method: "POST",
            url: "{{url('createSubComment')}}",
            data: {discussion_post_id:postId,comment_id:commentId,parent_id:parentId,subcomment:subcomment,category_id:categoryId}
        })
        .done(function( msg ) {
          renderPosts(msg);
        });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }
  function confirmSubmitReplytoPost(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
      var categoryId = $('#category option:selected').data('id');
      var postId = $(ele).data('post_id');
      var comment = document.getElementById('comment_'+postId).value
      $.ajax({
          method: "POST",
          url: "{{url('createComment')}}",
          data: {discussion_post_id:postId, comment:comment,category_id:categoryId}
      })
      .done(function( msg ) {
        renderPosts(msg);
      });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }

  function updateComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
      var categoryId = $('#category option:selected').data('id');
      var postId = $(ele).data('post_id');
      var commentId = $(ele).data('comment_id');
      var comment = document.getElementById('comment_'+postId+'_'+commentId).value;
      $.ajax({
          method: "POST",
          url: "{{url('updateComment')}}",
          data: {post_id:postId,comment_id:commentId,comment:comment,category_id:categoryId}
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
      var categoryId = $('#category option:selected').data('id');
      var postId = $(ele).data('post_id');
      var commentId = $(ele).data('comment_id');
      var subcommentId = $(ele).data('subcomment_id');
      var comment = document.getElementById('update_subcomment_'+commentId+'_'+subcommentId).value;
      $.ajax({
          method: "POST",
          url: "{{url('updateSubComment')}}",
          data: {post_id:postId,comment_id:commentId,subcomment_id:subcommentId,comment:comment,category_id:categoryId}
      })
      .done(function( msg ) {
        renderPosts(msg);
      });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
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
                    var categoryId = $('#category option:selected').data('id');
                    $.ajax({
                        method: "POST",
                        url: "{{url('deleteComment')}}",
                        data: {comment_id:id,category_id:categoryId}
                    })
                    .done(function( msg ) {
                      renderPosts(msg);
                    });
                  }
          },
          Cancel: function () {
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
                    var categoryId = $('#category option:selected').data('id');
                    if( 0 < id ){
                       $.ajax({
                          method: "POST",
                          url: "{{url('deletePost')}}",
                          data: {post_id:id,category_id:categoryId}
                      })
                      .done(function( msg ) {
                        renderPosts(msg);
                      });
                    }
                  }
              },
              Cancel: function () {
              }
          }
        });
  }

  function updatePost(ele){
    var postId = $(ele).data('post_id');
    var updateQuestion = CKEDITOR.instances['updatequestion_'+postId].getData();
    if(document.getElementById('updated_solution_'+postId)){
      var updateSolution = CKEDITOR.instances['updated_solution_'+postId].getData();
    } else {
      var updateSolution = '';
    }
    if(document.getElementById('updated_answer1_'+postId)){
      var updatedAnswer1 = document.getElementById('updated_answer1_'+postId).value;
    } else {
      var updatedAnswer1 = '';
    }
    if(document.getElementById('updated_answer2_'+postId)){
      var updatedAnswer2 = document.getElementById('updated_answer2_'+postId).value;
    } else {
      var updatedAnswer2 = '';
    }
    if(document.getElementById('updated_answer3_'+postId)){
      var updatedAnswer3 = document.getElementById('updated_answer3_'+postId).value;
    } else {
      var updatedAnswer3 = '';
    }
    if(document.getElementById('updated_answer4_'+postId)){
      var updatedAnswer4 = document.getElementById('updated_answer4_'+postId).value;
    } else {
      var updatedAnswer4 = '';
    }
    if(document.getElementById('updated_answer_'+postId)){
      var updatedAnswer = document.getElementById('updated_answer_'+postId).value;
    } else {
      var updatedAnswer = '';
    }
    var isUpdatedFromDiscussion = 'true';
    var categoryId = $('#category option:selected').data('id');
    $.ajax({
        method: "POST",
        url: "{{url('updatePost')}}",
        data: {post_id:postId,update_question:updateQuestion,updated_solution:updateSolution,updated_answer1:updatedAnswer1,updated_answer2:updatedAnswer2,updated_answer3:updatedAnswer3,updated_answer4:updatedAnswer4,updated_answer:updatedAnswer,isUpdatedFromDiscussion:isUpdatedFromDiscussion,category_id:categoryId}
    })
    .done(function( msg ) {
      renderPosts(msg);
    });
  }

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
    var sortedPostArray = arrayPosts.reverse();
      $.each(sortedPostArray, function(idx, obj) {
        if(false == $.isEmptyObject(obj)){
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
          boxDivInnerHtml += '<button type="button" class="btn btn-box-tool toggle-dropdown" data-toggle="dropdown"><i class="fa fa-cog"></i></button><ul role="menu" class="dropdown-menu dropdown-menu-right"><li><a id="'+obj.id+'" onclick="confirmPostDelete(this);">Delete</a></li><li><a id="'+obj.id+'" onclick="editPost(this);">Edit</a></li></ul>'
        }
        boxDiv.innerHTML = boxDivInnerHtml;
        divMediaHeading.appendChild(boxDiv);
        divMedia.appendChild(divMediaHeading);

        var divPanel = document.createElement('div');
        divPanel.className = 'cmt-parent panel-collapse collapse in';
        divPanel.id = 'post'+obj.id;

        var commentBlockDiv = document.createElement('div');
        commentBlockDiv.className = 'user-block cmt-left-margin';

        if('system' == obj.image_exist){
          var userImagePath = "{{ asset('') }}"+obj.user_image;
          var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
        } else if('other' == obj.image_exist){
          var userImagePath = obj.user_image;
          var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
        } else {
          var userImagePath = "{{ asset('images/user1.png') }}";
          var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
        }

        commentBlockDiv.innerHTML = ''+userImage+'<span class="username">'+ obj.user_name +'</span><span class="description">Shared publicly - '+ obj.updated_at+'</span>';
        divPanel.appendChild(commentBlockDiv);

        var divMediaBody = document.createElement('div');
        divMediaBody.className = 'media-body';
        divMediaBody.setAttribute('data-toggle', 'lightbox');
        divMediaBody.innerHTML = '<br/>';
        var pBody = document.createElement('div');
        pBody.className = 'more img-ckeditor img-responsive cmt-left-margin';
        pBody.id ='editPostHide_'+ obj.id;
        pBody.innerHTML = obj.body + ' <br/>';
        divMediaBody.appendChild(pBody);

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

        var divForm = document.createElement('div');
        divForm.className = 'form-group hide';
        divForm.id = 'editPostShow_'+obj.id;

        divFormInnerHTML = '<textarea name="update_question" placeholder="update here" type="text" id="updatequestion_'+ obj.id +'" required>'+ obj.body +'</textarea>';
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
        if(obj.answer1 && obj.answer2 && obj.answer && obj.solution){
          if(obj.answer1){
            divFormInnerHTML += '<div class="form-group row"><label class="col-sm-3 col-form-label">Option 1:<span class="red-color">*</span></label><div class="col-sm-3"><input type="text" name="answer1" id="updated_answer1_'+obj.id+'" value="'+obj.answer1+'" required></div></div>';
          }
          if(obj.answer2){
            divFormInnerHTML += '<div class="form-group row"><label class="col-sm-3 col-form-label">Option 2:<span class="red-color">*</span></label><div class="col-sm-3"><input type="text" name="answer1" id="updated_answer2_'+obj.id+'" value="'+obj.answer2+'" required></div></div>';
          }
          if(obj.answer3){
            divFormInnerHTML += '<div class="form-group row"><label class="col-sm-3 col-form-label">Option 3:</label><div class="col-sm-3"><input type="text" name="answer1" id="updated_answer3_'+obj.id+'" value="'+obj.answer3+'" required></div></div>';
          }
          if(obj.answer4){
            divFormInnerHTML += '<div class="form-group row"><label class="col-sm-3 col-form-label">Option 4:</label><div class="col-sm-3"><input type="text" name="answer1" id="updated_answer4_'+obj.id+'" value="'+obj.answer4+'" required></div></div>';
          }
          divFormInnerHTML += '<div class="form-group row"><label class="col-sm-3 col-form-label">Right Answer:<span class="red-color">*</span></label><div class="col-sm-3"><input type="text" name="answer1" id="updated_answer_'+obj.id+'" value="'+obj.answer+'" required></div></div>';
          divFormInnerHTML += '<div class="form-group row"><label class="col-sm-3 col-form-label">Solution:<span class="red-color">*</span></label><div class="col-sm-9"><textarea name="solution" id="updated_solution_'+obj.id+'" required cols="40" rows="5">'+obj.solution+'</textarea>';
          var formSolutionId = 'updated_solution_'+ obj.id;
          $( document ).ready(function() {
            CKEDITOR.replace( formSolutionId, { enterMode: CKEDITOR.ENTER_BR } );
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
          divFormInnerHTML += '</div></div>';
        }
        divFormInnerHTML += '<button class="btn btn-primary" data-post_id="'+ obj.id +'" style="width: 100px;" onclick="updatePost(this);">Update</button><button class="btn btn-default" id="'+ obj.id +'" onclick="canclePost(this);">Cancle</button></div></form>';
        divForm.innerHTML = divFormInnerHTML;
        divMediaBody.appendChild(divForm);

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
        var postUserId = obj.user_id;
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
              if( userId == obj.user_id || userId == postUserId ){
                var commentEditDeleteDiv = document.createElement('div');
                commentEditDeleteDiv.className = 'dropdown pull-right';
                editDeleteInnerHtml = '<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>';
                editDeleteInnerHtml += '<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';
                if( userId == obj.user_id || userId == postUserId ){
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
                spanCommenInnerHtml +='<i id="comment_like_'+obj.id+'" data-post_id="'+obj.discussion_post_id+'" data-comment_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                spanCommenInnerHtml +='<span id="like1-bs3">';
                if(Object.keys(commentLikesCount[obj.id]['like_id']).length > 0){
                  spanCommenInnerHtml += Object.keys(commentLikesCount[obj.id]['like_id']).length;
                }
                spanCommenInnerHtml +='</span>';
              } else {
                spanCommenInnerHtml +='<i id="comment_like_'+obj.id+'" data-post_id="'+obj.discussion_post_id+'" data-comment_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
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
              subCommenDiv.innerHTML = '<div class="form-group"><label for="subcomment">Your Sub Comment</label><textarea name="subcomment" id="subcomment_'+postId+'_'+obj.id+'" class="form-control" rows="3"></textarea></div><input type="hidden" name="comment_id" value="'+ obj.id +'"><input type="hidden" name="discussion_post_id" value="'+postId+'"><button class="btn btn-default" data-post_id="'+postId+'" data-comment_id="'+obj.id+'"  onclick="confirmSubmitReplytoComment(this);">Send</button><button type="button" class="btn btn-default" data-id="replyToComment'+postId+'-'+obj.id+'" onclick="cancleReply(this);">Cancle</button>';
              commentReplyDiv.appendChild(subCommenDiv);
              mainCommentDiv.appendChild(commentReplyDiv);
              commentchatDiv.appendChild(mainCommentDiv);
              if( obj.subcomments ){
                if(false == $.isEmptyObject(obj.subcomments)){
                  var commentUserId = obj.user_id;
                  showSubComments(obj.subcomments, commentchatDiv, subcommentLikesCount, userId, commentUserId, postUserId);
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
        if( userId == obj.user_id || userId == commentUserId || userId == postUserId ){
          var subcommentEditDeleteDiv = document.createElement('div');
          subcommentEditDeleteDiv.className = 'dropdown pull-right';
          editDeleteInnerHtml = '<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>';
          editDeleteInnerHtml += '<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">';
          if(  userId == obj.user_id || userId == commentUserId || userId == postUserId ){
            editDeleteInnerHtml += '<li><a id="'+obj.discussion_comment_id+'_'+obj.id+'" data-subcomment_id="'+ obj.id +'" onclick="confirmSubCommentDelete(this);">Delete</a></li>';
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

        spanUpdateSubComment.innerHTML = '<textarea class="form-control" name="comment" id="update_subcomment_'+ obj.discussion_comment_id +'_'+ obj.id +'" rows="3">'+ obj.body+'</textarea><button class="btn btn-primary" data-post_id="'+ obj.discussion_post_id+'" data-comment_id="'+ obj.discussion_comment_id +'" data-subcomment_id="'+ obj.id +'" style="width: 100px;" onclick="updateSubComment(this);">Update</button><button type="button" class="btn btn-default" id="'+ obj.id +'" onclick="cancleSubComment(this);">Cancle</button>';
        subCommentMessageDiv.appendChild(spanUpdateSubComment);
        mainSubCommentDiv.appendChild(subCommentMessageDiv);

        var subcommentReplyDiv = document.createElement('div');
        subcommentReplyDiv.className = 'comment-meta reply-1';

        var spanCommenReply = document.createElement('span');
        spanCommenReply.id = 'sub_cmt_like_'+obj.id;
        var spanSubCommenInnerHtml = '';
        if( subcommentLikesCount[obj.id] && subcommentLikesCount[obj.id]['user_id'][userId]){
          spanSubCommenInnerHtml +='<i id="sub_comment_like_'+obj.id+'" data-post_id="'+obj.discussion_post_id+'" data-comment_id="'+obj.discussion_comment_id+'"  data-sub_comment_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
          spanSubCommenInnerHtml +='<span id="like1-bs3">';
          if(Object.keys(subcommentLikesCount[obj.id]['like_id']).length > 0){
            spanSubCommenInnerHtml += Object.keys(subcommentLikesCount[obj.id]['like_id']).length;
          }
          spanSubCommenInnerHtml +='</span>';

        } else {
          spanSubCommenInnerHtml +='<i id="sub_comment_like_'+obj.id+'" data-post_id="'+obj.discussion_post_id+'" data-comment_id="'+obj.discussion_comment_id+'" data-sub_comment_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
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
          spanSubCommenReplyButton.innerHTML = '<a class="" role="button" data-toggle="collapse" href="#replySubComment'+obj.discussion_comment_id+'-'+obj.id+'" aria-expanded="false" aria-controls="collapseExample">reply</a>';
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
        createSubCommenDiv.id = 'replySubComment'+obj.discussion_comment_id+'-'+obj.id;
        createSubCommenDivInnerHTML = '<div class="form-group"><label for="subcomment">Your Sub Comment</label>';
        if( userId != obj.user_id ){
          createSubCommenDivInnerHTML += '<textarea name="subcomment" class="form-control" id="create_subcomment_'+ obj.id +'"  rows="3">'+obj.user_name+'</textarea>';
        } else {
          createSubCommenDivInnerHTML += '<textarea name="subcomment" id="create_subcomment_'+ obj.id +'" class="form-control" rows="3"></textarea>';
        }
        createSubCommenDivInnerHTML += '</div><button class="btn btn-default"  data-post_id="'+ obj.discussion_post_id+'" data-comment_id="'+ obj.discussion_comment_id+'" data-parent_id="'+ obj.id+'" onclick="confirmSubmitReplytoSubComment(this);">Send</button><button type="button" class="btn btn-default" data-id="replySubComment'+obj.discussion_comment_id+'-'+obj.id+'" onclick="cancleReply(this);">Cancle</button>';
        createSubCommenDiv.innerHTML = createSubCommenDivInnerHTML;
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

  function cancleReply(ele){
    var id = $(ele).data('id');
    document.getElementById(id).classList.remove("in");
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
      showSubCommentEle = "{{ Session::get('show_subcomment_area')}}";
      showCommentEle = "{{ Session::get('show_comment_area')}}";
      showPostEle = "{{ Session::get('show_post_area')}}";
      if(showCommentEle > 0 && showPostEle == 0 &&  showSubCommentEle == 0){
        window.location.hash = '#showComment_'+showCommentEle;
      } else if(showCommentEle == 0 && showPostEle > 0 &&  showSubCommentEle == 0){
        window.location.hash = '#showPost_'+showPostEle;
      } else if(showCommentEle == 0 && showPostEle == 0 &&  showSubCommentEle > 0){
        window.location.hash = '#subcomment_'+showSubCommentEle;
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

  $(document).ready(function(){
    $.fn.modal.Constructor.prototype.enforceFocus = function () {
        modal_this = this
        $(document).on('focusin.modal', function (e) {
            if(modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length && $(e.target.parentNode).hasClass('cke_dialog_ui_labeled_content')) {
              modal_this.$element.focus()
            }
        })
    };
  });

</script>
@stop