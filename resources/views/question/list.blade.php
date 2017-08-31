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
		<form id="questionForm" action="{{url('admin/showQuestions')}}" method="POST">
			{{csrf_field()}}
			<div class="form-group row @if ($errors->has('category')) has-error @endif">
			    <label class="col-sm-2 col-form-label">Category Name:</label>
			    <div class="col-sm-3">
			      <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
			          <option value="">Select Category ...</option>
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
			        <option value="">Select Sub Category ...</option>
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
			        	<option value="">Select Subject ...</option>
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
			    	<select id="paper" class="form-control" name="paper" required title="Paper">
			    		<option value="">Select Paper ...</option>
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
		    	<label class="col-sm-2 col-form-label">Section Type:</label>
			    <div class="col-sm-3">
			      	<select id="section_type" class="form-control" name="section_type" required title="Section">
			    		<option value="">Select Section ...</option>
		                <option value="1" @if(1 == $searchSelectedSectionId) selected @endif >Aptitude</option>
				        <option value="0" @if(0 == $searchSelectedSectionId) selected @endif >Technical</option>
			    	</select>
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
		          	<th>Edit Question</th>
		          	<th>Delete Question</th>
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
					            <a href="{{url('admin/question')}}/{{$question->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit Question" /></a>
					          </td>
					          <td>
					          	<a id="{{$question->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete Question" />
					              <form id="deleteQuestion_{{$question->id}}" action="{{url('admin/deleteQuestion')}}" method="POST" style="display: none;">
					                  {{ csrf_field() }}
					                  {{ method_field('DELETE') }}
					                  <input type="hidden" name="question_id" value="{{$question->id}}">
					              </form>
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

	function confirmDelete(ele){
       $.confirm({
          title: 'Confirmation',
          content: 'You want to delete this question?',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteQuestion_'+id;
                      document.getElementById(formId).submit();
                    }
                },
                Cancle: function () {
                }
            }
          });
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
	            document.getElementById("subject").selectedIndex = '';
	            document.getElementById("paper").selectedIndex = '';
	            document.getElementById("section_type").selectedIndex = '';
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
	            document.getElementById("paper").selectedIndex = '';
	            document.getElementById("section_type").selectedIndex = '';
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
	            opt.innerHTML = 'Select Paper ...';
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
    	}
	}

  	function selectPaper(ele){
    	subjectId = parseInt($(ele).val());
    	getPapersBySubjectId(subjectId);
  	}

</script>

@stop