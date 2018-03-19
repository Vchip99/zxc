<nav class="navbar navbar-default navbar-fixed-top shrink" role="navigation">
  <div class="container">
    <div class="pull-left">
      @php
        if('local' == \Config::get('app.env')){
          $homeUrl = 'https://localvchip.com/';
        } else {
          $homeUrl = 'https://vchipedu.com/';
        }
      @endphp
      <a class="navbar-brand pull-left" href="{{$homeUrl}}">
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
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li class="" title="HOME"><a href="{{ url('/')}}">HOME</a></li>
          <!-- <li class="" title="Digital-edu platform">
            <a href="{{ url('digitaleducation')}}"> Digital-Edu </a>
          </li> -->
          <li class="dropdown" title="Digital Marketing">
            <a href="{{ url('digitalmarketing')}}"> Digital Marketing </a>
            </a>
          </li>
          <li class="dropdown" title="Web & App Development">
            <a href="{{ url('webdevelopment')}}"> Web & App </a>
            </a>
          </li>
          <li class="dropdown" title="Price">
            <a href="{{ url('pricing')}}"> Price </a>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>