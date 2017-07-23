<!DOCTYPE html>
<html lang="en" >
<head>
  <link rel="SHORTCUT ICON" href="{{asset('images/logo/vedu.png')}}"/>
  <title>Blog comment |Vchip Technology</title>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

  <meta name="description" content="Vchip Technology is working on Digital Education platform with name V-edu, V-edu provide great education platform equally in villages and remote areas along with urban area. In other word you can learn with fun from anywhere in the world. We always believes that better society is a best place to live and educated society is best society. We are currently focusing on formation of Digital Villages and bridging between well establish Industry/start-ups and educational organizations." />
  <meta name="author" content=" Vchip Technology" />
  <meta name="keywords" content="V-edu, vchipedu, Education sector, Online Courses, Digital Education, eLearning, Online learning, Online test series, Webinars, Online live courses, Live discussion,  vchip, Technology, vchip Technology, vchip Technology private ltd, vchip design and training, vchip design pvt ltd, vchip design and training pvt ltd, vishesh agrawal, web development, IoT, Internet of things, M2M, Mobile app development, Android app development, cloud formation, Internet of Everything, health sector, agriculture sector, food sector, Pune, Amravati." />

  <!-- Schema.org markup for Google+ -->
  <meta itemprop="name" content="V-edu - Digital Education, Online Courses & eLearning |Vchip Technology" />
  <meta itemprop="description" content="Vchip Technology is working on Digital Education platform with name V-edu, V-edu provide great education platform equally in villages and remote areas along with urban area. In other word you can learn with fun from anywhere in the world. We always believes that better society is a best place to live and educated society is best society. We are currently focusing on formation of Digital Villages and bridging between well establish Industry/start-ups and educational organizations." />
  <meta itemprop="image" content="img/V-edu_social_share.jpg" />

  <!-- Twitter card -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="V-edu - Digital Education, Online Courses & eLearning |Vchip Technology" />
  <meta name="twitter:site" content="@V-edu" />
  <meta name="twitter:creator" content="@V-edu"/>
  <meta name="twitter:image" content="img/V-edu_social_share.jpg" />
  <meta name="twitter:description" content="Vchip Technology is working on Digital Education platform with name V-edu, V-edu provide great education platform equally in villages and remote areas along with urban area. In other word you can learn with fun from anywhere in the world. We always believes that better society is a best place to live and educated society is best society. We are currently focusing on formation of Digital Villages and bridging between well establish Industry/start-ups and educational organizations." />

  <!-- Open graph  -->
  <meta property="og:type"   content="website" />
  <meta property="og:url"    content="http://vchipedu.com/" />
  <meta property="og:site_name" content="vchipedu.com" />
  <meta property="og:title" content="V-edu - Digital Education, Online Courses & eLearning |Vchip Technology" />
  <meta property="og:image"  content="img/V-edu_social_share.jpg" />
  <meta name="og:description" content="Vchip Technology is working on Digital Education platform with name V-edu, V-edu provide great education platform equally in villages and remote areas along with urban area. In other word you can learn with fun from anywhere in the world. We always believes that better society is a best place to live and educated society is best society. We are currently focusing on formation of Digital Villages and bridging between well establish Industry/start-ups and educational organizations."/>

  <link href="{{asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
  <link href="{{asset('css/font-awesome/css/font-awesome.min.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_main.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/solution.css?ver=1.0') }}" rel="stylesheet"/>
  <link href="{{asset('css/comment.css?ver=1.0') }}" rel="stylesheet"/>
  <link href="{{asset('css/jquery-confirm.min.css?ver=1.0')}}" rel="stylesheet"/>

  <script src="{{asset('js/jquery.min.js?ver=1.0')}}"></script>
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  <script src="{{asset('js/jquery-confirm.min.js?ver=1.0')}}"></script>

  <style type="text/css">

/*
 * Footer
 */

