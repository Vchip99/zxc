<!DOCTYPE html>
<html>
<head>
	<title>mail</title>
</head>
<body>
	<p>Hi Admin,</p>
		<p>we got an application from {{ $email }} for '<b>{{ $subject }}</b>'.</p>
		<p>Follwoing are the details:</p>
		<div>
			<p>Name : {{ $firstName }} {{ $lastName }}</p>
			<p>Company : {{ $company }} </p>
			<p>Email : {{ $email }} </p>
			<p>Address : {{ $address1 }} {{ $address2 }} </p>
			<p>City : {{ $city }} </p>
			<p>Zip Code : {{ $zip }} </p>
			<p>Country : {{ $country }} </p>
			<p>Phone : {{ $phone }} </p>
			<p>Gender : @if( 0 == $gender ) Male  @else Femail @endif</p>
		</div>
</body>
</html>