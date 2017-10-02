@extends('layouts.master')
@section('header-title')
  <title>Contact | Vchip-edu</title>
@stop
@section('header-css')
	@include('layouts.home-css')
@stop
@section('header-js')
	@include('header.header_menu')
	@include('layouts.home-js')
@stop
@section('content')
<section id="vchip-background" class="mrgn_60_btm">
	<div class="vchip-background-single" >
		<div class="vchip-background-img">
			<figure>
				<img src="{{ asset('images/contact-us.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="contact us" />
			</figure>
		</div>
		<div class="vchip-background-content">
		</div>
	</div>
</section>
<section id="" class="v_container ">
 <h2 class="v_h2_title text-center"> Verify Email</h2>
 <hr class="section-dash-dark"/>

    <div class="container">
      <div class="row">
          <div class="v_contactus-area col-md-10">
          	@if (count($errors) > 0)
			  <div class="alert alert-danger">
			      <ul>
			          @foreach ($errors->all() as $error)
			              <li>{{ $error }}</li>
			          @endforeach
			      </ul>
			  </div>
			@endif
            <div class="" >
                <form action="{{url('verifyEmail')}}" method="POST">
                {{ csrf_field() }}
                <div class="row text-center" >
                    <div class="col-md-8 col-md-offset-2" style="background: #eee; padding: 30px;">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span>
                                </span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter correct email id" required="required" /></div>
                        </div>
                        <div class="mrgn_20_btm">
                        <button type="submit" class="btn btn-primary pull-right" id="btnContactUs"> Send </button>
                        </div>
                    </div>

                </div>
                </form>
            </div>

    </div>
</div>
</div>
  </section>
@stop
@section('footer')
	@include('footer.footer')
@stop