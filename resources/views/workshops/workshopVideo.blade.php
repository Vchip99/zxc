@extends('layouts.master')
@section('header-title')
  <title>Live Online workshop episod by Industrial Expert |V-edu</title>
@stop
@section('header-css')
@include('layouts.home-css')
  <link href="{{ asset('css/episode.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
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
   .list-group-item .ellipsis{
      display:inline-block;
      width:60px;
      white-space: nowrap;
      overflow:hidden !important;
      text-overflow: ellipsis;
    }
          @media  (min-width: 350px) {
          .hidden-lg { display: none; }
        }
      @media  (max-width: 349px) {
          .hidden-sm { display: none; }
        }
.fa-comment-o, .first-like i, .your-cmt{
  font-weight: bold;
  font-size: 18px;
  color: #555;
}
.first-like i{
  margin-right: 5px;
}
hr{
  margin-top: 5px;
  margin-bottom: 2px;
  border-bottom: 1px solid ;
}
.comment-meta{
  margin-left: 30px;
  margin-top: 20px;
  margin-bottom: 20px;
}

  </style>
@stop
@section('header-js')
  @include('layouts.home-js')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
@stop
@section('content')
@include('header.header_menu')
<section id="" class="v_container " style="background: #3A5894;">
  <div class="container text-center">
    <div class="row mrgn_60_top">
      <div class="col-md-9">
        <div class="embed-responsive embed-responsive-16by9" width="854" height="480">
          {!! $video->video_path !!}
        </div>
      </div>
      <!-- <div class="col-md-3">
        <div class="scroll">
          <ol class="list-group">
            @if(count($workshopVideos)>0)
              @foreach($workshopVideos as $workshopVideo)
                <li class="list-group-item">
                  <a class="ellipsis" href="{{url('workshopVideo')}}/{{$workshopVideo->id}}" data-toggle="tooltip" title="{{$workshopVideo->name}}">{{$workshopVideo->name}} </a>
                  <span class="running-time"> {{ gmdate('H:i:s', $workshopVideo->duration)}} </span>
                </li>
              @endforeach
            @endif
          </ol>
        </div>
      </div> -->
    </div>
  </div>
</section>
<section class="v_container ">
  <div class="container">
    <div class="row">
      <div class="col-md-6 ">
        <span class="divider">&#9679;</span>
        <span class="running-time">Run Time- {{ gmdate('H:i:s', $video->duration)}}</span>
        <h4 class="v_h4_subtitle">
        <a >{{$video->name}}</a>
         </h4>
         <p class="more">{{$video->description}}</p>
         <span class="v_download" title="Download">
          <a class="btn btn-primary is-bold" role="button" data-toggle="collapse" href="#download_link" aria-expanded="false" aria-controls="collapseExample">
          Download</a></span>
          <div class="collapse" id="download_link">
            <div class="download_iteam">
              <a download data-toggle="tooltip" data-placement="bottom" title="Pdf">
                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
              </a>
              <a download data-toggle="tooltip" data-placement="bottom" title="Video">
                <i class="fa fa-video-camera" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-6 mrgn_10_tops">
        </div>
      </div>
    </div>
  </section>


@stop
@section('footer')
  @include('footer.footer')
  <script type="text/javascript">

  function AddFile(){
    var uploadedFilesDiv = document.getElementById("uploaded_files");
    var lengthEle = uploadedFilesDiv.getElementsByTagName("input").length+1;
    var inputEle = document.createElement('input');
    inputEle.setAttribute('type', 'file');
    inputEle.setAttribute('name', 'filesToUpload_'+lengthEle);
    inputEle.setAttribute('onChange', 'makeFileList(event);');
    uploadedFilesDiv.appendChild(inputEle);
  }

  function makeFileList(event) {
    var addImage = document.getElementById("comment");
    addImage.value  += '<img src="'+URL.createObjectURL(event.target.files[0])+'" name="'+event.target.files[0].name+'"><br/>';
    addImage.focus();
  }

  function confirmSubmit(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(userId > 0){
      document.getElementById('createCourseComment').submit();
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
                Cancel: function () {
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
                Cancel: function () {
                }
            }
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
              Cancel: function () {
              }
          }
        });
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
              Cancel: function () {
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
              Cancel: function () {
              }
          }
        });
  }

  function cancleReply(ele){
    var id = $(ele).data('id');
    document.getElementById(id).classList.remove("in");
  }

    $(document).on("click", "i[id^=video_like_]", function(e) {
        var videoId = $(this).data('video_id');
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
              url: "{{url('likeCourseVideo')}}",
              data: {video_id:videoId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
              var likeSpan = document.getElementById('like_'+videoId);
              likeSpan.innerHTML = '';
              if( 1 == dislike ){
                likeSpan.innerHTML +='<i id="video_like_'+videoId+'" data-video_id="'+videoId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"> Like </i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              } else {
                likeSpan.innerHTML +='<i id="video_like_'+videoId+'" data-video_id="'+videoId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"> Like </i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              }
            }
          });
        }
    });

    $(document).on("click", "i[id^=comment_like_]", function(e) {
        var videoId = $(this).data('video_id');
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
                Cancel: function () {
                }
            }
          });
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('likeCourseVideoComment')}}",
              data: {video_id:videoId, comment_id:commentId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
                var likeSpan = document.getElementById('cmt_like_'+commentId);
                likeSpan.innerHTML = '';
                if( 1 == dislike ){
                  likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-video_id="'+videoId+'" data-comment_id="'+commentId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like" style= "margin-right:5px;"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                } else {
                  likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-video_id="'+videoId+'" data-comment_id="'+commentId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like" style= "margin-right:5px;"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                }
          }
          });
        }
    });

    $(document).on("click", "i[id^=sub_comment_like_]", function(e) {
        var videoId = $(this).data('video_id');
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
                Cancel: function () {
                }
            }
          });
        } else {
          $.ajax({
              method: "POST",
              url: "{{url('likeCourseVideoSubComment')}}",
              data: {video_id:videoId, comment_id:commentId, sub_comment_id:subCommentId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
                var likeSpan = document.getElementById('sub_cmt_like_'+subCommentId);
                likeSpan.innerHTML = '';
                if( 1 == dislike ){
                  likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-video_id="'+videoId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                } else {
                  likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-video_id="'+videoId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                }
          }
          });
        }
    });
</script>
<script type="text/javascript">
  $( document ).ready(function() {
    showCommentEle = "{{ Session::get('course_comment_area')}}";
    showsubCommentEle = "{{ Session::get('show_subcomment_area')}}";

     if(showCommentEle > 0){
        window.location.hash = '#showComment_'+showCommentEle;
      } else if(showsubCommentEle > 0){
        window.location.hash = '#subcomment_'+showsubCommentEle;
      }
  });
</script>
<script type="text/javascript">
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
     </script>
@stop