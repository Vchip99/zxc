@extends('layouts.master')
@section('header-title')
  <title>Hobby Projects in Electronics, IoT, VLSI and V-kit |V-edu</title>
@stop
@section('header-css')
@include('layouts.home-css')
  <link href="{{asset('css/solution.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
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
        <img src="{{asset('images/projects.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip project data"/>
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
      <div class="col-md-9">
       <div class="box-border" ">
       <h2 class="v_h2_title text-center">Study Material</h2>
          <hr class="section-dash-dark"/>
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
      <div class="btn-group" role="group" title="Favourite">
        @if($registeredProjectIds && in_array($project->id, $registeredProjectIds))
          <a class=" voted-btn" id="favourite" data-favourite="true" onClick="registerProject(this);" data-project_id="{{$project->id}}" title="Favourite" style="color: #e91e63;"> <i class="fa fa-star " aria-hidden="true"></i> </a>
        @else
          <a class="voted-btn" id="favourite" data-favourite="false" onClick="registerProject(this);" data-project_id="{{$project->id}}" title="Un Favourite" style="color: #000;"> <i class="fa fa-star " aria-hidden="true"></i> </a>
        @endif
      </div>
    </div>
    <div class="vchip-right-sidebar ">
      <h3 class="v_h3_title text-center">Study Material</h3>
      <div class="text-center download_iteam">
        <a href="{{asset($project->project_pdf_path)}}" download data-toggle="tooltip" data-placement="bottom" title="{{basename($project->project_pdf_path)}}" >
          <i class="fa fa-file-pdf-o tex" aria-hidden="true"></i>{{basename($project->project_pdf_path)}}
        </a>
      </div>
    </div>
    <div class="vchip-right-sidebar mrgn_30_top_btm">
      <h3 class="v_h3_title text-center">Projects</h3>
      @if(count($projects) > 0)
        @foreach($projects as $vKitProject)
          <div class="right-sidebar">
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
<span>&nbsp;</span>
<section class="v_container">
    <div class="container">
      <div class="panel with-nav-tabs panel-info">
        <div class="panel-heading">
          <dir class="comment-meta">
            <span id="like_{{$project->id}}" >
              @if( isset($likesCount[$project->id]) && isset($likesCount[$project->id]['user_id'][$currentUser]))
                   <i id="project_like_{{$project->id}}" data-project_id="{{$project->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                   <span id="like1-bs3">{{count($likesCount[$project->id]['like_id'])}}</span>
              @else
                   <i id="project_like_{{$project->id}}" data-project_id="{{$project->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                   <span id="like1-bs3">@if( isset($likesCount[$project->id])) {{count($likesCount[$project->id]['like_id'])}} @endif</span>
              @endif
            </span>
            <span class="mrgn_5_left">
              <a class="" role="button" data-toggle="collapse" href="#replyToProject{{$project->id}}" aria-expanded="false" aria-controls="collapseExample">Reply</a>
            </span>
            <div class="collapse replyComment" id="replyToProject{{$project->id}}">
              <form action="{{ url('createProjectComment')}}" method="POST" id="createProjectComment">
                {{csrf_field()}}
                <div class="form-group">
                  <label for="comment">Your Comment</label>
                  <textarea name="comment" id="comment" placeholder="Comment here.." class="form-control"></textarea>
                  <script type="text/javascript">
                    CKEDITOR.replace( 'comment');
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
                <button type="button" class="btn btn-default" onclick="confirmSubmit(this);" >Send</button>
                <button type="button" class="btn btn-default" data-id="replyToProject{{$project->id}}" onclick="cancleReply(this);">Cancle</button>
              </form>
            </div>
          </dir>
        </div>
        <div class="panel-body">
          <div class="tab-content">
            <div class="tab-pane fade in active" id="questions" style="padding: 15px !important;">
              <div class="post-comments ">
                <div class="row">
                  <div class="cmt-bg ">
                    <div class="box-body chat " id="chat-box">
                      @if(count( $comments) > 0)
                        @foreach($comments as $comment)
                          <div class="item" id="showComment_{{$comment->id}}">
                            <img src="{{ asset('images/user1.png') }}" alt="User Image" />
                            <div class="message">
                              @if(is_object(Auth::user()) && (Auth::user()->id == $comment->user_id))
                              <div class="dropdown pull-right">
                                <button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                  @if(Auth::user()->id == $comment->user_id)
                                    <li><a id="{{$comment->id}}" onclick="confirmCommentDelete(this);">Delete</a></li>
                                    <form id="deleteComment_{{$comment->id}}" action="{{ url('deleteVkitProjectComment')}}" method="POST" style="display: none;">
                                      {{ csrf_field() }}
                                      {{ method_field('DELETE') }}
                                      <input type="hidden" name="comment_id" value="{{$comment->id}}">
                                      <input type="hidden" name="project_id" value="{{$project->id}}" >
                                    </form>
                                  @endif
                                  @if(Auth::user()->id == $comment->user_id)
                                    <li><a id="{{$comment->id}}" onclick="editComment(this);">Edit</a></li>
                                  @endif
                                </ul>
                              </div>
                              @endif
                                <a class="SubCommentName">{{ $user->find($comment->user_id)->name }}</a>
                                <div class="more img-responsive img-ckeditor " id="editCommentHide_{{$comment->id}}">{!! $comment->body !!}</div>
                                <form action="{{ url('updateVkitProjectComment')}}" method="POST" id="formUpdateComment{{$comment->id}}">
                                      {{csrf_field()}}
                                      {{ method_field('PUT') }}
                                  <div class="form-group hide" id="editCommentShow_{{$comment->id}}" >
                                     <textarea class="form-control" name="comment" id="comment_{{$comment->id}}" rows="3">{!! $comment->body !!}</textarea>
                                    <script type="text/javascript">
                                      CKEDITOR.replace( 'comment_{{$comment->id}}');
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
                                    <input type="hidden" name="comment_id" value="{{$comment->id}}">
                                    <input type="hidden" name="project_id" value="{{$project->id}}" >
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <button type="button" class="btn btn-default" id="{{$comment->id}}" onclick="cancleComment(this);">Cancle</button>
                                  </div>
                                </form>
                              </div>
                              <div class="comment-meta reply-1">
                                <span id="cmt_like_{{$comment->id}}" >
                                  @if( isset($commentLikesCount[$comment->id]) && isset($commentLikesCount[$comment->id]['user_id'][$currentUser]))
                                       <i id="comment_like_{{$comment->id}}" data-project_id="{{$project->id}}" data-comment_id="{{$comment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                       <span id="like1-bs3">{{count($commentLikesCount[$comment->id]['like_id'])}}</span>
                                  @else
                                       <i id="comment_like_{{$comment->id}}" data-project_id="{{$project->id}}" data-comment_id="{{$comment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                                       <span id="like1-bs3">@if( isset($commentLikesCount[$comment->id])) {{count($commentLikesCount[$comment->id]['like_id'])}} @endif</span>
                                  @endif
                                </span>
                               <span class="mrgn_5_left">
                                <a class="" role="button" data-toggle="collapse" href="#replyToComment{{$comment->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
                              </span>
                              <span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> {{$comment->updated_at->diffForHumans()}}</span>
                              <div class="collapse replyComment" id="replyToComment{{$comment->id}}">
                                <form action="{{ url('createVkitProjectSubComment')}}" method="POST" id="formReplyToComment{{$comment->id}}">
                                   {{csrf_field()}}
                                  <div class="form-group">
                                    <label for="subcomment">Your Sub Comment</label>
                                      <textarea name="subcomment" class="form-control" rows="3"></textarea>
                                  </div>
                                  <input type="hidden" name="comment_id" value="{{$comment->id}}">
                                  <input type="hidden" name="project_id" value="{{$project->id}}" >
                                  <button type="button" class="btn btn-default" onclick="confirmSubmitReplytoComment(this);" data-id="formReplyToComment{{$comment->id}}">Send</button>
                                  <button type="button" class="btn btn-default" data-id="replyToComment{{$comment->id}}" onclick="cancleReply(this);">Cancle</button>
                                </form>
                              </div>
                            </div>
                          </div>
                          @if(count( $comment->children ) > 0)
                            @include('vkits.comments', ['comments' => $comment->children, 'parent' => $comment->id, 'user' => $user, 'projectId' => $project->id])
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
  $(document).ready(function() {
      showCommentEle = "{{ Session::get('project_comment_area')}}";
      showsubCommentEle = "{{ Session::get('show_subcomment_area')}}";
      if(showCommentEle > 0){
        window.location.hash = '#showComment_'+showCommentEle;
      } else if(showsubCommentEle > 0){
        window.location.hash = '#subcomment_'+showsubCommentEle;
      }

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
  });
</script>
<script type="text/javascript">
  function registerProject(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var projectId = parseInt($(ele).data('project_id'));
    if( true == isNaN(userId)){
        $.alert({
          title: 'Alert!',
          content: 'Please login first and then add favourite project.',
        });
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
    var projectId = parseInt(document.getElementById('project_id').value);

    if(0 < userId && 0 < projectId){
      document.getElementById('createProjectComment').submit();
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

  function cancleReply(ele){
    var id = $(ele).data('id');
    document.getElementById(id).classList.remove("in");
  }

</script>
@stop