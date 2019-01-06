@extends('dashboard.dashboard')
@section('mytest_header')
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Questions </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-comments"></i> Discussion</li>
      <li class="active">My Questions</li>
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
              <div class="col-sm-2">
                <button class="btn btn-primary "  data-toggle="modal" data-target="#askQuestion" style="width: 100px;"> Ask Question</button>
              </div>
              <a href="{{ url('college/'.Session::get('college_user_url').'/discussion')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="Discussion"><i class="fa fa-comments"></i></a>&nbsp;
              <a href="{{ url('college/'.Session::get('college_user_url').'/myQuestions')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="My Questions"><i class="fa fa-question-circle"></i></a>&nbsp;
              <a href="{{ url('college/'.Session::get('college_user_url').'/myReplies')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="My Replies"><i class="fa fa-reply"></i></a>&nbsp;
             </div>
                <div class="post-comments ">
                  <div class="row" id="showAllPosts">
                    @if(count($posts) > 0)
                      @foreach($posts as $post)
                       <div class="media" id="showPost_{{$post->id}}">
                          <div class="media-heading" >
                            <div class="user-block ">
                              <a id="{{$post->id}}" class="tital" onClick="goToPost(this);" style="cursor: pointer;">{{$post->title}} </a>
                                <form id="goToPost_{{$post->id}}" action="{{ url('goToPost')}}" method="POST" style="display: none;" target="_blank">
                                  {{ csrf_field() }}
                                  <input type="hidden" name="post_id" value="{{$post->id}}">
                                </form>
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
                            @if(!empty($post->user->photo))
                              <img class="img-circle" src="{{ asset($post->user->photo) }}" alt="User Image" />
                            @else
                              <img class="img-circle" src="{{ asset('images/user1.png') }}" alt="User Image" />
                            @endif
                            <span class="username">{{ $user->find($post->user_id)->name }} </span>
                            <span class="description">Shared publicly - {{$post->updated_at->diffForHumans()}}</span>
                          </div>
                          <div  class="media-body" data-toggle="lightbox">
                            <br/>
                            <div class="more cmt-left-margin" id="editPostHide_{{$post->id}}">{!! $post->body !!}</div>
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
                                  @if( isset($likesCount[$post->id]) && isset($likesCount[$post->id]['user_id'][$currentUser->id]))
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
                          </div>
                          </div>
                        </div>
                      @endforeach
                    @else
                      No My Questions
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
                        <div class="widget-area  blank">
                              <div class="status-upload">
                                   <div class="input-group">
                                      <span class="input-group-addon">Title</span>
                                      <input id="title" type="text" class="form-control" name="title" placeholder="Add Title Here">
                                    </div>
                                     <textarea name="question" placeholder="Answer 1" type="text" id="question" required></textarea>
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
                                                  height.setValue('400');////Set Default height

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
                                    <button type="button" class="btn btn-success btn-circle text-uppercase" onclick="confirmSubmit(this);" id="createPost"><i class="fa fa-share"></i> Share</button>
                              </div><!-- Status Upload  -->
                            </div>
                      </div>
                      <div class="modal-footer ">
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal" style="margin-top: 10px;">close</button>
                      </div>
                    </div>
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

      if( isNaN(categoryId)) {
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
              url: "{{url('createMyPost')}}",
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
              url: "{{url('createMyPost')}}",
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
                    if( 0 < id ){
                       $.ajax({
                          method: "POST",
                          url: "{{url('deletePost')}}",
                          data: {post_id:id}
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
      var isUpdatedFromDiscussion = 'false';
      $.ajax({
          method: "POST",
          url: "{{url('updatePost')}}",
          data: {post_id:postId,update_question:updateQuestion,updated_solution:updateSolution,updated_answer1:updatedAnswer1,updated_answer2:updatedAnswer2,updated_answer3:updatedAnswer3,updated_answer4:updatedAnswer4,updated_answer:updatedAnswer,isUpdatedFromDiscussion:isUpdatedFromDiscussion}
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
      arrayComments = [];
      $.each(msg['posts'], function(idx, obj) {
        arrayComments[idx] = obj;
      });
      var sortedArray = arrayComments.reverse();
        $.each(sortedArray, function(idx, obj) {
          if(false == $.isEmptyObject(obj)){
          var divMedia = document.createElement('div');
          divMedia.className = 'media';
          divMedia.id = 'showPosts_'+obj.id;

          var divMediaHeading = document.createElement('div');
          divMediaHeading.className = 'media-heading';

          var titleDiv = document.createElement('div');
          titleDiv.className = 'user-block';
          var url = "{{ url('goToPost')}}";
          var csrfField = '{{ csrf_field() }}';
          titleDiv.innerHTML = '<a  id="'+ obj.id +'" class="tital" onClick="goToPost(this);" style="cursor: pointer;">'+ obj.title +'</a><form id="goToPost_'+ obj.id +'" action="'+url+'" method="POST" style="display: none;">'+csrfField+'<input type="hidden" name="post_id" value="'+obj.id+'"></form>';
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
          if(obj.user_image){
            if('system' == obj.image_exist){
              var userImagePath =  "{{ asset('') }}"+obj.user_image;
            } else if('other' == obj.image_exist){
              var userImagePath =  obj.user_image;
            }
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
          commentInnerHtml +='</span>';
          if(obj.answer1 && obj.answer2 && obj.answer && obj.solution){
            commentInnerHtml += '<span class="mrgn_5_left">| <a id="'+obj.id+'" onClick="toggleSolution(this);">Solution</a></span>';
          }
          divComment.innerHTML = commentInnerHtml;
          divMediaBody.appendChild(divComment);

          divPanel.appendChild(divMediaBody);
          divMedia.appendChild(divPanel);
          showPostsDiv.appendChild(divMedia);
          }
        });
        showMore();
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
                  Cancel: function () {
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
  </script>
@stop