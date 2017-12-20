<!DOCTYPE html>
<html>
<head>
	<title>Virtual Placement Drive</title>
</head>
<body>
	<p>Hi Admin,</p>
		<p>we have a mail about virtual placement drive.</p>
		<p>Follwoing are the details:</p>
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
			@if(!empty($org_name))
				<p>Organisation Name : {{ $org_name }} </p>
			@endif
			@if(!empty($subject))
				<p>Subject : {{ $subject }} </p>
			@endif
			@if(!empty($text_message))
				<p>Message : {{ $text_message }} </p>
			@endif
		</div>
		<p>Please reply. </p>
		<p>Thanks. </p>
</body>
</html>
