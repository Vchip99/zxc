<!DOCTYPE html>
<html>
<head>
	<title>your current plan bill</title>
</head>
<body>
	<p>Dear {{$client}},</p>
		<p> New bill Rs. {{$price}} has been generated for your current plan.Your current plan is {{ $plan }}.Please pay your bill with in one month otherwise your plan will be De-graded to basic(free) plan. </p>
		<p>Following are the details:</p>
		<div>
			<p>Plan : {{ $plan }}</p>
			<p>Price : {{ $price }} </p>
			<p>Start Date : {{ $startDate }} </p>
			<p>End Date : {{ $endDate }} </p>
		</div>
		<p>Please Follow below steps to do payment:</p>
		<p>1.login as a admin</p>
		<p>2.Go to Plans & Billing Menu</p>
		<p>3.a.Click on Billing menu and Click on pay to do payment</p>
		<p>3.b.Or Click on History menu and Click on pay to do payment</p>

	<p>Thanks and Regards</p>
	<p>Vchipedu</p>
</body>
</html>