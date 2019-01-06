@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Associate Question </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Associate Question </li>
    </ol>
  </section>
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
	@endphp
  	<input type="hidden" name="message_status" id="message_status" value="@if(Session::has('message')) 1 @else 0 @endif">
	<div  class="admin_div">
		<form id="questionForm" action="{{url('admin/associateSession')}}" method="POST">
			{{csrf_field()}}
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
			    	<select id="paper" class="form-control" name="paper" required title="Paper" >
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
		      <div class="offset-sm-2 col-sm-10" title="Submit">
		        <button id="submitButton" type="submit" class="btn btn-primary">Submit</button>
		      </div>
		    </div>
		</form>
	</div>
	<div class="form-group row ">
	      <div id="addQuestionDiv">
	        <a id="addQuestion" href="{{url('admin/createQuestion')}}" type="button" class="btn btn-primary" style="float: right; margin-right: 12px;" title="Add New Question">Add New Question</a>&nbsp;&nbsp;
	      </div>
	    </div>
	<div>
	    <table class="table admin_table">
	      	<thead class="thead-inverse">
		        <tr>
		          	<th>#</th>
		          	<th>Question</th>
		          	<th>Section</th>
		          	<th>Update </th>
		        </tr>
	      	</thead>
	      	<tbody>
	      		@if(count($questions) > 0)
			        @foreach($questions as $index => $question)
			        	@if(isset($question->id))
					        <tr>
					          <th scope="row">{{$index + 1}}</th>
					          <td>{!! $question->name !!}</td>
					          <td>
								    <div class="col-sm-5">
								      	<select id="section_type_{{ $question->id }}" class="form-control" name="section_type" required title="Section" data-question_id="{{ $question->id }}" onChange="setSession(this);">
								    		<option value="">Select Section</option>
								    		@if(count($paperSections)>0)
								    			@foreach($paperSections as $paperSection)
								    				@if($question->section_type == $paperSection->id)
									                	<option value="{{$paperSection->id}}" selected="true">{{$paperSection->name}}</option>
									                @else
											        	<option value="{{$paperSection->id}}" >{{$paperSection->name}}</option>
											        @endif
												@endforeach
								    		@endif
								    	</select>
								    </div>
					          </td>
					          <td >
					          	<a id="{{$question->id}}" onclick="confirmUpdate(this);" class="btn btn-default">Update</a>
					                  <input type="hidden" name="session_id" id="session_{{ $question->id }}" value="">
					          </td>
					        </tr>
					    @endif
			        @endforeach
	    		@else
	    			<tr><td colspan="3">No questions are created.</td></tr>
	    		@endif
	      	</tbody>
	    </table>
  	</div>
  	</div>
<script type="text/javascript">

	function confirmUpdate(ele){
		var questionId = $(ele).attr('id');
		var sessionId = document.getElementById('session_'+questionId).value;
		if(sessionId > 0){
			$.confirm({
	          title: 'Confirmation',
	          content: 'You want to update this question?',
	          type: 'red',
	          typeAnimated: true,
	          buttons: {
	                Ok: {
	                    text: 'Ok',
	                    btnClass: 'btn-red',
	                    action: function(){

					      	$.ajax({
					              method: "POST",
					              url: "{{url('admin/updateQuestionSession')}}",
					              data: {question_id:questionId, session_id:sessionId}
					          })
					          .done(function( msg ) {
					          	if('true' == msg){
					          		document.getElementById("section_type_"+questionId).value = sessionId;
					          	}

					      	});
					    }
	                },
	                Cancel: function () {
	                }
	            }
	      	});
	    } else {
	    	$.alert({
	          title: 'Alert!',
	          content: 'Please select section.',
	        });
	    }
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
	            // document.getElementById("section_type").selectedIndex = '';
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
	            // document.getElementById("section_type").selectedIndex = '';
	        });
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
	            // document.getElementById("section_type").selectedIndex = '';
          	});
    	}
	}

  	function selectPaper(ele){
    	subjectId = parseInt($(ele).val());
    	getPapersBySubjectId(subjectId);
  	}

 //  	function selectPaperSession(ele){
 //  		paperId = parseInt($(ele).val());
	// 	if( 0 < paperId ){
	//       	$.ajax({
	//              	method: "POST",
	//               	url: "{{url('admin/getPaperSessionsByPaperId')}}",
	//               	data: {paper_id:paperId}
 //          	}).done(function( msg ) {
	//             select = document.getElementById('section_type');
	//             select.innerHTML = '';
	//             var opt = document.createElement('option');
	//             opt.value = '';
	//             opt.innerHTML = 'Select Session';
	//             select.appendChild(opt);
	//             if( 0 < msg.length){
	// 	            $.each(msg, function(idx, obj) {
	// 	                var opt = document.createElement('option');
	// 	                opt.value = obj.id;
	// 	                opt.innerHTML = obj.name;
	// 	                select.appendChild(opt);
	// 	            });
	//             }
 //          	});
 //    	}
	// }

	function setSession(ele){
    	var sessionId = parseInt($(ele).val());
    	var questionId = parseInt($(ele).data('question_id'));
    	document.getElementById('session_'+questionId).value = sessionId;
  	}

</script>

@stop