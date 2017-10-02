<!DOCTYPE html>
<html>
<head>
	<title>mail</title>
</head>
<body>
	<p>Hi Admin,</p>
		@if(!empty($subject))
			<p>The person {{ $name }} of email id {{ $email }} send mail as a contact us for subject '<b>{{ $subject }}</b>'.</p>
		@else
			<p>The person {{ $name }} of email id {{ $email }} send a mail.</p>
		@endif
		<p>Follwoing are the details:</p>
		<div>
			<p>Name : {{ $name }}</p>
			<p>Email : {{ $email }} </p>
			@if(!empty($subject))
				<p>subject : {{ $subject }}</p>
			@endif
			@if(!empty($phone))
				<p>phone : {{ $phone }}</p>
			@endif
			<p>message : {{ $bodyMessage }} </p>
		</div>

</body>
</html>