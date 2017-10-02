@extends('layouts.master')
@section('header-title')
  <title>Hobby Projects in Electronics, IoT, VLSI and V-kit |Vchip-edu</title>
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
        <div class="" style="border: 2px solid #ddd; padding: 10px;">
          <div class="v_kit-single">
              <figure class="v_kit-img">
               <a href="#"><img class="img-responsive" alt="img" src="{{asset($project->header_image_path)}}"/></a>
               <figcaption class="v_kit-imgcaption">
                <a href="#">{{$project->author}}</a>
              </figcaption>
            </figure>
            <div class="v_kit-single-content">
              <h2><a href="#">{{$project->name}} </a></h2>
              <h4>Course Information</h4>
              <div class="img-responsive" >
                {!! $project->description !!}
              </div>
            </div>
          </div>
       </div>
     </div>
   </div>
  <div class="col-md-3">
    <div class="vchip-right-sidebar ">
      <label>Favourite : </label>&nbsp;
      @if(in_array($project->id, $registeredProjectIds))
        <input type="checkbox" name="favourite" id="favourite" checked="checked" data-project_id="{{$project->id}}" onClick="registerProject(this);"/>
      @else
        <input type="checkbox" name="favourite" id="favourite" data-project_id="{{$project->id}}" onClick="registerProject(this);"/>
      @endif
    </div>
    <div class="vchip-right-sidebar ">
      <h3 class="v_h3_title">Study Material</h3>
      <div class="text-center download_iteam">
        <a href="{{asset($project->project_pdf_path)}}" download data-toggle="tooltip" data-placement="bottom" title="Pdf" >
          <i class="fa fa-file-pdf-o tex" aria-hidden="true"></i>{{basename($project->project_pdf_path)}}
        </a>
      </div>
    </div>
    <div class="vchip-right-sidebar mrgn_30_top_btm">
      <h3 class="v_h3_title">Projects</h3>
      @if(count($projects) > 0)
        @foreach($projects as $vKitProject)
          <div class="right-sidebar">
            <div class="media">
              <div class=" media-left">
                <a href="#">
                @if(!empty($vKitProject->front_image_path))
                  <img class="media-object" src="{{ asset($vKitProject->front_image_path) }}" alt="vckits">
                @else
                  <img class="media-object" src="{{ asset('images/default_course_image.jpg') }}" alt="vckits">
                @endif
                </a>
              </div>
              <div class="media-body">
               <h4 class=""><a href="{{ url('vkitproject')}}/{{$vKitProject->id }}">{{$vKitProject->name }}</a></h4>
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
                            <div class="img-responsive"><p class="v_p_sm">{!! $post->body !!}</p></div>
                            <div class="comment-meta">
                              <span id="like_{{$post->id}}" >
                              @if( isset($postLikesCount[$post->id]) && isset($postLikesCount[$post->id]['user_id'][$currentUser]))
                                   <i id="post_like_{{$post->id}}" data-post_id="{{$post->id}}" data-project_id="{{$post->project_id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                   <span id="like1-bs3">{{count($postLikesCount[$post->id]['like_id'])}}</span>
                              @else
                                   <i id="post_like_{{$post->id}}" data-post_id="{{$post->id}}" data-project_id="{{$post->project_id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                                   <span id="like1-bs3">@if( isset($postLikesCount[$post->id])) {{count($postLikesCount[$post->id]['like_id'])}} @endif</span>
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
                                <input type="hidden" name="episode_id" value="0" >
                                <input type="hidden" name="project_id" value="{{$project->id}}" >
                                <button type="button" class="btn btn-default" onclick="confirmSubmit(this);" id="formReplyToPost{{$post->id}}">Send</button>
                              </form>
                            </div>
                          </div>
                          @if(count( $post->comments) > 0)
                          @include('vkits.comments', ['comments' => $post->comments, 'parent' => 0, 'user' => $user, 'projectId' => $project->id])
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
                   <!--  <textarea id="question" name="question" placeholder="What are you doing right now?"  contenteditable="true">
                    </textarea> -->
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
                      <input type="hidden" name="episode_id" value="0" >
                      <input type="hidden" name="project_id" value="{{$project->id}}" >
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
  function registerProject(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var projectId = parseInt($(ele).data('project_id'));
    if( true == isNaN(userId)){
      alert('please login first and then add favourite project.');
      $(ele).prop('checked', false);
    } else {
      $.ajax({
        method: "POST",
        url: "{{url('registerProject')}}",
        data: {user_id:userId, project_id:projectId}
      })
      .done(function( msg ) {

        // var favEle = document.getElementById('addFavourite');
        // favEle.readOnly = true;
        // favEle.innerHTML = 'Added to Favourite';
        // favEle.removeAttribute('onclick');
      });
    }
  }

    $(document).on("click", "i[id^=post_like_]", function(e) {
        var postId = $(this).data('post_id');
        var projectId = $(this).data('project_id');
        var dislike = $(this).data('dislike');
        var episodeId = 0;
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
                likeSpan.innerHTML +='<i id="post_like_'+postId+'" data-post_id="'+postId+'" data-project_id="'+projectId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              } else {
                likeSpan.innerHTML +='<i id="post_like_'+postId+'" data-post_id="'+postId+'" data-project_id="'+projectId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              }
            }
          });
        }
    });

</script>
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
</script>
<script type="text/javascript">
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
</script>
@stop