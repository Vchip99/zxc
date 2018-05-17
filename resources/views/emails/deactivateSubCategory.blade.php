<!DOCTYPE html>
<html>
<head>
	<title>DeActivate Purchased SubCategory</title>
</head>
<body>
	<p>Dear {{$client}},</p>
		<p>Your purchased sub category has been de-activated due to completion of sub category availability duration.You can purchase again this or others sub category.</p>
		<p>Following are the details:</p>
		<div>
			<p>Sub Category : {{ $subCategory }}</p>
			<p>Start Date : {{ $startDate }} </p>
			<p>End Date : {{ $endDate }} </p>
		</div>
		<p>Please Follow below steps to purchase sub category:</p>
		<p>1.login as a admin</p>
		<p>2.Go to Market Place Menu</p>
		<p>3.a.Click on Pay Now to do payment</p>
		<p>3.b.Or Click on Details and Click on Pay Now to do payment</p>

	<p>Thanks and Regards</p>
	<p>Vchipedu</p>
</body>
</html>