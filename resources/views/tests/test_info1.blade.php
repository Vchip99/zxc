@extends('layouts.master')
@section('header')
	@include('layouts.home-js-css')
	@include('layouts.test-css')
	@include('header.header_menu')
@stop
@section('content')
		<!-- Start features section -->
		<section id="mu-features">
			<div class="container">
  				<div class="row">
			        <div class="col-lg-12 col-md-12">
			          	<div class="mu-features-area">
				            <!-- Start Title -->
				            <div class="mu-title">
				              <h2>Test Series</h2>
				              <p>The complete help for the class 5th to 10th (English, Math, Science), 12th Art, commerce, Science(English) . You could be requiring the teacher just for guidance or doubts or complete education. Even if you feel that you have an adequate preparation you could still assess your preparation or remove your exam fear by taking admission in "Kaizen coaching classes and education academy".</p>
				            </div>
			          	</div>
		          	</div>
					<div class="col-lg-12 col-md-12">
						<div class="container">
						  	<h2>Test</h2>
						  	<div class="panel-group" id="accordion">
							  	<div class="panel panel-default">
							      	<div class="panel-heading">
						        		<!--Primary buttons with dropdown menu-->
								  		<div class="panel panel-default">
										    <div class="panel-heading">
										    	<!-- <form class="mu-category-form"> -->
								                  	<!-- <input type="category" id="cat" placeholder="Type Category"> -->
								                  	<select id="category" name="category" onChange="selectSubcategory(this);">
								                  		<option>Select Category ...</option>
								                  		@foreach($testCategories as $testCategory)
								                  		<option value="{{ $testCategory->id }}">
								                  			{{$testCategory->name}}
								                  		</option>
								                  		@endforeach
								                  	</select>
								                  	<!-- <button class="mu-category-btn" >CATEGORY</button> -->
								                  	<!-- <input type="category" id="cat" placeholder="Type your subcategory"> -->
								                  	<select id="subcategory" name="subcategory">
								                  		<option>Select Sub Category ...</option>
								                  	</select>
								                  	<!-- <button class="mu-category-btn" >SUBCATEGORY</button> -->
								                  	<button type="submit" class="msgBtn" onClick="selectPanel();">Show Tests</button>
							                	<!-- </form> -->
									      	</div>
								        	<div class="panel-body">
						                    	<button type="submit" class="msgBtn" onClick="">PRIZE</button>
					   		                	<button type="submit" class="msgBtn" onClick="">ADD TO CART</button>
									        </div>
								  		</div>
									</div>
      							</div>
      						</div>
						</div>


						<input type="hidden" name="user" id="user"
							@if(Auth::user())
							value="{{Auth::user()->id}}"
							@else
							value="null"
							@endif
						>
						<div id="div_panel">
						@foreach($testCategories as $testCategory)
						    <div class="panel panel-default">
						      	<div class="panel-heading">
						        	<button id="category_{{$testCategory->id}}" class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse{{$testCategory->id}}" aria-expanded="false" aria-controls="collapseExample">
						                {{ $testCategory->name }}
					                </button>
						      	</div>
						      	<div id="collapse{{$testCategory->id}}" class="panel-collapse collapse ">
									<div class="panel-body">
										@if(isset($testSubCategories[$testCategory->id]))
											@foreach( $testSubCategories[$testCategory->id] as $testSubCategory)
												<div class="panel-heading">
										        	<button id="subcategory_{{$testSubCategory->id}}" class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse{{$testCategory->id}}{{$testSubCategory->id}}" aria-expanded="false" aria-controls="collapseExample">
										                {{ $testSubCategory->name }}
									                </button>
										      	</div>
									      		<div id="collapse{{$testCategory->id}}{{$testSubCategory->id}}" class="panel-collapse collapse ">
													<div class="panel-body">
												      	@if(isset($testSubjects[$testSubCategory->id]))
												      		@foreach($testSubjects[$testSubCategory->id] as $subject)
												      			<div class="panel-heading">
														        	<button id="subcategory_{{$testSubCategory->id}}" class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse{{$testCategory->id}}{{$testSubCategory->id}}{{$subject->id}}" aria-expanded="false" aria-controls="collapseExample">
														                {{$subject->name}}
													                </button>
														      	</div>
														      	<div id="collapse{{$testCategory->id}}{{$testSubCategory->id}}{{$subject->id}}" class="panel-collapse collapse ">
																	<div class="panel-body">
																		<ul class="list-group">
																		@if(isset($testSubjectPapers[$subject->id]))
																			@foreach($testSubjectPapers[$subject->id] as $paper)
																			<div class="list-group-item">
																				<div id="outer"><h4 class="inner">{{ $paper->name }}</h4>
																					<div class="inner">
																						<button type="button" class="msgBtn" onClick="startTest(this);" data-paper="{{$paper->id}}" data-subject="{{$subject->id}}" data-category="{{$testCategory->id}}" data-subcategory="{{$testSubCategory->id}}">Start test</button>
																					</div>
																					<div class="inner"><button type="button" class="msgBtn2" onClick="">Result</button></div>
																					<div class="inner"><button class="msgBtnBack" onClick="">Date to Active</button></div>
																					<div class="inner"><button class="msgBtnBack" onClick="">prize</button></div>
																					<div class="inner"><button class="msgBtnBack" onClick="">Add to cart</button></div>
																			  	</div>
																			</div>
																			@endforeach
																		@else
																			<div class="list-group-item">
																				<div id="outer"><h4 class="inner">No Papers</h4></div>
																			</div>
																		@endif
																		</ul>
																	</div>
																</div>
												      		@endforeach
												      	@else
												      		<ul class="list-group">
												      			<div class="list-group-item">
																	<div id="outer"><h4 class="inner">No Subjects</h4></div>
																</div>
															</ul>
												      	@endif
												    </div>
												</div>
											@endforeach
										@else
											<ul class="list-group">
								      			<div class="list-group-item">
													<div id="outer"><h4 class="inner">No Sub Category.</h4></div>
												</div>
											</ul>
										@endif
									 </div>
								</div>
						    </div>
						@endforeach
						</div>
				  	</div>
				</div>
      		</div>
		</section>
