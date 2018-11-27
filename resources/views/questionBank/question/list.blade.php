@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Question </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-share"></i> Question Bank </li>
      <li class="active"> Manage Question </li>
    </ol>
  </section>
@stop
@section('admin_content')
	<div class="container ">
	@if(Session::has('message'))
		<div class="alert alert-success" id="message">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		  	{{ Session::get('message') }}
		</div>
	@endif
	@php
		if(Session::has('search_question_bank_category')){
			$searchSelectedCategoryId = Session::get('search_question_bank_category');
		} else {
			$searchSelectedCategoryId = 0;
		}
		if(Session::has('search_question_bank_subcategory')){
			$searchSelectedSubcategoryId = Session::get('search_question_bank_subcategory');
		} else {
			$searchSelectedSubcategoryId = 0;
		}
	@endphp
  	<input type="hidden" name="message_status" id="message_status" value="@if(Session::has('message')) 1 @else 0 @endif">
	<div  class="admin_div">
		<form id="questionForm" action="{{url('admin/showQuestionBankQuestions')}}" method="POST">
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
			      <select id="subcategory" class="form-control" name="subcategory" required title="Sub Category">
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
		  	<div class="form-group row">
		      <div class="offset-sm-2 col-sm-10" title="Submit">
		        <button id="submitButton" type="submit" class="btn btn-primary">Submit</button>
		      </div>
		    </div>
		</form>
	</div>
	<div class="form-group row ">
      	<div id="addQuestionDiv">
	        <a id="addQuestion" href="{{url('admin/createQuestionBankQuestion')}}" type="button" class="btn btn-primary" style="float: right; margin-right: 12px;" title="Add New Question">Add New Question</a>&nbsp;&nbsp;
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
					        <tr style="overflow: auto;">
					          <th scope="row">{{$index + 1}}</th>
					          <td>{!! $question->name !!}</td>
					          <td>
					            <a href="{{url('admin/questionBankQuestion')}}/{{$question->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit Question" /></a>
					          </td>
					          <td>
					          	<a id="{{$question->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete Question" />
					              <form id="deleteQuestion_{{$question->id}}" action="{{url('admin/deleteQuestionBankQuestion')}}" method="POST" style="display: none;">
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
</script>

@stop