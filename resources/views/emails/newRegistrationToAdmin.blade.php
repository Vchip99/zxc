<!DOCTYPE html>
<html>
<head>
	<title>New Registration</title>
</head>
<body>
	<p>Hi Admin,</p>
		<p>we have a new registration from <b>{{ $name }}</b>.</p>
		<p>Following are the details:</p>
		<div>
			@if(!empty($name))
				<p>Name : {{ $name }} </p>
			@endif
			@if(!empty($email))
				<p>Email : {{ $email }} </p>
			@endif
			@if(!empty($userType))
				<p>User Type : {{ $userType }} </p>
			@endif
			@if(!empty($degree))
				<p>Degree : {{ $degree }} </p>
			@endif
			@if(!empty($college))
				<p>College : {{ $college }} </p>
			@endif
			@if(!empty($department))
				<p>Department : {{ $department }} </p>
			@endif
			@if(!empty($rollNo))
				<p>Roll No : {{ $rollNo }} </p>
			@endif
			@if(!empty($otherSource))
				<p>Other Source : {{ $otherSource }} </p>
			@endif
			@if(!empty($domain))
				<p>Domain : {{ $domain }} </p>
			@endif
			@if(!empty($subdomain))
				<p>Subdomain : {{ $subdomain }} </p>
			@endif
		</div>
</body>
</html>
