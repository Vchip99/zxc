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
@stop
@section('dashboard_content')
	<section   class="v_container">
    <div class="container ">
      <div class="row">
        <div class="col-sm-9">
            <div class="ask-qst">
              <button class="btn btn-primary "  data-toggle="modal" data-target="#askQuestion" style="width: 100px;"> Ask Question</button>
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
                            <option value = "0"> Select Category ...</option>
                            @if(count($discussionCategories) > 0)
                              @foreach($discussionCategories as $discussionCategory)
                                <option value = "{{$discussionCategory->id}}"> {{$discussionCategory->name}} </option>
                              @endforeach
                            @endif
                        </select>
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
    function confirmSubmit(ele){
      var userId = parseInt(document.getElementById('user_id').value);
      var categoryId = parseInt(document.getElementById('post_category').value);
      var questionLength = CKEDITOR.instances.question.getData().length;
      var title = document.getElementById('title').value;
      if(0 < userId && 0 < categoryId && questionLength > 0 && title){
        var question = CKEDITOR.instances.question.getData();
          $.ajax({
              method: "POST",
              url: "{{url('createPost')}}",
              data: {title:title,post_category_id:categoryId,question:question}
          })
          .done(function( msg ) {
            $('#askQuestion').modal('hide');
            document.getElementById('post_category').value = 0;
            document.getElementById('title').value = '';
            CKEDITOR.instances.question.setData('');
            renderPosts(msg);
          });
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
          boxDiv.innerHTML = boxDivInnerHtml;
          divMediaHeading.appendChild(boxDiv);
          divMedia.appendChild(divMediaHeading);

          var divPanel = document.createElement('div');
          divPanel.className = 'cmt-parent panel-collapse collapse in';
          divPanel.id = 'post'+obj.id;

          var commentBlockDiv = document.createElement('div');
          commentBlockDiv.className = 'user-block cmt-left-margin';
          if(obj.user_image){
            var userImagePath = "{{ asset('') }}"+obj.user_image;
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
          pBody.className = 'more bold img-ckeditor img-responsive cmt-left-margin';
          pBody.id ='editPostHide_'+ obj.id;
          pBody.innerHTML = obj.body + ' <br/>';
          divMediaBody.appendChild(pBody);

          var borderDiv = document.createElement('div');
          borderDiv.className = 'border-bottom';
          divMediaBody.appendChild(borderDiv);

          var divComment = document.createElement('div');
          divComment.className = 'comment-meta main-reply-box cmt-left-margin';
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
      // showCommentEle = "{{ Session::get('show_comment_area')}}";
      // showPostEle = "{{ Session::get('show_post_area')}}";
      // if(showCommentEle > 0 &&  showPostEle == 0){
      //   window.location.hash = '#showComment_'+showCommentEle;
      // } else if(showCommentEle == 0 &&  showPostEle > 0){
      //   window.location.hash = '#showPost_'+showPostEle;
      // }
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
  </script>
@stop