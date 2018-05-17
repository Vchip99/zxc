<!DOCTYPE html>
<html>
<head>
	<title>Welcome to {{$domain }} Digital Education</title>
</head>
<body>
	<p>Hello from {{$domain }} ,</p>
		<p>Congratulations! You have successfully signup for {{$domain }}.</p>
		<p>Following are the details:</p>
		<div>
			@if(!empty($email))
				<p>Email/User Id : {{ $email }} </p>
			@endif
			@if(!empty($password))
				<p>Password : {{ $password }} </p>
			@endif
		</div>
		<p>Please login with credentails using this url- {{ $url }}. Or</p>
		<p>Please click on <a href="{{ $url }}">this url</a> to go to our website. </p>
		<p>We are delighted to welcome you to {{$domain }} .</p>

</body>
</html>