.blog-footer {
  background-color:#009ACD;
  padding: 2.5rem 0;
  color: #fff;
  text-align: center;
  border-top: .05rem solid #e5e5e5;
}
 .blog-footer a{
  color: blue;
}
.blog-footer p:last-child {
  margin-bottom: 0;

}

.navbar{
    background-color:#009ACD;
    padding: 10px;
    font-color: #fff;
}
.navbar .navbar-collapse ul li>a{
color: #fff;

}
.navbar .collapse .navbar-form .form-group>input{
width: 400px;
}
.navbar .collapse .navbar-form .form-group>input:hover{
    border-color: blue;
}
.navbar-header button
{background: hotpink;}
.navbar-header span{
  background: white;
}
</style>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
        }
    });
</script>
</head>
<body>
<nav class="navbar navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav ">
        <li><a href="{{url('/')}}">Home <span class="sr-only">(current)</span></a></li>
        <li><a href="{{url('blog')}}">Blog <span class="sr-only">(current)</span></a></li>
      </ul>
    </div>
  </div>
</nav>
<input type="hidden" name="user_id" id="user_id"
  @if(is_object(Auth::user()))
    value="{{Auth::user()->id}}"
  @endif
>
<section id="" class="v_container v_bg_grey mrgn_50_top">
  <div class="container ">
    <div class="row">
      <div class="col-md-9">
         <h2 class="v_h2_title">The Vchip Blog</h2>
         <h3 class="v_h3_title ">{{$blog->title}}</h3>
         <p>
            {{ date('F d, Y', strtotime($blog->created_at))}} by <a>{{$blog->author}}</a>
         </p>
         <p>{!! $blog->content !!}</p>
      </div>
      <div class="col-md-3">
        <div class="vchip-right-sidebar mrgn_30_top_btm">
          <h3 class="v_h3_title text-center">Recent Blog</h3>
          <ul class="vchip_list">
            @if(count($blogs) > 0)
              @foreach($blogs as $singleBlog)
                <li title="{{ $singleBlog->title }}"><a href="{{url('blogComment')}}/{{$singleBlog->id}}">{{ $singleBlog->title }}</a></li>
              @endforeach
            @endif
          </ul>
        </div>
        <div class="vchip-right-sidebar mrgn_30_top_btm">
          <h3 class="v_h3_title text-center">Tag </h3>
            @if(count($blogTags) > 0)
              @foreach($blogTags as $index => $blogTag)
                @if( (count($blogTags)-1) == $index)
                  <a href="{{url('tagBlogs')}}/{{$blogTag->id}}">{{$blogTag->name}}</a>
                @else
                  <a href="{{url('tagBlogs')}}/{{$blogTag->id}}">{{$blogTag->name}}</a>,
                @endif
              @endforeach
            @endif
        </div>
      </div>
    </div>
  </div>
