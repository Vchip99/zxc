@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Question </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Test </li>
      <li class="active"> Manage Question </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
	<script src="{{ asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
	@php
		if(Session::has('client_selected_category')){
			$clientSelectedCategoryId = Session::get('client_selected_category');
		} else {
			$clientSelectedCategoryId = 0;
		}
		if(Session::has('client_selected_subcategory')){
			$clientSelectedSubCategoryId = Session::get('client_selected_subcategory');
		} else {
			$clientSelectedSubCategoryId = 0;
		}
		if(Session::has('client_selected_subject')){
			$clientSelectedSubjectId = Session::get('client_selected_subject');
		} else {
			$clientSelectedSubjectId = 0;
		}
		if(Session::has('client_selected_paper')){
			$clientSelectedPaperId = Session::get('client_selected_paper');
		} else {
			$clientSelectedPaperId = 0;
		}
		if(Session::has('client_selected_section')){
			$clientSelectedSectionId = Session::get('client_selected_section');
		} else {
			$clientSelectedSectionId = 0;
		}
		if(Session::has('client_selected_question_type')){
			$clientSelectedQuestionType = Session::get('client_selected_question_type');
		} else {
			$clientSelectedQuestionType = 1;
		}
		if(Session::has('client_next_question_no')){
			$clientNextQuestionNo = Session::get('client_next_question_no');
		} else {
			$clientNextQuestionNo = 1;
		}
		if(Session::has('last_common_data')){
			$lastCommonData = Session::get('last_common_data');
		} else {
			$lastCommonData = '';
		}
	@endphp
	<div id="main ">
		<ul id ="ul">
	  		<li title="ADD MCQ"><a class="btn active" id="mcq_ques">ADD MCQ</a></li>
	  		<li title="ADD NUMERICAL"><a class="btn" id="num_ques">ADD NUMERICAL</a></li>
	  		@if($prevQuestionId > 0)
	  			<li title="Prev Question"><a class="btn" id="prev_ques" href="{{url('onlinetestquestion')}}/{{$prevQuestionId}}/edit">Prev Question</a></li>
	  		@else
	  			<li title="No Prev Question"><a class="btn" id="prev_ques">No Prev Question</a></li>
	  		@endif
	  		@if($nextQuestionId > 0)
		  		<li title="Next Question"><a class="btn" id="next_ques" href="{{url('onlinetestquestion')}}/{{$nextQuestionId}}/edit">Next Question</a></li>
	  		@elseif(( $prevQuestionId > 0 || null == $prevQuestionId ) && null == $nextQuestionId )
	  			<li title="Add Question"><a class="btn" id="next_ques" href="{{url('createOnlineTestQuestion')}}">Add Question</a></li>
	  		@else
	  			<li title="No Next Question"><a class="btn" id="next_ques">No Next Question</a></li>
	  		@endif
		</ul>
		<div class="container admin_div">
		@if(Session::has('message'))
			<div class="alert alert-success" id="message">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			  	{{ Session::get('message') }}
			</div>
		@endif
		@if(isset($testQuestion->id))
			<form id="createForm" name="updateQuestion" action="{{url('updateOnlineTestQuestion')}}" method="POST" role="form" data-toggle="validator">
			{{method_field('PUT')}}
			<input type="hidden" name="question_id" id="question_id" value="{{$testQuestion->id}}">
		@else
			<form id="createForm" name="createQuestion" action="{{url('createOnlineTestQuestion')}}" method="POST">
		@endif
		{{csrf_field()}}
			<div class="form-group row @if ($errors->has('category')) has-error @endif">
			    <label class="col-sm-2 col-form-label">Category Name:</label>
			    <div class="col-sm-3">
		          	@if(count($testCategories) > 0 && isset($testQuestion->id))
		          		@foreach($testCategories as $testCategory)
			              @if( $testCategory->id == $testQuestion->category_id )
			                <input class="form-control"  type="text" name="category_text" value="{{$testCategory->name}}" readonly="true">
			                <input type="hidden" name="category" value="{{$testCategory->id}}">
			              @endif
			            @endforeach
		          	@else
				      	<select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
				          	<option value="">Select Category</option>
				          	@if(count($testCategories) > 0)
					            @foreach($testCategories as $testCategory)
					            	@if($clientSelectedCategoryId == $testCategory->id)
					                	<option value="{{$testCategory->id}}" selected="true">{{$testCategory->name}}</option>
					                @else
					                	<option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
					                @endif
					            @endforeach
					        @endif
				      	</select>
		          	@endif
			      @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
			    </div>
			    <input type="hidden" id="selected_category_id" name="selected_category_id" value="{{$clientSelectedCategoryId}}">
		  	</div>
		  	<div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
			    <label class="col-sm-2 col-form-label">Sub Category Name:</label>
			    <div class="col-sm-3">
		        	@if(count($testSubCategories) > 0 && isset($testQuestion->id))
		        		@foreach($testSubCategories as $testSubCategory)
				            @if($testQuestion->subcat_id == $testSubCategory->id)
				                <input class="form-control"  type="text" name="subcategory_text" value="{{$testSubCategory->name}}" readonly="true">
			                	<input type="hidden" name="subcategory" value="{{$testSubCategory->id}}">
				            @endif
			          	@endforeach
			        @else
				      <select id="subcategory" class="form-control" name="subcategory" onChange="selectSubject(this);" required title="Sub Category">
				        <option value="">Select Sub Category</option>
				        	@if(count($testSubCategories) > 0)
					        	@foreach($testSubCategories as $testSubCategory)
						            @if($clientSelectedSubCategoryId == $testSubCategory->id)
						                <option value="{{$testSubCategory->id}}" selected="true">{{$testSubCategory->name}}</option>
						              @else
						                <option value="{{$testSubCategory->id}}">{{$testSubCategory->name}}</option>
						            @endif
					          	@endforeach
					        @endif
				      </select>
			        @endif
			      @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
			    </div>
			    <input type="hidden" id="selected_subcategory_id" name="selected_subcategory_id" value="{{$clientSelectedSubCategoryId}}">
		  	</div>
			<div class="form-group row @if ($errors->has('subject')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Subject Name:</label>
			    <div class="col-sm-3" >
			    	@if(count($testSubjects) > 0 && isset($testQuestion->id) )
		          		@foreach($testSubjects as $testSubject)
		          			@if($testQuestion->subject_id == $testSubject->id)
				                <input class="form-control"  type="text" name="subject_text" value="{{$testSubject->name}}" readonly="true">
			                	<input type="hidden" name="subject" value="{{$testSubject->id}}">
		            		@endif
		          		@endforeach
		        	@else
				      	<select id="subject" class="form-control" name="subject" onChange="selectPaper(this);" required title="Subject">
				        	<option value="">Select Subject</option>
			        		@if(count($testSubjects) > 0 )
				          		@foreach($testSubjects as $testSubject)
				          			@if($clientSelectedSubjectId == $testSubject->id)
				            			<option value="{{$testSubject->id}}" selected="true">{{$testSubject->name}}</option>
				            		@else
				            			<option value="{{$testSubject->id}}">{{$testSubject->name}}</option>
				            		@endif
				          		@endforeach
				          	@endif
			      		</select>
		        	@endif
		        	@if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
			    </div>
	          	<input type="hidden" id="selected_subject_id" name="selected_subject_id" value="{{$clientSelectedSubjectId}}">
		    </div>

		    <div class="form-group row @if ($errors->has('paper')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Paper Name:</label>
			    <div class="col-sm-3">
			    	@if(count($papers) > 0 && isset($testQuestion->id))
				        @foreach($papers as $paper)
				            @if($testQuestion->paper_id == $paper->id)
				                <input class="form-control"  type="text" name="paper_text" value="{{$paper->name}}" readonly="true">
			                	<input type="hidden" name="paper" value="{{$paper->id}}">
				            @endif
			         	@endforeach
			         	<input type="hidden" id="selected_paper_option_count" value="{{$testQuestion->paper->option_count}}">
		         	@else
				      	<select id="paper" class="form-control" name="paper" onChange="showQuestionCount(this);" required title="Paper">
				    		<option value="">Select Paper</option>
			    			@if(count($papers) > 0 )
					         	@foreach($papers as $paper)
						            @if($clientSelectedPaperId == $paper->id)
						                <option value="{{$paper->id}}" data-option_count="{{$paper->option_count}}" selected="true">{{$paper->name}}</option>
						              @else
						                <option value="{{$paper->id}}" data-option_count="{{$paper->option_count}}" >{{$paper->name}}</option>
						            @endif
					          	@endforeach
					        @endif
				    	</select>
			    	@endif
			        @if($errors->has('paper')) <p class="help-block">{{ $errors->first('paper') }}</p> @endif
			    </div>
			    <input type="hidden" id="selected_paper_id" name="selected_paper_id" value="{{$clientSelectedPaperId}}">
		    </div>
		    <div class="form-group row @if ($errors->has('section_type')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Section Name:</label>
			    <div class="col-sm-3">
			    	@if(isset($testQuestion->id))
			            @if(count($sessions) > 0)
				          	@foreach($sessions as $session)
				            	@if($session->id == $testQuestion->section_type)
					                <input class="form-control" type="text" name="section_type_text" value="{{$session->name}}" readonly="true">
			            			<input class="form-control" type="hidden" name="section_type" value="{{$testQuestion->section_type}}">
					            @endif
				          	@endforeach
				        @endif
			    	@else
				      	<select id="section_type" class="form-control" name="section_type" onChange="showQuestionCountOnSectionChange(this);" required title="Section">
				    		<option value="">Select Section</option>
					        @if(count($sessions) > 0)
					          	@foreach($sessions as $session)
					            	@if($session->id == $clientSelectedSectionId)
					                	<option value="{{$session->id}}" selected="true">{{$session->name}}</option>
						              @else
						                <option value="{{$session->id}}">{{$session->name}}</option>
						            @endif
					          	@endforeach
					        @endif
				    	</select>
				    @endif
			        @if($errors->has('section_type')) <p class="help-block">{{ $errors->first('section_type') }}</p> @endif
			    </div>
		    </div>
		    @if(!empty($testQuestion->id))
			    <div class="form-group row">
			    	<label class="col-sm-2 col-form-label">Current Question:</label>
				    <div class="col-sm-3">
			    		<span id="next_question">{{$currentQuestionNo}}</span>
				    </div>
			    </div>
			@else
				<div class="form-group row">
		    	<label class="col-sm-2 col-form-label">Next Question:</label>
			    <div class="col-sm-3">
				    	<span id="next_question">{{$clientNextQuestionNo}}</span>
			    </div>
		    </div>
		    @endif
		    <div class="form-group row">
			    <label for="paper" class="col-sm-2 col-form-label">Common Data Question:</label>
			    <div class="col-sm-3">
			    	@if(isset($testQuestion->id))
			        	<label class="radio-inline"><input type="radio" name="check_common_data" id="check_common_data" value="1" onClick="showHideCommonData(this);" @if(!empty($testQuestion->common_data)) checked @endif> Yes</label>
			        	<label class="radio-inline"><input type="radio" name="check_common_data" id="check_common_data" value="0" onClick="showHideCommonData(this);" @if(empty($testQuestion->common_data)) checked @endif> No</label>
			        @else
			        	<label class="radio-inline"><input type="radio" name="check_common_data" id="check_common_data" value="1" onClick="showHideCommonData(this);" @if(!empty($lastCommonData)) checked @endif> Yes</label>
			        	<label class="radio-inline"><input type="radio" name="check_common_data" id="check_common_data" value="0" onClick="showHideCommonData(this);" @if(empty($lastCommonData)) checked @endif> No</label>
			        @endif
			    </div>
			</div>
		    <div class="form-group row" id="show_common_data">
		    	<label class="col-sm-2 col-form-label">Enter Common Data Question:</label>
			    <div class="col-sm-10">
			      	<textarea name="common_data" cols="60" rows="4" id="common_data" placeholder="Enter your Common Data" required>
		    			@if(isset($testQuestion->id))
			 				{!! $testQuestion->common_data !!}
			 			@elseif(!empty($lastCommonData))
			 				{!! $lastCommonData !!}
			 			@endif
		    		</textarea>
				  	<script type="text/javascript">
				    	CKEDITOR.replace( 'common_data', { enterMode: CKEDITOR.ENTER_BR } );
				    	CKEDITOR.on('dialogDefinition', function (ev) {
			              	var dialogName = ev.data.name,
			                dialogDefinition = ev.data.definition;
			              	if (dialogName == 'image') {
			                  	var onOk = dialogDefinition.onOk;
			                  	dialogDefinition.onOk = function (e) {
			                      	var width = this.getContentElement('info', 'txtWidth');
			                      	width.setValue('100%');//Set Default Width
			                      	var height = this.getContentElement('info', 'txtHeight');
			                      	height.setValue('400');////Set Default height
			                      	onOk && onOk.apply(this, e);
			                  };
			              	}
			          	});
				  	</script>
			    </div>
		    </div>
		    <div class="form-group row">
		    	<label class="col-sm-2 col-form-label">Enter Question:</label>
			    <div class="col-sm-10">
			      	<textarea name="question" cols="60" rows="4" id="question" placeholder="Enter your Question" required>
		    			@if(isset($testQuestion->id))
			 				{!! $testQuestion->name !!}
			 			@endif
		    		</textarea>
				  	<script type="text/javascript">
				    	CKEDITOR.replace( 'question', { enterMode: CKEDITOR.ENTER_BR }  );
				    	CKEDITOR.on('dialogDefinition', function (ev) {
			              	var dialogName = ev.data.name,
			                dialogDefinition = ev.data.definition;
			              	if (dialogName == 'image') {
			                  	var onOk = dialogDefinition.onOk;
			                  	dialogDefinition.onOk = function (e) {
			                      	var width = this.getContentElement('info', 'txtWidth');
			                      	width.setValue('100%');//Set Default Width
			                      	var height = this.getContentElement('info', 'txtHeight');
			                      	height.setValue('400');////Set Default height
			                      	onOk && onOk.apply(this, e);
			                  };
			              	}
			          	});
				  	</script>
			    	<div class="hide" role="alert" id="question_error">
			    		<p>Please enter question.</p>
					</div>
			    </div>
		    </div>
		    <div class="form-group row mcq_ans">
		    	<label class="col-sm-2 col-form-label">Enter Answer1:</label>
			    <div class="col-sm-10">
			      	<textarea name="ans1" placeholder="Answer 1" type="text" id="ans1" size="85" maxlength="85" required>
	      				@if(isset($testQuestion->id))
			 				{!! $testQuestion->answer1 !!}
			 			@endif
	      			</textarea>
					<script type="text/javascript">
				    	CKEDITOR.replace( 'ans1' , { enterMode: CKEDITOR.ENTER_BR } );
				    	CKEDITOR.on('dialogDefinition', function (ev) {
			              	var dialogName = ev.data.name,
			                dialogDefinition = ev.data.definition;
			              	if (dialogName == 'image') {
			                  	var onOk = dialogDefinition.onOk;
			                  	dialogDefinition.onOk = function (e) {
			                      	var width = this.getContentElement('info', 'txtWidth');
			                      	width.setValue('100%');//Set Default Width
			                      	var height = this.getContentElement('info', 'txtHeight');
			                      	height.setValue('400');////Set Default height
			                      	onOk && onOk.apply(this, e);
			                  };
			              	}
			          	});
				  	</script>
			    </div>
		    </div>
		    <div class="form-group row mcq_ans">
		    	<label class="col-sm-2 col-form-label">Enter Answer2:</label>
			    <div class="col-sm-10">
			      	<textarea name="ans2" type="text" placeholder="Answer 2" id="ans2" size="85" maxlength="85" required>
		      			@if(isset($testQuestion->id))
			 				{!! $testQuestion->answer2 !!}
			 			@endif
		      		</textarea>
					<script type="text/javascript">
				    	CKEDITOR.replace( 'ans2', { enterMode: CKEDITOR.ENTER_BR }  );
				    	CKEDITOR.on('dialogDefinition', function (ev) {
			              	var dialogName = ev.data.name,
			                dialogDefinition = ev.data.definition;
			              	if (dialogName == 'image') {
			                  	var onOk = dialogDefinition.onOk;
			                  	dialogDefinition.onOk = function (e) {
			                      	var width = this.getContentElement('info', 'txtWidth');
			                      	width.setValue('100%');//Set Default Width
			                      	var height = this.getContentElement('info', 'txtHeight');
			                      	height.setValue('400');////Set Default height
			                      	onOk && onOk.apply(this, e);
			                  };
			              	}
			          	});
				  	</script>
			    </div>
		    </div>
		    <div class="form-group row mcq_ans">
		    	<label class="col-sm-2 col-form-label">Enter Answer3:</label>
			    <div class="col-sm-10">
			      	<textarea name="ans3" type="text" placeholder="Answer 3" id="ans3" size="85" maxlength="85" required>
		      			@if(isset($testQuestion->id))
		 					{!! $testQuestion->answer3 !!}
			 			@endif
		      		</textarea>
					<script type="text/javascript">
				    	CKEDITOR.replace( 'ans3', { enterMode: CKEDITOR.ENTER_BR }  );
				    	CKEDITOR.on('dialogDefinition', function (ev) {
			              	var dialogName = ev.data.name,
			                dialogDefinition = ev.data.definition;
			              	if (dialogName == 'image') {
			                  	var onOk = dialogDefinition.onOk;
			                  	dialogDefinition.onOk = function (e) {
			                      	var width = this.getContentElement('info', 'txtWidth');
			                      	width.setValue('100%');//Set Default Width
			                      	var height = this.getContentElement('info', 'txtHeight');
			                      	height.setValue('400');////Set Default height
			                      	onOk && onOk.apply(this, e);
			                  };
			              	}
			          	});
				  	</script>
			    </div>
		    </div>
		    <div class="form-group row mcq_ans">
		    	<label class="col-sm-2 col-form-label">Enter Answer4:</label>
			    <div class="col-sm-10">
			      	<textarea name="ans4" type="text" placeholder="Answer 4" id="ans4" size="85" maxlength="85">
		      			@if(isset($testQuestion->id))
		 					{!! $testQuestion->answer4 !!}
			 			@endif
		      		</textarea>
					<script type="text/javascript">
				    	CKEDITOR.replace( 'ans4', { enterMode: CKEDITOR.ENTER_BR }  );
				    	CKEDITOR.on('dialogDefinition', function (ev) {
			              	var dialogName = ev.data.name,
			                dialogDefinition = ev.data.definition;
			              	if (dialogName == 'image') {
			                  	var onOk = dialogDefinition.onOk;
			                  	dialogDefinition.onOk = function (e) {
			                      	var width = this.getContentElement('info', 'txtWidth');
			                      	width.setValue('100%');//Set Default Width
			                      	var height = this.getContentElement('info', 'txtHeight');
			                      	height.setValue('400');////Set Default height
			                      	onOk && onOk.apply(this, e);
			                  };
			              	}
			          	});
				  	</script>
			    </div>
		    </div>
		    @if(isset($testQuestion->id) && !empty($testQuestion->answer5))
			    <div class="form-group row " id="show_option5">
			    	<label class="col-sm-2 col-form-label">Enter Answer5:</label>
				    <div class="col-sm-10">
				      	<textarea name="ans5" type="text" placeholder="Answer 5" id="ans5" size="85" maxlength="85">
			 					{!! $testQuestion->answer5 !!}
			      		</textarea>
						<script type="text/javascript">
					    	CKEDITOR.replace( 'ans5', { enterMode: CKEDITOR.ENTER_BR } );
					    	CKEDITOR.on('dialogDefinition', function (ev) {
			              	var dialogName = ev.data.name,
			                dialogDefinition = ev.data.definition;
			              	if (dialogName == 'image') {
			                  	var onOk = dialogDefinition.onOk;
			                  	dialogDefinition.onOk = function (e) {
			                      	var width = this.getContentElement('info', 'txtWidth');
			                      	width.setValue('100%');//Set Default Width
			                      	var height = this.getContentElement('info', 'txtHeight');
			                      	height.setValue('400');////Set Default height
			                      	onOk && onOk.apply(this, e);
			                  };
			              	}
			          	});
					  	</script>
				    </div>
			    </div>
			@else
				<div class="form-group row mcq_ans hide" id="show_option5">
			    	<label class="col-sm-2 col-form-label">Enter Answer5:</label>
				    <div class="col-sm-10">
				      	<textarea name="ans5" type="text" placeholder="Answer 5" id="ans5" size="85" maxlength="85">
			 					{!! $testQuestion->answer5 !!}
			      		</textarea>
						<script type="text/javascript">
					    	CKEDITOR.replace( 'ans5', { enterMode: CKEDITOR.ENTER_BR } );
					    	CKEDITOR.on('dialogDefinition', function (ev) {
				              	var dialogName = ev.data.name,
				                dialogDefinition = ev.data.definition;
				              	if (dialogName == 'image') {
				                  	var onOk = dialogDefinition.onOk;
				                  	dialogDefinition.onOk = function (e) {
				                      	var width = this.getContentElement('info', 'txtWidth');
				                      	width.setValue('100%');//Set Default Width
				                      	var height = this.getContentElement('info', 'txtHeight');
				                      	height.setValue('400');////Set Default height
				                      	onOk && onOk.apply(this, e);
				                  };
				              	}
				          	});
					  	</script>
				    </div>
			    </div>
			@endif
		    <div class="form-group row mcq_ans">
		    	<label class="col-sm-2 col-form-label">Enter True Answer No:</label>
			    <div class="col-sm-3">
			      	<input class="form-control"  name="answer" type="number" placeholder="True Answer" id="answer" value="{{$testQuestion->answer}}" min="1" max="5" required="true">
			      	<div class="hide" role="alert" id="answer_error">
			    		<p>Answer number in between 1 to 5.</p>
					</div>
					<div class="hide" role="alert" id="empty_answer_error">
			    		<p>Please enter answer.</p>
					</div>
			    </div>
			    <label class="col-sm-4 col-form-label">Ex. 1 or 2 or 3 or 4 or 5 ( if have )</label>
		    </div>
		    <div class="form-group row num_ans hide">
		    	<label class="col-sm-2 col-form-label">Enter Answer:</label>
			    <div class="col-sm-3 ">
			    	FROM <input class="form-control" name="min" id="min" type="text" value="@if(isset($testQuestion->id)){!! $testQuestion->min !!}@endif"/>
			    	<div class="hide" role="alert" id="empty_min_error">
			    		<p>Please enter from value.</p>
					</div>
			    </div>
			    <div class="col-sm-3">
			      	To <input class="form-control" name="max" id="max" type="text" value="@if(isset($testQuestion->id)){!! $testQuestion->max !!}@endif"/>
			      	<div class="hide" role="alert" id="empty_max_error">
			    		<p>Please enter to value.</p>
					</div>
			    </div>
		    </div>
		    <div class="form-group row">
		    	<label class="col-sm-2 col-form-label" for="solution">Enter Solution:</label>
			    <div class="col-sm-10">
			      	<textarea name="solution" type="text" placeholder="Answer" id="solution" size="85" maxlength="85">
			 			@if(isset($testQuestion->id))
		 					{!! $testQuestion->solution !!}
			 			@endif
		 			</textarea>
					<script type="text/javascript">
				    	CKEDITOR.replace( 'solution', { enterMode: CKEDITOR.ENTER_BR }  );
				    	CKEDITOR.on('dialogDefinition', function (ev) {
			              	var dialogName = ev.data.name,
			                dialogDefinition = ev.data.definition;
			              	if (dialogName == 'image') {
			                  	var onOk = dialogDefinition.onOk;
			                  	dialogDefinition.onOk = function (e) {
			                      	var width = this.getContentElement('info', 'txtWidth');
			                      	width.setValue('100%');//Set Default Width
			                      	var height = this.getContentElement('info', 'txtHeight');
			                      	height.setValue('400');////Set Default height
			                      	onOk && onOk.apply(this, e);
			                  };
			              	}
			          	});
				  	</script>
					<div class="hide" role="alert" id="solution_error">
			    		<p>Please enter solution.</p>
					</div>
			    </div>
		    </div>
		    <div class="form-group row @if ($errors->has('pos_marks')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Enter Positive Marks:</label>
			    <div class="col-sm-3">
			      	<input class="form-control" name="pos_marks" type="number" placeholder="Positive Marks" id="pos_marks" value="{{$testQuestion->positive_marks}}" step="1">
			      	@if($errors->has('pos_marks')) <p class="help-block">{{ $errors->first('pos_marks') }}</p> @endif
			      	<div class="hide" role="alert" id="pos_marks_error">
			    		<p>Please enter positive marks.</p>
					</div>
					<div class="hide" role="alert" id="pos_num_error">
			    		<p>Number should be positive.</p>
					</div>
			    </div>
		    </div>
		    <div class="form-group row @if ($errors->has('neg_marks')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Enter Negative Marks:</label>
			    <div class="col-sm-3">
			      	<input class="form-control" name="neg_marks" type="number" placeholder="Negative Marks" id="neg_marks" value="{{$testQuestion->negative_marks}}" min="0" step="0.001" />
			      	@if($errors->has('neg_marks')) <p class="help-block">{{ $errors->first('neg_marks') }}</p> @endif
			      	<div class="hide" role="alert" id="neg_marks_error">
			    		<p>Please enter negative marks.</p>
					</div>
					<div class="hide" role="alert" id="neg_num_error">
			    		<p>Number should be positive.</p>
					</div>
			    </div>
		    </div>
		    <div class="form-group row ">
		    	<label class="col-sm-2 col-form-label"></label>
			    <div class="col-sm-10" title="@if(isset($testQuestion->id)) Update @else Add @endif Question">
			      	<input class="btn btn-primary" type="button" value="@if(isset($testQuestion->id)) Update @else Add @endif Question" onClick="checkCkeditor();">
      				<input type="hidden" name="question_type" id="question_type" value="@if(isset($testQuestion->id)){!! $testQuestion->question_type !!}@endif">
      				<input type="hidden" name="selected_question_type" id="selected_question_type" value="{{$clientSelectedQuestionType}}">
			    </div>
		    </div>
		</form>
		</div>
		<input type="hidden" name="paper_option_count" id="paper_option_count" value="">
