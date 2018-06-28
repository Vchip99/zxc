@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Upload Questions </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-share"></i> Question Bank </li>
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
		    <div class="col-sm-3"><a class="btn btn-primary" href="{{asset('Download Question Bank Excel File.xlsx')}}" download data-toggle="tooltip" data-placement="bottom">Download Question Bank Excel File</a>
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
		              	<form id="uploadImagesForm" action="{{url('admin/uploadQuestionBankImages')}}" method="POST" enctype="multipart/form-data">
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
		<form id="questionForm" action="{{url('admin/uploadQuestionBankQuestions')}}" method="POST" enctype="multipart/form-data">
			{{csrf_field()}}
			<div class="form-group row @if ($errors->has('category')) has-error @endif">
			    <label class="col-sm-2 col-form-label">Category Name:</label>
			    <div class="col-sm-3">
			      <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
			          <option value="">Select Category</option>
			          	@if(count($testCategories) > 0)
			            	@foreach($testCategories as $testCategory)
			                	<option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
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
			      </select>
			      @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
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