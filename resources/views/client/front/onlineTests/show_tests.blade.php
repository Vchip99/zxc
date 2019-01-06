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
		.pay-now span{
			color: #fff;
			font-weight: bold;
		}
  	</style>
@stop
@section('header-js')
  	@include('layouts.home-js')
@stop
@section('content')
  	@include('client.front.header_menu')
  	<section id="vchip-background" class="mrgn_60_btm">
	  <div class="vchip-background-single">
	    <div class="vchip-background-img">
	      <figure>
	        <img src="{{asset('images/exam.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip Exam" />
	      </figure>
	    </div>
	    <div class="vchip-background-content">
	      <h2 class="animated bounceInLeft">Digital Education</h2>
	    </div>
	  </div>
	</section>
  	<section class="v_container ">
 	<div class="container">
	   <h2 class="v_h2_title text-center"> Exam</h2>
	   <hr class="section-dash-dark"/>
	  	<div class="row label-primary">
	    <div class="col-md-8  col-md-offset-2  ">
	      	<div class="row text-center">
		        <div class="col-md-4 col-sm-4  col-xs-12 mrgn_10_top_btm  ">
		          	<select class="form-control" id="category" name="category" onChange="selectSubcategory(this);" title="Category">
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
		              	@if(count($payableTestCategories) > 0)
			              @foreach($payableTestCategories as $payableTestCategory)
			              		@if( $catId == $payableTestCategory->id)
			              			<option value="{{$payableTestCategory->id}}" selected="true">
			              		@else
			              			<option value="{{$payableTestCategory->id}}">
			              		@endif
			                  		{{$payableTestCategory->name}}
			                  	</option>
			              @endforeach
			            @endif
	          		</select>
		        </div>
		        <div class="col-md-4 col-sm-4  col-xs-12 mrgn_10_top_btm">
		          	<select class="form-control" id="subcategory" name="subcategory" onChange="selectPanel(this);" title="Sub Category">
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
		          		@if(count($payableTestSubCategories) > 0)
		          			@foreach($payableTestSubCategories as $payableTestSubCategory)
		          				@if($subcatId == $payableTestSubCategory->id)
		          					<option value="{{$payableTestSubCategory->id}}" data-price="{{$purchasedPayableSubCategories[$payableTestSubCategory->id]->client_user_price}}" selected>
			          			@else
			          				<option value="{{$payableTestSubCategory->id}}" data-price="{{$purchasedPayableSubCategories[$payableTestSubCategory->id]->client_user_price}}">
			          			@endif
			          				{{$payableTestSubCategory->name}}
			          			</option>
		          			@endforeach
		          		@endif
		          	</select>
		        </div>
		        <div class="col-md-4 col-sm-4 mrgn_10_top_btm " style="display: inline-block !important; ">
		        <div class="pay-now " >
		        	@if($selectedSubCategory->admin_price > 0 && $selectedSubCategory->client_user_price > 0)
        				<span id="price">Price: {{$selectedSubCategory->client_user_price}} Rs.</span>
        			@else
        				<span id="price">Price: {{$selectedSubCategory->price}} Rs.</span>
        			@endif
			       	@if(is_object($loginUser))
			        	@if('true' == $isTestSubCategoryPurchased)
				        	<a id="paidStatus" class="btn btn-sm btn-default" style="cursor: pointer;" >Paid</a>
				        @else
				        	@if(($selectedSubCategory->admin_price > 0 && $selectedSubCategory->client_user_price > 0) || $selectedSubCategory->price > 0)
								<a id="paidStatus" href="{{ url('purchaseTestSubCategory')}}/{{$subcatId}}" class="btn btn-sm btn-default" style="cursor: pointer;" >Pay Now</a>
							@else
								<a id="paidStatus" class="btn btn-sm btn-default" style="cursor: pointer;" >Free</a>
							@endif
						@endif
					@else
						@if(($selectedSubCategory->admin_price > 0 && $selectedSubCategory->client_user_price > 0) || $selectedSubCategory->price > 0)
							<a id="paidStatus" class="btn btn-sm btn-default" style="cursor: pointer;" onClick="checkLogin(this);">Pay Now</a>
						@else
							<a id="paidStatus" class="btn btn-sm btn-default" style="cursor: pointer;" >Free</a>
						@endif
					@endif
			     </div>
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
					          <h4 class="panel-title" title="{{ $testSubject->name }}">
					            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject{{$testSubject->id}}" aria-expanded="true" aria-controls="collapseOne" class="">
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
						                  	<th>Start test</th>
						                  	<th>Result</th>
						                  	<th>Date to Active</th>
						                  	<th>Price</th>
						                  	<th>Add to Favourite</th>
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
							                    	@elseif(!is_object($loginUser))
							                    		@if(1 == $testSubjectPaper->is_free && 1 == $testSubjectPaper->allowed_unauthorised_user)
										                	<td id="startTest_{{$testSubjectPaper->id}}"><button onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->category_id}}" data-subcategory="{{$testSubjectPaper->sub_category_id}}"  data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button></td>
										                @else
							                    			<td id="startTest_{{$testSubjectPaper->id}}"><button data-toggle="tooltip" title="Please login to give test." data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->category_id}}" data-subcategory="{{$testSubjectPaper->sub_category_id}}" onClick="checkLogin(this);"><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
							                    		@endif
							                    	@else
							                    		@if(in_array($testSubjectPaper->id, $alreadyGivenPapers))
									                    	<td id="startTest_{{$testSubjectPaper->id}}"><button disabled="true" data-toggle="tooltip" title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
										                @elseif( 'true' == $isTestSubCategoryPurchased || 1 == $testSubjectPaper->is_free || ('false' == $isPayableSubCategory && $selectedSubCategory->price <= 0 && $selectedSubCategory->admin_price > 0))
										                	<td id="startTest_{{$testSubjectPaper->id}}"><button onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->category_id}}" data-subcategory="{{$testSubjectPaper->sub_category_id}}"  data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button></td>
										                @else
										                	<td id="startTest_{{$testSubjectPaper->id}}"><button disabled="true" data-toggle="tooltip" title="Please purchase sub category to give test."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
									                    @endif
							                    	@endif

								                    <td id="showUserResultBtn_{{$testSubjectPaper->id}}">
								                    	@if($currentDate < $testSubjectPaper->date_to_active)
								                    		<button disabled="true" data-toggle="tooltip" title="Result will enabled after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
								                    	@elseif(!is_object($loginUser))
								                    		<button data-toggle="tooltip" title="Please login to see result" onClick="checkLogin(this);"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
								                    	@elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
								                    		<button  onClick="showUserTestResult(this);" data-paper_id="{{$testSubjectPaper->id}}" data-toggle="tooltip" title="Result!"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
								                    		<form id="showUserTestResult_{{$testSubjectPaper->id}}" method="POST" action="{{ url('showUserTestResult') }}">
								                    			{{ csrf_field() }}
								                    			<input type="hidden" name="paper_id" value="{{$testSubjectPaper->id}}">
								                    			<input type="hidden" name="category_id" value="{{$testSubjectPaper->category_id}}">
								                    			<input type="hidden" name="subcategory_id" value="{{$testSubjectPaper->sub_category_id}}">
								                    			<input type="hidden" name="subject_id" value="{{$testSubject->id}}">
								                    		</form>
									                    @else
									                    	<button  disabled="true" data-toggle="tooltip" title="Result will enabled after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
									                    @endif
								                    </td>
								                    <td class=" ">{{ date("Y-m-d", strtotime($testSubjectPaper->date_to_active)) }}</td>
								                    <td class="">{{($testSubjectPaper->is_free)?'Free':'Paid'}}</td>
								                    @if(is_object($loginUser))
									                    @if($currentDate < $testSubjectPaper->date_to_active)
									                    	<td><button disabled="true" data-toggle="tooltip" title="Add to Favourite will be enabled after date to active"><i class="fa fa-star" aria-hidden="true" ></i></button></td>
									                    @else
									                    	@if(in_array($testSubjectPaper->id, $registeredPaperIds))
										                    	<td><button disabled="true" data-toggle="tooltip" title="Already Added to Favourite!"><i class="fa fa-star" aria-hidden="true" style="color: rgb(233, 30, 99);"></i></button></td>
										                    @elseif('true' == $isTestSubCategoryPurchased )
										                    	<td id="registerPaper_{{$testSubjectPaper->id}}" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->category_id}}" data-subcategory="{{$testSubjectPaper->sub_category_id}}" onClick="registerPaper(this);"><button data-toggle="tooltip" title="Add to Favourite!"><i class="fa fa-star" aria-hidden="true" ></i></button></td>
										                    @else
										                    	@if(($selectedSubCategory->admin_price > 0 && $selectedSubCategory->client_user_price > 0) || $selectedSubCategory->price > 0)
										                    		<td><button disabled="true" data-toggle="tooltip" title="Please purchase sub category to Add to Favourite!"><i class="fa
										                    		fa-star" aria-hidden="true"></i></button></td>
										                    	@else
										                    		<td id="registerPaper_{{$testSubjectPaper->id}}" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->category_id}}" data-subcategory="{{$testSubjectPaper->sub_category_id}}" onClick="registerPaper(this);"><button data-toggle="tooltip" title="Add to Favourite!"><i class="fa fa-star" aria-hidden="true" ></i></button></td>
										                    	@endif
										                    @endif
									                    @endif
									                @else
									                	<td onClick="checkLogin(this);"><button data-toggle="tooltip" title="Add to Favourite!"><i class="fa fa-star" aria-hidden="true" ></i></button></td>
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
										                    		<li id="startTest_mobile_{{$testSubjectPaper->id}}"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button></li>
										                    	@elseif(!is_object($loginUser))
										                    		@if(1 == $testSubjectPaper->is_free && 1 == $testSubjectPaper->allowed_unauthorised_user)
													                	<li id="startTest_mobile_{{$testSubjectPaper->id}}"><button class="btn-magick btn-sm btn3d" onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->category_id}}" data-subcategory="{{$testSubjectPaper->sub_category_id}}"  data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true"></span>Start</button></li>
													                @else
										                    			<li id="startTest_mobile_{{$testSubjectPaper->id}}"><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to give test." data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->category_id}}" data-subcategory="{{$testSubjectPaper->sub_category_id}}" onClick="checkLogin(this);"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button></li>
										                    		@endif
										                    	@else
										                    		@if(in_array($testSubjectPaper->id, $alreadyGivenPapers))
											                    		<li id="startTest_mobile_{{$testSubjectPaper->id}}"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" >Start</span></button></li>
										                    		@elseif( 'true' == $isTestSubCategoryPurchased  || 1 == $testSubjectPaper->is_free ||('false' == $isPayableSubCategory && $selectedSubCategory->price <= 0))
													                	<li id="startTest_mobile_{{$testSubjectPaper->id}}"><button class="btn-magick btn-sm btn3d" onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->category_id}}" data-subcategory="{{$testSubjectPaper->sub_category_id}}"  data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true"></span>Start</button></li>
													                @else
													                	<li id="startTest_mobile_{{$testSubjectPaper->id}}"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Please purchase sub category to give test."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button></li>
												                    @endif
										                    	@endif

									                           	<li id="showUserResultMobileBtn_{{$testSubjectPaper->id}}">
									                           		@if($currentDate < $testSubjectPaper->date_to_active)
											                    		<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will enabled after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
											                    	@elseif(!is_object($loginUser))
											                    		<button class="btn-magick btn-sm btn3d" data-toggle="Please login to see result" onClick="checkLogin(this);"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
											                    	@elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
												                    	<button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="{{$testSubjectPaper->id}}" data-toggle="tooltip" title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
												                    @else
												                    	<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will enabled after test given."><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
												                    @endif
									                           	</li>
									                           	<li>
									                           		<button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-calendar"></span> {{ date("Y-m-d", strtotime($testSubjectPaper->date_to_active)) }}</button>
									                           	</li>
									                           	<li>
									                           		<button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-inr"></span> {{($testSubjectPaper->is_free)?'Free':'Paid'}} </button>
									                           	</li>
									                           	@if(is_object($loginUser))
									                           		@if($currentDate < $testSubjectPaper->date_to_active)
												                    	<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Add to Favourite will be enabled after date to active"><span class="fa fa-star" aria-hidden="true" ></span> Add</button></li>
												                    @else
												                    	@if(in_array($testSubjectPaper->id, $registeredPaperIds))
													                    	<li><button disabled="true" class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Already Added to Favourite!"><span class="fa fa-star" aria-hidden="true" style="color: rgb(233, 30, 99);"></span> Add</button></li>
													                    @elseif('true' == $isTestSubCategoryPurchased)
													                    	<li id="registerPaper_mobile_{{$testSubjectPaper->id}}" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->category_id}}" data-subcategory="{{$testSubjectPaper->sub_category_id}}" onClick="registerPaper(this);"><button data-toggle="tooltip" title="Add to Favourite!" class="btn-magick btn-sm btn3d"><span class="fa fa-star" aria-hidden="true" ></span> Add</button></li>
													                    @else
													                    	@if(($selectedSubCategory->admin_price > 0 && $selectedSubCategory->client_user_price > 0) || $selectedSubCategory->price > 0)
													                    		<li><button disabled="true" class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please purchase sub category to Add to Favourite!"><span class="fa fa-star" aria-hidden="true"></span> Add</button></li>
													                    	@else
													                    		<li id="registerPaper_mobile_{{$testSubjectPaper->id}}" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->category_id}}" data-subcategory="{{$testSubjectPaper->sub_category_id}}" onClick="registerPaper(this);"><button data-toggle="tooltip" title="Add to Favourite!" class="btn-magick btn-sm btn3d"><span class="fa fa-star" aria-hidden="true" ></span> Add</button></li>
													                    	@endif
													                    @endif
												                    @endif
												                @else
												                	<li onClick="checkLogin(this);"><button data-toggle="tooltip" title="Add to Favourite!" class="btn-magick btn-sm btn3d"><span class="fa fa-star" aria-hidden="true" ></span> Add</button></li>
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
@stop
@section('footer')
	@include('footer.client-footer')
