@extends('layouts.master')
@section('header-title')
  <title>Contact | Vchip-edu</title>
@stop
@section('header-css')
    @include('layouts.home-css')
@stop
@section('header-js')
    @include('layouts.home-js')
@stop
@section('content')
    @include('header.header_menu')
<section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single" >
        <div class="vchip-background-img">
            <figure>
                <img src="{{ asset('images/contact-us.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Contact Us" />
            </figure>
        </div>
        <div class="vchip-background-content">
            <h2 class="animated bounceInLeft">Digital Education</h2>
        </div>
    </div>
</section>
<section id="" class="v_container ">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    @if(count($errors) > 0)
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                @if('verify_email' == $error)
                  <li><a href="{{ url('verifyAccount')}}">Click here to resend verification email</a></li>
                @else
                  <li>{{ $error }}</li>
                @endif
              @endforeach
          </ul>
      </div>
    @endif
 <h2 class="v_h2_title text-center"> Contact us</h2>
 <hr class="section-dash-dark"/>
    <div class="container">
      <div class="row">
        <form class="form-horizontal" method="post" action="{{ url('sendContactUsMail')}}" enctype="multipart/form-data">
            <div class="v_contactus-area">
                <div class="well">
                    {{ csrf_field()}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">
                                    Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" required="required" />
                            </div>
                            <div class="form-group">
                                <label for="email">
                                    Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required="required" /></div>
                            </div>
                            <div class="form-group">
                                <label for="subject">
                                    Subject</label>
                                <select id="subject" name="subject" class="form-control" required="required">
                                    <option value="na" selected="">Choose One:</option>
                                    <option value="service">General Customer Service</option>
                                    <option value="suggestions">Suggestions</option>
                                    <option value="product">Product Support</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">
                                    Message</label>
                                <textarea name="message" id="message" class="form-control" rows="9" cols="25" required="required"
                                    placeholder="Message"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary pull-right" id="btnContactUs" title="Send Message">
                                Send Message</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
  </section>
@stop
@section('footer')
    @include('footer.footer')
@stop