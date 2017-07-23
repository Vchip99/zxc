@extends('layouts.master')
@section('content')
	<div class="container">
		<Center></center>
		<div class="login-form">
			<h2 class="text-center" style="color:green"><b>GATE PAPER</b></h2>
			<div class="form-header">
				<i class="fa fa-user"></i>
			</div>
			<form id="login-form" method="post" class="form-signin" role="form" action="{{ url('home') }}">
				<input name="email" id="email" type="email" class="form-control" placeholder="Email address" autofocus> 
				<br/>
				<input name="password" id="password" type="password" class="form-control" placeholder="Password"> 
				<!-- <select class="form-control" name="technology" id="technology" required>
					<option value="">Select Technolgoy</option>										
				</select> -->
				<br/>				
				<button class="btn btn-block bt-login" type="submit">Sign in</button>
			</form>
			<div class="form-footer">
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6">
						<i class="fa fa-lock"></i>
						<a href=""> Forgot password? </a>
					
					</div>
					
					<div class="col-xs-6 col-sm-6 col-md-6">
						<i class="fa fa-check"></i>
						<!-- <a href=""> Sign Up </a> -->
						<a href="{{ url('/register') }}"> Sign Up </a>
					</div>
				</div>
			</div>
			<br/>
			<div class="text-center well">
				<p>Demo Email/Password: vchip@gmail.com/vchip</p>
			</div>
		</div>
	</div>
@endsection