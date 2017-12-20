@extends('layouts.master')
@section('header-title')
  <title>Blog for IoT, Education and Technology |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/solution.css') }}" rel="stylesheet"/>
  <link href="{{ asset('css/comment.css') }}" rel="stylesheet"/>
  <style type="text/css">
  .ellipsed {
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}
p.ellipsed{
  cursor: pointer;
}
  .v_p_heding{font-weight: bolder;}
 .panel-heading img {
  width: 30px;
  height: 30px;
  float: left;
  border: 2px solid #d2d6de;
  padding: 1px;
}
.username{
margin-left: 10px;
margin-right: 10px;

}
.username {
  font-size: 16px;
  font-weight: 600;
  color:#b6b6b6;
}
.fa-calendar-o{ font-weight: bolder;
margin-right: 5px;
}
.date{
color: #b6b6b6;
}
.row {
    margin-right: -15px !important;
    margin-left: -15px !important;
}
@media (min-width: 768px) and (max-width: 991px){
.vchip-right-sidebar {
     width:100%;
    }
    .vchip_list, .right-sidebar {
    margin-left: -25%;
}
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
        <div class="row">

          <div class="col-sm-3 col-sm-push-9 ">
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
            <div class="hidden-div1">
              <div class="advertisement-area" style="padding-right: 5px;">
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
                        <a class="img-course-box" href="http://ssgmce.org/Default.aspx?ReturnUrl=%2f" target="_blank">
                          <img src="{{ asset('images/logo/ssgmce-logo.jpg') }}" alt="SSGMCE"  class="img-responsive" />
                        </a>
                        <div class="course-box-content">
                          <h4 class="course-box-title" title="SSGMCE" data-toggle="tooltip" data-placement="bottom">
                            <a href="http://ssgmce.org/Default.aspx?ReturnUrl=%2f" target="_blank">SSGMCE</a>
                          </h4>
                          <p class="more"> SSGMCE</p>
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
          <div class="col-sm-9 col-sm-pull-3">
            <div class="">
              <h2 class="v_h2_title text-center">Blogs</h2>
              <div id="blogs">
                @if(count($blogs) > 0)
                  @foreach($blogs as $blog)
                    <div class="panel panel-default container-fluid slideanim">
                      <div class="panel-heading row">
                        <p class="ellipsed"> <a class="uppercase v_p_heding " href="{{url('blogComment')}}/{{$blog->id}}" target="_blank"> {{$blog->title}}</a>
                        </p>
                        <figcaption class="blog-by">
                          <span>
                            @if(!empty($blog->user->photo))
                              <img src="{{ asset($blog->user->photo)}} " class="img-circle" alt="User Image">
                            @else
                              <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
                            @endif
                          </span>
                          <span class="username">{{$blog->author}}</span>
                          <span class="date"><i class="fa fa-calendar-o"></i><span> {{ $blog->created_at->format('M d , Y') }}</span></span>
                        </figcaption>
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
                @else
                  No blogs available.
                @endif
              </div>
            </div>
          </div>
          <div class="col-sm-12 hidden-div">
            <div class="">
              <div class="advertisement-area" style="padding-right: 5px;">
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
                        <a class="img-course-box" href="http://ssgmce.org/Default.aspx?ReturnUrl=%2f" target="_blank">
                          <img src="{{ asset('images/logo/ssgmce-logo.jpg') }}" alt="SSGMCE"  class="img-responsive" />
                        </a>
                        <div class="course-box-content">
                          <h4 class="course-box-title" title="SSGMCE" data-toggle="tooltip" data-placement="bottom">
                            <a href="http://ssgmce.org/Default.aspx?ReturnUrl=%2f" target="_blank">SSGMCE</a>
                          </h4>
                          <p class="more"> SSGMCE</p>
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
          mainDiv.className = 'panel panel-default container-fluid';
          var panelDiv = document.createElement('div');
          panelDiv.className = 'panel-heading row';
          var panelHeadingDiv = document.createElement('div');
          var url = "{{url('blogComment')}}/"+obj.id;
          panelHeadingDiv.innerHTML = '<p class="ellipsed"> <a class="uppercase" href="'+url+'" target="_blank">'+ obj.title +'</a></p><figcaption class="blog-by"><span><img src="images/user1.png" class="img-circle" alt="User Image"></span><span class="username">'+ obj.author+'</span><span class="date"><i class="fa fa-calendar-o"></i><span> '+ new Date(obj.created_at).getDate() +'/'+ new Date(obj.created_at).getMonth() +'/'+ new Date(obj.created_at).getFullYear()+'</span></span></figcaption>';
          panelDiv.appendChild(panelHeadingDiv);
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
