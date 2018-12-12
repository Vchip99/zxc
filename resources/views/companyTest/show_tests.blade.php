@extends('layouts.master')
@section('header-title')
  <title>Online Test Series for GATE, CAT, Aptitude |Vchip-edu</title>
@stop
@section('header-css')
	@include('layouts.home-css')
	<link href="{{asset('css/exam.css?ver=1.0')}}" rel="stylesheet"/>
	<style type="text/css">
		.padding-body{
			padding: 15px !important;
		}
		@media screen and (max-width: 768px) {
		 .col-md-6{
		        width: 45%;
		        float: left;
		        margin-left: 20px;
		   }
		 }
		 @media screen and (max-width: 527px) {
		 .col-md-6{
		        width: 100%;
		        float: left;
		         margin-left: 0px;
		   }
		 }
		 .label-primary{margin-right: 2px;margin-left: 2px;}
		 .divider {
		  border-color: grey;
		  border-style: solid;
		  border-width: 0 0 1px;
		  height: 10px;
		  line-height: 20px;
		  text-align:center;
		  overflow: visible;
		}
		#all-result .panel-body {
		    max-height: 400px;
		    overflow: scroll;
		}
		.resultModels td, .resultModels th {
		    padding: 6px;
		    border: 1px solid #ccc;
		    text-align: left;
		}
		.resultModels th {
		    background: #333;
		    color: white;
		    font-weight: bold;
		}
  	</style>
  	<style type="text/css">
		@media screen and (max-width: 768px) {
		 .col-md-6{
		        width: 45%;
		        float: left;
		        margin-left: 20px;
		   }
		}
		@media screen and (max-width: 527px) {
		 .col-md-6{
		        width: 100%;
		        float: left;
		         margin-left: 0px;
		   }
	    }
		#members {
		    background: #eee !important;
		}

		.btn-primary:hover,
		.btn-primary:focus {
		    background-color: #108d6f;
		    border-color: #108d6f;
		    box-shadow: none;
		    outline: none;
		}

		#members .card {
		    border: none;
		    background: #ffffff;
		}

		.image-flip:hover .backside,
		.image-flip.hover .backside {
		    -webkit-transform: rotateY(0deg);
		    -moz-transform: rotateY(0deg);
		    -o-transform: rotateY(0deg);
		    -ms-transform: rotateY(0deg);
		    transform: rotateY(0deg);
		    border-radius: .25rem;
		}

		.image-flip:hover .frontside,
		.image-flip.hover .frontside {
		    -webkit-transform: rotateY(180deg);
		    -moz-transform: rotateY(180deg);
		    -o-transform: rotateY(180deg);
		    transform: rotateY(180deg);
		}

		.mainflip {
		    -webkit-transition: 1s;
		    -webkit-transform-style: preserve-3d;
		    -ms-transition: 1s;
		    -moz-transition: 1s;
		    -moz-transform: perspective(1000px);
		    -moz-transform-style: preserve-3d;
		    -ms-transform-style: preserve-3d;
		    transition: 1s;
		    transform-style: preserve-3d;
		    position: relative;
		}

		.frontside {
		    position: relative;
		    -webkit-transform: rotateY(0deg);
		    -ms-transform: rotateY(0deg);
		    z-index: 2;
		    margin-bottom: 30px;
		}

		.backside {
		    position: absolute;
		    top: 0;
		    left: 0;
		    background: white;
		    -webkit-transform: rotateY(-180deg);
		    -moz-transform: rotateY(-180deg);
		    -o-transform: rotateY(-180deg);
		    -ms-transform: rotateY(-180deg);
		    transform: rotateY(-180deg);
		    -webkit-box-shadow: 5px 7px 9px -4px rgb(158, 158, 158);
		    -moz-box-shadow: 5px 7px 9px -4px rgb(158, 158, 158);
		    box-shadow: 5px 7px 9px -4px rgb(158, 158, 158);
		}

		.frontside,
		.backside {
		    -webkit-backface-visibility: hidden;
		    -moz-backface-visibility: hidden;
		    -ms-backface-visibility: hidden;
		    backface-visibility: hidden;
		    -webkit-transition: 1s;
		    -webkit-transform-style: preserve-3d;
		    -moz-transition: 1s;
		    -moz-transform-style: preserve-3d;
		    -o-transition: 1s;
		    -o-transform-style: preserve-3d;
		    -ms-transition: 1s;
		    -ms-transform-style: preserve-3d;
		    transition: 1s;
		    transform-style: preserve-3d;
		}

		.frontside .card,
		.backside .card {
		    min-height: 312px;
		}

		.frontside .card .card-body img {
		    width: 120px;
		    height: 120px;
		    border-radius: 50%;
		}
		/*.iframe-container iframe{
	      width: 100% !important;
	    }*/
	    .iframe-container {
		    padding-bottom: 60%;
		    padding-top: 30px;
		    height: 0;
		    overflow: hidden;
		}
	  	.iframe-container iframe,
		  .iframe-container object,
		  .iframe-container embed {
		    position: absolute;
		    top: 0;
		    left: 0;
		    width: 100%;
		    height: 100%;
		}
	  	.modal-backdrop {
		  position: fixed;
		  top: 0;
		  right: 0;
		  bottom: 0;
		  left: 0;
		  z-index: -1;
		  background-color: #000000;
		}
  	</style>
