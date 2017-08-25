@extends('layouts.master')
@section('header-title')
  <title>Online Test Series for GATE, CAT, Aptitude |V-edu</title>
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
	        <div class="col-md-6 col-sm-6  col-xs-12 mrgn_10_top_btm  ">
	          <select class="form-control" id="category" name="category" title="Category" onChange="selectSubcategory(this);">
          		<option>Select Category ...</option>
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
	        <div class="col-md-6 col-sm-6  col-xs-12 mrgn_10_top_btm">
	          <select class="form-control" id="subcategory" name="subcategory" title="Sub Category" onChange="selectPanel();">
	          		<option>Select Sub Category ...</option>
	          		@foreach($testSubCategories as $testSubCategory)
	          			@if($subcatId == $testSubCategory->id)
	          				<option value="{{$testSubCategory->id}}" selected>
	          			@else
	          				<option value="{{$testSubCategory->id}}">
	          			@endif
	          				{{$testSubCategory->name}}
	          			</option>
	          		@endforeach
	          	</select>
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
	    		@foreach($testSubjects as $testSubject)
	    			@if($subject == $testSubject->id)
				    	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="border-style: dotted;border-color: red;">
				    @else
				    	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" >
				    @endif
				      	<div class="panel panel-default">
					        <div class="panel-heading" role="tab" id="headingOne">
					          <h4 class="panel-title">
					            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject{{$testSubject->id}}" aria-expanded="true" aria-controls="collapseOne" title="{{ $testSubject->name }}">
					              <i class="more-less glyphicon glyphicon-plus"></i>
					              {{ $testSubject->name }}
					            </a>
					          </h4>
					        </div>
					        <div id="subject{{$testSubject->id}}" class="panel-collapse collapse panel-lg" role="tabpanel" aria-labelledby="headingOne">
					          	<div class="panel-body">
					            <table class="table data-lg">
					              	<thead>
					                	<tr>
						                  	<th>Test Number</th>
						                  	<th>Start test</th>
						                  	<th>Result</th>
						                  	<th>Date to Active</th>
						                  	<th>Price</th>
						                  	<th>Add to cart</th>
					                	</tr>
					              	</thead>
					              	<tbody>
			                      		@if(isset($testSubjectPapers[$testSubject->id]))
				        					@foreach($testSubjectPapers[$testSubject->id] as $testSubjectPaper)
							                	<tr>
				        						@if($paper == $testSubjectPaper->id)
								                	<td class=" ">{{ $testSubjectPaper->name }} <b style="color: red;">[new]</b></td>
								                @else
								                    <td class=" ">{{ $testSubjectPaper->name }}</td>
								                @endif
							                    	@if($currentDate < $testSubjectPaper->date_to_active)
							                    		<td id="startTest_{{$testSubjectPaper->id}}"><button disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
							                    	@elseif(!is_object(Auth::user()))
							                    		<td id="startTest_{{$testSubjectPaper->id}}"><button disabled="true" data-toggle="tooltip" title="Please login to give test."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
							                    	@else
							                    		@if(in_array($testSubjectPaper->id, $registeredPaperIds))
							                    			@if(in_array($testSubjectPaper->id, $alreadyGivenPapers))
									                    		<td id="startTest_{{$testSubjectPaper->id}}"><button disabled="true" data-toggle="tooltip" title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
										                    @else
										                    	<td id="startTest_{{$testSubjectPaper->id}}" onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->test_category_id}}" data-subcategory="{{$testSubjectPaper->test_sub_category_id}}"><button data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button></td>
										                    @endif
										                @else
										                	<td id="startTest_{{$testSubjectPaper->id}}"><button disabled="true" data-toggle="tooltip" title="Add to cart to give test."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>
									                    @endif
							                    	@endif

								                    <td id="showUserResultBtn_{{$testSubjectPaper->id}}">
								                    	@if($currentDate < $testSubjectPaper->date_to_active)
								                    		<button disabled="true" data-toggle="tooltip" title="Result will enabled after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
								                    	@elseif(!is_object(Auth::user()))
								                    		<button disabled="true" data-toggle="tooltip" title="Please login to see result.""><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
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
								                    <td class=" ">{{ $testSubjectPaper->date_to_active }}</td>
								                    <td class=""><i class="fa fa-inr"></i>{{ $testSubjectPaper->price }}</td>
								                    @if($currentDate < $testSubjectPaper->date_to_active)
								                    	<td><button disabled="true" data-toggle="tooltip" title="Add to Cart will be enabled on date to active"><i class="fa fa-cart-plus" aria-hidden="true" ></i></button></td>
								                    @else
								                    	@if(in_array($testSubjectPaper->id, $registeredPaperIds))
									                    	<td><button disabled="true" data-toggle="tooltip" title="Already Added to Cart!"><i class="fa fa-cart-plus" aria-hidden="true"></i></button></td>
									                    @else
									                    	<td id="registerPaper_{{$testSubjectPaper->id}}" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->test_category_id}}" data-subcategory="{{$testSubjectPaper->test_sub_category_id}}" onClick="registerPaper(this);"><button  data-toggle="tooltip" title="Add to Cart!"><i class="fa fa-cart-plus" aria-hidden="true" ></i></button></td>
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
					                			<div class="toggle panel-heading" data-toggle="paper{{$testSubjectPaper->id}}">{{$testSubjectPaper->name}}<span class="col-xs-2 pull-right"><i class="fa fa-chevron-down pull-right"></i></span>
					                			</div>
							                  	<div id="paper{{$testSubjectPaper->id}}" class="panel-body">
								                    <div class="container">
								                      	<div class="fluid-row">
									                       	<ul class="">
								                           		@if($currentDate < $testSubjectPaper->date_to_active)
										                    		<li id="startTest_mobile_{{$testSubjectPaper->id}}" ><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button></li>
										                    	@elseif(!is_object(Auth::user()))
										                    		<li id="startTest_mobile_{{$testSubjectPaper->id}}" ><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Please login to give test."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button></li>
										                    	@else
										                    		@if(in_array($testSubjectPaper->id, $registeredPaperIds))
										                    			@if(in_array($testSubjectPaper->id, $alreadyGivenPapers))
												                    		<li id="startTest_mobile_{{$testSubjectPaper->id}}" ><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" >Start</span></button></li>
													                    @else
													                    	<li id="startTest_mobile_{{$testSubjectPaper->id}}" onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->test_category_id}}" data-subcategory="{{$testSubjectPaper->test_sub_category_id}}" ><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true"></span>Start</button></li>
													                    @endif
													                @else
													                	<li id="startTest_mobile_{{$testSubjectPaper->id}}" ><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Add to cart to give test."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button></li>
												                    @endif
										                    	@endif
									                           	<li id="showUserResultMobileBtn_{{$testSubjectPaper->id}}">
									                           		@if($currentDate < $testSubjectPaper->date_to_active)
											                    		<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will enabled after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
											                    	@elseif(!is_object(Auth::user()))
											                    		<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Please login to see result.""><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
											                    	@elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
											                    		<form id="showUserTestResult_{{$testSubjectPaper->id}}" method="POST" action="{{ url('showUserTestResult') }}">
											                    			{{ csrf_field() }}
											                    			<input type="hidden" name="paper_id" value="{{$testSubjectPaper->id}}">
											                    			<input type="hidden" name="category_id" value="{{$testSubjectPaper->test_category_id}}">
											                    			<input type="hidden" name="subcategory_id" value="{{$testSubjectPaper->test_sub_category_id}}">
											                    			<input type="hidden" name="subject_id" value="{{$testSubject->id}}">
											                    		</form>
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
											                    	<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Add to Cart will be enabled on date to active"><span class="fa fa-cart-plus" aria-hidden="true" ></span> Add</button></li>
											                    @else
											                    	@if(in_array($testSubjectPaper->id, $registeredPaperIds))
												                    	<li><button  disabled="true" class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Already Added to Cart!"><span class="fa fa-cart-plus" aria-hidden="true"></span> Add</button></li>
												                    @else
												                    	<li id="registerPaper_mobile_{{$testSubjectPaper->id}}" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->test_category_id}}" data-subcategory="{{$testSubjectPaper->test_sub_category_id}}" onClick="registerPaper(this);"><button class="btn-magick btn-sm btn3d"  data-toggle="tooltip" title="Add to Cart!"><span class="fa fa-cart-plus" aria-hidden="true" ></span> Add</button></li>
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
@stop
@section('footer')
	@include('footer.footer')
	<script>

	function startTest(ele){
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
					window.open("{{ url('instructions')}}", 'My Window', 'height=900px !important,width=1500px !important');
	        	}
	        });
		} else {
			$.confirm({
	          title: 'Confirmation',
	          content: 'Please login first to start test. Click "Ok" button to login.',
	          type: 'red',
	          typeAnimated: true,
	          buttons: {
	                Ok: {
	                    text: 'Ok',
	                    btnClass: 'btn-red',
	                    action: function(){
	                      window.location="{{url('/home')}}";
	                    }
	                },
	                Cancle: function () {
	                }
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
		        showUserResultMobileBtnInnerHtml = '<form id="showUserTestResult_'+paper+'" method="POST" action="'+url+'">'+csrfToken;
		        showUserResultMobileBtnInnerHtml += '<input type="hidden" name="paper_id" value="'+paper+'"><input type="hidden" name="category_id" value="'+category+'"><input type="hidden" name="subcategory_id" value="'+subcategory+'"><input type="hidden" name="subject_id" value="'+subject+'"></form>';

		        showUserResultMobileBtnInnerHtml += '<button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="'+paper+'" data-toggle="tooltip" title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
		        showUserResultMobileBtn.innerHTML = showUserResultMobileBtnInnerHtml;

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
			    opt.innerHTML = 'Select Sub Category ...';
			    select.appendChild(opt);
			    if( 0 < msg.length){
					$.each(msg, function(idx, obj) {
					    var opt = document.createElement('option');
					    opt.value = obj.id;
					    opt.innerHTML = obj.name;
					    select.appendChild(opt);
					});
				}
	        });
		}
	}

	function selectPanel(argument) {
		var cat = parseInt($('select#category').val());
		var subcat = parseInt($('select#subcategory').val());
		var userId = parseInt(document.getElementById('user_id').value);
		if( 0 < cat && 0 < subcat ){
			$.ajax({
	            method: "POST",
	            url: "{{url('getDataByCatSubCat')}}",
	            data: {cat:cat, subcat:subcat}
	        })
	        .done(function( msg ) {
	        	divEle = document.getElementById('subjects');
	        	divEle.innerHTML = '';
	        	if(undefined !== msg['subjects'] && 0 < msg['subjects'].length) {
		        	$.each(msg['subjects'], function(ind, obj){
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
		        		h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject'+ subId +'" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-plus"></i>'+ obj.name + '</a>';
		        		firstMainDiv.appendChild(h4Ele);
		        		defaultPanelDiv.appendChild(firstMainDiv);

		        		var secondMainDiv = document.createElement('div');
		        		secondMainDiv.id = 'subject'+ subId;
		        		secondMainDiv.className = "panel-collapse collapse panel-lg";
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
			                trInnerhtml += '<th>Add to Cart</th>';
			                tableTr.innerHTML = trInnerhtml;
			                tableHead.appendChild(tableTr);
			                tableEle.appendChild(tableHead);
		                }
		                var tableBody = document.createElement('tbody');

		                if (undefined !== msg['papers'][subId] && msg['papers'][subId].length) {
	                		$.each(msg['papers'][subId], function(ind, obj){
	                			var tbodyTr = document.createElement("tr");
	                			var divInnerHtml = '';
	                			divInnerHtml += '<td class=" ">'+ obj.name+'</td>';
	                			if(msg['currentDate'] < obj.date_to_active){
	                				divInnerHtml += '<td id="startTest_'+obj.id+'"><button disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
	                			} else if(true == isNaN(userId)) {
	                				divInnerHtml += '<td id="startTest_'+obj.id+'"><button disabled="true" data-toggle="tooltip" title="Please login to give test."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
	                			} else {
	                				if(msg['registeredPaperIds'].length > 0 && true == msg['registeredPaperIds'].indexOf(obj.id) > -1){
	                					if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
	                						divInnerHtml += '<td id="startTest_'+obj.id+'"><button disabled="true" data-toggle="tooltip" title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
	                					} else {
                							divInnerHtml += '<td id="startTest_'+obj.id+'"><button onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.test_subject_id +'" data-category="'+ obj.test_category_id +'" data-subcategory="'+ obj.test_sub_category_id+'" data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
	                					}
	                				} else {
	                					divInnerHtml += '<td id="startTest_'+obj.id+'"><button disabled="true" data-toggle="tooltip" title="Add to cart to give test."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
	                				}
							    }
							    if(msg['currentDate'] < obj.date_to_active){
							    	divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
								    divInnerHtml += '<button disabled="true" data-toggle="tooltip" title="Result will display after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
								    divInnerHtml += '</td>';
								} else if(true == isNaN(userId)) {
	                				divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
								    divInnerHtml += '<button disabled="true" data-toggle="tooltip" title="Please login to see result."><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
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
							    	divInnerHtml += '<td><button disabled="true" data-toggle="tooltip" title="Add to Cart will be enabled on date to active"><i class="fa fa-cart-plus" aria-hidden="true" ></i></button></td>';
							    } else {
								    if(msg['registeredPaperIds'].length > 0 && true == msg['registeredPaperIds'].indexOf(obj.id) > -1){
								    	divInnerHtml += '<td><button disabled="true" data-toggle="tooltip" title="Already Added to Cart."><i class="fa fa-cart-plus" aria-hidden="true" ></i></button></td>';
								    } else {
								    	divInnerHtml += '<td id="registerPaper_'+obj.id+'" data-paper="'+ obj.id +'" data-subject="'+ obj.test_subject_id +'" data-category="'+ obj.test_category_id +'" data-subcategory="'+ obj.test_sub_category_id+'" onClick="registerPaper(this);" ><button data-toggle="tooltip" title="Add to Cart!"><i class="fa fa-cart-plus" aria-hidden="true" ></i></button></td>';
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
	                				ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Please login to give test."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
	                			} else {
	                				if(msg['registeredPaperIds'].length > 0 && true == msg['registeredPaperIds'].indexOf(obj.id) > -1){
	                					if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
	                						ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
	                					} else {
                							ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.test_subject_id +'" data-category="'+ obj.test_category_id +'" data-subcategory="'+ obj.test_sub_category_id+'" data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
	                					}
	                				} else {
	                					ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Add to cart to give test."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
	                				}
							    }
							    if(msg['currentDate'] < obj.date_to_active){
							    	ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
								    ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will display after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
								    ulDivInnerHtml += '</li>';
								} else if(true == isNaN(userId)) {
	                				ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
								    ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Please login to see result."><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
								    ulDivInnerHtml += '</li>';
	                			} else if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
							    	var testUrl = "{{ url('showUserTestResult') }}";
							    	var csrf_token = '{{ csrf_field() }}';
								    ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
								    ulDivInnerHtml += '<form id="showUserTestResult_'+obj.id+'" method="POST" action="'+testUrl+'">';
								    ulDivInnerHtml += csrf_token;
									ulDivInnerHtml +='<input type="hidden" name="paper_id" value="'+obj.id+'"><input type="hidden" name="category_id" value="'+ obj.test_category_id +'"><input type="hidden" name="subcategory_id" value="'+ obj.test_sub_category_id+'"><input type="hidden" name="subject_id" value="'+ obj.test_subject_id +'"></form>';
								    ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="'+obj.id+'" data-toggle="tooltip" title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
								    ulDivInnerHtml += '</li>';
								} else {
									ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
								    ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will display after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
								    ulDivInnerHtml += '</li>';
								}

							    ulDivInnerHtml += '<li class=" "><button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-inr"></span>'+ obj.date_to_active +'</button></li>';
							    ulDivInnerHtml += '<li class=""><button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-inr"></span>'+ obj.price +'</button></li>';

							    if(msg['currentDate'] < obj.date_to_active){
							    	ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Add to Cart will be enabled on date to active"><span class="fa fa-cart-plus" aria-hidden="true" ></span>Add</button></li>';
							    } else {
								    if(msg['registeredPaperIds'].length > 0 && true == msg['registeredPaperIds'].indexOf(obj.id) > -1){
								    	ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Already Added to Cart."><span class="fa fa-cart-plus" aria-hidden="true" ></span>Add</button></li>';
								    } else {
								    	ulDivInnerHtml += '<li id="registerPaper_mobile_'+obj.id+'" data-paper="'+ obj.id +'" data-subject="'+ obj.test_subject_id +'" data-category="'+ obj.test_category_id +'" data-subcategory="'+ obj.test_sub_category_id+'" onClick="registerPaper(this);" ><button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Add to Cart!"><span class="fa fa-cart-plus" aria-hidden="true" ></span>Add</button></li>';
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
		        	});
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
	        		h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject'+ subId +'" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-plus"></i>No subjects are available.</a>';
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
    		h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-plus"></i>No subjects are available.</a>';
    		firstMainDiv.appendChild(h4Ele);
    		defaultPanelDiv.appendChild(firstMainDiv);
    		mainPanelDiv.appendChild(defaultPanelDiv);
			divEle.appendChild(mainPanelDiv);

		}
	}

	function registerPaper(ele){
		var paper = parseInt($(ele).data('paper'));
		var subject = parseInt($(ele).data('subject'));
		var category = parseInt($(ele).data('category'));
		var subcategory = parseInt($(ele).data('subcategory'));
		var userId = parseInt(document.getElementById('user_id').value);
	    if( true == isNaN(userId)){
	      $.alert({
	          title: 'Alert!',
	          content: 'Please login first and then add test.',
	        });
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
						        registerEle.innerHTML = '<button data-toggle="tooltip" title="Already Added to Cart!"><i class="fa fa-cart-plus" aria-hidden="true"></i></button>';


						        var registerMobileEle = document.getElementById('registerPaper_mobile_'+paper);
						        registerMobileEle.setAttribute('data-paper', 0);
								registerMobileEle.setAttribute('data-subject', 0);
								registerMobileEle.setAttribute('data-category', 0);
								registerMobileEle.setAttribute('data-subcategory', 0);
								registerMobileEle.removeAttribute('onclick');
						        registerMobileEle.innerHTML ='';
						        registerMobileEle.innerHTML = '<button class="btn-magick btn-sm btn3d" data-toggle="tooltip" title="Already Added to Cart!"><span class="fa fa-cart-plus" aria-hidden="true"></span>Add</button>';

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