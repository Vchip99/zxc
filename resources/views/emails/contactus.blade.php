<!DOCTYPE html>
<html>
<head>
	<title>mail</title>
</head>
<body>
	<p>Hi Admin,</p>
		<p>The person {{ $name }} of email id {{ $email }} send mail as a contact us for subject '<b>{{ $subject }}</b>'.</p>
		<p>Follwoing are the details:</p>
		<div>
			<p>Name : {{ $name }}</p>
			<p>Email : {{ $email }} </p>
			<p>subject : {{ $subject }}</p>
			<p>message : {{ $bodyMessage }} </p>
		</div>

</body>
</html>