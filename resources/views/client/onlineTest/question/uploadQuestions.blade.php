@extends('client.dashboard')
@section('module_title')
<section class="content-header">
	<h1> Upload Questions </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Upload Questions </li>
    </ol>
</section>
@stop
@section('dashboard_content')
	&nbsp;
	<div class="container ">
	@if(Session::has('message'))
		<div class="alert alert-success" id="message">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		  	{{ Session::get('message') }}
		</div>
	@endif
  	<input type="hidden" name="message_status" id="message_status" value="@if(Session::has('message')) 1 @else 0 @endif">
	<div  class="admin_div">
		<div class="form-group row">
		    <label class="col-sm-2 col-form-label">Download Excel File:</label>
		    <div class="col-sm-3"><a class="btn btn-primary" href="{{asset('Download Excel Questions File.xlsx')}}" download data-toggle="tooltip" data-placement="bottom">Download Excel Questions File</a>
		    </div>
	  	</div>
		<form id="questionForm" action="{{url('uploadQuestions')}}" method="POST" enctype="multipart/form-data">
			{{csrf_field()}}
		  	<div class="form-group row @if ($errors->has('institute_course')) has-error @endif">
			    <label class="col-sm-2 col-form-label">Institute Course Name:</label>
			    <div class="col-sm-3">
			      <select class="form-control" name="institute_course" id="institute_course" required title="Category" onChange="selectCategory(this);" >
			          <option value="">Select Institute Course</option>
			          @if(count($instituteCourses) > 0)
			            @foreach($instituteCourses as $instituteCourse)
			                <option value="{{$instituteCourse->id}}">{{$instituteCourse->name}}</option>
			            @endforeach
			          @endif
			        </select>
			        @if($errors->has('institute_course')) <p class="help-block">{{ $errors->first('institute_course') }}</p> @endif
			    </div>
		  	</div>
			<div class="form-group row @if ($errors->has('category')) has-error @endif">
			    <label class="col-sm-2 col-form-label">Category Name:</label>
			    <div class="col-sm-3">
			      <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
			          <option value="">Select Category</option>
			      </select>
			      @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
			    </div>
		  	</div>
		  	<div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
			    <label class="col-sm-2 col-form-label">Sub Category Name:</label>
			    <div class="col-sm-3">
			      <select id="subcategory" class="form-control" name="subcategory" onChange="selectSubject(this);" required title="Sub Category">
			        <option value="">Select Sub Category</option>
			      </select>
			      @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
			    </div>
		  	</div>
		 	<div class="form-group row @if ($errors->has('subject')) has-error @endif">
		    <label class="col-sm-2 col-form-label">Subject Name:</label>
			    <div class="col-sm-3">
			      	<select id="subject" class="form-control" name="subject" onChange="selectPaper(this);" required title="Subject">
			        	<option value="">Select Subject</option>
			      	</select>
			      	@if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
			    </div>
		    </div>
		    <div class="form-group row @if ($errors->has('paper')) has-error @endif">
			    <label for="name" class="col-sm-2 col-form-label">Paper Name:</label>
			    <div class="col-sm-3">
			    	<select id="paper" class="form-control" name="paper" required title="Paper" onChange="selectSection();" >
			    		<option value="">Select Paper</option>
			    	</select>
			    	@if($errors->has('paper')) <p class="help-block">{{ $errors->first('paper') }}</p> @endif
			    </div>
		  	</div>
		  	<div class="form-group row @if ($errors->has('section_type')) has-error @endif">
			    <label for="section_type" class="col-sm-2 col-form-label">Section Name:</label>
			    <div class="col-sm-3">
			    	<select id="section_type" class="form-control" name="section_type" required title="Section Type">
			    		<option value="">Select Section</option>
			    	</select>
			    	@if($errors->has('section_type')) <p class="help-block">{{ $errors->first('section_type') }}</p> @endif
			    </div>
		  	</div>
		  	<div class="form-group row">
		    	<label class="col-sm-2 col-form-label">Upload File:</label>
			    <div class="col-sm-3">
			    	<input type="file" name="questions" class="form-control" />
			    </div>
		    </div>
		  	<div class="form-group row">
		      <div class="offset-sm-2 col-sm-10" title="Submit">
		        <button id="submitButton" type="submit" class="btn btn-primary">Submit</button>
		      </div>
		    </div>
		</form>
	</div>
  	</div>
  	</div>
<script type="text/javascript">
	function selectCategory(ele){
	    var id = parseInt($(ele).val());
	    if( 0 < id ){
	      $.ajax({
	              method: "POST",
	              url: "{{url('getOnlineTestCategories')}}",
	              data: {id:id}
	          })
	          .done(function( msg ) {
	            select = document.getElementById('category');
	            select.innerHTML = '';
	            var opt = document.createElement('option');
	            opt.value = '';
	            opt.innerHTML = 'Select Category';
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
		    select = document.getElementById('category');
	      	select.innerHTML = '';
	      	var opt = document.createElement('option');
	      	opt.value = '';
	      	opt.innerHTML = 'Select Category';
	      	select.appendChild(opt);
	    }
	      	document.getElementById("subcategory").selectedIndex = '';
	      	document.getElementById("subject").selectedIndex = '';
        	document.getElementById("paper").selectedIndex = '';
	}

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
          	});
	            document.getElementById("subject").selectedIndex = '';
	            document.getElementById("paper").selectedIndex = '';
	    }
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
	        });
	            document.getElementById("paper").selectedIndex = '';
    	}
  	}

	function getPapersBySubjectId(subjectId){
		if( 0 < subjectId ){
	      	$.ajax({
	             	method: "POST",
	              	url: "{{url('getOnlinePapersBySubjectId')}}",
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
          	});
    	}
	}

  	function selectPaper(ele){
    	subjectId = parseInt($(ele).val());
    	getPapersBySubjectId(subjectId);
  	}

  	function selectSection(){
		var instituteCourse = document.getElementById("institute_course").value;
		var paperId = parseInt(document.getElementById('paper').value);
		if( 0 < instituteCourse ){
	      	$.ajax({
	             	method: "POST",
	              	url: "{{url('getOnlinePaperSectionsByInstituteCourseId')}}",
	              	data: {institute_course:instituteCourse, paper_id:paperId}
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
    	}
	}

</script>

@stop