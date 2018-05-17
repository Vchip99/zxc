@extends('layouts.master')
@section('header')
@include('layouts.home-css')
<link href="css/background.css" rel="stylesheet"/>
<link href="css/classconnect.css" rel="stylesheet"/>
<link href="css/classpad.css" rel="stylesheet"/>
<link id="cpswitch" href="css/slideanim.css" rel="stylesheet" />
<script type="text/javascript" src="js/slideanim.js"></script>
<style type="text/css">
/*==================
 ERROR PAGE
 ====================*/
 #v_car{
 	margin-top: 100px;

 }
 .modal-title{
 	text-align: center;
 	background: #01bafd;
 }

 button.accordion {

 	background-color: #eee;
 	color: #0077b3;
 	cursor: pointer;
 	width: 100%;
 	border-left: 3px solid #0077b3;
 	border-radius: 10px;
 	text-align: left;
 	outline: none;
 	font-size: 15px;
 	transition: 0.4s;
 	padding: 12px 20px 12px 10px;
 	font-size: 1.1em;

 }
 button.accordion.active:hover{
 	background-color:#0077b3;
 	color: white;
 }

 button.accordion:hover {
 	background-color: #ddd;
 }

 button.accordion:after {

 	content: '\002B';/* Unicode character for "plus" sign (+) */
 	font-weight: bold;
 	float: right;
 	margin-left: 5px;

 }

 button.accordion.active:after {
 	content: "\2212";/* Unicode character for "minus" sign (-) */
 }

 div.panel {
 	padding:0px;
 	background-color: white;
 	max-height: 0;
 	overflow: hidden;
 	transition: max-height 0.2s ease-out;
 	margin:10px;
 	color:#A9A9A9;

 }
 div.panel .panel-content strong{
 	color: #1E90FF;
 	margin-right: 10px;
 }
/*==================
 heding SECTION
 ====================*/
 #v_ourPartner {
 	display: inline;
 	float: left;
 	padding: 100px 0;
 	width: 100%;
 }
 #v_ourPartner .v_ourPartner-area {
 	display: inline;
 	float: left;
 	width: 100%;
 	text-align: left;
 	margin-bottom: 80px;
 }
 #v_ourPartner .v_ourPartner-area p {
 	line-height: 1.5;
 	font-size: 20px;
 	color:#A9A9A9;

 }
 #v_ourPartner .v_ourPartner-area h2{
 	font-size: 30px;
 	line-height: 1.7;
 	color:#01bafd;
 }
