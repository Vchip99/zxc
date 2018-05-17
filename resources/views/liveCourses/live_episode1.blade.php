@extends('layouts.master')
@section('header-title')
  <title>Live Online Video Courses episod by Industrial Expert |Vchip-edu</title>
@stop
@section('header-css')
@include('layouts.home-css')
<link href="{{asset('css/episode.css?ver=1.0')}}" rel="stylesheet"/>
<!-- <link href="{{asset('css/discussion.css?ver=1.0')}}" rel="stylesheet"/> -->
<link href="{{asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
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
  </style>
@stop
@section('header-js')
  @include('layouts.home-js')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
@stop
@section('content')
@include('header.header_menu')
<section id="" class="v_container v_bg_grey" style="background: #3A5894;">
  <div class="container text-center">
    <div class="row mrgn_60_top">
      <div class="col-md-9">
        <div class="embed-responsive embed-responsive-16by9" width="854" height="480">
          <!-- <iframe class="embed-responsive-item" width="854" height="480" src="{{$liveVideo->video_path}}" frameborder="0" allowfullscreen></iframe> -->
          {!! $liveVideo->video_path !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="scroll">
          <ol class="list-group">
            @if(count($liveCourseVideos)>0)
              @foreach($liveCourseVideos as $courseVideo)
                <li class="list-group-item">
                  <a href="{{url('liveEpisode')}}/{{$courseVideo->id}}">{{$courseVideo->name}} </a>
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
<section class="v_container v_bg_grey">
  <div class="container">
    <div class="row">
      <div class="col-md-6 ">
       <!-- <a href="" class="position">Episode {{$liveVideo->id}}</a> -->
        <span class="divider">&#9679;</span>
        <span class="running-time">Run Time- {{ gmdate('H:i:s', $liveVideo->duration)}}</span>
        <h4 class="v_h4_subtitle">
        <a href="">{{$liveVideo->name}}</a>
        <!-- <span class="">Free</span> -->
         </h4>
         <p>{{$liveVideo->description}}</p>
         <span class="v_download">
          <a class="btn btn-primary is-bold" role="button" data-toggle="collapse" href="#download_link" aria-expanded="false" aria-controls="collapseExample">
          Download</a></span>
          <div class="collapse" id="download_link">
            <div class="download_iteam">
              <a href="" download data-toggle="tooltip" data-placement="bottom" title="Pdf">
                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
              </a>
              <a href="" download data-toggle="tooltip" data-placement="bottom" title="Video">
                <i class="fa fa-video-camera" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-6 mrgn_10_tops">
          <p>{{$liveVideo->description}}</p>
        </div>
      </div>
    </div>
  </section>

<!-- <span>&nbsp;</span> -->
  <section class="v_container">
    <div class="container">
      <div class="panel with-nav-tabs panel-info">
        <div class="panel-heading">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#questions" data-toggle="tab">Question</a></li>
            <li><a href="#askQuestion" data-toggle="tab">Ask Question</a></li>
          </ul>
        </div>
        <div class="panel-body">
          <div class="tab-content">
            <div class="tab-pane fade in active" id="questions">
              <div class="post-comments">
                <div class="row" id="showAllPosts">
                  @if(count($posts) > 0)
                    @foreach($posts as $post)
                      <div class="media">
                        <div class="media-heading">
                          <button class="btn btn-default btn-xs cmt_plus_minus_symbol" type="button" data-toggle="collapse" data-target="#collapsePost{{$post->id}}" aria-expanded="false" aria-controls="collapseExample">
                          </button>
                          <span class="label label-info">{{$post->title}}</span> <i class="fa fa-clock-o" aria-hidden="true"></i> {{ $user->find($post->user_id)->name }} {{$post->updated_at->diffForHumans()}}
                        </div>
                        <div class="cmt-parent panel-collapse collapse in" id="collapsePost{{$post->id}}">
                          <div  class="media-body " data-toggle="lightbox ">
                            <p class="v_p_sm">{!! $post->body !!}</p>
                            <div class="comment-meta">
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
                            <div class="collapse" id="replyToPost{{$post->id}}">
                              <form action="{{ url('createAllPostComment')}}" method="POST" id="formReplyToPost{{$post->id}}">
                                {{csrf_field()}}
                                <div class="form-group">
                                  <label for="comment">Your Comment</label>
                                  <textarea name="comment" class="form-control" rows="5"></textarea>
                                </div>
                                <input type="hidden" name="all_post_module_id" value="{{$allPostModule}}">
                                <input type="hidden" name="all_post_id" value="{{$post->id}}">
                                <input type="hidden" name="episode_id" value="{{$liveVideo->id}}" >
                                <input type="hidden" name="project_id" value="0" >
                                <button type="button" class="btn btn-default" onclick="confirmSubmit(this);" id="formReplyToPost{{$post->id}}">Send</button>
                              </form>
                            </div>
                          </div>
                          @if(count( $post->comments) > 0)
                          @include('liveCourses.comments', ['comments' => $post->comments, 'parent' => 0, 'user' => $user, 'episodeId' => $liveVideo->id])
                          @endif
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
                <div class="widget-area no-padding blank">
                  <div class="status-upload">
                    <form action="{{url('createAllPost')}}" method="POST" id="createAllPost">
                      {{csrf_field()}}
                     <div class="input-group">
                      <span class="input-group-addon">Title</span>
                      <input id="title" type="text" class="form-control" name="title" placeholder="Add Title Here">
                    </div>
                    <textarea name="question" placeholder="Answer 1" type="text" id="question" required>
                    </textarea>
                    <script type="text/javascript">
                      CKEDITOR.replace( 'question' );
                      CKEDITOR.config.width="100%";
                      CKEDITOR.config.height="400px";
                      CKEDITOR.on('dialogDefinition', function (ev) {

                          var dialogName = ev.data.name,
                              dialogDefinition = ev.data.definition;

                          if (dialogName == 'image') {
                              var onOk = dialogDefinition.onOk;

                              dialogDefinition.onOk = function (e) {
                                  var width = this.getContentElement('info', 'txtWidth');
                                  width.setValue('100%');//Set Default Width

                                  var height = this.getContentElement('info', 'txtHeight');
                                  height.setValue('500');////Set Default height

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
                    <input type="hidden" name="all_post_module_id" value="{{$allPostModule}}" id="all_post_module_id">
                      <input type="hidden" name="episode_id" value="{{$liveVideo->id}}" >
                      <input type="hidden" name="project_id" value="0" >
                      <button type="button" class="btn btn-success" id="createAllPost" style="background-color: green;" onclick="confirmSubmit(this);" ><i class="fa fa-share"></i> Share</button>
                    </form>
                  </div><!-- Status Upload  -->
                </div><!-- Widget Area -->
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
  function confirmSubmit(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var allPostModuleId = parseInt(document.getElementById('all_post_module_id').value);

    if(0 < userId && 0 < allPostModuleId){
      formId = $(ele).attr('id');
      form = document.getElementById(formId);
      form.submit();
    } else if( isNaN(userId)) {
      if(confirm('Please login first. Click "Ok" button to login.')){
        window.location="{{url('/home')}}";
      }
    }
  }

  $(document).ready(function() {
    $('i.fa-thumbs-o-up, i.fa-thumbs-o-down').click(function(){
      var $this = $(this),
      c = $this.data('count');
      if (!c) c = 0;
      c++;
      $this.data('count',c);
      $('#'+this.id+'-bs3').html(c);
    });
    $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox();
    });

    $(document).on("click", "i[id^=post_like_]", function(e) {
        var postId = $(this).data('post_id');
        var episodeId = $(this).data('episode_id');
        var dislike = $(this).data('dislike');
        var projectId = 0;
        var userId = parseInt(document.getElementById('user_id').value);
         if( isNaN(userId)) {
          if(confirm('Please login first. Click "Ok" button to login.')){
            window.location="{{url('/home')}}";
          }
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('likePost')}}",
              data: {post_id:postId, episode_id:episodeId, dis_like:dislike, project_id:projectId}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
              var likeSpan = document.getElementById('like_'+postId);
              likeSpan.innerHTML = '';
              if( 1 == dislike ){
                likeSpan.innerHTML +='<i id="post_like_'+postId+'" data-post_id="'+postId+'" data-episode_id="'+episodeId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              } else {
                likeSpan.innerHTML +='<i id="post_like_'+postId+'" data-post_id="'+postId+'" data-episode_id="'+episodeId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
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
          if(confirm('Please login first. Click "Ok" button to login.')){
            window.location="{{url('/home')}}";
          }
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('likeComment')}}",
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

  });

</script>
<script type="text/javascript">

 (function () {
    // hold onto the drop down menu
    var dropdownMenu;

    // and when you show it, move it to the body
    $(window).on('show.bs.dropdown', function (e) {

        // grab the menu
        dropdownMenu = $(e.target).find('.dropdown-menu');

        // detach it and append it to the body
        $('body').append(dropdownMenu.detach());

        // grab the new offset position
        var eOffset = $(e.target).offset();

        // make sure to place it where it would normally go (this could be improved)
        dropdownMenu.css({
          'display': 'block',
          'top': eOffset.top + $(e.target).outerHeight(),
          'left': eOffset.left
        });
      });

    // and when you hide it, reattach the drop down, and hide it normally
    $(window).on('hide.bs.dropdown', function (e) {
      $(e.target).append(dropdownMenu.detach());
      dropdownMenu.hide();
    });
  })();
</script>
<script type="text/javascript">
  $(".toggle").slideUp();
  $(".trigger").click(function(){
    $(this).next(".toggle").slideToggle("slow");
  });



  var acc = document.getElementsByClassName("cmt_plus_minus_symbol");
  var i;

  for (i = 0; i < acc.length; i++) {
    acc[i].onclick = function() {
      this.classList.toggle("active");
      var panel = this.nextElementSibling;
      if (panel.style.maxHeight){
        panel.style.maxHeight = null;
      } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
      }
    }
  }
</script>
@stop