@extends('layouts.master')
@section('header-title')
  <title>Online Test Series for GATE, CAT, Aptitude |Vchip-edu</title>
@stop
@section('header-css')
	@include('layouts.home-css')
	<link href="{{asset('css/exam.css?ver=1.0')}}" rel="stylesheet"/>
	<link rel="stylesheet" href="{{ asset('css/star-rating.css') }}" />
  	<style type="text/css">
	    .fa {
	      font-size: medium !important;
	    }
	    .rating-container .filled-stars{
	      color: #e7711b;
	      border-color: #e7711b;
	    }
	    .rating-xs {
	        font-size: 0em;
	    }
	    .user-block img {
	      width: 40px;
	      height: 40px;
	      float: left;
	      border: 2px solid #d2d6de;
	      padding: 1px;
	    }
	    .img-circle {
	      border-radius: 50%;
	    }
	    .user-block .username, .user-block .description{
	        display: block;
	        margin-left: 50px;
	    }
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
		.pay-now span {
		    color: #fff;
		    font-weight: bold;
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
	        <img src="{{asset('images/exam.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Vchip Exam" />
	      </figure>
	    </div>
	    <div class="vchip-background-content">
	      <h2 class="animated bounceInLeft">Digital Education</h2>
	    </div>
	  </div>
	</section>
  	<section class="v_container ">
	 <div class="container">
	 	<div class="row">
	      	@if(Session::has('message'))
	        	<div class="alert alert-success" id="message">
	          		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	            	{{ Session::get('message') }}
        		</div>
	      	@endif
	      	@if(count($errors) > 0)
	        	<div class="alert alert-danger">
	            	<ul>
	                	@foreach ($errors->all() as $error)
	                  	<li>{{ $error }}</li>
	                	@endforeach
	            	</ul>
	        	</div>
	      	@endif
	    </div>
	    <div class="row pull-right" id="ratingDiv">
	    	<a data-toggle="modal" data-target="#review-model-{{$subcatId}}" style="cursor: pointer;">
		      	<span style= "position:relative; top:7px;">
	            	@if(isset($reviewData[$subcatId])) {{$reviewData[$subcatId]['avg']}} @else 0 @endif
	          	</span>
	          	<div style="display: inline-block;">
	            	<input id="rating_input{{$subcatId}}" name="input-{{$subcatId}}" class="rating rating-loading" value="@if(isset($reviewData[$subcatId])) {{$reviewData[$subcatId]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
	          	</div>
	          	<span style= "position:relative; top:7px;">
	              	@if(isset($reviewData[$subcatId]))
	                	{{count($reviewData[$subcatId]['rating'])}} <i class="fa fa-group"></i>
	              	@else
	                	0 <i class="fa fa-group"></i>
	              	@endif
	          	</span>
	        </a>
	    </div>
	   <h2 class="v_h2_title text-center"> Exam</h2>
	   <hr class="section-dash-dark"/>
	  <div class="row label-primary">
	    <div class="col-md-8  col-md-offset-2  ">
	      <div class="row text-center">
	        <div class="col-md-4 col-sm-4  col-xs-12 mrgn_10_top_btm  ">
	          <select class="form-control" id="category" name="category" title="Category" onChange="selectSubcategory(this);">
          		<option>Select Category</option>
          		@foreach($testCategories as $testCategory)
          			@if( $catId == $testCategory->id)
              			<option value="{{$testCategory->id}}" selected="true">
              		@else
              			<option value="{{$testCategory->id}}">
              		@endif
              			{{$testCategory->name}}
              		</option>
          		@endforeach
          	</select>
	        </div>
	        <div class="col-md-4 col-sm-4  col-xs-12 mrgn_10_top_btm">
	          <select class="form-control" id="subcategory" name="subcategory" title="Sub Category" onChange="selectPanel(this);">
	          		<option>Select Sub Category</option>
	          		@foreach($testSubCategories as $testSubCategory)
	          			@if($subcatId == $testSubCategory->id)
	          				<option value="{{$testSubCategory->id}}" data-price="{{$testSubCategory->price}}" selected>
	          			@else
	          				<option value="{{$testSubCategory->id}}" data-price="{{$testSubCategory->price}}">
	          			@endif
	          				{{$testSubCategory->name}}
	          			</option>
	          		@endforeach
	          	</select>
	        </div>
	        <div class="col-md-4 col-sm-4 col-xs-12 mrgn_10_top_btm pay-now">
	          	<span id="price">Price: {{$testSubCategory->price}} Rs.</span>
              	@if(is_object(Auth::user()))
	                @if(true == $isSubCategoryPurchased)
	                  	<a id="paidStatus" class="btn btn-default" style="min-width: 100px;">Paid</a>
	                  	<div id="pay-now-form"></div>
	                @else
	                  	@if($testSubCategory->price > 0)
		                    <a id="paidStatus" class="btn btn-default" style="min-width: 100px;" onClick="purchaseSubCategory(this);">Pay Now</a>
		                    <div id="pay-now-form">
			                    <form id="purchaseSubCategory" method="POST" action="{{ url('purchaseTestSubCategory') }}">
			                      {{ csrf_field() }}
			                      <input type="hidden" name="category_id" value="{{$testSubCategory->test_category_id}}">
			                      <input type="hidden" name="subcategory_id" value="{{$testSubCategory->id}}">
			                    </form>
			                </div>
	                  	@else
	                    	<a id="paidStatus" class="btn btn-default" style="min-width: 100px;">Free</a>
	                    	<div id="pay-now-form"></div>
                  		@endif
	                @endif
              	@else
	                @if($testSubCategory->price > 0)
	                  	<a id="paidStatus" class="btn btn-default" style="min-width: 100px;"  onClick="checkLogin();">Pay Now</a>
	                @else
	                  	<a id="paidStatus" class="btn btn-default" style="min-width: 100px;">Free</a>
	                @endif
	                <div id="pay-now-form"></div>
              	@endif
	        </div>
	      </div>
	    </div>
	  </div>
	  <div>
	    <h3 class="divider">
	      <span></span>
	    </h3>
	    <div class="divider">
	      <span></span>
	    </div>
	  </div>
	 </div>
	</section>
  	<section>
	  	<div class="container exam-panel" id="subjects">
			@if(count($testSubjects)>0)
	    		@foreach($testSubjects as $index => $testSubject)
	    			@if($subject == $testSubject->id)
				    	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="border-style: dotted;border-color: red;">
				    @else
				    	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" >
				    @endif
				      	<div class="panel panel-default">
					        <div class="panel-heading" role="tab" id="headingOne">
					          <h4 class="panel-title">
					            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject{{$testSubject->id}}" aria-expanded="true" aria-controls="collapseOne" title="{{ $testSubject->name }}">
			              			<i class="more-less glyphicon glyphicon-minus"></i>
					              {{ $testSubject->name }}
					            </a>
					          </h4>
					        </div>
				        	<div id="subject{{$testSubject->id}}" class="panel-collapse panel-lg collapse in" role="tabpanel" aria-labelledby="headingOne">
					          	<div class="panel-body">
					            <table class="table data-lg">
					              	<thead>
					                	<tr>
						                  	<th>Test Name</th>
						                  	<th>Start Test</th>
						                  	<th>Result</th>
						                  	<th>Date to Active</th>
						                  	<th>Price</th>
						                  	<th>Purchase Test</th>
					                	</tr>
					              	</thead>
					              	<tbody>
			                      		@if(isset($testSubjectPapers[$testSubject->id]))
				        					@foreach($testSubjectPapers[$testSubject->id] as $testSubjectPaper)
							                	@if(in_array($testSubjectPaper->id, $alreadyGivenPapers))
					                              <tr style="background-color: #b3c2dc;">
					                            @else
					                              <tr>
					                            @endif
					        						@if($paper == $testSubjectPaper->id)
									                	<td class=" ">{{ $testSubjectPaper->name }} <b style="color: red;">[new]</b></td>
									                @else
									                    <td class=" ">{{ $testSubjectPaper->name }}</td>
									                @endif
							                    	@if($currentDate < $testSubjectPaper->date_to_active)
							                    		<td id="startTest_{{$testSubjectPaper->id}}"><button disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
							                    	@elseif(!is_object(Auth::user()))
							                    		<td id="startTest_{{$testSubjectPaper->id}}"><button data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();"><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
							                    	@else
							                    		@if(in_array($testSubjectPaper->id, $registeredPaperIds) || true == $isSubCategoryPurchased)
							                    			@if(in_array($testSubjectPaper->id, $alreadyGivenPapers))
									                    		<td id="startTest_{{$testSubjectPaper->id}}"><button disabled="true" data-toggle="tooltip" title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
										                    @else
										                    	<td id="startTest_{{$testSubjectPaper->id}}" onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->test_category_id}}" data-subcategory="{{$testSubjectPaper->test_sub_category_id}}"><button data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button></td>
										                    @endif
										                @elseif($testSubjectPaper->price < 1 )
										                	<td id="startTest_{{$testSubjectPaper->id}}" onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->test_category_id}}" data-subcategory="{{$testSubjectPaper->test_sub_category_id}}"><button data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button></td>
										                @else
										                	<td id="startTest_{{$testSubjectPaper->id}}"><button disabled="true" data-toggle="tooltip" title="Please purchase to give test."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
									                    @endif
							                    	@endif
								                    <td id="showUserResultBtn_{{$testSubjectPaper->id}}">
								                    	@if($currentDate < $testSubjectPaper->date_to_active)
								                    		<button disabled="true" data-toggle="tooltip" title="Result will enabled after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
								                    	@elseif(!is_object(Auth::user()))
								                    		<button data-toggle="tooltip" title="Please login to see result." onClick="checkLogin();"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
								                    	@elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
								                    		<form id="showUserTestResult_{{$testSubjectPaper->id}}" method="POST" action="{{ url('showUserTestResult') }}">
								                    			{{ csrf_field() }}
								                    			<input type="hidden" name="paper_id" value="{{$testSubjectPaper->id}}">
								                    			<input type="hidden" name="category_id" value="{{$testSubjectPaper->test_category_id}}">
								                    			<input type="hidden" name="subcategory_id" value="{{$testSubjectPaper->test_sub_category_id}}">
								                    			<input type="hidden" name="subject_id" value="{{$testSubject->id}}">
								                    		</form>
									                    	<button onClick="showUserTestResult(this);" data-paper_id="{{$testSubjectPaper->id}}" data-toggle="tooltip" title="Result!"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
									                    @else
									                    	<button disabled="true" data-toggle="tooltip" title="Result will enabled after test given."><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
									                    @endif
								                    </td>
								                    <td class="">{{ $testSubjectPaper->date_to_active }}</td>
								                    <td class=""><i class="fa fa-inr"></i>{{ $testSubjectPaper->price }}</td>
								                    @if($currentDate < $testSubjectPaper->date_to_active)
								                    	<td>
								                    		<button class="btn-primary" disabled="true" data-toggle="tooltip" title="Purchase test will be enabled on date to active">
								                    		@if($testSubjectPaper->price > 0)
									                    		Pay Now
									                    	@else
									                    		Free
									                    	@endif
								                    		</button>
								                    	</td>
								                    @else
								                    	@if(in_array($testSubjectPaper->id, $registeredPaperIds) || true == $isSubCategoryPurchased)
								                    		@if($testSubjectPaper->price > 0)
									                    		<td>Paid</td>
									                    	@else
									                    		<td>Free</td>
									                    	@endif
									                    @else
									                    	@if($testSubjectPaper->price > 0)
									                    		@if(!is_object(Auth::user()))
									                    			<td><button class="btn-primary" data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();">Pay Now</button></td>
								                    			@else
								                    				<td data-paper="{{$testSubjectPaper->id}}" onClick="purchaseTest(this);">
										                    		<form id="purchaseTest_{{$testSubjectPaper->id}}" method="POST" action="{{ url('purchaseTest') }}">
										                    			{{ csrf_field() }}
										                    			<input type="hidden" name="paper_id" value="{{$testSubjectPaper->id}}">
										                    			<input type="hidden" name="category_id" value="{{$testSubjectPaper->test_category_id}}">
										                    			<input type="hidden" name="subcategory_id" value="{{$testSubjectPaper->test_sub_category_id}}">
										                    			<input type="hidden" name="subject_id" value="{{$testSubject->id}}">
										                    		</form>
										                    		<button class="btn-primary" data-toggle="tooltip" title="Purchase Test">Pay Now</button>
									                    			</td>
									                    		@endif
									                    	@else
									                    		@if(!is_object(Auth::user()))
									                    			<td><button  data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();">Free</button></td>
								                    			@else
									                    			<td>Free</td>
									                    		@endif
									                    	@endif
									                    @endif
								                    @endif
								                </tr>
								            @endforeach
								        @else
								        	<tr><td class=" ">No Papers.</td></tr>
								        @endif
					              	</tbody>
					            </table>
					            <div class="data-sm">
					            	@if(isset($testSubjectPapers[$testSubject->id]))
			        					@foreach($testSubjectPapers[$testSubject->id] as $testSubjectPaper)
					              			<div class=" panel panel-info" >
					                			@if(in_array($testSubjectPaper->id, $alreadyGivenPapers))
				                                  <div class="toggle panel-heading" data-toggle="paper{{$testSubjectPaper->id}}" style="background-color: #b3c2dc;">
				                                @else
				                                  <div class="toggle panel-heading" data-toggle="paper{{$testSubjectPaper->id}}">
				                                @endif
					                			{{$testSubjectPaper->name}}<span class="col-xs-2 pull-right"><i class="fa fa-chevron-down pull-right"></i></span>
					                			</div>
							                  	<div id="paper{{$testSubjectPaper->id}}" class="panel-body" style="padding:2px 0px;">
								                    <div class="container">
								                      	<div class="fluid-row">
									                       	<ul class="">
								                           		@if($currentDate < $testSubjectPaper->date_to_active)
										                    		<li id="startTest_mobile_{{$testSubjectPaper->id}}" ><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button></li>
										                    	@elseif(!is_object(Auth::user()))
										                    		<li id="startTest_mobile_{{$testSubjectPaper->id}}" ><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button></li>
										                    	@else
										                    		@if(in_array($testSubjectPaper->id, $registeredPaperIds) || true == $isSubCategoryPurchased)
										                    			@if(in_array($testSubjectPaper->id, $alreadyGivenPapers))
												                    		<li id="startTest_mobile_{{$testSubjectPaper->id}}" ><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" >Start</span></button></li>
													                    @else
													                    	<li id="startTest_mobile_{{$testSubjectPaper->id}}" onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->test_category_id}}" data-subcategory="{{$testSubjectPaper->test_sub_category_id}}" ><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true"></span>Start</button></li>
													                    @endif
													                @elseif($testSubjectPaper->price < 1 )
													                	<li id="startTest_mobile_{{$testSubjectPaper->id}}" onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->test_category_id}}" data-subcategory="{{$testSubjectPaper->test_sub_category_id}}" ><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true"></span>Start</button></li>
													                @else
													                	<li id="startTest_mobile_{{$testSubjectPaper->id}}" ><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Please purcahse to give test."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button></li>
												                    @endif
										                    	@endif
									                           	<li id="showUserResultMobileBtn_{{$testSubjectPaper->id}}">
									                           		@if($currentDate < $testSubjectPaper->date_to_active)
											                    		<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will enabled after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
											                    	@elseif(!is_object(Auth::user()))
											                    		<button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to see result." onClick="checkLogin();"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
											                    	@elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
												                    	<button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="{{$testSubjectPaper->id}}" data-toggle="tooltip" title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
												                    @else
												                    	<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will enabled after test given."><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
												                    @endif
									                           	</li>
									                           	<li>
									                           		<button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-calendar"></span> {{ $testSubjectPaper->date_to_active }}</button>
									                           	</li>
									                           	<li>
									                           		<button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-inr"></span> {{ $testSubjectPaper->price }} </button>
									                           	</li>
								                           		@if($currentDate < $testSubjectPaper->date_to_active)
											                    	<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Purchase test will be enabled on date to active">
											                    		@if($testSubjectPaper->price > 0)
											                    			Pay Now
											                    		@else
											                    			Free
											                    		@endif
											                    	</button></li>
											                    @else
											                    	@if(in_array($testSubjectPaper->id, $registeredPaperIds) || true == $isSubCategoryPurchased)
												                    	<li><button  disabled="true" class="btn-magick btn-sm btn3d" data-toggle="tooltip">
												                    		@if($testSubjectPaper->price > 0)
												                    			Paid
												                    		@else
												                    			Free
												                    		@endif
												                    	</button></li>
												                    @else
												                    	@if($testSubjectPaper->price > 0)
												                    		@if(!is_object(Auth::user()))
												                    			<li><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();">Pay Now</button></li>
											                    			@else
												                    			<li data-paper="{{$testSubjectPaper->id}}" onClick="purchaseMobileTest(this);"><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Purchase Test"> Pay Now</button></li>
													                    		<form id="purchaseTest_mobile_{{$testSubjectPaper->id}}" method="POST" action="{{ url('purchaseTest') }}">
													                    			{{ csrf_field() }}
													                    			<input type="hidden" name="paper_id" value="{{$testSubjectPaper->id}}">
													                    			<input type="hidden" name="category_id" value="{{$testSubjectPaper->test_category_id}}">
													                    			<input type="hidden" name="subcategory_id" value="{{$testSubjectPaper->test_sub_category_id}}">
													                    			<input type="hidden" name="subject_id" value="{{$testSubject->id}}">
													                    		</form>
												                    		@endif
												                    	@else
												                    		@if(!is_object(Auth::user()))
												                    			<li><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();">Free</button></li>
											                    			@else
												                    			<li><button  disabled="true" class="btn-magick btn-sm btn3d" data-toggle="tooltip">Free</button></li>
												                    		@endif
												                    	@endif
												                    @endif
											                    @endif
								                         	</ul>
								                       	</div>
							                     	</div>
							                  	</div>
					              			</div>
					              		@endforeach
					              	@else
							        	No Papers.
							        @endif
					            </div>
				          	</div>
					        </div>
				      	</div>
				    </div>
				@endforeach
			@else
            	No subjects are available.
			@endif
	  	</div>
	</section>
	<div id="review-model-{{$subcatId}}" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              &nbsp;&nbsp;&nbsp;
              <button class="close" data-dismiss="modal">×</button>
              <div class="form-group row ">
                <span style= "position:relative; top:7px;">
                  @if(isset($reviewData[$subcatId])) {{$reviewData[$subcatId]['avg']}} @else 0 @endif
                </span>
                <div  style="display: inline-block;">
                  <input name="input-{{$subcatId}}" class="rating rating-loading" value="@if(isset($reviewData[$subcatId])) {{$reviewData[$subcatId]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                </div>
                <span style= "position:relative; top:7px;">
                  @if(isset($reviewData[$subcatId]))
                    {{count($reviewData[$subcatId]['rating'])}} <i class="fa fa-group"></i>
                  @else
                    0 <i class="fa fa-group"></i>
                  @endif
                </span>
                @if(is_object(Auth::user()))
                  <button class="pull-right" data-toggle="modal" data-target="#rating-model-{{$subcatId}}">
                  @if(isset($reviewData[$subcatId]) && isset($reviewData[$subcatId]['rating'][Auth::user()->id]))
                    Edit Rating
                  @else
                    Give Rating
                  @endif
                  </button>
                @else
                  <button class="pull-right" onClick="checkLogin();">Give Rating</button>
                @endif
              </div>
            </div>
            <div class="modal-body row">
              <div class="form-group row" style="overflow: auto;">
                @if(isset($reviewData[$subcatId]))
                  @foreach($reviewData[$subcatId]['rating'] as $userId => $review)
                    <div class="user-block cmt-left-margin">
                      @if(is_file($userNames[$userId]['photo']) || (!empty($userNames[$userId]['photo']) && false == preg_match('/userStorage/',$userNames[$userId]['photo'])))
                        <img src="{{ asset($userNames[$userId]['photo'])}} " class="img-circle" alt="User Image">
                      @else
                        <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
                      @endif
                      <span class="username">{{ $userNames[$userId]['name'] }} </span>
                      <span class="description">Shared publicly - {{$review['updated_at']}}</span>
                    </div>
                    <br>
                    <input id="rating_input-{{$subcatId}}-{{$userId}}" name="input-{{$subcatId}}" class="rating rating-loading" value="{{$review['rating']}}" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                    {{$review['review']}}
                    <hr>
                  @endforeach
                @else
                  Please give ratings
                @endif
              </div>
            </div>
          </div>
        </div>
  	</div>
  	<div id="rating-model-{{$subcatId}}" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button class="close" data-dismiss="modal">×</button>
              Rate and Review
            </div>
            <div class="modal-body row">
              <form action="{{ url('giveRating')}}" method="POST">
                <div class="form-group row ">
                  {{ csrf_field() }}
                  @if(isset($reviewData[$subcatId]) && is_object(Auth::user()) && isset($reviewData[$subcatId]['rating'][Auth::user()->id]))
                    <input id="rating_input-{{$subcatId}}" name="input-{{$subcatId}}" class="rating rating-loading" value="{{$reviewData[$subcatId]['rating'][Auth::user()->id]['rating']}}" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                  @else
                    <input id="rating_input-{{$subcatId}}" name="input-{{$subcatId}}" class="rating rating-loading" value="0" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                  @endif
                  Review:<input type="text" name="review-text" class="form-control" value="@if(isset($reviewData[$subcatId])  && is_object(Auth::user()) && isset($reviewData[$subcatId]['rating'][Auth::user()->id])) {{trim($reviewData[$subcatId]['rating'][Auth::user()->id]['review'])}} @endif">
                  <br>
                  <input type="hidden" name="module_id" value="{{$subcatId}}">
                  <input type="hidden" name="module_type" value="2">
                  <input type="hidden" name="rating_id" value="@if(isset($reviewData[$subcatId]) && is_object(Auth::user()) && isset($reviewData[$subcatId]['rating'][Auth::user()->id])) {{$reviewData[$subcatId]['rating'][Auth::user()->id]['review_id']}} @endif">
                  <button type="submit" class="pull-right">Submit</button>
                </div>
              </form>
            </div>
          </div>
        </div>
  	</div>