<script type="text/javascript">
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

	function startTest(ele){
		var paper = parseInt($(ele).data('paper'));
		var subject = parseInt($(ele).data('subject'));
		var category = parseInt($(ele).data('category'));
		var subcategory = parseInt($(ele).data('subcategory'));

		var userId = parseInt(document.getElementById('user').value);

		if(0 < userId){
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
			if(confirm('Please login first to start test. Click "Ok" button to login.')){
				window.location="{{url('/home')}}";
			}
		}
	}

	function selectPanel(argument) {
		var cat = parseInt($('select#category').val());
		var subcat = parseInt($('select#subcategory').val());
		if( 0 < cat && 0 < subcat ){
			$.ajax({
	            method: "POST",
	            url: "{{url('getDataByCatSubCat')}}",
	            data: {cat:cat, subcat:subcat}
	        })
	        .done(function( msg ) {
	        	divEle = document.getElementById('div_panel');
	        	divEle.innerHTML = '';
	        	if(undefined !== msg['subjects'] && 0 < msg['subjects'].length) {
		        	$.each(msg['subjects'], function(ind, obj){
		        		var subId = obj.id;
		        		var divPanel = document.createElement('div');
		        		divPanel.className = "panel panel-default";
		        		var divPanelHeading = document.createElement('div');
		        		divPanelHeading.className = "panel-heading";
		        		divPanelHeading.innerHTML = '<button id="category_1" class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse'+subId+'" aria-expanded="false" aria-controls="collapseExample">'+ obj.name +'</button>';
		        		divPanel.appendChild(divPanelHeading);
		        		var divCollapse = document.createElement('div');
		        		divCollapse.id = "collapse"+ subId;
		        		divCollapse.className = "panel-collapse collapse";

		        		var divPanelBody = document.createElement('div');
		        		divPanelBody.className = "panel-body";

		        		if (undefined !== msg['papers'][subId] && msg['papers'][subId].length) {
			        		var divPanelBodyUl = document.createElement('ul');
			        		divPanelBodyUl.className = "list-group";

			        		$.each(msg['papers'][subId], function(ind, obj){
			        			var divPanelBodyUlAnc = document.createElement('div');
			        			divPanelBodyUlAnc.className = "list-group-item";
			        			divPanelBodyUlAnc.setAttribute('href','#');

			        			var addDiv ='';
								addDiv += '<div id="outer"><h4 class="inner">'+ obj.name +'</h4>';
								addDiv += '<button type="button" class="msgBtn" onClick="startTest(this);" data-category="'+cat+'" data-subcategory="'+subcat+'" data-paper="'+obj.id+'" data-subject="'+subId+'">Start test</button>';
								addDiv += '<div class="inner"><button type="submit" class="msgBtn2" onclick="">Result</button></div>';
								addDiv += '<div class="inner"><button class="msgBtnBack" onclick="">Date to Active</button></div>';
								addDiv += '<div class="inner"><button class="msgBtnBack" onclick="">prize</button></div>';
								addDiv += '<div class="inner"><button class="msgBtnBack" onclick="">Add to cart</button></div>';
								addDiv += '</div>';
								divPanelBodyUlAnc.innerHTML = addDiv;
								divPanelBodyUl.appendChild(divPanelBodyUlAnc);
			        		});

		        			divPanelBody.appendChild(divPanelBodyUl);
		        		} else {
		        			var addDiv ='';
							addDiv += '<div id="outer"><h4 class="inner">No Papers.</h4>';
							addDiv += '</div>';
		        			divPanelBody.innerHTML = addDiv;
		        		}
						divCollapse.appendChild(divPanelBody);

		        		divPanel.appendChild(divCollapse);
		        		divEle.appendChild(divPanel);
		        	});
		    	} else {
		    		addDivNew = '';
        			addDivNew += '<div class="panel panel-default">';
        			addDivNew += '<div class="panel-heading">';
        			addDivNew += '<button id="category_1" class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse13" aria-expanded="false" aria-controls="collapseExample">No Subjects</button>';
    				addDivNew += '</div></div>';
					divEle.innerHTML = addDivNew;

		    	}
	        });
		} else {
			divEle = document.getElementById('div_panel');
        	addDivNew = '';
			addDivNew += '<div class="panel panel-default">';
			addDivNew += '<div class="panel-heading">';
			addDivNew += '<button id="category_1" class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse13" aria-expanded="false" aria-controls="collapseExample">No Subjects</button>';
			addDivNew += '</div></div>';
			divEle.innerHTML = addDivNew;

		}
	}

</script>
@stop
@section('footer')
	@include('footer.footer')
@stop