/*==================
 ABOUT SECTION
 ====================*/
 #mu-about-us {
 	display: inline;
 	float: left;
 	width: 100%;
 	padding: 100px 0;
 }
 #mu-about-us .mu-about-us-area {
 	display: inline;
 	float: left;
 	width: 100%;
 }
 #mu-about-us .mu-about-us-area .mu-about-us-left {
 	display: inline;
 	float: left;
 	width: 100%;
 }
 #mu-about-us .mu-about-us-area .mu-about-us-left h2 {
 	font-size: 25px;
 	margin-bottom: 20px;
 	text-align: left;
 	color:#01bafd;
 }
 #mu-about-us .mu-about-us-area .mu-about-us-left ul {
 	margin-left: 25px;
 	margin-bottom: 15px;
 }
 #mu-about-us .mu-about-us-area .mu-about-us-left ul li  {
 	line-height: 30px;
 	list-style: circle;
 }
 #mu-about-us .mu-about-us-area .mu-about-us-right {
 	display: inline;
 	float: left;
 	width: 100%;
 	display: block;
 	width: 100%;
 	background-color: #ccc;
 	border-radius: 10px;
 	box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);

 }
 #mu-about-us .mu-about-us-area .mu-about-us-right {
 	display: block;
 	width: 100%;
 	position: relative;

 }
 #mu-about-us .mu-about-us-area .mu-about-us-right img {
 	width: 100%;
 	height: 350px;
 	border-radius: 10px;

 }
 #mu-about-us .mu-about-us-area .mu-about-us-right :after {
 	background-color: rgba(0, 0, 0, 0.8);
 	border-radius: 10px;

 }

 /*==== about us dynamic video player ====*/
 #about-video-popup {
 	background-color: rgba(0, 0, 0, 0.9);
 	position: fixed;
 	left: 0;
 	top: 0;
 	right: 0;
 	text-align: center;
 	bottom: 0;
 	z-index: 99999;

 }
 #about-video-popup span {
 	color: #fff;
 	cursor: pointer;
 	float: right;
 	font-size: 30px;
 	margin-right: 50px;
 	margin-top: 50px;

 }
 #about-video-popup iframe {
 	background: center center no-repeat;
 	margin: 10% auto;
 	width: 650px;
 	height: 450px;

 }

 .hr{
 	border: 0;
 	margin-top: 70px;
 	height: 1px;

 	background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgb(192,192,192), rgba(0, 0, 0, 0));
 }
 /*text animation one by one*/
 .v_ourPartner-area .font_size h2{
 	height: 50px;
 	float: left;
 	margin-right: 0.3em;
 }
 b {
 	float: left;
 	overflow: hidden;
 	position: relative;
 	height: 50px;
 }
 .span1 {
 	display: inline-block;
 	color: #e74c3c;
 	position: relative;
 	white-space: nowrap;
 	top: 0;
 	left: 0;
 	font-size: 30px;
 	padding: 2em 0em;
 	-webkit-animation: move 5s;
 	-webkit-animation-iteration-count: infinite;
 	-webkit-animation-delay: 1s;
 }

 @keyframes move {
 	0%  { top: 0px; }
 	20% { top: -50px; }
 	40% { top: -100px; }
 	60% { top: -150px; }
 	80% { top: -200px; }
 }
 .form-horizontal{
 	background: #eee;
 }
</style>
@include('header.header_menu')
<section id="mu-background">
	<div class="mu-background-single">
		<div class="mu-background-img">
			<figure><img src="{{ asset('images/career.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed"/></figure>
		</div>
		<div class="mu-background-content"></div>
	</div>