@stop
@section('footer')
	@include('footer.footer')
	<script src="{{ asset('js/star-rating.js') }}"></script>
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
		        window.location.reload();
        	}
        });
	}
	function selectSubcategory(ele){
		id = parseInt($(ele).val());
		if( 0 < id ){
			$.ajax({
	            method: "POST",
	            url: "{{url('getSubCategories')}}",
	            data: {id:id}
	        })
	        .done(function( msg ) {
	        	select = document.getElementById('subcategory');
	        	select.innerHTML = '';
	        	var opt = document.createElement('option');
			    opt.value = '';
			    opt.innerHTML = 'Select Sub Category';
			    select.appendChild(opt);
			    if( 0 < msg.length){
					$.each(msg, function(idx, obj) {
					    var opt = document.createElement('option');
					    opt.value = obj.id;
					    opt.innerHTML = obj.name;
					    opt.setAttribute('data-price', obj.price);
					    select.appendChild(opt);
					});
				}
	        });
		}
	}
	function selectPanel(ele) {
		var cat = parseInt($('select#category').val());
		var subcat = parseInt($('select#subcategory').val());
		var userId = parseInt(document.getElementById('user_id').value);

		if($(ele).find(':selected').data('price') > 0){
			$('#price').html('Price: '+$(ele).find(':selected').data('price')+' Rs.');
			if( userId > 0 ){
				document.getElementById('paidStatus').text = 'Pay Now';
				document.getElementById('paidStatus').setAttribute('onClick', 'purchaseSubCategory(this);');
				var payNowDiv = document.getElementById('pay-now-form');
				var payUrl = "{{ url('purchaseTestSubCategory') }}";
				var csrfField = '{{ csrf_field() }}';
				payNowDiv.innerHTML = '<form id="purchaseSubCategory" method="POST" action="'+payUrl+'">'+csrfField +'<input type="hidden" name="category_id" value="'+cat+'"><input type="hidden" name="subcategory_id" value="'+subcat+'"></form>';
			} else {
				document.getElementById('paidStatus').setAttribute('onClick', 'checkLogin(this);');
				document.getElementById('paidStatus').text = 'Pay Now';
				document.getElementById('pay-now-form').innerHTML = '';
			}
		} else {
			$('#price').html('Price: 0 Rs.');
			document.getElementById('paidStatus').removeAttribute('onClick');
			document.getElementById('paidStatus').text = 'Free';
			document.getElementById('pay-now-form').innerHTML = '';
		}

		if( 0 < cat && 0 < subcat ){
			$.ajax({
	            method: "POST",
	            url: "{{url('getDataByCatSubCat')}}",
	            data: {cat:cat, subcat:subcat,user_id:userId}
	        })
	        .done(function( msg ) {
	        	divEle = document.getElementById('subjects');
	        	divEle.innerHTML = '';
	        	if(undefined !== msg['subjects'] && 0 < msg['subjects'].length) {
		        	$.each(msg['subjects'], function(ind, obj){
		        		if(false == $.isEmptyObject(obj)){
			        		var subId = obj.id;

			        		var mainPanelDiv = document.createElement('div');
			        		mainPanelDiv.className = "panel-group";
			        		mainPanelDiv.setAttribute('role','tablist');
			        		mainPanelDiv.setAttribute('aria-multiselectable','true');
			        		mainPanelDiv.id = 'headingOne';

			        		var defaultPanelDiv = document.createElement('div');
			        		defaultPanelDiv.className = "panel panel-default";

			        		var firstMainDiv = document.createElement('div');
			        		firstMainDiv.className = "panel-heading";
			        		firstMainDiv.setAttribute('role','tab');
			        		firstMainDiv.id = 'headingOne';

			        		var h4Ele = document.createElement('h4');
			        		h4Ele.className = 'panel-title';
			        		h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject'+ subId +'" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-minus"></i>'+ obj.name + '</a>';
			        		firstMainDiv.appendChild(h4Ele);
			        		defaultPanelDiv.appendChild(firstMainDiv);

			        		var secondMainDiv = document.createElement('div');
			        		secondMainDiv.id = 'subject'+ subId;
		        			secondMainDiv.className = "panel-collapse panel-lg collapse in";
			        		secondMainDiv.setAttribute('role','tabpanel');
			        		secondMainDiv.setAttribute('aria-labelledby','headingOne');

			        		var tableDiv = document.createElement('div');
			        		tableDiv.className = 'panel-body';

			        		var tableEle = document.createElement('table');
			        		tableEle.className = "table data-lg";
			        		if (undefined !== msg['papers'][subId] && msg['papers'][subId].length) {
				        		var tableHead = document.createElement('thead');
				        		var tableTr = document.createElement('tr');
				        		var trInnerhtml = '';
				        		trInnerhtml += '<th>Test Name</th>';
				                trInnerhtml += '<th>Start Test</th>';
				                trInnerhtml += '<th>Result</th>';
				                trInnerhtml += '<th>Date to Active</th>';
				                trInnerhtml += '<th>Price</th>';
				                trInnerhtml += '<th>Purchase Test</th>';
				                tableTr.innerHTML = trInnerhtml;
				                tableHead.appendChild(tableTr);
				                tableEle.appendChild(tableHead);
			                }
			                var tableBody = document.createElement('tbody');

			                if (undefined !== msg['papers'][subId] && msg['papers'][subId].length) {
		                		$.each(msg['papers'][subId], function(ind, obj){
		                			var tbodyTr = document.createElement("tr");
		                			if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
				                    	tbodyTr.setAttribute('style','background-color: #b3c2dc;');
				                    }
		                			var divInnerHtml = '';
		                			divInnerHtml += '<td class=" ">'+ obj.name+'</td>';
		                			if(msg['currentDate'] < obj.date_to_active){
		                				divInnerHtml += '<td id="startTest_'+obj.id+'"><button disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
		                			} else if(true == isNaN(userId)) {
		                				divInnerHtml += '<td id="startTest_'+obj.id+'"><button data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();"><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
		                			} else {
		                				if(true == (msg['registeredPaperIds'][obj.id] > 0) ||  true == msg['isSubCategoryPurchased']){
		                					if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
		                						divInnerHtml += '<td id="startTest_'+obj.id+'"><button disabled="true" data-toggle="tooltip" title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
		                					} else {
	                							divInnerHtml += '<td id="startTest_'+obj.id+'"><button onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.test_subject_id +'" data-category="'+ obj.test_category_id +'" data-subcategory="'+ obj.test_sub_category_id+'" data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
		                					}
		                				} else if(obj.price < 1){
		                					divInnerHtml += '<td id="startTest_'+obj.id+'"><button onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.test_subject_id +'" data-category="'+ obj.test_category_id +'" data-subcategory="'+ obj.test_sub_category_id+'" data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
		                				} else {
		                					divInnerHtml += '<td id="startTest_'+obj.id+'"><button disabled="true" data-toggle="tooltip" title="Please purchase to give test."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
		                				}
								    }
								    if(msg['currentDate'] < obj.date_to_active){
								    	divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
									    divInnerHtml += '<button disabled="true" data-toggle="tooltip" title="Result will display after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
									    divInnerHtml += '</td>';
									} else if(true == isNaN(userId)) {
		                				divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
									    divInnerHtml += '<button data-toggle="tooltip" title="Please login to see result." onClick="checkLogin();"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
									    divInnerHtml += '</td>';
		                			} else if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
								    	var testUrl = "{{ url('showUserTestResult') }}";
								    	var csrf_token = '{{ csrf_field() }}';
									    divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
									    divInnerHtml += '<form id="showUserTestResult_'+obj.id+'" method="POST" action="'+testUrl+'">';
									    divInnerHtml += csrf_token;
										divInnerHtml +='<input type="hidden" name="paper_id" value="'+obj.id+'"><input type="hidden" name="category_id" value="'+ obj.test_category_id +'"><input type="hidden" name="subcategory_id" value="'+ obj.test_sub_category_id+'"><input type="hidden" name="subject_id" value="'+ obj.test_subject_id +'"></form>';
									    divInnerHtml += '<button onClick="showUserTestResult(this);" data-paper_id="'+obj.id+'" data-toggle="tooltip" title="Result!"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
									    divInnerHtml += '</td>';
									} else {
										divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
									    divInnerHtml += '<button disabled="true" data-toggle="tooltip" title="Result will display after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
									    divInnerHtml += '</td>';
									}

								    divInnerHtml += '<td class=" ">'+ obj.date_to_active +'</td>';
								    divInnerHtml += '<td class=""><i class="fa fa-inr"></i>'+ obj.price +'</td>';

								    if(msg['currentDate'] < obj.date_to_active){
								    	if(obj.price > 1){
								    		divInnerHtml += '<td><button class="btn-primary" disabled="true" data-toggle="tooltip" title="Purchase test will be enabled on date to active">Pay Now</button></td>';
								    	} else {
								    		divInnerHtml += '<td><button disabled="true" data-toggle="tooltip" title="Purchase test will be enabled on date to active">Free</button></td>';
								    	}
								    } else {
									    if(true == (msg['registeredPaperIds'][obj.id] > 0) ||  true == msg['isSubCategoryPurchased']){
									    	if(obj.price > 1){
									    		divInnerHtml += '<td>Paid</td>';
									    	} else {
									    		divInnerHtml += '<td>Free</td>';
									    	}
									    } else {
									    	if(obj.price > 1){
									    		if(true == isNaN(userId)) {
												    divInnerHtml += '<td><button class="btn-primary" data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();">Pay Now</button></td>';
					                			} else{
				                				var purchaseTestUrl = "{{ url('purchaseTest') }}";
							    				var csrf_token = '{{ csrf_field() }}';
												divInnerHtml +='<td data-paper="'+obj.id+'" onClick="purchaseTest(this);"><form id="purchaseTest_'+obj.id+'" method="POST" action="'+purchaseTestUrl+'">'+csrf_token+'<input type="hidden" name="paper_id" value="'+obj.id+'"><input type="hidden" name="category_id" value="'+obj.test_category_id+'"><input type="hidden" name="subcategory_id" value="'+obj.test_sub_category_id+'"><input type="hidden" name="subject_id" value="'+obj.test_subject_id+'"></form><button class="btn-primary" data-toggle="tooltip" title="Purchase Test">Pay Now</button></td>';
					                			}
									    	} else {
									    		if(true == isNaN(userId)) {
												    divInnerHtml += '<td><button data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();">Free</button></td>';
					                			} else {
					                				divInnerHtml += '<td>Free</td>';
					                			}
									    	}
									    }
									}
								    tbodyTr.innerHTML = divInnerHtml;
								    tableBody.appendChild(tbodyTr);
								    tableEle.appendChild(tableBody);
		                		});
				   			} else {
			        			tableBody.innerHTML = 'No papers are available..';
			        			tableEle.appendChild(tableBody);
			        		}
			        		tableDiv.appendChild(tableEle);

			        		var mainSmallDiv = document.createElement('div');
	    					mainSmallDiv.className = "data-sm";

	    					if (undefined !== msg['papers'][subId] && msg['papers'][subId].length) {
		                		$.each(msg['papers'][subId], function(ind, obj){
		                			var panelDiv = document.createElement('div');
		                			panelDiv.className = 'panel panel-info';

		                			var panelHeadingDiv = document.createElement('div');
		                			panelHeadingDiv.className = 'toggle panel-heading';
		                			panelHeadingDiv.setAttribute('data-toggle','paper'+obj.id);
		                			if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
				                    	panelHeadingDiv.setAttribute('style','background-color: #b3c2dc;');
				                    }
		                			panelHeadingDiv.innerHTML = obj.name;

		                			var spanEle = document.createElement('span');
		                			spanEle.className = 'col-xs-2 pull-right';
		                			spanEle.innerHTML = '<i class="fa fa-chevron-down pull-right"></i>';
		                			panelHeadingDiv.appendChild(spanEle);

		                			panelDiv.appendChild(panelHeadingDiv);

		                			var panelContentDiv = document.createElement('div');
		                			panelContentDiv.id = 'paper'+obj.id;
		                			panelContentDiv.className = 'panel-body';

		                			var containerDiv = document.createElement('div');
		                			containerDiv.className='container';

		                			var rowDiv = document.createElement('div');
		                			rowDiv.className = 'fluid-row';

		                			var ulDiv = document.createElement('ul');
		                			ulDivInnerHtml = '';

		                			if(msg['currentDate'] < obj.date_to_active){
		                				ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
		                			} else if(true == isNaN(userId)) {
		                				ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
		                			} else {
		                				if(true == (msg['registeredPaperIds'][obj.id] > 0) ||  true == msg['isSubCategoryPurchased']){
		                					if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
		                						ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
		                					} else {
	                							ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.test_subject_id +'" data-category="'+ obj.test_category_id +'" data-subcategory="'+ obj.test_sub_category_id+'" data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
		                					}
		                				} else if(obj.price < 1){
		                					ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.test_subject_id +'" data-category="'+ obj.test_category_id +'" data-subcategory="'+ obj.test_sub_category_id+'" data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
		                				} else {
		                					ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Please purchase to give test."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
		                				}
								    }
								    if(msg['currentDate'] < obj.date_to_active){
								    	ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
									    ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will display after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
									    ulDivInnerHtml += '</li>';
									} else if(true == isNaN(userId)) {
		                				ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
									    ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to see result." onClick="checkLogin();"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
									    ulDivInnerHtml += '</li>';
		                			} else if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
								    	var testUrl = "{{ url('showUserTestResult') }}";
								    	var csrf_token = '{{ csrf_field() }}';
									    ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
									    ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="'+obj.id+'" data-toggle="tooltip" title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
									    ulDivInnerHtml += '</li>';
									} else {
										ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
									    ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will display after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
									    ulDivInnerHtml += '</li>';
									}

								    ulDivInnerHtml += '<li class=" "><button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-calendar"></span>'+ obj.date_to_active +'</button></li>';
								    ulDivInnerHtml += '<li class=""><button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-inr"></span>'+ obj.price +'</button></li>';

								    if(msg['currentDate'] < obj.date_to_active){

								    	if(obj.price > 1){
								    		ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Purchase test will be enabled on date to active">Pay Now</button></li>';
								    	} else {
								    		ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Purchase test will be enabled on date to active">Free</button></li>';
								    	}
								    } else {
									    if(true == (msg['registeredPaperIds'][obj.id] > 0) ||  true == msg['isSubCategoryPurchased']){
									    	if(obj.price > 1){
									    		ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip">Paid</button></li>';
									    	} else {
									    		ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip">Free</button></li>';
									    	}
									    } else {
									    	if(obj.price > 1){
									    		if(true == isNaN(userId)) {
												    ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to see result." onClick="checkLogin();">Pay Now</button></li>';
					                			} else{
					                				var purchaseTestUrl = "{{ url('purchaseTest') }}";
								    				var csrf_token = '{{ csrf_field() }}';
								    				ulDivInnerHtml += '<li data-paper="'+obj.id+'" onClick="purchaseTest(this);"><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Purchase Test">Pay Now</button></li><form id="purchaseTest_mobile_'+obj.id+'" method="POST" action="'+purchaseTestUrl+'">'+csrf_token+'<input type="hidden" name="paper_id" value="'+obj.id+'"><input type="hidden" name="category_id" value="'+ obj.test_category_id +'"><input type="hidden" name="subcategory_id" value="'+ obj.test_sub_category_id +'"><input type="hidden" name="subject_id" value="'+ obj.test_subject_id +'"></form>';
					                			}
									    	} else {
									    		if(true == isNaN(userId)) {
												    ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to give test." onClick="checkLogin();">Free</button></li>';
					                			} else {
					                				ulDivInnerHtml += '<li><button disabled="true" class="btn-magick btn-sm btn3d" data-toggle="tooltip">Free</button></li>';
					                			}
									    	}
									    }
									}

									ulDiv.innerHTML = ulDivInnerHtml;
									rowDiv.appendChild(ulDiv);
									containerDiv.appendChild(rowDiv);
									panelContentDiv.appendChild(containerDiv);
									panelDiv.appendChild(panelContentDiv);
									mainSmallDiv.appendChild(panelDiv);
		                		});
		                	}
		                	tableDiv.appendChild(mainSmallDiv);
							secondMainDiv.appendChild(tableDiv);
							defaultPanelDiv.appendChild(secondMainDiv);
							mainPanelDiv.appendChild(defaultPanelDiv);
							divEle.appendChild(mainPanelDiv);

			        	}
			        });
					if( userId > 0 ){
						if(true == msg['isSubCategoryPurchased']){
							document.getElementById('paidStatus').text = 'Paid';
							document.getElementById('paidStatus').removeAttribute('onClick');
							document.getElementById('pay-now-form').innerHTML = '';
						}
					}
		    	} else {
		    		var mainPanelDiv = document.createElement('div');
	        		mainPanelDiv.className = "panel-group";
	        		mainPanelDiv.setAttribute('role','tablist');
	        		mainPanelDiv.setAttribute('aria-multiselectable','true');
	        		mainPanelDiv.id = 'headingOne';

		    		var defaultPanelDiv = document.createElement('div');
	        		defaultPanelDiv.className = "panel panel-default";

	        		var firstMainDiv = document.createElement('div');
	        		firstMainDiv.className = "panel-heading";
	        		firstMainDiv.setAttribute('role','tab');
	        		firstMainDiv.id = 'headingOne'

	        		var h4Ele = document.createElement('h4');
	        		h4Ele.className = 'panel-title';
	        		h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject'+ subId +'" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-minus"></i>No subjects are available.</a>';
	        		firstMainDiv.appendChild(h4Ele);
	        		defaultPanelDiv.appendChild(firstMainDiv);
	        		mainPanelDiv.appendChild(defaultPanelDiv);
					divEle.appendChild(mainPanelDiv);
		    	}
		    	$('[id^=paper]').hide();
		    	$('.toggle').click(function() {
			      $input = $( this );
			      $target = $('#'+$input.attr('data-toggle'));
			      $target.slideToggle();
			      if($input.find('.col-xs-2 i').attr('class')=="fa fa-chevron-down pull-right")
			      {
			       $input.find('.col-xs-2 i').removeClass('fa-chevron-down');
			       $input.find('.col-xs-2 i').addClass('fa-chevron-up');
			     }
			     else if($input.find('.col-xs-2 i').attr('class')=="fa pull-right fa-chevron-up")
			     {
			       $input.find('.col-xs-2 i').removeClass('fa-chevron-up');
			       $input.find('.col-xs-2 i').addClass('fa-chevron-down');
			     }
			     else if($input.find('.col-xs-2 i').attr('class')=="fa pull-right fa-chevron-down")
			     {
			       $input.find('.col-xs-2 i').removeClass('fa-chevron-down');
			       $input.find('.col-xs-2 i').addClass('fa-chevron-up');
			     }
			   });
		    	var ratingDiv = document.getElementById('ratingDiv');
                ratingDiv.className = "row pull-right";
                ratingDiv.innerHTML = '';
                if(msg['ratingData'][subcat] && msg['ratingData'][subcat]['avg']){
                  ratingDiv.innerHTML = '<a data-toggle="modal" data-target="#review-model-'+subcat+'" style="cursor: pointer;"><span style= "position:relative; top:7px;">'+msg['ratingData'][subcat]['avg']+'</span><div style="display: inline-block;"><input id="rating_input'+subcat+'" name="input-" class="rating rating-loading" value="'+msg['ratingData'][subcat]['avg']+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><span style= "position:relative; top:7px;">'+Object.keys(msg['ratingData'][subcat]['rating']).length+' <i class="fa fa-group"></i></span></a>';
                } else {
                  ratingDiv.innerHTML = '<a data-toggle="modal" data-target="#review-model-'+subcat+'" style="cursor: pointer;"><span style= "position:relative; top:7px;">0</span><div style="display: inline-block;"><input id="rating_input'+subcat+'" name="input-" class="rating rating-loading" value="0" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><span style= "position:relative; top:7px;"> 0 <i class="fa fa-group"></i></span></a>';
                }

                var reviewModel = document.createElement('div');
                reviewModel.setAttribute('id','review-model-'+subcat);
                reviewModel.setAttribute('class','modal fade');
                reviewModel.setAttribute('role','dialog');

                reviewModelInnerHTML = '';
                if(msg['ratingData'][subcat] && msg['ratingData'][subcat]['avg']){
                  reviewModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">&nbsp;&nbsp;&nbsp;<button class="close" data-dismiss="modal">×</button><div class="form-group row "><span style= "position:relative; top:7px;">'+msg['ratingData'][subcat]['avg']+'</span><div  style="display: inline-block;"><input name="input-'+subcat+'" class="rating rating-loading" value="'+msg['ratingData'][subcat]['avg']+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><span style= "position:relative; top:7px;">'+Object.keys(msg['ratingData'][subcat]['rating']).length+' <i class="fa fa-group"></i></span>';
                } else {
                  reviewModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">&nbsp;&nbsp;&nbsp;<button class="close" data-dismiss="modal">×</button><div class="form-group row "><span style= "position:relative; top:7px;">0</span><div  style="display: inline-block;"><input name="input-'+subcat+'" class="rating rating-loading" value="0" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><span style= "position:relative; top:7px;"> 0 <i class="fa fa-group"></i></span>';
                }
                if(userId > 0){
                  reviewModelInnerHTML += '<button class="pull-right" data-toggle="modal" data-target="#rating-model-'+subcat+'">';
                  if(msg['ratingData'][subcat] && msg['ratingData'][subcat]['rating'][userId]){
                    reviewModelInnerHTML += 'Edit Rating';
                  } else {
                    reviewModelInnerHTML += 'Give Rating';
                  }
                  reviewModelInnerHTML += '</button>';
                } else {
                  reviewModelInnerHTML += '<button class="pull-right" onClick="checkLogin()">Give Rating</button>';
                }
                reviewModelInnerHTML += '</div></div>';

                reviewModelInnerHTML += '<div class="modal-body row">';
                if(msg['ratingData'][subcat] && msg['ratingData'][subcat]['rating']){
                  $.each(msg['ratingData'][subcat]['rating'], function(userId, reviewData) {
                    if('system' == msg['userNames'][userId]['image_exist']){
	                	var userImagePath = "{{ asset('') }}"+msg['userNames'][userId]['photo'];
		                var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
              		} else if('other' == obj.image_exist){
		                var userImagePath = msg['userNames'][userId]['photo'];
		                var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
	              	} else {
		                var userImagePath = "{{ asset('images/user1.png') }}";
		                var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
	              	}
	              	reviewModelInnerHTML += '<div class="user-block cmt-left-margin">'+userImage+'<span class="username">'+msg['userNames'][userId]['name']+'</span><span class="description">Shared publicly - '+reviewData.updated_at+'</span></div><br/>';
                    reviewModelInnerHTML += '<input id="rating_input-'+subcat+'-'+userId+'" name="input-'+subcat+'" class="rating rating-loading" value="'+reviewData.rating+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>'+reviewData.review+'<hr>';
                  });
                } else {
                  reviewModelInnerHTML += 'Please give ratings';
                }
                reviewModelInnerHTML += '</div></div></div></div></div>';
                reviewModel.innerHTML = reviewModelInnerHTML;
                divEle.appendChild(reviewModel);

                var ratingModel = document.createElement('div');
                ratingModel.setAttribute('id','rating-model-'+subcat);
                ratingModel.setAttribute('class','modal fade');
                ratingModel.setAttribute('role','dialog');
                var ratingUrl = "{{ url('giveRating')}}";
                var csrfField = '{{ csrf_field() }}';
                ratingModelInnerHTML = '';
                ratingModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">×</button>Rate and Review</div><div class="modal-body row"><form action="'+ratingUrl+'" method="POST"><div class="form-group row ">'+csrfField;
                if(msg['ratingData'][subcat] && msg['ratingData'][subcat]['rating'][userId]){
                  ratingModelInnerHTML += '<input id="rating_input-'+subcat+'" name="input-'+subcat+'" class="rating rating-loading" value="'+msg['ratingData'][subcat]['rating'][userId]['rating']+'" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">Review:';
                } else {
                  ratingModelInnerHTML += '<input id="rating_input-'+subcat+'" name="input-'+subcat+'" class="rating rating-loading" value="" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">Review:';
                }
                if(msg['ratingData'][subcat] && msg['ratingData'][subcat]['rating'][userId]){
                  ratingModelInnerHTML += '<input type="text" name="review-text" class="form-control" value="'+msg['ratingData'][subcat]['rating'][userId]['review']+'">';
                  ratingModelInnerHTML += '<br><input type="hidden" name="module_id" value="'+subcat+'"><input type="hidden" name="module_type" value="2"><input type="hidden" name="rating_id" value="'+msg['ratingData'][subcat]['rating'][userId]['review_id']+'"><button type="submit" class="pull-right">Submit</button></div></form></div></div></div>';
                } else {
                  ratingModelInnerHTML += '<input type="text" name="review-text" class="form-control" value="">';
                  ratingModelInnerHTML += '<br><input type="hidden" name="module_id" value="'+subcat+'"><input type="hidden" name="module_type" value="2"><input type="hidden" name="rating_id" value=""><button type="submit" class="pull-right">Submit</button></div></form></div></div></div>';
                }

                ratingModel.innerHTML = ratingModelInnerHTML;
                divEle.appendChild(ratingModel);
                var inputRating = $('input.rating');
		        if(inputRating.length) {
		        	inputRating.removeClass('rating-loading').addClass('rating-loading').rating();
		        }
	        });
		} else {
			divEle = document.getElementById('subjects');
        	divEle.innerHTML = '';

			var mainPanelDiv = document.createElement('div');
    		mainPanelDiv.className = "panel-group";
    		mainPanelDiv.setAttribute('role','tablist');
    		mainPanelDiv.setAttribute('aria-multiselectable','true');
    		mainPanelDiv.id = 'headingOne';

        	var defaultPanelDiv = document.createElement('div');
    		defaultPanelDiv.className = "panel panel-default";

    		var firstMainDiv = document.createElement('div');
    		firstMainDiv.className = "panel-heading";
    		firstMainDiv.setAttribute('role','tab');
    		firstMainDiv.id = 'headingOne'

    		var h4Ele = document.createElement('h4');
    		h4Ele.className = 'panel-title';
    		h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-minus"></i>No subjects are available.</a>';
    		firstMainDiv.appendChild(h4Ele);
    		defaultPanelDiv.appendChild(firstMainDiv);
    		mainPanelDiv.appendChild(defaultPanelDiv);
			divEle.appendChild(mainPanelDiv);
		}
	}
	function showUserTestResult(ele){
		var paperId = parseInt($(ele).data('paper_id'));
		document.getElementById('showUserTestResult_'+paperId).submit();
	}
	function purchaseTest(ele){
		$.confirm({
		    title: 'Confirmation',
		    content: 'Do you want to purchase this test?',
		    type: 'red',
		    typeAnimated: true,
		    buttons: {
		        	Ok: {
			            text: 'Ok',
			            btnClass: 'btn-red',
			            action: function(){
							var paperId = parseInt($(ele).data('paper'));
							document.getElementById('purchaseTest_'+paperId).submit();
					}
		        },
		        Cancel: function () {
		        }
		    }
		});
	}
	function purchaseMobileTest(ele){
		$.confirm({
		    title: 'Confirmation',
		    content: 'Do you want to purchase this test?',
		    type: 'red',
		    typeAnimated: true,
		    buttons: {
		        	Ok: {
			            text: 'Ok',
			            btnClass: 'btn-red',
			            action: function(){
							var paperId = parseInt($(ele).data('paper'));
							document.getElementById('purchaseTest_mobile_'+paperId).submit();
					}
		        },
		        Cancel: function () {
		        }
		    }
		});
	}
	function purchaseSubCategory(ele){
        $.confirm({
          title: 'Confirmation',
          content: 'Do you want to purchase this sub category?',
          type: 'red',
          typeAnimated: true,
          buttons: {
            Ok: {
              text: 'Ok',
              btnClass: 'btn-red',
              action: function(){
                document.getElementById('purchaseSubCategory').submit();
              }
            },
            Cancel: function () {
            }
          }
        });
    }