</section>
<section class="v_container">
    <div class="container">
      <div class="panel with-nav-tabs panel-info">
        <div class="panel-heading">
          <dir class="comment-meta">
            <span id="like_{{$blog->id}}" >
              @if( isset($likesCount[$blog->id]) && isset($likesCount[$blog->id]['user_id'][$currentUser]))
                   <i id="blog_like_{{$blog->id}}" data-blog_id="{{$blog->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                   <span id="like1-bs3">{{count($likesCount[$blog->id]['like_id'])}}</span>
              @else
                   <i id="blog_like_{{$blog->id}}" data-blog_id="{{$blog->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                   <span id="like1-bs3">@if( isset($likesCount[$blog->id])) {{count($likesCount[$blog->id]['like_id'])}} @endif</span>
              @endif
            </span>
            <span class="mrgn_5_left">
              <a class="" role="button" data-toggle="collapse" href="#replyToBlog{{$blog->id}}" aria-expanded="false" aria-controls="collapseExample">Reply</a>
            </span>
            <div class="collapse replyComment" id="replyToBlog{{$blog->id}}">
              <form action="{{ url('createBlogComment')}}" method="POST" id="createBlogComment">
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
                                height.setValue('400');
                                onOk && onOk.apply(this, e);
                            };
                        }
                    });
                  </script>
                </div>
                <input type="hidden" name="blog_id" value="{{$blog->id}}">
                <button type="button" class="btn btn-default" onclick="confirmSubmit(this);" id="formReplyToBlog{{$blog->id}}">Send</button>
                <button type="button" class="btn btn-default" data-id="replyToBlog{{$blog->id}}" onclick="cancleReply(this);">Cancle</button>
              </form>
            </div>
          </dir>
        </div>
        <div class="panel-body">
          <div class="tab-content">
            <div class="tab-pane fade in active" id="questions">
              <div class="post-comments">
                <div class="row" id="showAllPosts">
                  @if(count($comments) > 0)
                    @foreach($comments as $comment)
                    <div class="cmt-bg">
                      <div class="box-body chat" id="chat-box">
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
                                <form id="deleteComment_{{$comment->id}}" action="{{ url('deleteBlogComment')}}" method="POST" style="display: none;">
                                  {{ csrf_field() }}
                                  {{ method_field('DELETE') }}
                                  <input type="hidden" name="comment_id" value="{{$comment->id}}">
                                  <input type="hidden" name="blog_id" value="{{$blog->id}}">
                                </form>
                              @endif
                              @if(Auth::user()->id == $comment->user_id)
                                <li><a id="{{$comment->id}}" onclick="editComment(this);">Edit</a></li>
                              @endif
                            </ul>
                          </div>
                          @endif
                            <a class="SubCommentName">{{ $user->find($comment->user_id)->name }}</a>
                            <div class="more" id="editCommentHide_{{$comment->id}}">{!! $comment->body !!}</div>
                            <form action="{{ url('updateBlogComment')}}" method="POST" id="formUpdateComment{{$comment->id}}">
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
                                                  height.setValue('400');
                                                  onOk && onOk.apply(this, e);
                                              };
                                          }
                                      });
                                    </script>
                                <input type="hidden" name="comment_id" value="{{$comment->id}}">
                                <input type="hidden" name="blog_id" value="{{$blog->id}}">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-default" id="{{$comment->id}}" onclick="cancleComment(this);">Cancle</button>
                              </div>
                            </form>
                          </div>
                          <div class="comment-meta reply-1">
                            <span id="cmt_like_{{$comment->id}}" >
                              @if( isset($commentLikesCount[$comment->id]) && isset($commentLikesCount[$comment->id]['user_id'][$currentUser]))
                                   <i id="comment_like_{{$comment->id}}" data-blog_id="{{$comment->blog_id}}" data-comment_id="{{$comment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                   <span id="like1-bs3">{{count($commentLikesCount[$comment->id]['like_id'])}}</span>
                              @else
                                   <i id="comment_like_{{$comment->id}}" data-blog_id="{{$comment->blog_id}}" data-comment_id="{{$comment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                                   <span id="like1-bs3">@if( isset($commentLikesCount[$comment->id])) {{count($commentLikesCount[$comment->id]['like_id'])}} @endif</span>
                              @endif
                            </span>
                           <span class="mrgn_5_left">
                            <a class="" role="button" data-toggle="collapse" href="#replyToComment{{$blog->id}}-{{$comment->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
                          </span>
                          <span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> {{$comment->updated_at->diffForHumans()}}</span>
                          <div class="collapse replyComment" id="replyToComment{{$blog->id}}-{{$comment->id}}">
                            <form action="{{ url('createBlogSubComment')}}" method="POST" id="formReplyToComment{{$blog->id}}{{$comment->id}}">
                               {{csrf_field()}}
                              <div class="form-group">
                                <label for="subcomment">Your Sub Comment</label>
                                  <textarea name="comment" class="form-control" rows="3"></textarea>
                              </div>
                              <input type="hidden" name="blog_id" value="{{$blog->id}}">
                              <input type="hidden" name="comment_id" value="{{$comment->id}}">
                              <button type="button" class="btn btn-default" onclick="confirmSubmitReplytoComment(this);" data-id="formReplyToComment{{$blog->id}}{{$comment->id}}">Send</button>
                              <button type="button" class="btn btn-default" data-id="replyToComment{{$blog->id}}-{{$comment->id}}" onclick="cancleReply(this);">Cancle</button>
                            </form>
                          </div>
                        </div>
                      </div>
                      @if(count( $comment->children ) > 0)
                        @include('blog.child_comments', ['comments' => $comment->children, 'parent' => $comment->id, 'user' => $user])
                      @endif
                    </div>
                    </div>
                    @endforeach
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
  <footer class="blog-footer ">
    <p>Blog  by <a href="http://vchiptech.com/">Vishes Agrawal</a>.</p>
    <p>
      <a href="#">Back to top</a>
    </p>
  </footer>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="{{ asset('js/togleForFilterBy.js') }}"></script>
  <script type="text/javascript">
    function confirmSubmit(ele){
      var userId = parseInt(document.getElementById('user_id').value);
      var comment = CKEDITOR.instances.comment.getData().length;
      if(0 < userId && comment){
        document.getElementById('createBlogComment').submit();
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
    function cancleReply(ele){
      var id = $(ele).data('id');
      document.getElementById(id).classList.remove("in");
    }

     $(document).on("click", "i[id^=comment_like_]", function(e) {
        var blogId = $(this).data('blog_id');
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
              url: "{{ url('likeBlogComment') }}",
              data: {blog_id:blogId, comment_id:commentId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
              var likeSpan = document.getElementById('cmt_like_'+commentId);
                likeSpan.innerHTML = '';
                if( 1 == dislike ){
                  likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-blog_id="'+blogId+'" data-comment_id="'+commentId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                } else {
                  likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-blog_id="'+blogId+'" data-comment_id="'+commentId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                }
            }
          });
        }
      });

     $(document).on("click", "i[id^=sub_comment_like_]", function(e) {
        var blogId = $(this).data('blog_id');
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
              url: "{{url('likeBlogSubComment')}}",
              data: {blog_id:blogId, comment_id:commentId, sub_comment_id:subCommentId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
                var likeSpan = document.getElementById('sub_cmt_like_'+subCommentId);
                likeSpan.innerHTML = '';
                if( 1 == dislike ){
                  likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-blog_id="'+blogId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                } else {
                  likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-blog_id="'+blogId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                  likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
                }
          }
          });
        }
      });
     $(document).on("click", "i[id^=blog_like_]", function(e) {
        var blogId = $(this).data('blog_id');
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
              url: "{{url('likeBlog')}}",
              data: {blog_id:blogId, dis_like:dislike}
          })
          .done(function( msg ) {
            if( 'false' != msg ){
              var likeSpan = document.getElementById('like_'+blogId);
              likeSpan.innerHTML = '';
              if( 1 == dislike ){
                likeSpan.innerHTML +='<i id="blog_like_'+blogId+'" data-blog_id="'+blogId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              } else {
                likeSpan.innerHTML +='<i id="blog_like_'+blogId+'" data-blog_id="'+blogId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              }
            }
          });
        }
    });

    function editComment(ele){
      var id = $(ele).attr('id');
      document.getElementById('editCommentHide_'+id).classList.add("hide");
      document.getElementById('editCommentShow_'+id).classList.remove("hide");
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
    function editSubComment(ele){
      var id = $(ele).attr('id');
      document.getElementById('editSubCommentHide_'+id).classList.add("hide");
      document.getElementById('editSubCommentShow_'+id).classList.remove("hide");
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

    $( document ).ready(function() {
      showCommentEle = "{{ Session::get('blog_comment_area')}}";
      if(showCommentEle > 0){
        window.location.hash = '#showComment_'+showCommentEle;
      }
      showMore();
    });

  </script>
</body>
</html>