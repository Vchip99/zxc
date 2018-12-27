<!DOCTYPE html>
<html>
<head>
	<title>New Mentor Sign Up</title>
</head>
<body>
	<p>Hi Admin,</p>
		<p>we have a new mentor sign up from <b>{{ $name }}</b>.</p>
		<p>Following are the details:</p>
		<div>
			@if(!empty($name))
				<p>Name : {{ $name }} </p>
			@endif
			@if(!empty($email))
				<p>Email : {{ $email }} </p>
			@endif
			@if(!empty($mobile))
				<p>Mobile : {{ $mobile }} </p>
			@endif
		</div>
</body>
</html>
