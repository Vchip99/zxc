@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Question Bank</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Question Bank</li>
    </ol>
  </section>
  <style type="text/css">
  	img{
		position: relative;
		outline: none;
		max-width: 100%;
		height: auto;
	}
	.row-id {
	  width: 1% !important;
	}
	.row-que {
	  width: 80% !important;
	}
	.row-opt {
	  width: 5% !important;
	}
	.container{
		padding-left: 0px !important;
		padding-right: 0px !important;
	}
  </style>
@stop
@section('admin_content')
&nbsp;
	<div class="container ">
		@if(Session::has('message'))
			<div class="alert alert-success" id="message">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			  	{{ Session::get('message') }}
			</div>
		@endif
		@if (count($errors) > 0)
		  <div class="alert alert-danger">
		      <ul>
		          @foreach ($errors->all() as $error)
		              <li>{{ $error }}</li>
		          @endforeach
		      </ul>
		  </div>
		@endif
		@php
			if(Session::has('search_selected_category')){
				$searchSelectedCategoryId = Session::get('search_selected_category');
			} else {
				$searchSelectedCategoryId = 0;
			}
			if(Session::has('search_selected_subcategory')){
				$searchSelectedSubcategoryId = Session::get('search_selected_subcategory');
			} else {
				$searchSelectedSubcategoryId = 0;
			}
			if(Session::has('search_selected_subject')){
				$searchSelectedSubjectId = Session::get('search_selected_subject');
			} else {
				$searchSelectedSubjectId = 0;
			}
			if(Session::has('search_selected_paper')){
				$searchSelectedPaperId = Session::get('search_selected_paper');
			} else {
				$searchSelectedPaperId = 0;
			}
			if(Session::has('search_selected_section')){
				$searchSelectedSectionId = Session::get('search_selected_section');
			} else {
				$searchSelectedSectionId = 0;
			}
			if(Session::has('search_question_bank_category')){
				$searchSelectedBankCategoryId = Session::get('search_question_bank_category');
			} else {
				$searchSelectedBankCategoryId = 0;
			}
			if(Session::has('search_question_bank_subcategory')){
				$searchSelectedBankSubcategoryId = Session::get('search_question_bank_subcategory');
			} else {
				$searchSelectedBankSubcategoryId = 0;
			}
		@endphp
	  	<input type="hidden" name="message_status" id="message_status" value="@if(Session::has('message')) 1 @else 0 @endif">
	  	<form id="questionForm" action="{{url('admin/useQuestionBank')}}" method="POST">
			{{csrf_field()}}
			<div  class="admin_div" style="border-bottom: 1px solid white;">
				<div class="form-group row @if ($errors->has('category')) has-error @endif">
				    <label class="col-sm-2 col-form-label">Category Name:</label>
				    <div class="col-sm-3">
				      <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
				          <option value="">Select Category</option>
				          @if(count($testCategories) > 0)
				            @foreach($testCategories as $testCategory)
				              @if( $testCategory->id == $searchSelectedCategoryId )
				                <option value="{{$testCategory->id}}" selected="true" readonly="true">{{$testCategory->name}}</option>
				              @else
				                <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
				              @endif
				            @endforeach
				          @endif
				      </select>
				      @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
				    </div>
			  	</div>
			  	<div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
				    <label class="col-sm-2 col-form-label">Sub Category Name:</label>
				    <div class="col-sm-3">
				      <select id="subcategory" class="form-control" name="subcategory" onChange="selectSubject(this);" required title="Sub Category">
				        <option value="">Select Sub Category</option>
				        @if(count($testSubCategories) > 0)
				        	@foreach($testSubCategories as $testSubCategory)
					            @if($searchSelectedSubcategoryId == $testSubCategory->id)
					                <option value="{{$testSubCategory->id}}" selected="true">{{$testSubCategory->name}}</option>
				              	@else
					                <option value="{{$testSubCategory->id}}">{{$testSubCategory->name}}</option>
					            @endif
				          	@endforeach
				        @endif
				      </select>
				      @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
				    </div>
			  	</div>
			 	<div class="form-group row @if ($errors->has('subject')) has-error @endif">
			    	<label class="col-sm-2 col-form-label">Subject Name:</label>
				    <div class="col-sm-3">
				      	<select id="subject" class="form-control" name="subject" onChange="selectPaper(this);" required title="Subject">
				        	<option value="">Select Subject</option>
				          	@if(count($testSubjects) > 0 )
				          		@foreach($testSubjects as $testSubject)
				          			@if($testSubject->id == $searchSelectedSubjectId)
		            					<option value="{{$testSubject->id}}" selected="true">{{$testSubject->name}}</option>
		            				@else
		            					<option value="{{$testSubject->id}}">{{$testSubject->name}}</option>
		            				@endif
				          		@endforeach
				        	@endif
				      	</select>
				      	@if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
				    </div>
			    </div>
			    <div class="form-group row @if ($errors->has('paper')) has-error @endif">
				    <label for="name" class="col-sm-2 col-form-label">Paper Name:</label>
				    <div class="col-sm-3">
				    	<select id="paper" class="form-control" name="paper" required title="Paper" onChange="selectPaperSession(this);" >
				    		<option value="">Select Paper</option>
				    		@if(count($papers)>0)
				    			@foreach($papers as $paper)
				    				@if($paper->id == $searchSelectedPaperId)
		            					<option value="{{$paper->id}}" selected="true">{{$paper->name}}</option>
		            				@else
		            					<option value="{{$paper->id}}">{{$paper->name}}</option>
		            				@endif
				    			@endforeach
				    		@endif
				    	</select>
				    	@if($errors->has('paper')) <p class="help-block">{{ $errors->first('paper') }}</p> @endif
				    </div>
			  	</div>
			  	<div class="form-group row">
			    	<label class="col-sm-2 col-form-label">Section:</label>
				    <div class="col-sm-3">
				      	<select id="section_type" class="form-control" name="section_type" required title="Section">
				    		<option value="">Select Section</option>
				    		@if(count($paperSections)>0)
				    			@foreach($paperSections as $paperSection)
				    				@if($searchSelectedSectionId == $paperSection->id)
					                	<option value="{{$paperSection->id}}" selected="true">{{$paperSection->name}}</option>
					                @else
							        	<option value="{{$paperSection->id}}" >{{$paperSection->name}}</option>
							        @endif
								@endforeach
				    		@endif
				    	</select>
				    </div>
			    </div>
			    <div class="form-group row">
				    <div class="col-sm-3">
				      <select id="bank_category" class="form-control" name="bank_category" onChange="selectBankSubcategory(this);" required title="Bank Category">
				          <option value="">Question Bank Category</option>
				          @if(count($bankCategories) > 0)
				            @foreach($bankCategories as $bankCategory)
				            	@if($searchSelectedBankCategoryId == $bankCategory->id)
				                	<option value="{{$bankCategory->id}}" selected>{{$bankCategory->name}}</option>
				                @else
				                	<option value="{{$bankCategory->id}}">{{$bankCategory->name}}</option>
				                @endif
				            @endforeach
				          @endif
				      </select>
				    </div>
				    <div class="col-sm-3">
				      <select id="bank_sub_category" class="form-control" name="bank_sub_category" required title="Bank Sub Category">
				          <option value="">Question Bank Sub Category</option>
				          @if(count($bankSubCategories) > 0)
				            @foreach($bankSubCategories as $bankSubCategory)
				            	@if($searchSelectedBankSubcategoryId == $bankSubCategory->id)
				                	<option value="{{$bankSubCategory->id}}" selected>{{$bankSubCategory->name}}</option>
				                @else
				                	<option value="{{$bankSubCategory->id}}">{{$bankSubCategory->name}}</option>
				                @endif
				            @endforeach
				          @endif
				      </select>
				    </div>
				    <div class="col-sm-2">
						<button type="submit" class="form-control btn btn-primary" > Submit</button>
				    </div>
			  	</div>
			</div>
		</form>
		<form id="questionBankForm" action="{{url('admin/exportQuestionBank')}}" method="POST">
			{{csrf_field()}}
			<div class="admin_div" style="overflow: auto;">
			    <table class="table ">
			      	<thead class="thead-inverse">
				        <tr>
				          	<th class="row-id">#</th>
				          	<th class="row-que">Question</th>
				          	<th class="row-opt">Positive Mark</th>
				          	<th class="row-opt">Negative Mark</th>
				          	<th class="row-opt">Selected</th>
				        </tr>
			      	</thead>
			      	<tbody>
			      		@if(count($questions) > 0)
					        @foreach($questions as $index => $question)
					        	@if(isset($question->id))
							        <tr>
							          	<th scope="row">{{$index + 1}}</th>
							          	<td>
							          		<div class="col-sm-2" >
							          			{!! $question->name !!}
												@if( 1 == $question->question_type )
													<div class="row answer">A.<input type="radio" class="radio1" disabled/>
														{!! $question->answer1 !!}
													</div>
													<div class="row answer">B.<input type="radio" class="radio1" disabled/>
														{!! $question->answer2 !!}
													</div>
													<div class="row answer">C.<input type="radio" class="radio1" disabled/>
														{!! $question->answer3 !!}
													</div>
													<div class="row answer">D.<input type="radio" class="radio1" disabled/>
														{!! $question->answer4 !!}
													</div>
													<div class="row answer">E.<input type="radio" class="radio1" disabled/>
														{!! $question->answer5 !!}
													</div>
												@endif
												@if(1 == $question->question_type)
													Answer:{!! $question->answer !!}
												@else
													<br>
													Answer:{{$question->min}} - {{$question->max}}
												@endif
											</div>
						          		</td>
							          	<td>
							            	<input type="text" class="form-control" id="positive_{{$question->id}}" name="positive_{{$question->id}}" value="">
							          	</td>
							          	<td>
							            	<input type="text" class="form-control" id="negative_{{$question->id}}" name="negative_{{$question->id}}" value="">
							          	</td>
							          	<td>
							          		<input type="checkbox" name="selected[]" value="{{$question->id}}" onClick="checkValues(this);">
							          	</td>
							        </tr>
							    @endif
					        @endforeach
					        <input type="hidden" name="selected_category" id="selected_category" value="">
					        <input type="hidden" name="selected_subcategory" id="selected_subcategory" value="">
					        <input type="hidden" name="selected_subject" id="selected_subject" value="">
					        <input type="hidden" name="selected_paper" id="selected_paper" value="">
					        <input type="hidden" name="selected_section_type" id="selected_section_type" value="">
			    		@else
			    			<tr><td colspan="3">No questions are created.</td></tr>
			    			<input type="hidden" name="selected_category" id="selected_category" value="">
					        <input type="hidden" name="selected_subcategory" id="selected_subcategory" value="">
					        <input type="hidden" name="selected_subject" id="selected_subject" value="">
					        <input type="hidden" name="selected_paper" id="selected_paper" value="">
					        <input type="hidden" name="selected_section_type" id="selected_section_type" value="">
			    		@endif
			      	</tbody>
			    </table>
		  	</div>
		  	<div class="">
			    <button type="submit" class="form-control btn btn-primary" style="float: right;width: 130px;" > Transfer Selected</button>
			</div>
		</form>
	</div>
