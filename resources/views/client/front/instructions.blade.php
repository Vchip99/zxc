 <!DOCTYPE html>
<html lang="en">
<head>
  <title>ONLINE TEST SERIES</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="{{asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
  <link href="{{asset('css/jquery-confirm.min.css?ver=1.0')}}" rel="stylesheet"/>
  <script src="{{asset('js/jquery.min.js?ver=1.0')}}"></script>
  <script src="{{asset('js/bootstrap.min.js?ver=1.0')}}"></script>
  <script src="{{asset('js/jquery-confirm.min.js?ver=1.0')}}"></script>
</head>
<style type="text/css">
body{font-family:  Century Gothic, serif}
	#instr .panel{border-radius: 0px;}
	#instr .panel-heading{font-weight: bolder;}
	#instr .instruction-panel{overflow:auto;}
	#instr .panel-body{height: 680px;}
	#iner-instr .panel{overflow: hidden;}
	#iner-instr .panel-body{height: 620px; overflow: auto;}
	.user-pic{text-align: left; padding: 10px;}
	.user-pic img{  margin: 0px auto;
	width: 150px;
	height: 150px; }
	#user .panel{ margin-top: 50%; }
	@media (max-width: 1350px){
		.user-pic{text-align: left; font-size:  10px; padding: 10px;}
		.user-pic img{  margin: 0px auto;
	width: 60px;
	height: 60px; }
	}
	@media (max-width: 993px){
        img{ width: 60px;
		   	height: 60px; margin: 0px auto;}
		#iner-instr .panel-body{height: 400px; overflow: auto;}
		.user-pic{text-align: center; padding: 10px;}
		#user .panel{ margin-top: 0%; }
		#user .panel{ margin-right: -15px;
			margin-left: -15px; }
	}
	@media (max-width: 320px){
		   /*	#instr .panel-body{height: 370px;}
		   #iner-instr .panel-body{height: 150px; overflow: auto;}*/
		   .next-btn{margin-left:  40%;}
		   .user-pic{text-align: center;}
/*		   .user-pic img{ width: 60px;
		   	height: 60px; margin: 0px auto;}*/
		   	ul{ margin-left: 0px;
		   		padding-left: 0px; }
		   	}
		   	@media (max-width: 480px){
		   /*	#instr .panel-body{height: 450px;}
		   #iner-instr .panel-body{height: 150px; overflow: auto;}*/

		   .user-pic{text-align: center;}
			/*.user-pic img{ width: 60px;
				height: 60px; margin: 0px auto;}*/
			}
			#instr .mrgn {
				margin-top: 2px;
			}
			ul.list3 {
				counter-reset: li;
				list-style: none outside none;
				margin-bottom: 20px;

			}
			ul.list3 li {
				position: relative;
				margin: 17px 0 5px 0px;

			}

			ul.custom-list-style {
				list-style: none;
			}
			ul.custom-list-style li {
				font-size: 15px;

			}
			.btn-voilate{  background: #7f43c4;
			}
			h3{color: #c00000; font-size: 15px; font-weight: bolder;}
</style>
<body>
	<div class="container-fluid" id="instr">
		<div class="row">
			<form name = "instructionForm" method="post" action="{{url('questions')}}">
			{{ csrf_field() }}
			<div class="panel panel-primary">
	            <div class="panel-heading text-center">ONLINE TEST</div>
				<div class="panel-body">
					<div class="col-lg-2 col-md-2" id="user">
						<div class="panel panel-info">
			                <div class="panel-heading">
			                    <h3 class="panel-title text-center">USER</h3>
			                </div>
							<div class="user-pic ">
								<img src="{{ asset('images/user/user.png') }}" class="img-responsive" alt="user" />
								<br/>
								<div class="form-group">
									<span><b>Name :</b>{{Auth::guard('clientuser')->user()->name}}</span><br/>
									<span><b>Email :</b> {{Auth::guard('clientuser')->user()->email}}</span>
								</div>
							</div>
						</div><br/>
					</div>
					<div class="col-lg-10 col-md-10 ">
						<div class="row" id="iner-instr">
							<div class="panel panel-info">
				                <div class="panel-heading">
				                    <h3 class="panel-title text-center">General Instructions</h3>
				                </div>
	            				<div class="panel-body">
									<ul class="list3">
										<li>1. The clock will be set at the server. The countdown timer at the right corner of the screen will display the remaining time available for you to complete the examination. When the timer reaches zero the examination ends by itself. You need to terminate the examination or submit the paper.</li>
										<li>2. The question palette displayed on the right side of the screen will show the status of each question using one of the following color:
											<ul class="custom-list-style">
												<li><button type="button" class="btn btn-default btn-circle"></button>You have not visited the question yet
												</li>
												<li><button type="button" class="btn btn-danger btn-circle"></button>You have not answered the question.</li>
												<li><button type="button" class="btn btn-success btn-circle"></button>You have answered the question.</li>
												<li><button type="button" class="btn btn-voilate btn-circle"></button>Marked the question for review.</li>
											</ul>
										</li>
										<li>3. The Marked for Review status for a question simply indicates that you would like to look at that question again. If a question is answered, but marked for review, then the answer to that question will be considered in the evaluation, unless the status is modified by the candidate.
										</li>
										<h3> Navigating to a Question </h3>
						 				<li>4. To answer a question, do the following:
											<ul class="custom-list-style-1">
												<li>Click on the question number in the Question Palette to go to that question directly.</li>
										        <li>Select an answer for a multiple choice type question by clicking on bubble placed before the 4 choices A, B, C, D. Use the virtual numeric keypad to enter a number as an answer to a numerical type question.</li>

										        <li>Click on Save and Next to save your answer for the current question and then go to the next question.</li>

										        <li>Click on Mark for Review and Next to save your answer for the current question, mark it for review, and then go to the next question.</li>
											</ul>
										</li>
										<li>5. Caution: Note that your answer for the current question will not be saved, if you navigate to another question directly by clicking on its question number.
										You can view all the questions by clicking on the Question Paper button. This feature is provided so that you can see the entire question paper at a glance. Note that the options for multiple choice type questions will not be shown.</li>
										<h3> Answering a Question</h3>
						    			<li>6. Procedure for answering a multiple choice type question:
										    <ul class="custom-list-style-1">
										      	<li> To select your answer, click on the button of one of the options A, B, C, D</li>
										        <li>To deselect your chosen answer, click on the button of the chosen option again or click on the Clear Response button</li>
										       <li> To change your chosen answer, click on the button of another option</li>
										       <li> To save your answer, you MUST click on the Save & Next button</li>
										       <li> To mark the question for review, click on the Mark for Review & Next button. If an answer is selected for a question that is Marked for Review, that answer will be considered in the evaluation.</li>
											</ul>
										</li>
						   				<li>7.  Procedure for answering a numerical answer type question:
											<ul class="custom-list-style-1">
											    <li>To enter a number as your answer, use the virtual numerical keypad</li>
											    <li>In Numerical Type Questions, you are limited to answer till two decimal points.</li>
											    <li>A fraction (eg. -0.3 or -.3) can be entered as an answer with or without "0" before the decimal point</li>
											    <li>To clear your answer, click on the Clear Response button
													<ul class="custom-list-style-1">
												    	<li> To save your answer, you MUST click on the Save & Next button</li>
												        <li> To mark the question for review, click on the Mark for Review and Next button. If an answer is entered for a question that is Marked for Review, that answer will be considered in the evaluation.</li>
												        <li> To change your answer to a question that has already been answered, first select that question for answering and then follow the procedure for answering that type of question.</li>
												        <li>Note that ONLY Questions for which answers are saved or MARKED FOR REVIEW after answering will be considered for evaluation</li>
												    </ul>
												</li>
											</ul>
										</li>
										<h3>8. Sections</h3>
										<ul class="custom-list-style-1">
											<li>a. Sections in the question paper will be displayed in the top bar of the screen. Questions in a section can be viewed by clicking on the name of that Section. The Section you are currently viewing will be highlighted</li>
											<li>b. After clicking on Save & the Next button for the last question in a Section, you will be automatically taken to the first question of the next Section in a sequence.</li>
											<li>c. You can move the mouse cursor over the name of the Section to view the answering status of that Section.</li>
											<li>d. You can shuffle between different Sections or change the optional Sections any number of times.</li>
										</ul>
										<h3>9. Calculators</h3>
										Online Scientific Calculators will be available in GATE Online Test Platform.
										<h3>Disclaimer:</h3>
											This Online Test Series is designed by studying and surveying the previous platforms of the respective examinations. And efforts are made to replicate the actual exam patterns in all the aspects. However ONLINE TEST will not be responsible for any changes in exam pattern or functionality updates in the actual examinations.
										<h3>Declaration:</h3>
											I have read and understood all the above mentioned instructions. Also, I confirm that at the start of the examination all the computer hardware and internet connection are in proper working condition.
									</ul>
	                			</div>
	        				</div>
	    				</div>
					</div>
				</div>

			  	<div class="panel-footer">
			  		<div class="row">
					  	<div class="col-md-6 col-sm-6 col-xs-12">
							<label class="checkbox-inline"><input type="checkbox" name="checkMe" value="" class="mrgn" id="checkMe">I am agree all terms and conditions.</label>
							<button type="button"  class=" next-btn" onClick="return checkCondition();" >Next</button>
					  	</div>
			  			<div class="col-md-6 col-sm-6 col-xs-12 "></div>
			  		</div>
			  	</div>
			</div>
			<input type="hidden" id="category_id" name="category_id" value="{{$categoryId}}">
		    <input type="hidden" id="sub_category_id" name="sub_category_id" value="{{$subcategoryId}}">
		    <input type="hidden" id="subject_id" name="subject_id" value="{{$subjectId}}">
		    <input type="hidden" id="paper_id" name="paper_id" value="{{$paperId}}">
			</form>
		</div>
	</div>
	<script>
	function checkCondition(){
		if(document.getElementById('checkMe').checked == true){
			document.instructionForm.submit();
		} else {
			$.alert({
			    title: 'Alert!',
			    content: 'Please mark Checkbox.',
			});
			return false;
		}
	}
</script>
</body>
</html>