</script>
<script >
	function toggleIcon(e) {
	    $(e.target)
	    .prev('.panel-heading')
	    .find(".more-less")
	    .toggleClass('glyphicon-plus glyphicon-minus');
  	}
	$('.panel-group').on('hidden.bs.collapse', toggleIcon);
	$('.panel-group').on('shown.bs.collapse', toggleIcon);
  	$(document).ready(function() {
	    $('[id^=paper]').hide();
	    $('.toggle').click(function() {
	      $input = $( this );
	      $target = $('#'+$input.attr('data-toggle'));
	      $target.slideToggle();
	      if($input.find('.col-xs-2 i').attr('class')=="fa fa-chevron-down pull-right")
	      {
	       $input.find('.col-xs-2 i').removeClass('fa-chevron-down');
	       $input.find('.col-xs-2 i').addClass('fa-chevron-up');
	     }
	     else if($input.find('.col-xs-2 i').attr('class')=="fa pull-right fa-chevron-up")
	     {
	       $input.find('.col-xs-2 i').removeClass('fa-chevron-up');
	       $input.find('.col-xs-2 i').addClass('fa-chevron-down');
	     }
	     else if($input.find('.col-xs-2 i').attr('class')=="fa pull-right fa-chevron-down")
	     {
	       $input.find('.col-xs-2 i').removeClass('fa-chevron-down');
	       $input.find('.col-xs-2 i').addClass('fa-chevron-up');
	     }

	   });
  	});
</script>
@stop