<script type="text/javascript">
	$(document).ready(function(){
		setInputs();
	});
	function checkValues(ele){
		if($(ele).prop('checked')){
			var id = $(ele).val();
			if(!$('#positive_'+id).val() || !$('#negative_'+id).val()){
				$(ele).prop('checked', false);
				alert('Please Enter Positive Mark/Min and Negative Mark/Max');
			} else {
				$('#positive_'+id).prop('required', true);
				$('#negative_'+id).prop('required', true);
			}
		}
	}
	function setInputs(){
		var category = parseInt(document.getElementById('category').value);
	    var subcategory = parseInt(document.getElementById('subcategory').value);
	    var subject = parseInt(document.getElementById('subject').value);
	    var paper = parseInt(document.getElementById('paper').value);
	    var sectionType = parseInt(document.getElementById('section_type').value);
	    document.getElementById('selected_category').value = category;
	    document.getElementById('selected_subcategory').value = subcategory;
	    document.getElementById('selected_subject').value = subject;
	    document.getElementById('selected_paper').value = paper;
	    document.getElementById('selected_section_type').value = sectionType;
	}


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
	            document.getElementById("subject").selectedIndex = '';
	            document.getElementById("paper").selectedIndex = '';
	            document.getElementById("section_type").selectedIndex = '';
          	});
	        setInputs();
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
	            document.getElementById("paper").selectedIndex = '';
	            document.getElementById("section_type").selectedIndex = '';
	        });
	        setInputs();
    	}
  	}
	function getPapersBySubjectId(subjectId){
		if( 0 < subjectId ){
	      	$.ajax({
	             	method: "POST",
	              	url: "{{url('admin/getPapersBySubjectId')}}",
	              	data: {subjectId:subjectId}
          	}).done(function( msg ) {
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
		                select.appendChild(opt);
		            });
	            }
	            document.getElementById("section_type").selectedIndex = '';
          	});
          	setInputs();
    	}
	}

  	function selectPaper(ele){
    	subjectId = parseInt($(ele).val());
    	getPapersBySubjectId(subjectId);
  	}

  	function selectPaperSession(ele){
  		paperId = parseInt($(ele).val());
		if( 0 < paperId ){
	      	$.ajax({
	             	method: "POST",
	              	url: "{{url('admin/getPaperSectionsByPaperId')}}",
	              	data: {paper_id:paperId}
          	}).done(function( msg ) {
	            select = document.getElementById('section_type');
	            select.innerHTML = '';
	            var opt = document.createElement('option');
	            opt.value = '';
	            opt.innerHTML = 'Select Session';
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
          	setInputs();
    	}
	}

	function selectBankSubcategory(ele){
	    id = parseInt($(ele).val());
	    if( 0 < id ){
	      $.ajax({
	              method: "POST",
	              url: "{{url('admin/getQuestionBankSubCategories')}}",
	              data: {id:id}
	          })
	          .done(function( msg ) {
	            select = document.getElementById('bank_sub_category');
	            select.innerHTML = '';
	            var opt = document.createElement('option');
	            opt.value = '';
	            opt.innerHTML = 'Question Bank Sub Category';
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
	    setInputs();
  	}

</script>

@stop