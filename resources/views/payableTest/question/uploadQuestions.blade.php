@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Upload Questions </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-credit-card-alt"></i> Payable Test </li>
      <li class="active"> Upload Questions </li>
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
	<div  class="admin_div">
		<div class="form-group row">
		    <label class="col-sm-2 col-form-label">Download Excel File:</label>
		    <div class="col-sm-3"><a class="btn btn-primary" href="{{asset('Download Excel Questions File.xlsx')}}" download data-toggle="tooltip" data-placement="bottom">Download Excel Questions File</a>
		    </div>
		    <div class="col-sm-3">
		    	<button id="submitButton" type="submit" class="btn btn-primary" data-toggle="modal" data-target="#modelUploadImages">Upload Images</button>
		    </div>
		    <div class="modal fade" id="modelUploadImages" >
	          	<div class="modal-dialog" role="document" >
		            <div class="modal-content" >
		              	<div class="modal-header">
			                <h5 class="modal-title model-title">Upload Multiple Images</h5>
			                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			                  <span aria-hidden="true">&times;</span>
			                </button>
		              	</div>
		              	<form id="uploadImagesForm" action="{{url('admin/uploadPayableImages')}}" method="POST" enctype="multipart/form-data">
						{{csrf_field()}}
			              	<div class="modal-body">
				                <div class="form-group row">
				                  <label class="col-sm-3 col-form-label">Upload Images:</label>
				                  <div class="col-sm-3">
				                    <input type="file" class="model-input" id="upload_images" name="images[]" multiple="multiple" required="true">
				                  </div>
				                </div>
			              	</div>
			              	<div class="modal-footer">
			                	<button class="btn btn-primary" data-dismiss="modal" onclick="uploadImages();">Save/Upload</button>
			                	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			              	</div>
			            </form>
		            </div>
	          	</div>
        	</div>
	  	</div>
		<form id="questionForm" action="{{url('admin/uploadPayableQuestions')}}" method="POST" enctype="multipart/form-data">
			{{csrf_field()}}
		  	<div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
			    <label class="col-sm-2 col-form-label">Sub Category Name:</label>
			    <div class="col-sm-3">
			      <select id="subcategory" class="form-control" name="subcategory" onChange="selectSubject(this);" required title="Sub Category">
			        <option value="">Select Sub Category</option>
			        @if(count($testSubCategories) > 0)
		            	@foreach($testSubCategories as $testSubCategory)
		                	<option value="{{$testSubCategory->id}}">{{$testSubCategory->name}}</option>
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
			      	</select>
			      	@if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
			    </div>
		    </div>
		    <div class="form-group row @if ($errors->has('paper')) has-error @endif">
			    <label for="name" class="col-sm-2 col-form-label">Paper Name:</label>
			    <div class="col-sm-3">
			    	<select id="paper" class="form-control" name="paper" required title="Paper" onChange="selectPaperSection();">
			    		<option value="">Select Paper</option>
			    	</select>
			    	@if($errors->has('paper')) <p class="help-block">{{ $errors->first('paper') }}</p> @endif
			    </div>
		  	</div>
		  	<div class="form-group row @if ($errors->has('section_type')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Select Section:</label>
			    <div class="col-sm-3">
		    		<select id="section_type" class="form-control" name="section_type" required title="Section">
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
		  	<div class="form-group row" style="margin-left: 20px;">
		        <button id="submitButton" type="submit" class="btn btn-primary">Submit</button>
		    </div>

		</form>
	</div>
  	</div>
<script type="text/javascript">

  	function selectSubject(ele){
	    subcatId = parseInt($(ele).val());
	    if( 0 < subcatId ){
	      $.ajax({
	              method: "POST",
	              url: "{{url('admin/getPayableSubjectsBySubcatId')}}",
	              data: {subcatId:subcatId}
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
	        });
    	}
  	}

  	function selectPaper(ele){
    	subjectId = parseInt($(ele).val());
    	if( 0 < subjectId ){
	      	$.ajax({
	             	method: "POST",
	              	url: "{{url('admin/getPayablePapersBySubjectId')}}",
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
          	});
    	};
  	}

  	function selectPaperSection(){
  		var paperId = parseInt(document.getElementById('paper').value);
		if( 0 < paperId ){
	      	$.ajax({
	             	method: "POST",
	              	url: "{{url('admin/getPayablePaperSectionsByPaperId')}}",
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
    	}
	}

	function uploadImages(){
		if(document.getElementById('upload_images').value.length > 0){
			document.getElementById('uploadImagesForm').submit();
		} else {
			alert('please select an image.');
        	return false;
		}
	}
</script>

@stop