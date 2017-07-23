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
                  {{$blogs->links()}}
                @endif
              </dir>
            </div>
          </div>
          <div class="col-md-3 ">
            <h4 class="v_h4_subtitle "> Sorted By</h4>
            <div class="dropdown mrgn_20_top_btm" id="cat">
              <select class="form-control" id="category_id" name="category_id" title="Category" onChange="showBlogs(this);">
                <option value="">Select Category ...</option>
                @if(count($blogCategories) > 0)
                  @foreach($blogCategories as $blogCategory)
                    <option value="{{$blogCategory->id}}">{{$blogCategory->name}}</option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="vchip-right-sidebar mrgn_30_top_btm">
              <h3 class="v_h3_title text-center">Recent Blog</h3>
              <ul class="vchip_list">
                @if(count($blogs) > 0)
                  @foreach($blogs as $blog)
                    <li title="{{ $blog->title }}"><a href="{{url('blogComment')}}/{{$blog->id}}">{{ $blog->title }}</a></li>
                  @endforeach
                @endif
              </ul>
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

// $(document).ready(function() {
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
// });


function showBlogs(ele){
  var categoryId = parseInt($(ele).val());
  if( 0 < categoryId ){
    $.ajax({
      method: "POST",
      url: "{{url('getBlogsByCategoryId')}}",
      data: {id:categoryId}
    })
    .done(function( msg ) {
      blogs = document.getElementById('blogs');
      blogs.innerHTML = '';
      if( null != msg.next_page_url){
        var currentPage = msg.current_page;
        var paginationUl = document.createElement('ul');
        paginationUl.className = 'pagination';
        var paginationLi = document.createElement('li');
        paginationLi.className = 'disabled';
        paginationLi.innerHTML = '<span>«</span>';
        paginationUl.appendChild(paginationLi);
        for(i = msg.current_page; i <= msg.last_page; i++ ){
          if( 1 == i){
            var paginationLi = document.createElement('li');
            paginationLi.className = 'active';
            paginationLi.innerHTML = '<span>'+ i +'</span>';
            paginationUl.appendChild(paginationLi);
          } else {
            var paginationLi = document.createElement('li');
            var url = "{{url('')}}/getBlogsByCategoryId?category_id="+categoryId+'&page='+i;
            paginationLi.innerHTML = '<a href='+ url +'>'+i+'</a>';
            paginationUl.appendChild(paginationLi);
          }
        }
      }
      if( 0 < msg.data.length){
        $.each(msg.data, function(idx, obj) {
          var mainDiv = document.createElement('div');
          mainDiv.className = 'panel panel-info container-fluid';
          var panelDiv = document.createElement('div');
          panelDiv.className = 'panel-heading row';
          var panelHeadingDiv = document.createElement('div');
          panelHeadingDiv.className = 'col-xs-6';
          var url = "{{url('blogComment')}}/"+obj.id;
          panelHeadingDiv.innerHTML = '<a class="uppercase" href="'+url+'" target="_blank">'+ obj.title +'</a><figcaption class="blog-by"><span><i class="fa fa-user" aria-hidden="true"> <a href="#">'+ obj.author+'</a></i></span></figcaption>';
          panelDiv.appendChild(panelHeadingDiv);

          var panelDateDiv = document.createElement('div');
          panelDateDiv.className = 'col-xs-6';
          panelDateDiv.innerHTML = '<div class="crl"><div class="entry-time-day">'+ new Date(obj.created_at).getDate() +'</div><div class="entry-time-month">'+ new Date(obj.created_at).toLocaleString("en-us", { month: "short" }) +'</div></div>';
          panelDiv.appendChild(panelDateDiv);
          mainDiv.appendChild(panelDiv);

          var panelContentDiv = document.createElement('div');
          panelContentDiv.className = 'panel-body mrgn_10_top_btm more';
          panelContentDiv.innerHTML = obj.content;
          mainDiv.appendChild(panelContentDiv);

          var panelFooterDiv = document.createElement('div');
          panelFooterDiv.className = 'panel-footer row';
          panelFooterDiv.innerHTML = '<div class="col-xs-12"><i class="fa fa-comments" aria-hidden="true"><a href="'+url+'" target="_blank"> Leave a comment</a></i></div>';
          mainDiv.appendChild(panelFooterDiv);
          blogs.appendChild(mainDiv);
        });
      }
      if( null != msg.next_page_url){
        var paginationLi = document.createElement('li');
        var nextPage = msg.current_page++;
        var url = "{{url('')}}/getBlogsByCategoryId?category_id="+categoryId+'&page='+nextPage;
        paginationLi.innerHTML = '<a href='+ url +' rel="next">»</a>';
        paginationUl.appendChild(paginationLi);
        blogs.appendChild(paginationUl);
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
  }
}
</script>
@stop