@stop
@section('header-js')
	@include('layouts.home-js')
@stop
@section('content')
	@include('header.header_menu')
  	<section id="vchip-background" class="mrgn_60_btm">
	  <div class="vchip-background-single">
	    <div class="vchip-background-img">
	      <figure>
	        <img src="{{asset('images/exam.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Company Exam" />
	      </figure>
	    </div>
	    <div class="vchip-background-content">
	      <h2 class="animated bounceInLeft">Digital Education</h2>
	    </div>
	  </div>
	</section>
	<section class="v_container ">
	 	<div class="container">
	   		<h2 class="v_h2_title text-center">Online Mock Tests</h2>
	   		<hr class="section-dash-dark"/>
	 	</div>
	</section>
	@if(is_object($companyPaper))
  	<section>
	  	<div class="container exam-panel">
		    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		      	<div class="panel panel-default">
		        	<div class="panel-heading" role="tab" id="headingOne">
		          		<h4 class="panel-title">
				            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
				              	<i class="more-less glyphicon glyphicon-minus"></i>
				              	{{$companyPaper->name}}
				            </a>
		          		</h4>
		        	</div>
			        <div id="collapseOne" class="panel-collapse collapse panel-lg in" role="tabpanel" aria-labelledby="headingOne" aria-expanded="true">
			          	<div class="panel-body">
			            	<table class="table data-lg">
			              		<thead>
			                		<tr>
			                  			<th>Date</th>
					                  	<th>Time</th>
					                  	<th>Start test</th>
					                  	<th>Result</th>
					                  	<th>Favourite</th>
			                		</tr>
			              		</thead>
				              	<tr style="overflow: auto;">
					                <td class=" ">{{explode(' ', $companyPaper->date_to_active)[0]}}</td>
					                <td class=" ">{{date('h:i a', strtotime(explode(' ', $companyPaper->date_to_active)[1]))}} to {{date('h:i a', strtotime(explode(' ', $companyPaper->date_to_inactive)[1]))}}</td>
					                @if($currentDate < $companyPaper->date_to_active)
				                		<td id="startTest_{{$companyPaper->id}}"><button disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
				                	@elseif(!is_object(Auth::user()))
				                		<td id="startTest_{{$companyPaper->id}}"><button data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();"><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
				                	@else
				                		@if(in_array($companyPaper->id, $registeredPaperIds))
				                			@if(in_array($companyPaper->id, $alreadyGivenPapers))
					                    		<td id="startTest_{{$companyPaper->id}}"><button disabled="true" data-toggle="tooltip" title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
						                    @else
						                    	<td id="startTest_{{$companyPaper->id}}" onClick="startTest(this);" data-paper="{{$companyPaper->id}}" data-subject="{{$companyPaper->test_subject_id}}" data-category="{{$companyPaper->test_category_id}}" data-subcategory="{{$companyPaper->test_sub_category_id}}"><button data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button></td>
						                    @endif
						                @elseif($companyPaper->price < 1 )
						                	<td id="startTest_{{$companyPaper->id}}" onClick="startTest(this);" data-paper="{{$companyPaper->id}}" data-subject="{{$companyPaper->test_subject_id}}" data-category="{{$companyPaper->test_category_id}}" data-subcategory="{{$companyPaper->test_sub_category_id}}"><button data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button></td>
						                @else
						                	<td id="startTest_{{$companyPaper->id}}"><button disabled="true" data-toggle="tooltip" title="Add to favourite to give test."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
					                    @endif
				                	@endif

					                <td id="showUserResultBtn_{{$companyPaper->id}}">
				                    	@if($currentDate < $companyPaper->date_to_active)
				                    		<button disabled="true" data-toggle="tooltip" title="Result will enabled after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
				                    	@elseif(!is_object(Auth::user()))
				                    		<button data-toggle="tooltip" title="Please login to see result." onClick="checkLogin();"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
				                    	@elseif(in_array($companyPaper->id, $alreadyGivenPapers))
				                    		<form id="showUserTestResult_{{$companyPaper->id}}" method="POST" action="{{ url('showUserTestResult') }}">
				                    			{{ csrf_field() }}
				                    			<input type="hidden" name="paper_id" value="{{$companyPaper->id}}">
				                    			<input type="hidden" name="category_id" value="{{$companyPaper->test_category_id}}">
				                    			<input type="hidden" name="subcategory_id" value="{{$companyPaper->test_sub_category_id}}">
				                    			<input type="hidden" name="subject_id" value="{{$companyPaper->test_subject_id}}">
				                    		</form>
					                    	<button onClick="showUserTestResult(this);" data-paper_id="{{$companyPaper->id}}" data-toggle="tooltip" title="Result!"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
					                    @else
					                    	<button disabled="true" data-toggle="tooltip" title="Result will enabled after test given."><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
					                    @endif
				                    </td>
					                @if($currentDate < $companyPaper->date_to_active)
				                    	<td><button disabled="true" data-toggle="tooltip" title="Add to Favourite will be enabled on date to active"><i class="fa fa-star" aria-hidden="true" ></i></button></td>
				                    @else
				                    	@if(in_array($companyPaper->id, $registeredPaperIds))
					                    	<td><button disabled="true" data-toggle="tooltip" title="Already Added to Favourite!" ><i class="fa fa-star" aria-hidden="true" style="color: rgb(233, 30, 99);"></i></button></td>
					                    @else
					                    	<td id="registerPaper_{{$companyPaper->id}}" data-paper="{{$companyPaper->id}}" data-subject="{{$companyPaper->test_sub_category_id}}" data-category="{{$companyPaper->test_category_id}}" data-subcategory="{{$companyPaper->test_sub_category_id}}" onClick="registerPaper(this);"><button  data-toggle="tooltip" title="Add to Favourite!"><i class="fa fa-star" aria-hidden="true" ></i></button></td>
					                    @endif
				                    @endif
				              	</tr>
				            </table>
				            <!-- PANEL -->
				            <div class="data-sm">
				              	<div class=" panel panel-info" >
				                  	<div id="detail-1" class="panel-body">
				                    	<div class="container">
				                      		<div class="fluid-row">
						                        <ul class="">
						                          	<li>
						                           		<button type="button" class="btn-magick btn-sm btn3d"><span class="fa fa-calendar" title="Start"></span> {{explode(' ', $companyPaper->date_to_active)[0]}}</button>
						                          	</li>
						                          	<li>
						                           		<button type="button" class="btn-magick btn-sm btn3d"><span class="fa fa-clock-o" title="Start"></span> {{date('h:i a', strtotime(explode(' ', $companyPaper->date_to_active)[1]))}} to {{date('h:i a', strtotime(explode(' ', $companyPaper->date_to_inactive)[1]))}}</button>
						                          	</li>
						                          	@if($currentDate < $companyPaper->date_to_active)
							                    		<li id="startTest_mobile_{{$companyPaper->id}}" ><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button></li>
							                    	@elseif(!is_object(Auth::user()))
							                    		<li id="startTest_mobile_{{$companyPaper->id}}" ><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button></li>
							                    	@else
							                    		@if(in_array($companyPaper->id, $registeredPaperIds))
							                    			@if(in_array($companyPaper->id, $alreadyGivenPapers))
									                    		<li id="startTest_mobile_{{$companyPaper->id}}" ><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" >Start</span></button></li>
										                    @else
										                    	<li id="startTest_mobile_{{$companyPaper->id}}" onClick="startTest(this);" data-paper="{{$companyPaper->id}}" data-subject="{{$companyPaper->test_subject_id}}" data-category="{{$companyPaper->test_category_id}}" data-subcategory="{{$companyPaper->test_sub_category_id}}" ><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true"></span>Start</button></li>
										                    @endif
										                @elseif($companyPaper->price < 1 )
										                	<li id="startTest_mobile_{{$companyPaper->id}}" onClick="startTest(this);" data-paper="{{$companyPaper->id}}" data-subject="{{$companyPaper->test_subject_id}}" data-category="{{$companyPaper->test_category_id}}" data-subcategory="{{$companyPaper->test_sub_category_id}}" ><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true"></span>Start</button></li>
										                @else
										                	<li id="startTest_mobile_{{$companyPaper->id}}" ><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Add to favourite to give test."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button></li>
									                    @endif
							                    	@endif
						                          	<li id="showUserResultMobileBtn_{{$companyPaper->id}}">
						                           		@if($currentDate < $companyPaper->date_to_active)
								                    		<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will enabled after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
								                    	@elseif(!is_object(Auth::user()))
								                    		<button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to see result." onClick="checkLogin();"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
								                    	@elseif(in_array($companyPaper->id, $alreadyGivenPapers))
									                    	<button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="{{$companyPaper->id}}" data-toggle="tooltip" title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
									                    @else
									                    	<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will enabled after test given."><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
									                    @endif
						                           	</li>
						                           	@if($currentDate < $companyPaper->date_to_active)
								                    	<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Add to Favourite will be enabled on date to active"><span class="fa fa-star" aria-hidden="true" ></span> Add</button></li>
								                    @else
								                    	@if(in_array($companyPaper->id, $registeredPaperIds))
									                    	<li><button  disabled="true" class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Already Added to Favourite!"><span class="fa fa-star" aria-hidden="true" style="color: rgb(233, 30, 99);"></span> Add</button></li>
									                    @else
									                    	<li id="registerPaper_mobile_{{$companyPaper->id}}" data-paper="{{$companyPaper->id}}" data-subject="{{$companyPaper->test_subject_id}}" data-category="{{$companyPaper->test_category_id}}" data-subcategory="{{$companyPaper->test_sub_category_id}}" onClick="registerPaper(this);"><button class="btn-magick btn-sm btn3d"  data-toggle="tooltip" title="Add to Favourite!"><span class="fa fa-star" aria-hidden="true" ></span> Add</button></li>
									                    @endif
								                    @endif
						                        </ul>
				                       		</div>
			                     		</div>
				                  	</div>
				              	</div>
				            </div>
			          	</div>
			        </div>
		      	</div>
		    </div>
	  	</div>
	</section>
	@endif
	<section>
		<div class="container exam-panel" id="all-result">
		    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		      	<div class="panel panel-default">
		        	<div class="panel-heading" role="tab" id="headingOne">
		          		<h4 class="panel-title">
				            <a role="button" data-toggle="collapse" data-parent="#accordion" href="" aria-expanded="true" aria-controls="">
				              	Schedule
				            </a>
		          		</h4>
		        	</div>
			        <div id="" >
			          	<div class="panel-body">
							<table class="table">
						  		<thead>
						    		<tr>
						              	<th>Exam Name</th>
						      			<th>Date</th>
						              	<th>Time</th>
						              	<th>Result</th>
						    		</tr>
						  		</thead>
						  		<tbody>
						  			@if(count($allTestPapers) > 0)
						  				@foreach($allTestPapers as $allTestPaper)
								  			<tr>
								              	<td>{{$allTestPaper->name}}</td>
								      			<td>{{explode(' ', $allTestPaper->date_to_active)[0]}}</td>
								              	<td>{{date('h:i a', strtotime(explode(' ', $allTestPaper->date_to_active)[1]))}} to {{date('h:i a', strtotime(explode(' ', $allTestPaper->date_to_inactive)[1]))}}</td>
								              	<td>
								              		@if($currentDate > $allTestPaper->date_to_inactive)
								              			<a href="#paper_{{$allTestPaper->id}}" data-toggle="modal">Result</a>
								              		@endif
								              	</td>
								    		</tr>
								    	@endforeach
						    		@endif
						  		</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		@if(count($testScores) > 0)
			@foreach($testScores as $testPaperId => $paperScores)
				@if(count($paperScores) > 0)
		          	<div class="modal resultModels" id="paper_{{$testPaperId}}" role="dialog" style="display: none;">
		                <div class="modal-dialog modal-lg">
		                  	<div class="modal-content">
			                    <div class="modal-header">
			                      	<button type="button" class="close" data-dismiss="modal">×</button>
			                      	<h4 class="modal-title">Result of {{$testPaperNames[$testPaperId]}}</h4>
				                    <table id="" width="100%">
				                        <thead>
				                        	<tr>
				                            	<th>#</th>
				                            	<th>User</th>
				                            	<th>Score</th>
				                            	<th>Rank</th>
				                          	</tr>
				                        </thead>
				                        <tbody>
				                        @foreach($paperScores as $index => $paperScore)
				                        	<tr>
				                        		<td>{{$index + 1}}</td>
				                        		<td>{{$testUsers[$paperScore['user']]->name}}</td>
				                        		<td>{{$paperScore['test_score']}}</td>
				                        		<td>{{$paperScore['rank']}}</td>
				                        	</tr>
				                        @endforeach
				                        </tbody>
				                    </table>
			                    </div>
		                  	</div>
		                </div>
		      		</div>
			    @endif
			@endforeach
      	@endif
	</section>
	<section>
		<div class="container exam-panel" id="all-result">
		    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		      	<div class="panel panel-default">
		        	<div class="panel-heading" role="tab" id="headingOne">
		          		<h4 class="panel-title">
				            <a role="button" data-toggle="collapse" data-parent="#accordion" href="" aria-expanded="true" aria-controls="">
				              	Features
				            </a>
		          		</h4>
		        	</div>
			        <div id="" >
			          	<div class="panel-body">
			          		<ol>
			          			<li>In first week of every month, we conduct Online Virtual placement drive</li>
			          			<li>At first, on 1st of every month, we conduct online aptitude test according to pattern of most of companies.</li>
			          			<li>On week-end of same week, we conduct interview of selected students on skype.</li>
			          			<li>Record these interviews and put on our platform, so our partner companies will go through it. If they like the performance then they can directly call for second round.</li>
			          		</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="container" id="members" style="padding: 0 10px;"><br/>
        <h2>Mock Interviews</h2>
        @if(is_object($completedPapers) && false == $completedPapers->isEmpty())
        	@foreach($completedPapers as $completedPaper)
	        <h4>{{$completedPaper->name}} on {{date('Y-m-d h:i a',strtotime($completedPaper->date_to_active))}}</h4>
	        <div class="row">
	        	@if( isset($selectedUserResults[$completedPaper->id]) && count($selectedUserResults[$completedPaper->id]) > 0)
	        		@foreach($selectedUserResults[$completedPaper->id] as $paperResult)
			            <div class="col-xs-12 col-sm-6 col-md-4">
			                <div class="image-flip" ontouchstart="this.classList.toggle('hover');">
			                	@php
                                	$expArr = explode(',',$paperResult->experiance);
                                	$skillArr = explode(',',$paperResult->skill_ids);
                                @endphp
			                    <div class="mainflip">
			                        <div class="frontside" style="width: 90%;">
			                            <div class="card">
			                                <div class="card-body text-center"><br/>
			                                    <p>
				                                    @if(!empty($testUsers[$paperResult->user_id]->photo) && is_file($testUsers[$paperResult->user_id]->photo))
				                                    	<img class="" src="{{ asset($testUsers[$paperResult->user_id]->photo)}}" alt="user image">
				                                    @else
				                                    	<img class="" src="{{ asset('images/user/user1.png')}}" alt="user image">
				                                    @endif
			                                    </p>
			                                    <h4 class="card-title"> @if(isset($testUsers[$paperResult->user_id])) {{ $testUsers[$paperResult->user_id]->name}} @endif</h4>
			                                    <p class="card-text">
			                                    	@if(count($skillArr) > 0)
			                                    		@foreach($skillArr as $skill)
			                                    			#{{$userSkills[$skill]}}
			                                    		@endforeach
			                                    	@endif
			                                    </p>
			                                </div>
			                            </div>
			                        </div>
			                        <div class="backside"  style="width: 90%;">
			                            <div class="card" style="padding-left: 10px;">
			                                <div class="card-body mt-4">
			                                    <h4 class="card-title"> @if(isset($testUsers[$paperResult->user_id])) {{ $testUsers[$paperResult->user_id]->name }} @endif</h4>

			                                    <div class="desc"><b>Experience:</b> {{$expArr[0]}} yr {{$expArr[1]}} month</div>
			                                    <div class="desc"><b>Company Name:</b> {{ $paperResult->company }}</div>
			                                    <div class="desc"><b>Education:</b> {{ $paperResult->education }}</div>
			                                    <div class="desc"> <b>Skill:</b>
			                                    	@if(count($skillArr) > 0)
			                                    		@foreach($skillArr as $skill)
			                                    			#{{$userSkills[$skill]}}
			                                    		@endforeach
			                                    	@endif
			                                    </div>
			                                    <ul class="list-inline">
			                                    	@if(!empty($paperResult->facebook))
			                                        <li class="list-inline-item">
			                                            <a class="social-icon text-xs-center" target="_blank" href="{{$paperResult->facebook}}">
			                                                <i class="fa fa-facebook"></i>
			                                            </a>
			                                        </li>
			                                        @endif
			                                        @if(!empty($paperResult->twitter))
			                                        <li class="list-inline-item">
			                                            <a class="social-icon text-xs-center" target="_blank" href="{{$paperResult->twitter}}">
			                                                <i class="fa fa-twitter"></i>
			                                            </a>
			                                        </li>
			                                        @endif
			                                        @if(!empty($paperResult->skype))
			                                        <li class="list-inline-item">
			                                            <a class="social-icon text-xs-center" target="_blank" href="{{$paperResult->skype}}">
			                                                <i class="fa fa-skype"></i>
			                                            </a>
			                                        </li>
			                                        @endif
			                                        @if(!empty($paperResult->google))
			                                        <li class="list-inline-item">
			                                            <a class="social-icon text-xs-center" target="_blank" href="{{$paperResult->google}}">
			                                                <i class="fa fa-google"></i>
			                                            </a>
			                                        </li>
			                                        @endif
			                                    </ul>
			                                    <div>
			                                    	@if(!empty($paperResult->youtube))
			                                        	<a class="btn btn-warning btn-sm" rel="publisher"  href="#student_{{$paperResult->id}}" data-toggle="modal" title="Mock Interview"><i class="fa fa-youtube"></i></a>
			                                        @endif
			                                        @if(!empty($paperResult->resume) && is_file($paperResult->resume))
			                                        	<a href="{{asset($paperResult->resume)}}" class="btn btn-primary" download  title="Download Resume"><i class="fa fa-book" aria-hidden="true"></i></a>
			                                        @endif
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                    </div>
			                </div>
			            </div>
			        @endforeach
	            @endif
	        </div>
	        @endforeach
	    @endif
	    @if(is_object($completedPapers) && false == $completedPapers->isEmpty())
        	@foreach($completedPapers as $completedPaper)
	        	@if( isset($selectedUserResults[$completedPaper->id]) && count($selectedUserResults[$completedPaper->id]) > 0)
	        		@foreach($selectedUserResults[$completedPaper->id] as $paperResult)
                    	@if(!empty($paperResult->youtube))
	                      	<div id="student_{{$paperResult->id}}" class="modal fade" role="dialog">
		                        <div class="modal-dialog" style="width: 80%; height: 70%;">
		                            <div class="modal-content">
		                              	<div class="modal-header">
		                                	<button class="close" data-dismiss="modal" id="close_{{$paperResult->id}}">×</button>
	                                		<h2  class="modal-title">Interview</h2>
		                              	</div>
		                              	<div class="modal-body">
		                                	<div class="iframe-container" id="iframe_{{$paperResult->id}}">
		                                  		{!! $paperResult->youtube !!}
	                                		</div>
		                              	</div>
		                            </div>
		                        </div>
		                    </div>
                        @endif
			        @endforeach
	            @endif
	        @endforeach
	    @endif
	</section>
	<br>
