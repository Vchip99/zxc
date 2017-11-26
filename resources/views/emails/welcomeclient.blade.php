<!DOCTYPE html>
<html>
<head>
	<title>Welcome to Digital Education</title>
</head>
<body>
	<p>Hello from Vchipedu,</p>
		<p>Congratulations! You have successfully signup for vchipedu.</p>
		<p>Follwoing are the details:</p>
		<div>
			@if(!empty($name))
				<p>Name : {{ $name }} </p>
			@endif
			@if(!empty($email))
				<p>Email : {{ $email }} </p>
			@endif
			@if(!empty($subdomain))
				<p>Subdomain : {{ $subdomain }} </p>
			@endif
		</div>
		<p>Please login with credentails on above url(subdomain). </p>
		<p>Please click on <a href="{{ $subdomain }}">your url</a> to go to your website. </p>
		<p>We are delighted to welcome you to Vchipedu.</p>

</body>
</html>