</section>
<section id="v_ourPartner">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="v_ourPartner-area">
					<div class="font_size">
						<h2 >WE ARE HIRING<em> tutors for!</em></h2>
						<b>
							<span class="span1">
								Data Science <br />
								Quantitative Aptitude <br />
								Reasoning<br />
								Android App Development
							</span>
						</b>
					</div>
					<div class="hr"></div><br />
					<div><em>Performance of a company is directly proportional to his team...</em></div><br />
					<p> Vchip-edu have open, engaging, fun, employee-centric work environment with competitive compensation and rewards and fast-track programs for high potential employees. We are always be searching for new generation of entrepreneur. Our team is well balanced of both newly graduate and  experienced guys. We believe that newcomers known the exact market demand and our experienced guys fulfill it. We always welcome new ideas, technologically and ready to change. </p>

					<p>Our main motive is bridging between educational organization  and Industry along with Digital village. So our most of tutors are belong to industries.</p>
					<div class="hr"></div>
				</div>
			</div>

		</div>
		<div class="row">
			<!-- col-8 aboutus -->
			<div class="col-md-12" id="mu-about-us">
				<div class="mu-about-us-area">
					<div class="row">
						<div class="col-lg-6 col-md-6">
							<div class="mu-about-us-left">
								<!-- Start Title -->
								<div class="mu-title">
									<h2>Features of Vchip-edu</h2>
								</div>
								<!-- End Title -->

								<ul>

									<li>Dynamic, healthy and happy Working Environment</li>
									<li>Work on innovative and challenging topics</li>
									<li>Innovative thinking inspires you to dream beyond boundaries</li>
									<li>Involved in decision making , give filling of ownership</li>
									<li>Your ideas and suggestions are always welcome</li>
									<li>Great working culture</li>
									<li>Make career while contributing learning and education</li>
								</ul>

							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="mu-about-us-right">
								<!-- <a id="mu-abtus-video" href="" target="mutube-video">
							</a> -->
							<img src="{{ asset('images/item3.jpg')}}" alt="img">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12 v_hr">
		<div class="hr"></div>
	</div>
	<div class="row " id="v_car">
		<!-- col-8 aboutus -->
		<div class="col-md-12">
			<button class="accordion">DATA SCIENCE</button>
			<div class="panel">
				<div class="panel-content">
					<p class="qualification">
						<strong>Qualification:</strong>Qualification BE/B.Tech(CS,IT,EXTC)
					</p>
					<p class="industryType">
						<strong>Industry Type:</strong>Education
						<strong>Functional Area:  </strong> Tutor
					</p>
					<p>• Minimum 5 year of industrial experience in the field of Data Analysis.</p>
					<p>• Having Exposure in Basic python, Python Data Analysis Library — pandas, SQL Server.</p>
					<p>• Good communication skills.</p>
					<p>• To participate in interactive discussion.</p>
					<p class="location"><strong>Location: </strong>Pune </p>
					<p><a class="btn btn-primary push-bottom" data-toggle="modal" data-target="#myModal" >Apply Now</a>
					</p>

				</div>

			</div>

			<button class="accordion">QUANTITATIVE APTITUDE</button>
			<div class="panel">
				<div class="panel-content">
					<p class="qualification">
						<strong>Qualification:</strong>Qualification MBA(From IIM)
					</p>
					<p class="industryType">
						<strong>Industry Type:</strong>Education
						<strong>Functional Area:  </strong> Tutor
					</p>
					<p>• Minimum 2 year of industrial experience in the field of Analysis </p>
					<p>• Having very good AIR in CAT exam.</p>
					<p>• Good communication skills.</p>
					<p>• To participate in interactive discussion.</p>
					<p class="location"><strong>Location: </strong>Pune </p>
					<p><a class="btn btn-primary push-bottom" data-toggle="modal" data-target="#myModal" >Apply Now</a>
					</p>
				</div>

			</div>
			<button class="accordion">REASONING</button>
			<div class="panel">
				<div class="panel-content">
					<p class="qualification">
						<strong>Qualification:</strong>Qualification MBA(From IIM)
					</p>
					<p class="industryType">
						<strong>Industry Type:</strong>Education
						<strong>Functional Area:  </strong> Tutor
					</p>
					<p>• Minimum 2 year of industrial experience in the field of Analysis </p>
					<p>• Having very good AIR in CAT exam.</p>
					<p>• Good communication skills.</p>
					<p>• To participate in interactive discussion.</p>
					<p class="location"><strong>Location: </strong>Pune </p>
					<p><a class="btn btn-primary push-bottom" data-toggle="modal" data-target="#myModal" >Apply Now</a>
					</p>
				</div>

			</div>
			<button class="accordion">ANDROID APP DEVELOPMENT</button>
			<div class="panel">
				<div class="panel-content">
					<p class="qualification">
						<strong>Qualification:</strong>Qualification BE/B.Tech(CS,IT,EXTC)
					</p>
					<p class="industryType">
						<strong>Industry Type:</strong>Education
						<strong>Functional Area:  </strong> Tutor
					</p>
					<p>• Minimum 5 year of industrial experience in the field of Android app development </p>
					<p>• Having very good AIR in CAT exam.</p>
					<p>• Good communication skills.</p>
					<p>• To participate in interactive discussion.</p>
					<p class="location"><strong>Location: </strong>Pune </p>
					<p><a class="btn btn-primary push-bottom" data-toggle="modal" data-target="#myModal" >Apply Now</a>
					</p>
				</div>

			</div>
			<!--Application  Form  -->
			<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Application Form</h4>
						</div>
						<div class="modal-body">
							<form class="form-horizontal" >
								<fieldset>

									<!-- Form Name -->
									<legend>Vchip</legend>

									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-4 control-label" for="fn">First name</label>
										<div class="col-md-4">
											<input id="fn" name="fn" type="text" placeholder="first name" class="form-control input-md" required="">

										</div>
									</div>

									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-4 control-label" for="ln">Last name</label>
										<div class="col-md-4">
											<input id="ln" name="ln" type="text" placeholder="last name" class="form-control input-md" required="">

										</div>
									</div>

									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-4 control-label" for="cmpny">Company</label>
										<div class="col-md-4">
											<input id="cmpny" name="cmpny" type="text" placeholder="company" class="form-control input-md" required="">

										</div>
									</div>

									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-4 control-label" for="email">Email</label>
										<div class="col-md-4">
											<input id="email" name="email" type="text" placeholder="email" class="form-control input-md" required="">

										</div>
									</div>

									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-4 control-label" for="add1">Address 1</label>
										<div class="col-md-4">
											<input id="add1" name="add1" type="text" placeholder="" class="form-control input-md" required="">

										</div>
									</div>

									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-4 control-label" for="add2">Address 2</label>
										<div class="col-md-4">
											<input id="add2" name="add2" type="text" placeholder="" class="form-control input-md">

										</div>
									</div>

									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-4 control-label" for="city">City</label>
										<div class="col-md-4">
											<input id="city" name="city" type="text" placeholder="city" class="form-control input-md" required="">

										</div>
									</div>

									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-4 control-label" for="zip">Zip Code</label>
										<div class="col-md-4">
											<input id="zip" name="zip" type="text" placeholder="Zip Code" class="form-control input-md" required="">

										</div>
									</div>

									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-4 control-label" for="ctry">Country</label>
										<div class="col-md-4">
											<input id="ctry" name="ctry" type="text" placeholder="Country" class="form-control input-md" required="">

										</div>
									</div>

									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-4 control-label" for="phone">Text InputPhone</label>
										<div class="col-md-4">
											<input id="phone" name="phone" type="text" placeholder="Phone#" class="form-control input-md" required="">

										</div>
									</div>



									<!-- Multiple Radios (inline) -->
									<div class="form-group">
										<label class="col-md-4 control-label" for="Networking_Reception">Gender</label>
										<div class="col-md-4">
											<label class="radio-inline" for="Networking_Reception-0">
												<input type="radio" name="Networking_Reception" id="Networking_Reception-0" value="meet_yes" checked="checked">
												Male
											</label>
											<label class="radio-inline" for="Networking_Reception-1">
												<input type="radio" name="Networking_Reception" id="Networking_Reception-1" value="meet_no">
												Female
											</label>
										</div>
									</div>

									<!-- Select Basic -->
									<div class="form-group">
										<label class="col-md-4 control-label" for="selectbasic">Select Basic</label>
										<div class="col-md-4">
											<select id="selectbasic" name="selectbasic" class="form-control input-md">
												<option>Option one</option>
												<option>Option two</option>
											</select>
										</div>
									</div>




									<!-- Button -->
									<div class="form-group">
										<label class="col-md-4 control-label" for="submit"></label>
										<div class="col-md-4">
											<button id="submit" name="submit" class="btn btn-primary">SUBMIT</button>
										</div>
									</div>

								</fieldset>
							</form>

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>

			<!--ens Application  Form  -->

			<script>
				var acc = document.getElementsByClassName("accordion");
				var i;

				for (i = 0; i < acc.length; i++) {
					acc[i].onclick = function() {
						this.classList.toggle("active");
						var panel = this.nextElementSibling;
						if (panel.style.maxHeight){
							panel.style.maxHeight = null;
						} else {
							panel.style.maxHeight = panel.scrollHeight + "px";
						}
					}
				}
			</script>
			<!--for toltip-->
			<script>
				$(document).ready(function(){
					$('[data-toggle="tooltip"]').tooltip();
				});
			</script>
		</div>
	</div>
</div>

</section>
@stop
@section('footer')
@include('footer.footer')
@stop