@stop
@section('footer')
	@include('footer.footer')
<script>

	function startTest(ele){
		var windowHeight = screen.height;
		var windowWidth = screen.width;
		var popup_window =window.open("", 'My Window', 'height='+windowHeight+'px !important,width='+windowWidth+'px !important');
		var paper = parseInt($(ele).data('paper'));
		var subject = parseInt($(ele).data('subject'));
		var category = parseInt($(ele).data('category'));
		var subcategory = parseInt($(ele).data('subcategory'));
		var userId = parseInt(document.getElementById('user_id').value);
		if(0 < userId && 0 < paper && 0 < subject && 0 < category && 0 < subcategory){
			$.ajax({
	            method: "POST",
	            url: "{{url('setSessions')}}",
	            data: {paper:paper, subject:subject, category:category, subcategory:subcategory}
	        })
	        .done(function( msg ) {
	        	if( msg ){
	        		popup_window.location = "{{ url('instructions')}}";
	        		popup_window.focus();
	        	}
	        });
		} else {
			$('#loginUserModel').modal();
		}
	}
	function checkIsTestGiven(paper,subject,category,subcategory,userId){
		$.ajax({
            method: "POST",
            url: "{{url('isTestGiven')}}",
            data: {paper:paper, subject:subject, category:category, subcategory:subcategory, userId:userId}
        })
        .done(function( msg ) {
        	if( 'true' == msg ){
        		var ele = document.getElementById('startTest_'+paper);
		        ele.innerHTML = '<button disabled="true" data-toggle="tooltip" title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i>';

				var startTestMobile = document.getElementById('startTest_mobile_'+paper);
		        startTestMobile.innerHTML = '<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" >Start</span></button>';

                var showUserResultBtn = document.getElementById('showUserResultBtn_'+paper);
		        var csrfToken = '{{ csrf_field() }}';
		        var url = "{{ url('showUserTestResult') }}";
		        showUserResultBtnInnerHtml = '<form id="showUserTestResult_'+paper+'" method="POST" action="'+url+'">'+csrfToken;
		        showUserResultBtnInnerHtml += '<input type="hidden" name="paper_id" value="'+paper+'"><input type="hidden" name="category_id" value="'+category+'"><input type="hidden" name="subcategory_id" value="'+subcategory+'"><input type="hidden" name="subject_id" value="'+subject+'"></form>';
				showUserResultBtnInnerHtml += '<button  onClick="showUserTestResult(this);" data-paper_id="'+paper+'" data-toggle="tooltip" title="Result!"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
				showUserResultBtn.innerHTML = showUserResultBtnInnerHtml;


		        var showUserResultMobileBtn = document.getElementById('showUserResultMobileBtn_'+paper);
		        showUserResultMobileBtn.innerHTML= '<button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="'+paper+'" data-toggle="tooltip" title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';

        	}
        });
	}
	function registerPaper(ele){
		var paper = parseInt($(ele).data('paper'));
		var subject = parseInt($(ele).data('subject'));
		var category = parseInt($(ele).data('category'));
		var subcategory = parseInt($(ele).data('subcategory'));
		var userId = parseInt(document.getElementById('user_id').value);
	    if( true == isNaN(userId)){
	    	$('#loginUserModel').modal();
	    } else if(paper > 0 && subject > 0 && category > 0 && subcategory > 0) {
	    	$.confirm({
		    title: 'Confirmation',
		    content: 'Do you want to add this paper/test.',
		    type: 'red',
		    typeAnimated: true,
		    buttons: {
		        	Ok: {
			            text: 'Ok',
			            btnClass: 'btn-red',
			            action: function(){
							$.ajax({
						        method: "POST",
						        url: "{{url('registerPaper')}}",
						        data: {user_id:userId, paper_id:paper}
						    })
						    .done(function( msg ) {
						        var registerEle = document.getElementById('registerPaper_'+paper);
						        registerEle.setAttribute('data-paper', 0);
								registerEle.setAttribute('data-subject', 0);
								registerEle.setAttribute('data-category', 0);
								registerEle.setAttribute('data-subcategory', 0);
								registerEle.removeAttribute('onclick');
						        registerEle.innerHTML ='';
						        registerEle.innerHTML = '<button data-toggle="tooltip" title="Already Added to Favourite!"><i class="fa fa-star" aria-hidden="true" style="color: rgb(233, 30, 99);"></i></button>';


						        var registerMobileEle = document.getElementById('registerPaper_mobile_'+paper);
						        registerMobileEle.setAttribute('data-paper', 0);
								registerMobileEle.setAttribute('data-subject', 0);
								registerMobileEle.setAttribute('data-category', 0);
								registerMobileEle.setAttribute('data-subcategory', 0);
								registerMobileEle.removeAttribute('onclick');
						        registerMobileEle.innerHTML ='';
						        registerMobileEle.innerHTML = '<button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Already Added to Favourite!"><span class="fa fa-star" aria-hidden="true"></span>Add</button>';

						        var startTestEle = document.getElementById('startTest_'+paper);
						        startTestEle.setAttribute('data-paper', paper);
								startTestEle.setAttribute('data-subject', subject);
								startTestEle.setAttribute('data-category', category);
								startTestEle.setAttribute('data-subcategory', subcategory);
								startTestEle.setAttribute('onClick', 'startTest(this);');
						        startTestEle.innerHTML = '';
						        startTestEle.innerHTML = '<button data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>';

						        var startTestMobileEle = document.getElementById('startTest_mobile_'+paper);
						        startTestMobileEle.setAttribute('data-paper', paper);
								startTestMobileEle.setAttribute('data-subject', subject);
								startTestMobileEle.setAttribute('data-category', category);
								startTestMobileEle.setAttribute('data-subcategory', subcategory);
								startTestMobileEle.setAttribute('onClick', 'startTest(this);');
						        startTestMobileEle.innerHTML = '';
						        startTestMobileEle.innerHTML = '<button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button>';

					      	});
		    			}
			        },
			        Cancle: function () {
			        }
			    }
			});
		}
	}
	function showUserTestResult(ele){
		$.confirm({
		    title: 'Confirmation',
		    content: 'Do you want to see result?',
		    type: 'red',
		    typeAnimated: true,
		    buttons: {
		        	Ok: {
			            text: 'Ok',
			            btnClass: 'btn-red',
			            action: function(){
							var paperId = parseInt($(ele).data('paper_id'));
							document.getElementById('showUserTestResult_'+paperId).submit();
					}
		        },
		        Cancle: function () {
		        }
		    }
		});
	}

	window.onclick = function(event) {
	    var modelId = $(event.target).attr('id');
	    if(undefined != modelId){
	      var id = modelId.split('_')[1];
	      if(id > 0) {
	        toggleVideo('hide', id);
	      }
	    }
	}

	function toggleVideo(state, id) {
	    // if state == 'hide', hide. Else: show video
	    var div = document.getElementById("student_"+id);

	    if(div.getElementsByTagName("iframe").length > 0){
	      var iframe = div.getElementsByTagName("iframe")[0].contentWindow;
	      func = (state == 'hide' ) ? 'pauseVideo' : 'playVideo';
	      iframe.postMessage('{"event":"command","func":"' + func + '","args":""}','*');
	    }
	}
</script>
@stop