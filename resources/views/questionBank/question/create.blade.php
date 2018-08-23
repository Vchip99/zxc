@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Question </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-share"></i> Online Test </li>
      <li class="active"> Manage Question </li>
    </ol>
  </section>
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
@stop
@section('admin_content')
	<script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>

	@php
		if(Session::has('selected_question_bank_category')){
			$selectedCategoryId = Session::get('selected_question_bank_category');
		} else {
			$selectedCategoryId = 0;
		}
		if(Session::has('selected_question_bank_subcategory')){
			$selectedSubCategoryId = Session::get('selected_question_bank_subcategory');
		} else {
			$selectedSubCategoryId = 0;
		}
		if(Session::has('selected_question_bank_question_type')){
			$selectedQuestionType = Session::get('selected_question_bank_question_type');
		} else {
			$selectedQuestionType = 1;
		}
		if(Session::has('question_bank_next_question_no')){
			$nextQuestionNo = Session::get('question_bank_next_question_no');
		} else {
			$nextQuestionNo = 1;
		}
	@endphp
	<div id="main ">
		<ul id ="ul">
			@if(Session::has('message'))
				<div class="alert alert-success" id="message">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				  	{{ Session::get('message') }}
				</div>
			@endif
	  		<li title="ADD MCQ"><a class="btn active" id="mcq_ques">ADD MCQ</a></li>
	  		<li title="ADD NUMERICAL"><a class="btn" id="num_ques">ADD NUMERICAL</a></li>
	  		@if($prevQuestionId > 0)
	  			<li title="Prev Question"><a class="btn" id="prev_ques" href="{{url('admin/questionBankQuestion')}}/{{$prevQuestionId}}/edit">Prev Question</a></li>
	  		@else
	  			<li title="No Prev Question"><a class="btn" id="prev_ques">No Prev Question</a></li>
	  		@endif
	  		@if($nextQuestionId > 0)
		  		<li title="Next Question"><a class="btn" id="next_ques" href="{{url('admin/questionBankQuestion')}}/{{$nextQuestionId}}/edit">Next Question</a></li>
	  		@elseif(( $prevQuestionId > 0 || null == $prevQuestionId ) && null == $nextQuestionId )
	  			<li title="Add Question"><a class="btn" id="next_ques" href="{{url('admin/createQuestionBankQuestion')}}">Add Question </a></li>
	  		@else
	  			<li title="No Next Question"><a class="btn" id="next_ques">No Next Question</a></li>
	  		@endif
		</ul>

		@if(isset($testQuestion->id))
			<form id="createForm" action="{{url('admin/updateQuestionBankQuestion')}}" method="POST" role="form" data-toggle="validator">
			{{method_field('PUT')}}
			<input type="hidden" name="question_id" id="question_id" value="{{$testQuestion->id}}">
		@else
			<form id="createForm" action="{{url('admin/createQuestionBankQuestion')}}" method="POST">
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
			          <option value="">Select Category</option>
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
				      <select id="subcategory" class="form-control" name="subcategory" onChange="showQuestionCount(this);" required title="Sub Category">
				        <option value="">Select Sub Category</option>
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
				    	CKEDITOR.replace( 'ans1', { enterMode: CKEDITOR.ENTER_BR } );
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
				    	CKEDITOR.replace( 'ans2', { enterMode: CKEDITOR.ENTER_BR } );
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
				    	CKEDITOR.replace( 'ans3' , { enterMode: CKEDITOR.ENTER_BR });
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
				    	CKEDITOR.replace( 'ans4', { enterMode: CKEDITOR.ENTER_BR } );
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
				    	CKEDITOR.replace( 'solution', { enterMode: CKEDITOR.ENTER_BR } );
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


<script type="text/javascript">

  	function selectSubcategory(ele){
	    id = parseInt($(ele).val());
	    if( 0 < id ){
	      $.ajax({
	              method: "POST",
	              url: "{{url('admin/getQuestionBankSubCategories')}}",
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
          	});
	    }
  	}

	function checkCkeditor(){
		var errorCount = 0;
		var questionLength = CKEDITOR.instances.question.getData().length;

		if(0 == questionLength){
			$('#question_error').removeClass('hide');
			$('#question_error').addClass('alert alert-danger');
			errorCount += 1;
		} else {
			$('#question_error').addClass('hide');
			$('#question_error').removeClass('alert alert-danger');
		}

		var solutionLength = CKEDITOR.instances.solution.getData().length;

		if(0 == solutionLength){
			$('#solution_error').removeClass('hide');
			$('#solution_error').addClass('alert alert-danger');
			errorCount += 1;
		} else {
			$('#solution_error').addClass('hide');
			$('#solution_error').removeClass('alert alert-danger');
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
		}

		if( 0 == errorCount){
			var form = document.getElementById('createForm');
			form.submit();
		} else{
			return false;
		}
	}

  	function getQuestionCount(categoryId,subcategoryId){
  		var questionId = parseInt($('#question_id').val());
  		if( 0 < categoryId && 0 < subcategoryId && false == isNaN(questionId)){
	      	$.ajax({
	             	method: "POST",
	              	url: "{{url('admin/getCurrentQuestionBankQuestionCount')}}",
	              	data: {categoryId:categoryId,subcategoryId:subcategoryId,questionId:questionId}
          	}).done(function( msg ) {
	            nextQuestion = document.getElementById('next_question');
	            nextQuestion.innerHTML = msg;
          	});
    	} else{
    		$.ajax({
	             	method: "POST",
	              	url: "{{url('admin/getNextQuestionBankQuestionCount')}}",
	              	data: {categoryId:categoryId,subcategoryId:subcategoryId}
          	}).done(function( msg ) {
	            nextQuestion = document.getElementById('next_question');
	            nextQuestion.innerHTML = msg;
          	});
    	}
  	}

  	function showQuestionCount(ele){
  		subcategoryId = parseInt($(ele).val());
  		categoryId = parseInt(document.getElementById('category').value);
  		getQuestionCount(categoryId,subcategoryId);
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
	});
</script>
@stop