@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Question </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Manage Question </li>
    </ol>
  </section>
@stop
@section('admin_content')
	<script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>

	@php
		if(Session::has('selected_category')){
			$selectedCategoryId = Session::get('selected_category');
		} else {
			$selectedCategoryId = 0;
		}
		if(Session::has('selected_subcategory')){
			$selectedSubCategoryId = Session::get('selected_subcategory');
		} else {
			$selectedSubCategoryId = 0;
		}
		if(Session::has('selected_subject')){
			$selectedSubjectId = Session::get('selected_subject');
		} else {
			$selectedSubjectId = 0;
		}
		if(Session::has('selected_paper')){
			$selectedPaperId = Session::get('selected_paper');
		} else {
			$selectedPaperId = 0;
		}
		if(Session::has('selected_section')){
			$selectedSectionId = Session::get('selected_section');
		} else {
			$selectedSectionId = 0;
		}
		if(Session::has('selected_question_type')){
			$selectedQuestionType = Session::get('selected_question_type');
		} else {
			$selectedQuestionType = 2;
		}
		if(Session::has('next_question_no')){
			$nextQuestionNo = Session::get('next_question_no');
		} else {
			$nextQuestionNo = 1;
		}
	@endphp
	<div id="main ">
		@if(Session::has('message'))
			<div class="alert alert-success" id="message">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			  	{{ Session::get('message') }}
			</div>
		@endif
		<ul id ="ul">
	  		<li title="ADD MCQ"><a class="btn active" id="mcq_ques">ADD MCQ</a></li>
	  		<li title="ADD NUMERICAL"><a class="btn" id="num_ques">ADD NUMERICAL</a></li>
	  		@if($prevQuestionId > 0)
	  			<li title="Prev Question"><a class="btn" id="prev_ques" href="{{url('admin/question')}}/{{$prevQuestionId}}/edit">Prev Question</a></li>
	  		@else
	  			<li title="No Prev Question"><a class="btn" id="prev_ques">No Prev Question</a></li>
	  		@endif
	  		@if($nextQuestionId > 0)
		  		<li title="Next Question"><a class="btn" id="next_ques" href="{{url('admin/question')}}/{{$nextQuestionId}}/edit">Next Question</a></li>
	  		@elseif(( $prevQuestionId > 0 || null == $prevQuestionId ) && null == $nextQuestionId )
	  			<li title="Add Question"><a class="btn" id="next_ques" href="{{url('admin/createQuestion')}}">Add Question </a></li>
	  		@else
	  			<li title="No Next Question"><a class="btn" id="next_ques">No Next Question</a></li>
	  		@endif
		</ul>

		@if(isset($testQuestion->id))
			<form id="createForm" name="updateQuestion" action="{{url('admin/updateQuestion')}}" method="POST" role="form" data-toggle="validator">
			{{method_field('PUT')}}
			<input type="hidden" name="question_id" id="question_id" value="{{$testQuestion->id}}">
		@else
			<form id="createForm" name="createQuestion" action="{{url('admin/createQuestion')}}" method="POST">
		@endif
		{{csrf_field()}}
		<div class="container admin_div">
			<div class="form-group row @if ($errors->has('category')) has-error @endif">
			    <label class="col-sm-2 col-form-label">Category Name:</label>
			    <div class="col-sm-3">
			    @if(count($testCategories) > 0 && isset($testQuestion->id))
			    	@foreach($testCategories as $testCategory)
		              @if( $testCategory->id == $testQuestion->category_id )
		              	<input class="form-control" type="text" name="category_text" value="{{$testCategory->name}}" readonly="true">
		              	<input class="form-control" type="hidden" name="category" value="{{$testQuestion->category_id}}">
		              @endif
		            @endforeach
			    @else
			      <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
			          <option value="">Select Category ...</option>
			            @foreach($testCategories as $testCategory)
			            	@if($selectedCategoryId == $testCategory->id)
			                	<option value="{{$testCategory->id}}" selected="true">{{$testCategory->name}}</option>
			                @else
			                	<option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
			                @endif
			            @endforeach
			      </select>
	          	@endif
		      	@if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
			    </div>
			    <input type="hidden" id="selected_category_id" name="selected_category_id" value="{{$selectedCategoryId}}">
		  	</div>
		  	<div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
			    <label class="col-sm-2 col-form-label">Sub Category Name:</label>
			    <div class="col-sm-3">
		        	@if(count($testSubCategories) > 0 && isset($testQuestion->id))
			        	@foreach($testSubCategories as $testSubCategory)
				            @if($testQuestion->subcat_id == $testSubCategory->id)
				                <input class="form-control" type="text" name="subcategory_text" value="{{$testSubCategory->name}}" readonly="true">
				                <input class="form-control" type="hidden" name="subcategory" value="{{$testQuestion->subcat_id}}">
				            @endif
			          	@endforeach
			        @else
				      <select id="subcategory" class="form-control" name="subcategory" onChange="selectSubject(this);" required title="Sub Category">
				        <option value="">Select Sub Category ...</option>
				        	@foreach($testSubCategories as $testSubCategory)
					            @if($selectedSubCategoryId == $testSubCategory->id)
					                <option value="{{$testSubCategory->id}}" selected="true">{{$testSubCategory->name}}</option>
				              	@else
					                <option value="{{$testSubCategory->id}}">{{$testSubCategory->name}}</option>
					            @endif
				          	@endforeach
				      	</select>
				    @endif
			      @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
			    </div>
			    <input type="hidden" id="selected_subcategory_id" name="selected_subcategory_id" value="{{$selectedSubCategoryId}}">
		  	</div>
			<div class="form-group row @if ($errors->has('subject')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Select Subject:</label>
			    <div class="col-sm-3" >
	          		@if(count($testSubjects) > 0 && isset($testQuestion->id) )
	          			@foreach($testSubjects as $testSubject)
		          			@if($testQuestion->subject_id == $testSubject->id)
	            			 	<input class="form-control" type="text" name="subject_text" value="{{$testSubject->name}}" readonly="true">
	            			 	<input class="form-control" type="hidden" name="subject" value="{{$testQuestion->subject_id}}">
		            		@endif
		          		@endforeach
	          		@else
				      	<select id="subject" class="form-control" name="subject" onChange="selectPaper(this);" required title="Subject">
				        	<option value="">Select Subject ...</option>
				          	@foreach($testSubjects as $testSubject)
			          			@if($selectedSubjectId == $testSubject->id)
			            			<option value="{{$testSubject->id}}" selected="true">{{$testSubject->name}}</option>
			            		@else
			            			<option value="{{$testSubject->id}}">{{$testSubject->name}}</option>
			            		@endif
			          		@endforeach
				        	@if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
			      		</select>
	        		@endif
			    </div>
	          	<input type="hidden" id="selected_subject_id" name="selected_subject_id" value="{{$selectedSubjectId}}">
		    </div>
		    <div class="form-group row @if ($errors->has('paper')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Select Paper:</label>
			    <div class="col-sm-3">
	    			@if(count($papers) > 0 && isset($testQuestion->id))
	    				@foreach($papers as $paper)
				            @if($testQuestion->paper_id == $paper->id)
				                <input class="form-control" type="text" name="paper_text" value="{{$paper->name}}" readonly="true">
				                <input class="form-control" type="hidden" name="paper" value="{{$testQuestion->paper_id}}">
				            @endif
			          	@endforeach
    			   	@else
				      	<select id="paper" class="form-control" name="paper" onChange="showQuestionCount(this);" required title="Paper">
				    		<option value="">Select Paper ...</option>
					       		@foreach($papers as $paper)
						            @if($selectedPaperId == $paper->id)
						                <option value="{{$paper->id}}" selected="true">{{$paper->name}}</option>
						              @else
						                <option value="{{$paper->id}}">{{$paper->name}}</option>
						            @endif
					          	@endforeach
					        @if($errors->has('paper')) <p class="help-block">{{ $errors->first('paper') }}</p> @endif
				    	</select>
			        @endif
			    </div>
			    <input type="hidden" id="selected_paper_id" name="selected_paper_id" value="{{$selectedPaperId}}">
		    </div>
		    <div class="form-group row @if ($errors->has('section_type')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Select Section:</label>
			    <div class="col-sm-3">
			    	@if(isset($testQuestion->id))
			    		@if(1== $testQuestion->section_type)
			                <input class="form-control" type="text" name="section_type_text" value="Aptitude" readonly="true">
			            @else
			            	<input class="form-control" type="text" name="section_type_text" value="Technical" readonly="true">
			            @endif
			            <input class="form-control" type="hidden" name="section_type" value="{{$testQuestion->section_type}}">
			    	@else
			      	<select id="section_type" class="form-control" name="section_type" onChange="showQuestionCountOnSectionChange(this);" required title="Section">
			    		<option value="">Select Section ...</option>
		                <option value="1"
			    			@if((isset($testQuestion->id) && 1 == $testQuestion->section_type) || (empty($testQuestion->id) && 1 == $selectedSectionId))
			    				selected = "true"
			    			@endif
		                 readonly="true">Aptitude</option>
				        <option value="0"
				        	@if((isset($testQuestion->id) && 0 == $testQuestion->section_type) || (empty($testQuestion->id) && 0 == $selectedSectionId))
			    				selected = "true"
			    			@endif
				         readonly="true">Technical</option>
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
				    	<span id="next_question">{{$nextQuestionNo}}</span>
			    </div>
		    </div>
		    @endif
		    <div class="form-group row">
		    	<label class="col-sm-2 col-form-label">Enter Question:</label>
			    <div class="col-sm-10">
			      	<textarea name="question" cols="60" rows="4" id="question" placeholder="Enter your Question" required>
		    			@if(isset($testQuestion->id))
			 				{!! $testQuestion->name !!}
			 			@endif
		    		</textarea>
				  	<script type="text/javascript">
				    	CKEDITOR.replace( 'question', { enterMode: CKEDITOR.ENTER_BR } );
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
				    	CKEDITOR.replace( 'ans1', { enterMode: CKEDITOR.ENTER_BR } );
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
				    	CKEDITOR.replace( 'ans2', { enterMode: CKEDITOR.ENTER_BR } );
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
				    	CKEDITOR.replace( 'ans3' , { enterMode: CKEDITOR.ENTER_BR });
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
				    	CKEDITOR.replace( 'ans4', { enterMode: CKEDITOR.ENTER_BR } );
				  	</script>
			    </div>
		    </div>
		    <div class="form-group row mcq_ans">
		    	<label class="col-sm-2 col-form-label">Enter True Answer No:</label>
			    <div class="col-sm-3">
			      	<input class="form-control"  name="answer" type="text" placeholder="True Answer" id="answer" value="@if(isset($testQuestion->id)) {!! $testQuestion->answer !!} @endif" required>
			    </div>
			    <label class="col-sm-2 col-form-label">Ex. 1 or 2 or 3 or 4</label>
		    </div>
		    <div class="form-group row num_ans hide">
		    	<label class="col-sm-2 col-form-label">Enter Answer:</label>
			    <div class="col-sm-3 ">
			    	FROM <input class="form-control" name="min" type="text" value="@if(isset($testQuestion->id)){!! $testQuestion->min !!}@endif"/>

			    </div>
			    <div class="col-sm-3">
			      	To <input class="form-control" name="max" type="text" value="@if(isset($testQuestion->id)){!! $testQuestion->max !!}@endif"/>
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
				    	CKEDITOR.replace( 'solution', { enterMode: CKEDITOR.ENTER_BR } );
				  	</script>
					<div class="hide" role="alert" id="solution_error">
			    		<p>Please enter solution.</p>
					</div>
			    </div>
		    </div>
		    <div class="form-group row @if ($errors->has('pos_marks')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Enter Positive Marks:</label>
			    <div class="col-sm-3">
			      	<input class="form-control" name="pos_marks" type="text" placeholder="Positive Marks" id="pos_marks" value="@if(isset($testQuestion->id)) {!! $testQuestion->positive_marks !!} @endif" required="true">
			      	@if($errors->has('pos_marks')) <p class="help-block">{{ $errors->first('pos_marks') }}</p> @endif
			      	<div class="hide" role="alert" id="pos_marks_error">
			    		<p>Please enter positive marks.</p>
					</div>
			    </div>
		    </div>
		    <div class="form-group row @if ($errors->has('neg_marks')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Enter Negative Marks:</label>
			    <div class="col-sm-3">
			      	<input class="form-control" name="neg_marks" type="text" placeholder="Negative Marks" id="neg_marks" required="true"@if(isset($testQuestion->id)) value="{!! $testQuestion->negative_marks !!}"@endif />
			      	@if($errors->has('neg_marks')) <p class="help-block">{{ $errors->first('neg_marks') }}</p> @endif
			      	<div class="hide" role="alert" id="neg_marks_error">
			    		<p>Please enter negative marks.</p>
					</div>
			    </div>
		    </div>
		    <div class="form-group row ">
		    	<label class="col-sm-2 col-form-label"></label>
			    <div class="col-sm-10" title="@if(isset($testQuestion->id)) Update @else Add @endif Question">
			      	<input class="btn btn-primary" type="button" value="@if(isset($testQuestion->id)) Update @else Add @endif Question" onClick="checkCkeditor();">
      				<input type="hidden" name="question_type" id="question_type" value="@if(isset($testQuestion->id)){!! $testQuestion->question_type !!}@endif">
      				<input type="hidden" name="selected_question_type" id="selected_question_type" value="{{$selectedQuestionType}}">
			    </div>
		    </div>
		</div>

	</form>
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

  	function selectSubcategory(ele){
	    id = parseInt($(ele).val());
	    if( 0 < id ){
	      $.ajax({
	              method: "POST",
	              url: "{{url('admin/getSubCategories')}}",
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
	            toggleNextPrev();
          	});
	    }
  	}

  	function selectSubject(ele){
	    subcatId = parseInt($(ele).val());
	    catId = parseInt(document.getElementById('category').value);
	    if( 0 < catId && 0 < subcatId ){
	      $.ajax({
	              method: "POST",
	              url: "{{url('admin/getSubjectsByCatIdBySubcatId')}}",
	              data: {catId:catId, subcatId:subcatId}
	          })
	          .done(function( msg ) {
	            selectSub = document.getElementById('subject');
	            selectSub.innerHTML = '';
	            var opt = document.createElement('option');
	            opt.value = '';
	            opt.innerHTML = 'Select Subject ...';
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
    	}
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
		} else{
			$('#neg_marks_error').addClass('hide');
			$('#neg_marks_error').removeClass('alert alert-danger');
		}
		var pos_marks= document.getElementById('pos_marks').value;
		if( "" === pos_marks){
			$('#pos_marks_error').removeClass('hide');
			$('#pos_marks_error').addClass('alert alert-danger');
			errorCount += 1;
		} else{
			$('#pos_marks_error').addClass('hide');
			$('#pos_marks_error').removeClass('alert alert-danger');
		}
		if( 0 == errorCount){
			var form = document.getElementById('createForm');
			form.submit();
		} else{
			return false;
		}
	}

	function getPapersBySubjectId(subjectId){
	    if( 0 < subjectId ){
	      	$.ajax({
	             	method: "POST",
	              	url: "{{url('admin/getPapersBySubjectId')}}",
	              	data: {subjectId:subjectId}
          	}).done(function( msg ) {
          		var selectedPaperId = document.getElementById('selected_paper_id').value;
	            select = document.getElementById('paper');
	            select.innerHTML = '';
	            var opt = document.createElement('option');
	            opt.value = '';
	            opt.innerHTML = 'Select Paper ...';
	            select.appendChild(opt);
	            if( 0 < msg.length){
		            $.each(msg, function(idx, obj) {
		                var opt = document.createElement('option');
		                opt.value = obj.id;
		                opt.innerHTML = obj.name;
		                if(0 < selectedPaperId){
		                	opt.selected = true;
		                }
		                select.appendChild(opt);
		            });
	            }
          	});
    	}
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
	              	url: "{{url('admin/getCurrentQuestionCount')}}",
	              	data: {categoryId:categoryId,subcategoryId:subcategoryId,paperId:paperId,subjectId:subjectId,section_type:section_type,questionId:questionId}
          	}).done(function( msg ) {
	            nextQuestion = document.getElementById('next_question');
	            nextQuestion.innerHTML = msg;
          	});
    	} else{
    		$.ajax({
	             	method: "POST",
	              	url: "{{url('admin/getNextQuestionCount')}}",
	              	data: {categoryId:categoryId,subcategoryId:subcategoryId,paperId:paperId,subjectId:subjectId,section_type:section_type}
          	}).done(function( msg ) {
	            nextQuestion = document.getElementById('next_question');
	            nextQuestion.innerHTML = msg;
          	});
    	}
  	}
  	function showQuestionCount(ele){
  		paperId = parseInt($(ele).val());
  		categoryId = parseInt(document.getElementById('category').value);
  		subcategoryId = parseInt(document.getElementById('subcategory').value);
  		subjectId = parseInt(document.getElementById('subject').value);
  		section_type = parseInt(document.getElementById('section_type').value);
  		getQuestionCount(categoryId,subcategoryId,paperId,subjectId,section_type);
  		toggleNextPrev();
  	}

  	function showQuestionCountOnSectionChange(ele){
  		section_type = parseInt($(ele).val());
  		paperId = parseInt(document.getElementById('paper').value);
  		categoryId = parseInt(document.getElementById('category').value);
  		subcategoryId = parseInt(document.getElementById('subcategory').value);
  		subjectId = parseInt(document.getElementById('subject').value);
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
	              	url: "{{url('admin/getPrevQuestion')}}",
	              	data: {categoryId:categoryId,subcategoryId:subcategoryId,paperId:paperId,subjectId:subjectId,section_type:section_type}
          	}).done(function( msg ) {
          		if( 0 < msg ){
		            var prevEle = document.getElementById('prev_ques');
		            prevEle.classList.remove("hide");
		            var url = "{{url('admin/question')}}"+'/'+msg+'/edit';
					prevEle.setAttribute('href', url);
					prevEle.innerHTML = 'Prev Question';
		        }
          	});
  		}
  	}

  	$( document ).ready(function() {

		$(document).on('click', '#mcq_ques', function(){
			$('#mcq_ques').addClass('active');
			$('#num_ques').removeClass('active');
			$('div.num_ans').addClass('hide');
			$('div.mcq_ans').removeClass('hide');
			$('#question_type').val(1);
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

		// var selectedSubjectId = $('#selected_subject_id').val();
		// if(0 < selectedSubjectId){
		// 	getPapersBySubjectId(selectedSubjectId);
		// }
	});
</script>
@stop