<!DOCTYPE html>
<html>
<head>
	<title>New Registration</title>
</head>
<body>
	<p>Hi Admin,</p>
		<p>we have a new registration from <b>{{ $name }}</b>. please verify to <b>{{ $name }}</b> to access our digital platform.</p>
		<p>Follwoing are the details:</p>
		<div>
			@if(!empty($name))
				<p>Name : {{ $name }} </p>
			@endif
			@if(!empty($email))
				<p>Email : {{ $email }} </p>
			@endif
		</div>
</body>
</html>
