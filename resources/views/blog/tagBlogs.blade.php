@extends('layouts.master')
@section('header-title')
  <title>Blog for IoT, Education and Technology |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/solution.css') }}" rel="stylesheet"/>
  <link href="{{ asset('css/comment.css') }}" rel="stylesheet"/>
  <style type="text/css">
    .crl{
      float: right;
      background-color:#01bafd;
      padding: 0px 10px;
      display: inline-block;
      -moz-border-radius: 100px;
      -webkit-border-radius: 100px;
      border-radius: 100px;
      -moz-box-shadow: 0px 0px 2px #888;
      -webkit-box-shadow: 0px 0px 2px #888;
      box-shadow: 0px 0px 2px #888;
      color: #fff;
    }
  </style>
@stop
@section('header-js')
  @include('layouts.home-js')
@stop
@section('content')
  @include('header.header_menu')
  <section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single">
      <div class="vchip-background-img">
        <figure>
          <img src="{{ asset('images/blog.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip blog" />
        </figure>
      </div>
      <div class="vchip-background-content">
        <h2 class="animated bounceInLeft">Digital Education</h2>
      </div>
    </div>
  </section>
  <section id="" class="v_container v_bg_grey">
    <div class="container ">
      <div class="container">
        <div class="row">
          <div class="col-md-9">
            <div class="">
              <h2 class="v_h2_title text-center">Blogs</h2>
              <dir id="blogs">
                @if(count($blogs) > 0)
                  @foreach($blogs as $blog)
                    <div class="panel panel-info container-fluid">
                      <div class="panel-heading row">
                        <div class="col-xs-6 ">
                          <a class="uppercase" href="{{url('blogComment')}}/{{$blog->id}}" target="_blank"> {{$blog->title}}</a>
                          <figcaption class="blog-by">
                            <span><i class="fa fa-user" aria-hidden="true"> <a href="#">{{$blog->author}}</a></i></span>
                          </figcaption>
                        </div>
                        <div class="col-xs-6">
                          <div class="crl">
                           <div class="entry-time-day">{{ $blog->created_at->format('d') }}</div>
                           <div class="entry-time-month">{{ $blog->created_at->format('M') }}</div>
                         </div>
                        </div>
                      </div>
                      <div class="panel-body mrgn_10_top_btm more">
                        {!! $blog->content !!}
                      </div>
                      <div class="panel-footer row">
                        <div class="col-xs-12">
                          <i class="fa fa-comments" aria-hidden="true"><a href="{{url('blogComment')}}/{{$blog->id}}" target="_blank"> Leave a comment</a></i>
                        </div>
                      </div>
                    </div>
                  @endforeach
                @endif
              </dir>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@stop
@section('footer')
@include('footer.footer')
  <script type="text/javascript">

  var showChar = 700;
  var ellipsestext = "...";
  var moretext = "Read more";
  var lesstext = "less";
  $('.more').each(function() {
    var content = $(this).html();

    if(content.length > showChar) {

      var c = content.substr(0, showChar);
      var h = content.substr(showChar, content.length - showChar);

      var html = c + '<span class="moreellipses" style="color:#01bafd; margin-left:5px;">' + ellipsestext+ '</span><span class="morecontent" ><span style="display:none;">' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink" style="color:#01bafd";>' + moretext + '</a></span>';

      $(this).html(html);
    }

  });

  $(".morelink").click(function(){
    if($(this).hasClass("less")) {
      $(this).removeClass("less");
      $(this).html(moretext);
    } else {
      $(this).addClass("less");
      $(this).html(lesstext);
    }
    $(this).parent().prev().toggle();
    $(this).prev().toggle();
    return false;
  });


</script>
@stop
