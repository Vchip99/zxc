 <!DOCTYPE html>
<html lang="en">
<head>
  <title>ONLINE TEST SERIES</title>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  	<link href="{{asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
  	<link href="{{asset('css/jquery-confirm.min.css?ver=1.0')}}" rel="stylesheet"/>
  	<script src="{{asset('js/jquery.min.js?ver=1.0')}}"></script>
	<script src="{{asset('js/bootstrap.min.js?ver=1.0')}}"></script>
  	<script src="{{asset('js/jquery-confirm.min.js?ver=1.0')}}"></script>
  	<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  </script>

<style type="text/css">
	body{font-family:  Century Gothic, serif}
	#instr .panel{border-radius: 0px;}
	#instr .panel-heading{font-weight: bolder;}
	#instr .instruction-panel{overflow:auto;}
	/*#instr .panel-body{height: 680px;}*/
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
			.btn-unsolved{  background: #bce8f1;
			}
			h3{color: #c00000; font-size: 15px; font-weight: bolder;}

</style>
</head>
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
				<input type="hidden" name="mode"  id="mode" value=""/>
				<img src="{{ asset('images/user/user1.png') }}" class="img-responsive" alt="user" />
				<br/>
				<div class="form-group">
				<span><b>Name :</b>{{Auth::user()->name}}</span><br/>
				<span><b>Email :</b> {{Auth::user()->email}}</span>
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
						<li>1. The clock will be set at the server. The countdown timer at the right corner of the screen will display the remaining time available for you to complete the examination. When the timer reaches zero the examination ends by itself.</li>

						<li>2. The question palette displayed on the right side of the screen will show the status of each question using one of the following color:</li>
						<ul class="custom-list-style">
							<li><button type="button" class="btn btn-unsolved btn-circle"></button>You have not visited the question yet
							</li>
							<li><button type="button" class="btn btn-danger btn-circle"></button>You have not answered the question.</li>
							<li><button type="button" class="btn btn-success btn-circle"></button>You have answered the question.</li>
							<li><button type="button" class="btn btn-voilate btn-circle"></button>Marked the question for review.</li>
						</ul>
						<li>3. The Marked for Review status for a question simply indicates that you would like to look at that question again. If a question is answered, but marked for review, then the answer to that question will be considered in the evaluation, unless the status is modified by the candidate.
						</li>
					</ul>
	                </div>
	                <div class="panel-footer">
	                	@if($isVerificationCode)
	                		<label >Please enter correct verification code and verification code can use only once.</label><br>
	                		<label class="checkbox-inline">Verification Code:</label>
	                		<input type="text" name="verification_code" id="verification_code" value="" class="mrgn" placeholder="Verification Code" required>
							<a class="btn btn-primary next-btn" onClick="checkVerificationCode();" >Next</a>
	                	@else
	                		<button type="submit" class="next-btn">Next</button>
						@endif
			  		</div>
	        </div>
	    </div>

	</div>
</div>
		<input type="hidden" id="category_id" name="category_id" value="{{$categoryId}}">
	    <input type="hidden" id="sub_category_id" name="sub_category_id" value="{{$subcategoryId}}">
	    <input type="hidden" id="subject_id" name="subject_id" value="{{$subjectId}}">
	    <input type="hidden" id="paper_id" name="paper_id" value="{{$paperId}}">
</div>
</form>
</div>
</div>
<script>
	function checkVerificationCode(){
		var verificationCode = document.getElementById('verification_code').value;
		var paperId = document.getElementById('paper_id').value;
		if(verificationCode && paperId){
			$.ajax({
	          method:'POST',
	          url: "{{url('checkVerificationCode')}}",
	          data:{verification_code:verificationCode,paper_id:paperId}
	        }).done(function( msg ) {
	          if('true' == msg){
	            document.instructionForm.submit();
	          } else {
	            $.alert({
				    title: 'Alert!',
				    content: 'Please enter correct verification code.',
				});
	          }
	        });
		} else {
			$.alert({
			    title: 'Alert!',
			    content: 'Please enter verification code.',
			});
		}
	}
</script>
</body>
</html>