<script>
	function checkLogin(ele){
		var paper = $(ele).data('paper');
		var subject = $(ele).data('subject');
		var category = $(ele).data('category');
		var subcategory = $(ele).data('subcategory');
	    $('#loginUserModel').modal();
	    if(category > 0 && subcategory > 0 && subject > 0 && paper > 0){
			$('#loginModelBtn').attr('data-paper',parseInt(paper));
			$('#loginModelBtn').attr('data-subject',parseInt(subject));
			$('#loginModelBtn').attr('data-category',parseInt(category));
			$('#loginModelBtn').attr('data-subcategory',parseInt(subcategory));
		}
	    return false;
	}

	function startTest(ele){
		var windowHeight = screen.height;
		var windowWidth = screen.width;
		var popup_window =window.open("", 'My Window', 'height='+windowHeight+'px !important,width='+windowWidth+'px !important');
		var paper = parseInt($(ele).data('paper'));
		var subject = parseInt($(ele).data('subject'));
		var category = parseInt($(ele).data('category'));
		var subcategory = parseInt($(ele).data('subcategory'));
		var userId = parseInt(document.getElementById('user_id').value);
		if(0 < paper && 0 < subject && 0 < subcategory){
			$.ajax({
	            method: "POST",
	            url: "{{url('setClientUserSessions')}}",
	            data: {paper:paper, subject:subject, category:category, subcategory:subcategory}
	        })
	        .done(function( msg ) {
	        	if( msg ){
					popup_window.location = "{{ url('instructions')}}";
					popup_window.focus();
	        	}
	        });
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
		        showUserResultMobileBtn.innerHTML = '<button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="'+paper+'" data-toggle="tooltip" title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
		        window.location.reload();
        	}
        });
	}
	function selectSubcategory(ele){
		id = parseInt($(ele).val());
		if( 0 < id ){
			$.ajax({
	            method: "POST",
	            url: "{{url('getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion')}}",
	            data: {id:id}
	        })
	        .done(function( msg ) {
	        	select = document.getElementById('subcategory');
	        	select.innerHTML = '';
	        	var opt = document.createElement('option');
			    opt.value = '';
			    opt.innerHTML = 'Select Sub Category';
			    select.appendChild(opt);
			    if(msg['sub_categories']){
		    		$.each(msg['sub_categories'], function(idx, obj) {
					    var opt = document.createElement('option');
					    opt.value = obj.id;
					    opt.setAttribute('data-price', obj.price);
					    opt.innerHTML = obj.name;
					    select.appendChild(opt);
					});
				}
				if(msg['clientPurchasedSubCategories']){
		    		$.each(msg['clientPurchasedSubCategories'], function(idx, obj) {
					    var opt = document.createElement('option');
					    opt.value = obj['sub_category_id'];
					    opt.setAttribute('data-price', obj['client_user_price']);
					    opt.innerHTML = obj['sub_category'];
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
				var url = "{{ url('purchaseTestSubCategory')}}/"+subcat;
				document.getElementById('paidStatus').setAttribute('href', url);
				document.getElementById('paidStatus').text = 'Pay Now';
			} else {
				document.getElementById('paidStatus').setAttribute('onClick', 'checkLogin(this);');
				document.getElementById('paidStatus').text = 'Pay Now';
			}
		} else {
			$('#price').html('Price: 0 Rs.');
			document.getElementById('paidStatus').removeAttribute('href');
			document.getElementById('paidStatus').removeAttribute('onClick');
			document.getElementById('paidStatus').text = 'Free';
		}

		if( 0 < cat && 0 < subcat ){
			$.ajax({
	            method: "POST",
	            url: "{{url('getOnlineSubjectsAndPapersByCatIdBySubcatIdAssociatedWithQuestion')}}",
	            data: {cat:cat, subcat:subcat}
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
			        		h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject'+ subId +'" aria-expanded="true" aria-controls="collapseOne" class=""><i class="more-less glyphicon glyphicon-minus"></i>'+ obj.name + '</a>';
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
				                trInnerhtml += '<th>Add to Favourite</th>';
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
		                				if(1 == obj.is_free && 1 == obj.allowed_unauthorised_user){
		                					divInnerHtml += '<td id="startTest_'+obj.id+'"><button onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.subject_id +'" data-category="'+ obj.category_id +'" data-subcategory="'+ obj.sub_category_id+'" data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
										} else {
		                					divInnerHtml += '<td id="startTest_'+obj.id+'"><button data-toggle="tooltip" title="Please login to give test." data-paper="'+ obj.id +'" data-subject="'+ obj.subject_id +'" data-category="'+ obj.category_id +'" data-subcategory="'+ obj.sub_category_id+'" onClick="checkLogin(this);"><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
										}
		                			} else {
	                					if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
	                						divInnerHtml += '<td id="startTest_'+obj.id+'"><button disabled="true" data-toggle="tooltip" title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
	                					} else if('true' == msg['isTestSubCategoryPurchased'] || 1 == obj.is_free || $(ele).find(':selected').data('price') <= 0) {
                							divInnerHtml += '<td id="startTest_'+obj.id+'"><button onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.subject_id +'" data-category="'+ obj.category_id +'" data-subcategory="'+ obj.sub_category_id+'" data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
	                					}else {
		                					divInnerHtml += '<td id="startTest_'+obj.id+'"><button disabled="true" data-toggle="tooltip" title="Please purchase sub category to give test."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
		                				}

								    }
								    if(msg['currentDate'] < obj.date_to_active){
								    	divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
									    divInnerHtml += '<button disabled="true" data-toggle="tooltip" title="Result will display after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
									    divInnerHtml += '</td>';
									} else if(true == isNaN(userId)) {

		                				divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
									    divInnerHtml += '<button data-toggle="tooltip" title="Please login to see result" onClick="checkLogin(this);"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
									    divInnerHtml += '</td>';
		                			} else if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
								    	var testUrl = "{{ url('showUserTestResult') }}";
								    	var csrf_token = '{{ csrf_field() }}';
									    divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
									    divInnerHtml += '<button onClick="showUserTestResult(this);" data-paper_id="'+obj.id+'" data-toggle="tooltip" title="Result!"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
									    divInnerHtml += '<form id="showUserTestResult_'+obj.id+'" method="POST" action="'+testUrl+'">';
									    divInnerHtml += csrf_token;
										divInnerHtml +='<input type="hidden" name="paper_id" value="'+obj.id+'"><input type="hidden" name="category_id" value="'+ obj.category_id +'"><input type="hidden" name="subcategory_id" value="'+ obj.sub_category_id+'"><input type="hidden" name="subject_id" value="'+ obj.subject_id +'"></form>';
									    divInnerHtml += '</td>';
									} else {
										divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
									    divInnerHtml += '<button disabled="true" data-toggle="tooltip" title="Result will display after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
									    divInnerHtml += '</td>';
									}

								    divInnerHtml += '<td class=" ">'+ format_time(new Date(obj.date_to_active)) +'</td>';
								    divInnerHtml += '<td class="">';
								    if(1 == obj.is_free){
								    	divInnerHtml += 'Free';
								    } else {
								    	divInnerHtml += 'Paid';
								    }
								    divInnerHtml += '</td>';

								    if(false == isNaN(userId)) {
									    if(msg['currentDate'] < obj.date_to_active){
									    	divInnerHtml += '<td disabled="true" ><button data-toggle="tooltip" title="Add to Favourite will be enabled after date to active"><i class="fa fa-star" aria-hidden="true" ></i></button></td>';
									    } else {
										    if(msg['registeredPaperIds'].length > 0 && true == msg['registeredPaperIds'].indexOf(obj.id) > -1){
										    	divInnerHtml += '<td><button disabled="true" data-toggle="tooltip" title="Already Added to Favourite." ><i class="fa fa-star" aria-hidden="true" style="color: rgb(233, 30, 99);"></i></button></td>';
										    } else if('true' == msg['isTestSubCategoryPurchased']) {
										    	divInnerHtml += '<td id="registerPaper_'+obj.id+'" data-paper="'+ obj.id +'" data-subject="'+ obj.subject_id +'" data-category="'+ obj.category_id +'" data-subcategory="'+ obj.sub_category_id+'" onClick="registerPaper(this);" ><button data-toggle="tooltip" title="Add to Favourite!"><i class="fa fa-star" aria-hidden="true" ></i></button></td>';
										    } else {
										    	if($(ele).find(':selected').data('price') > 0){
													divInnerHtml += '<td><button disabled="true" data-toggle="tooltip" title="Please purchase sub category to Add to Favourite."><i class="fa fa-star" aria-hidden="true" ></i></button></td>';
												} else {
													divInnerHtml += '<td id="registerPaper_'+obj.id+'" data-paper="'+ obj.id +'" data-subject="'+ obj.subject_id +'" data-category="'+ obj.category_id +'" data-subcategory="'+ obj.sub_category_id+'" onClick="registerPaper(this);" ><button data-toggle="tooltip" title="Add to Favourite!"><i class="fa fa-star" aria-hidden="true" ></i></button></td>';
												}
										    }
										}
									} else {
										divInnerHtml += '<td onClick="checkLogin(this);" ><button data-toggle="tooltip" title="Add to Favourite!"><i class="fa fa-star" aria-hidden="true" ></i></button></td>';
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
		                			panelHeadingDiv.innerHTML = obj.name + '<span class="col-xs-2 pull-right"><i class="fa fa-chevron-down pull-right"></i></span>';
		                			panelDiv.appendChild(panelHeadingDiv);

		                			var panelContentDiv = document.createElement('div');
		                			panelContentDiv.id = 'paper'+obj.id;
		                			panelContentDiv.className = 'panel-body';
		                			panelContentDiv.setAttribute('style', 'padding:2px 0px;');

		                			var containerDiv = document.createElement('div');
		                			containerDiv.className = 'container'

		                			var rowDiv = document.createElement('div');
		                			rowDiv.className = 'fluid-row';

		                			var ulDiv = document.createElement('ul');
		                			ulDivInnerHtml = '';
		                			if(msg['currentDate'] < obj.date_to_active){
		                				ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
		                			} else if(true == isNaN(userId)) {
		                				if(1 == obj.is_free && 1 == obj.allowed_unauthorised_user){
		                					ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.subject_id +'" data-category="'+ obj.category_id +'" data-subcategory="'+ obj.sub_category_id+'" data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
										} else {
		                					ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to give test." onClick="checkLogin(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.subject_id +'" data-category="'+ obj.category_id +'" data-subcategory="'+ obj.sub_category_id+'"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
		                				}
		                			} else {
	                					if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
	                						ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
	                					} else if('true' == msg['isTestSubCategoryPurchased'] || 1 == obj.is_free || $(ele).find(':selected').data('price') <= 0){
                							ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.subject_id +'" data-category="'+ obj.category_id +'" data-subcategory="'+ obj.sub_category_id+'" data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';

	                					} else {
		                					ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Please purchase sub category to give test."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
		                				}
								    }
								    if(msg['currentDate'] < obj.date_to_active){
								    	ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
									    ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will display after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
									    ulDivInnerHtml += '</li>';
									} else if(true == isNaN(userId)) {
		                				ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
									    ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Please login to see result" onClick="checkLogin(this);"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
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

								    ulDivInnerHtml += '<li class=" "><button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-calendar"></span>'+ format_time(new Date(obj.date_to_active)) +'</button></li>';
								    ulDivInnerHtml += '<li class=""><button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-rs"></span>';
								    if(1 == obj.is_free){
								    	ulDivInnerHtml += 'Free';
								    } else {
								    	ulDivInnerHtml += 'Paid';
								    }
								    ulDivInnerHtml += '</button></li>';

								    if(false == isNaN(userId)) {
									    if(msg['currentDate'] < obj.date_to_active){
									    	ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Add to Cart will be enabled after date to active"><span class="fa fa-star" aria-hidden="true" ></span>Add</button></li>';
									    } else {
										    if(msg['registeredPaperIds'].length > 0 && true == msg['registeredPaperIds'].indexOf(obj.id) > -1){
										    	ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Already Added to Favourite."><span class="fa fa-star" aria-hidden="true" style="color: rgb(233, 30, 99);"></span>Add</button></li>';
										    } else if('true' == msg['isTestSubCategoryPurchased']) {
										    	ulDivInnerHtml += '<li id="registerPaper_mobile_'+obj.id+'" data-paper="'+ obj.id +'" data-subject="'+ obj.subject_id +'" data-category="'+ obj.category_id +'" data-subcategory="'+ obj.sub_category_id+'" onClick="registerPaper(this);" ><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Add to Cart!"><span class="fa fa-star" aria-hidden="true" ></span>Add</button></li>';
										    } else {
										    	if($(ele).find(':selected').data('price') > 0){
													ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Please purchase sub category to Add to Cart."><span class="fa fa-star" aria-hidden="true" ></span>Add</button></li>';
												} else {
													ulDivInnerHtml += '<li id="registerPaper_mobile_'+obj.id+'" data-paper="'+ obj.id +'" data-subject="'+ obj.subject_id +'" data-category="'+ obj.category_id +'" data-subcategory="'+ obj.sub_category_id+'" onClick="registerPaper(this);" ><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Add to Cart!"><span class="fa fa-star" aria-hidden="true" ></span>Add</button></li>';
												}
										    }
										}
									} else {
										ulDivInnerHtml += '<li onClick="checkLogin(this);" ><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Add to Cart!"><span class="fa fa-star" aria-hidden="true" ></span>Add</button></li>';
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
						if('true' == msg['isTestSubCategoryPurchased']){
							document.getElementById('paidStatus').text = 'Paid';
							document.getElementById('paidStatus').removeAttribute('href');
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
	        		h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-minus"></i>No subjects are available.</a>';
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

	function format_time(date_obj) {
	  // formats a javascript Date object into a 12h AM/PM time string
	  var day = date_obj.getDate();
	  var month = date_obj.getMonth()+1;
	  var year = date_obj.getFullYear();
	  var hour = date_obj.getHours();
	  var minute = date_obj.getMinutes();
	  var amPM = (hour > 11) ? " PM" : " AM";
	  if(hour > 12) {
	    hour -= 12;
	  } else if(hour == 0) {
	    hour = "12";
	  }
	  if(minute < 10) {
	    minute = "0" + minute;
	  }
	  return year+"-"+month+"-"+day;
	}

	function registerPaper(ele){
		var paper = parseInt($(ele).data('paper'));
		var subject = parseInt($(ele).data('subject'));
		var category = parseInt($(ele).data('category'));
		var subcategory = parseInt($(ele).data('subcategory'));
		var userId = parseInt(document.getElementById('user_id').value);

	    if( true == isNaN(userId)){
	      	$('#loginUserModel').modal();
	    } else if(paper > 0) {
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
						        url: "{{url('registerClientUserPaper')}}",
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
						        registerMobileEle.innerHTML = '<button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Already Added to Favourite!" ><span class="fa fa-star" aria-hidden="true" style="color: rgb(233, 30, 99);"></span>Add</button>';
					      	});
				    	}
			        },
			        Cancel: function () {
			        }
			    }
			});
		}
	}

	function showUserTestResult(ele){
	  	var paperId = $(ele).data('paper_id');
    	document.getElementById('showUserTestResult_'+paperId).submit();
	}

	function checkRegisterPaperPermission(ele){
		$.alert({
		    title: 'Alert!',
		    content: 'Please register this paper to add to cart.',
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