<style>
ul#ul {
	width:1200px;
    list-style-type: none;
    margin-left: auto;
	 margin-right: auto;
    padding: 0;
    overflow: hidden;
    background-color: #333;
}

ul#ul > li {
    float: left;
}

ul#ul > li > a {
    display: block;
    color: white;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
}

ul#ul > li > a:hover:not(.active) {
    background-color: #111;
}

.active {
    background-color: #4CAF50;
}
</style>
<script type="text/javascript">

	function showHideCommonData(ele){
      if(1 == $(ele).val()){
        $('#show_common_data').removeClass('hide');
      } else {
        $('#show_common_data').addClass('hide');
      }
    }

  	function selectSubcategory(ele){
	    id = parseInt($(ele).val());
	    if( 0 < id ){
	      $.ajax({
	              method: "POST",
	              url: "{{url('getOnlineTestSubCategories')}}",
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
	                  select.appendChild(opt);
	              });
	            }
	            toggleNextPrev();
          	});
	    } else {
	    	resetSubCategory();
	    }
    	resetSubject();
    	resetPaper();
    	resetSection();
  	}

  	function selectSubject(ele){
	    subcatId = parseInt($(ele).val());
	    catId = parseInt(document.getElementById('category').value);
	    if( 0 < catId && 0 < subcatId ){
	      $.ajax({
	              method: "POST",
	              url: "{{url('getOnlineSubjectsByCatIdBySubcatId')}}",
	              data: {catId:catId, subcatId:subcatId}
	          })
	          .done(function( msg ) {
	            selectSub = document.getElementById('subject');
	            selectSub.innerHTML = '';
	            var opt = document.createElement('option');
	            opt.value = '';
	            opt.innerHTML = 'Select Subject';
	            selectSub.appendChild(opt);
	            if( 0 < msg.length){
	              $.each(msg, function(idx, obj) {
	                  var opt = document.createElement('option');
	                  opt.value = obj.id;
	                  opt.innerHTML = obj.name;
	                  selectSub.appendChild(opt);
	              });
	            }
	            toggleNextPrev();
	        });
    	} else {
    		resetSubject();
	    }
    	resetPaper();
    	resetSection();
  	}

	function checkCkeditor(){
		var errorCount = 0;
		var questionLength = CKEDITOR.instances.question.getData().length; //var question = document.getElementById('question').value;

		if(0 == questionLength){
			$('#question_error').removeClass('hide');
			$('#question_error').addClass('alert alert-danger');
			errorCount += 1;
		} else {
			$('#question_error').addClass('hide');
			$('#question_error').removeClass('alert alert-danger');
		}

		var solutionLength = CKEDITOR.instances.solution.getData().length; //var question = document.getElementById('question').value;

		if(0 == solutionLength){
			$('#solution_error').removeClass('hide');
			$('#solution_error').addClass('alert alert-danger');
			errorCount += 1;
		} else {
			$('#solution_error').addClass('hide');
			$('#solution_error').removeClass('alert alert-danger');
		}
		var neg_marks= document.getElementById('neg_marks').value;
		if( "" === neg_marks){
			$('#neg_marks_error').removeClass('hide');
			$('#neg_marks_error').addClass('alert alert-danger');
			errorCount += 1;
		} else if(neg_marks < 0){
			$('#neg_num_error').removeClass('hide');
			$('#neg_num_error').addClass('alert alert-danger');
			errorCount += 1;
		} else{
			$('#neg_marks_error').addClass('hide');
			$('#neg_marks_error').removeClass('alert alert-danger');
			$('#neg_num_error').addClass('hide');
			$('#neg_num_error').removeClass('alert alert-danger');
		}
		var pos_marks= document.getElementById('pos_marks').value;
		if( "" === pos_marks){
			$('#pos_marks_error').removeClass('hide');
			$('#pos_marks_error').addClass('alert alert-danger');
			errorCount += 1;
		} else if( pos_marks < 0){
			$('#pos_num_error').removeClass('hide');
			$('#pos_num_error').addClass('alert alert-danger');
			errorCount += 1;
		} else{
			$('#pos_marks_error').addClass('hide');
			$('#pos_marks_error').removeClass('alert alert-danger');
			$('#pos_num_error').addClass('hide');
			$('#pos_num_error').removeClass('alert alert-danger');
		}

		if($('#mcq_ques').hasClass('active')){
			var answerNum= document.getElementById('answer').value;
			if( "" == answerNum){
				$('#empty_answer_error').removeClass('hide');
				$('#empty_answer_error').addClass('alert alert-danger');
				errorCount += 1;
			}else if( answerNum >= 6){
				$('#answer_error').removeClass('hide');
				$('#answer_error').addClass('alert alert-danger');
				errorCount += 1;
			} else{
				$('#answer_error').addClass('hide');
				$('#answer_error').removeClass('alert alert-danger');
				$('#empty_answer_error').addClass('hide');
				$('#empty_answer_error').removeClass('alert alert-danger');
			}
		} else {
			var min= document.getElementById('min').value;
			if( "" == min){
				$('#empty_min_error').removeClass('hide');
				$('#empty_min_error').addClass('alert alert-danger');
				errorCount += 1;
			} else {
				$('#empty_min_error').addClass('hide');
				$('#empty_min_error').removeClass('alert alert-danger');
			}

			var max= document.getElementById('max').value;
			if( "" == max){
				$('#empty_max_error').removeClass('hide');
				$('#empty_max_error').addClass('alert alert-danger');
				errorCount += 1;
			} else {
				$('#empty_max_error').addClass('hide');
				$('#empty_max_error').removeClass('alert alert-danger');
			}
		}
		if( 0 == errorCount){
			var form = document.getElementById('createForm');
			form.submit();
		} else{
			return false;
		}
	}

	function resetSubCategory(){
		select = document.getElementById('subcategory');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Sub Category';
        select.appendChild(opt);
	}

	function resetSubject(){
		selectSub = document.getElementById('subject');
        selectSub.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Subject';
        selectSub.appendChild(opt);
	}

	function resetPaper(){
		select = document.getElementById('paper');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Paper';
        select.appendChild(opt);
	}

	function resetSection(){
		select = document.getElementById('section_type');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Section';
        select.appendChild(opt);
	}

	function getPapersBySubjectId(subjectId){
	    if( 0 < subjectId ){
	      	$.ajax({
	             	method: "POST",
	              	url: "{{url('getOnlinePapersBySubjectId')}}",
	              	data: {subjectId:subjectId}
          	}).done(function( msg ) {
          		var selectedPaperId = document.getElementById('selected_paper_id').value;
	            select = document.getElementById('paper');
	            select.innerHTML = '';
	            var opt = document.createElement('option');
	            opt.value = '';
	            opt.innerHTML = 'Select Paper';
	            select.appendChild(opt);
	            if( 0 < msg.length){
		            $.each(msg, function(idx, obj) {
		                var opt = document.createElement('option');
		                opt.value = obj.id;
		                opt.innerHTML = obj.name;
		                opt.setAttribute('data-option_count', obj.option_count);
		                if(0 < selectedPaperId){
		                	opt.selected = true;
		                }
		                select.appendChild(opt);
		            });
	            }
          	});
    	} else {
    		resetPaper();
	    }
    	resetSection();
  	}

	function selectPaper(ele){
    	subjectId = parseInt($(ele).val());
    	getPapersBySubjectId(subjectId);
    	toggleNextPrev();
  	}

  	function getQuestionCount(categoryId,subcategoryId,paperId,subjectId,section_type){
  		var questionId = parseInt($('#question_id').val());
  		if( 0 < categoryId && 0 < subcategoryId && 0 < paperId && 0 < subjectId && false == isNaN(questionId)){
	      	$.ajax({
	             	method: "POST",
	              	url: "{{url('getClientCurrentQuestionCount')}}",
	              	data: {categoryId:categoryId,subcategoryId:subcategoryId,paperId:paperId,subjectId:subjectId,section_type:section_type,questionId:questionId}
          	}).done(function( msg ) {
	            nextQuestion = document.getElementById('next_question');
	            nextQuestion.innerHTML = msg;
          	});
    	} else{
    		$.ajax({
	             	method: "POST",
	              	url: "{{url('getClientNextQuestionCount')}}",
	              	data: {categoryId:categoryId,subcategoryId:subcategoryId,paperId:paperId,subjectId:subjectId,section_type:section_type}
          	}).done(function( msg ) {
	            nextQuestion = document.getElementById('next_question');
	            nextQuestion.innerHTML = msg;
          	});
    	}
  	}

  	function showQuestionCount(ele){
  		if( 5 == $(ele).find(':selected').data('option_count')){
  			document.getElementById('paper_option_count').value = 5;
  			if(document.getElementById('mcq_ques').classList.contains('active')){
	  			document.getElementById('show_option5').classList.remove('hide');
	  		}
  		} else {
  			document.getElementById('paper_option_count').value = 4;
  			if(document.getElementById('mcq_ques').classList.contains('active')){
	  			document.getElementById('show_option5').classList.add('hide');
	  		}
  		}
  		selectSection();
  	}

  	function showQuestionCountOnSectionChange(ele){
  		section_type = parseInt($(ele).val());
  		subjectId = parseInt(document.getElementById('subject').value);
  		paperId = parseInt(document.getElementById('paper').value);
  		categoryId = parseInt(document.getElementById('category').value);
  		subcategoryId = parseInt(document.getElementById('subcategory').value);
  		getQuestionCount(categoryId,subcategoryId,paperId,subjectId,section_type);
  		toggleNextPrev();
  	}

  	function toggleNextPrev(){
  		paperId = parseInt(document.getElementById('paper').value);
  		categoryId = parseInt(document.getElementById('category').value);
  		subcategoryId = parseInt(document.getElementById('subcategory').value);
  		subjectId = parseInt(document.getElementById('subject').value);
  		section_type = parseInt(document.getElementById('section_type').value);
  		if( 0 < categoryId && 0 < subcategoryId && 0 < paperId && 0 < subjectId){
  			$.ajax({
	             	method: "POST",
	              	url: "{{url('getClientPrevQuestion')}}",
	              	data: {categoryId:categoryId,subcategoryId:subcategoryId,paperId:paperId,subjectId:subjectId,section_type:section_type}
          	}).done(function( msg ) {
          		if( 0 < msg ){
		            var prevEle = document.getElementById('prev_ques');
		            prevEle.classList.remove("hide");
		            var url = "{{url('onlinetestquestion')}}"+'/'+msg+'/edit';
					prevEle.setAttribute('href', url);
					prevEle.innerHTML = 'Prev Question';
		        }
          	});
  		}
  	}
  	function selectSection(){
		var paperId = parseInt(document.getElementById('paper').value);
		if( 0 < paperId ){
	      	$.ajax({
             	method: "POST",
              	url: "{{url('paperSectionsByPaperId')}}",
              	data: {paper_id:paperId}
          	}).done(function( msg ) {
	            select = document.getElementById('section_type');
	            select.innerHTML = '';
	            var opt = document.createElement('option');
	            opt.value = '';
	            opt.innerHTML = 'Select Section';
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
    	} else {
    		resetSection();
    	}
	}

  	$( document ).ready(function() {
  		if( (document.getElementById('selected_paper_option_count') && 5 == document.getElementById('selected_paper_option_count').value) || 5 == $('#paper').find('option:selected').data('option_count')){
  			if(document.getElementById('mcq_ques').classList.contains('active')){
	  			document.getElementById('show_option5').classList.remove('hide');
	  		}
	  		document.getElementById('paper_option_count').value = 5;
  		}

		$(document).on('click', '#mcq_ques', function(){
			$('#mcq_ques').addClass('active');
			$('#num_ques').removeClass('active');
			$('div.num_ans').addClass('hide');
			$('div.mcq_ans').removeClass('hide');
			$('#question_type').val(1);
			if(5 == document.getElementById('paper_option_count').value){
				document.getElementById('show_option5').classList.remove('hide');
			} else {
				document.getElementById('show_option5').classList.add('hide');
			}
		});

		$(document).on('click', '#num_ques', function(){
			$('#num_ques').addClass('active');
			$('#mcq_ques').removeClass('active');
			$('div.mcq_ans').addClass('hide');
			$('div.num_ans').removeClass('hide');
			$('#question_type').val(0);
		});
		var questionId = $('#question_id').val();
		var questionType = $('#question_type').val();
		var selectedQuestionType = $('#selected_question_type').val();
		if( 0 < questionId ){
			if(0 == questionType ){
				$('#num_ques').trigger("click");
			} else {
				$('#mcq_ques').trigger("click");
			}
		} else {
			if( 1 == selectedQuestionType){
				$('#mcq_ques').trigger("click");
			} else {
				$('#num_ques').trigger("click");
			}
		}

  		if(1 == $("#check_common_data:checked").val()){
	  		$('#show_common_data').removeClass('hide');
	    } else {
	    	$('#show_common_data').addClass('hide');
	    }
	});
</script>
@stop