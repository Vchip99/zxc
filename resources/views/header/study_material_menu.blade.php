<nav class=" navbar-lower navbar navbar-default  shrink navbar-fixed-top" style=" z-index: 1030; ">
  <div class="container">
    <div class="pull-left" >
      <a class="navbar-brand pull-left" href="{{ url('/')}}">
        <span>Vchip-edu</span>
      </a>
    </div>
    <div class="navbar-header pull-right">
      <button type="button" class="navbar-toggle pull-left" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        @if(count($categories) > 0)
          @foreach($categories as $categoryId => $category)
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Digital Education">{{$category}} <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" role="menu">
                <div class="navbar-content">
                @if(count($subcategories[$categoryId] > 0))
                  @foreach($subcategories[$categoryId] as $subcategoryId => $subcategoryArr)
                    <li><a href="{{url('study-material')}}/{{$subcategoryId}}/{{$subcategoryArr['subject']}}/{{$subcategoryArr['topic_id']}}"> {{$subcategoryArr['name']}}</a></li>
                    <li class="divider"></li>
                  @endforeach
                @endif
                </div>
              </ul>
            </li>
          @endforeach
        @endif
      </ul>
    </div>
  </div>
</nav>
<script type="text/javascript">
  $(function () {
  $('.navbar-collapse ul li a:not(.dropdown-toggle)').click(function () {
    $('.navbar-toggle:visible').click();
  });
});
if ($(window).width() > 1201) {
  $('.dropdown').hover(function() {
    $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
  }, function() {
    $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
  });
}
</script>