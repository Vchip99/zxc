@extends('admin.master')
@section('module_title')
	<section class="content-header">
	    <h1> User Data </h1>
	    <ol class="breadcrumb">
	      <li><i class="fa fa-files-o"></i> Users Info </li>
	      <li class="active"> User Data </li>
	    </ol>
	</section>
@stop
@section('admin_content')
	@if(isset($userData->id))
		<form id="createForm" action="{{url('admin/updateUserData')}}" method="POST" role="form" data-toggle="validator" enctype="multipart/form-data">
		{{method_field('PUT')}}
		<input type="hidden" name="user_data_id" id="user_data_id" value="{{$userData->id}}">
	@else
		<form id="createForm" action="{{url('admin/createUserData')}}" method="POST" enctype="multipart/form-data">
	@endif
	{{csrf_field()}}
		<div class="container admin_div">
			<div class="form-group row @if ($errors->has('category')) has-error @endif">
			    <label class="col-sm-2 col-form-label">Category:</label>
			    <div class="col-sm-3">
			    @if(count($testCategories) > 0 && isset($userData->id))
			    	@foreach($testCategories as $testCategory)
		              @if( $testCategory->id == $userData->category_id )
		              	<input class="form-control" type="text" name="category_text" value="{{$testCategory->name}}" readonly="true">
		              	<input class="form-control" type="hidden" name="category" value="{{$userData->category_id}}">
		              @endif
		            @endforeach
			    @else
			      <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
			          <option value="">Select Category</option>
			            @foreach($testCategories as $testCategory)
		                	<option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
			            @endforeach
			      </select>
	          	@endif
		      	@if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
			    </div>
		  	</div>
		  	<div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
			    <label class="col-sm-2 col-form-label">Sub Category:</label>
			    <div class="col-sm-3">
		        	@if(count($testSubCategories) > 0 && isset($userData->id))
			        	@foreach($testSubCategories as $testSubCategory)
				            @if($userData->sub_category_id == $testSubCategory->id)
				                <input class="form-control" type="text" name="subcategory_text" value="{{$testSubCategory->name}}" readonly="true">
				                <input class="form-control" type="hidden" name="subcategory" value="{{$userData->sub_category_id}}">
				            @endif
			          	@endforeach
			        @else
				      <select id="subcategory" class="form-control" name="subcategory" onChange="selectSubject(this);" required title="Sub Category">
				        <option value="">Select Sub Category</option>
				        	@foreach($testSubCategories as $testSubCategory)
				                <option value="{{$testSubCategory->id}}">{{$testSubCategory->name}}</option>
				          	@endforeach
				      	</select>
				    @endif
			      @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
			    </div>
		  	</div>
			<div class="form-group row @if ($errors->has('subject')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Subject:</label>
			    <div class="col-sm-3" >
	          		@if(count($testSubjects) > 0 && isset($userData->id) )
	          			@foreach($testSubjects as $testSubject)
		          			@if($userData->subject_id == $testSubject->id)
	            			 	<input class="form-control" type="text" name="subject_text" value="{{$testSubject->name}}" readonly="true">
	            			 	<input class="form-control" type="hidden" name="subject" value="{{$userData->subject_id}}">
		            		@endif
		          		@endforeach
	          		@else
				      	<select id="subject" class="form-control" name="subject" onChange="selectPaper(this);" required title="Subject">
				        	<option value="">Select Subject</option>
				          	@foreach($testSubjects as $testSubject)
		            			<option value="{{$testSubject->id}}">{{$testSubject->name}}</option>
			          		@endforeach
				        	@if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
			      		</select>
	        		@endif
			    </div>
		    </div>
		    <div class="form-group row @if ($errors->has('paper')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">Paper:</label>
			    <div class="col-sm-3">
	    			@if(count($papers) > 0 && isset($userData->id))
	    				@foreach($papers as $paper)
				            @if($userData->paper_id == $paper->id)
				                <input class="form-control" type="text" name="paper_text" value="{{$paper->name}}" readonly="true">
				                <input class="form-control" type="hidden" name="paper" value="{{$userData->paper_id}}">
				            @endif
			          	@endforeach
    			   	@else
				      	<select id="paper" class="form-control" name="paper" required title="Paper">
				    		<option value="">Select Paper</option>
					       		@foreach($papers as $paper)
					                <option value="{{$paper->id}}">{{$paper->name}}</option>
					          	@endforeach
					        @if($errors->has('paper')) <p class="help-block">{{ $errors->first('paper') }}</p> @endif
				    	</select>
			        @endif
			    </div>
		    </div>
		    <div class="form-group row @if ($errors->has('user')) has-error @endif">
		    	<label class="col-sm-2 col-form-label">User:</label>
			    <div class="col-sm-3">
	    			@if(is_object($user) > 0 && isset($userData->id))
			            @if($userData->user_id == $user->id)
			                <input class="form-control" type="text" name="user_text" value="{{$user->name}}" readonly="true">
			                <input class="form-control" type="hidden" name="user" value="{{$userData->user_id}}">
			            @endif
    			   	@else
				      	<select id="user" class="form-control" name="user" required title="user">
				    		<option value="">Select User</option>
					       		@foreach($users as $user)
					                <option value="{{$user->id}}">{{$user->name}}</option>
					          	@endforeach
					        @if($errors->has('user')) <p class="help-block">{{ $errors->first('user') }}</p> @endif
				    	</select>
			        @endif
			    </div>
		    </div>
		    <div class="form-group row ">
		    	<label class="col-sm-2 col-form-label">Experiance:</label>
			    <div class="col-sm-3">
			      	<select id="year" class="form-control" name="year" required title="year">
			    		<option value="">Select Year</option>
				       		@foreach($years as $year)
				       			@if( count($expArr) > 0 && (int)$expArr[0] == $year)
				                	<option value="{{$year}}" selected>{{$year}}</option>
				                @else
				                	<option value="{{$year}}">{{$year}}</option>
				                @endif
				          	@endforeach
			    	</select>
			    </div>
			    <div class="col-sm-3">
			    	<select id="month" class="form-control" name="month" required title="month">
			    		<option value="">Select Month</option>
				       		@foreach($months as $month)
				       			@if( count($expArr) > 0 && (int)$expArr[1] == $month)
				                	<option value="{{$month}}" selected>{{$month}}</option>
				                @else
				                	<option value="{{$month}}">{{$month}}</option>
				                @endif
				          	@endforeach
			    	</select>
			    </div>
		    </div>
		    <div class="form-group row ">
		    	<label class="col-sm-2 col-form-label">Company:</label>
			    <div class="col-sm-3">
			      	<input type="text" class="form-control" name="company" placeholder="company" value="{{($userData->company)?$userData->company:NULL}}" required>
			    </div>
		    </div>
		    <div class="form-group row ">
		    	<label class="col-sm-2 col-form-label">Education:</label>
			    <div class="col-sm-3">
			      	<input type="text" class="form-control" name="education" placeholder="education" value="{{($userData->education)?$userData->education:NULL}}" required>
			    </div>
		    </div>
		    <div class="form-group row ">
		    	<label class="col-sm-2 col-form-label">Skills:</label>
			    <div class="col-sm-10">
			    	@if(is_object($skills) && false == $skills->isEmpty())
			    		@foreach($skills as $skill)
			    			@if(in_array($skill->id,$skillArr))
			      				<input type="checkbox" name="skills[]" value="{{$skill->id}}" checked="checked">{{$skill->name}} &nbsp;
			      			@else
			      				<input type="checkbox" name="skills[]" value="{{$skill->id}}">{{$skill->name}} &nbsp;
			      			@endif
			      		@endforeach
			      	@endif
			    </div>
		    </div>
		    <div class="form-group row ">
		    	<label class="col-sm-2 col-form-label">Facebook:</label>
			    <div class="col-sm-3">
			      	<input type="text" class="form-control" name="facebook" placeholder="facebook" value="{{($userData->facebook)?$userData->facebook:NULL}}">
			    </div>
		    </div>
		    <div class="form-group row ">
		    	<label class="col-sm-2 col-form-label">Twitter:</label>
			    <div class="col-sm-3">
			      	<input type="text" class="form-control" name="twitter" placeholder="twitter" value="{{($userData->twitter)?$userData->twitter:NULL}}">
			    </div>
		    </div>
		    <div class="form-group row ">
		    	<label class="col-sm-2 col-form-label">Skype:</label>
			    <div class="col-sm-3">
			      	<input type="text" class="form-control" name="skype" placeholder="skype" value="{{($userData->skype)?$userData->skype:NULL}}">
			    </div>
		    </div>
		    <div class="form-group row ">
		    	<label class="col-sm-2 col-form-label">Google:</label>
			    <div class="col-sm-3">
			      	<input type="text" class="form-control" name="google" placeholder="google" value="{{($userData->google)?$userData->google:NULL}}">
			    </div>
		    </div>
		    <div class="form-group row ">
		    	<label class="col-sm-2 col-form-label">Interview:</label>
			    <div class="col-sm-3">
			      	<input type="text" class="form-control" name="youtube" placeholder="youtube" value="{{($userData->youtube)?$userData->youtube:NULL}}">
			    </div>
		    </div>
		    <div class="form-group row ">
		    	<label class="col-sm-2 col-form-label">Resume:</label>
			    <div class="col-sm-3">
			      	<input type="file" class="form-control" name="resume" placeholder="resume">
			    </div>
			    @if(isset($userData->resume))
		          <b><span>Existing Image: {!! basename($userData->resume) !!}</span></b>
		        @endif
		    </div>
		    <div class="form-group row">
		      	<div class="offset-sm-2 col-sm-3" title="Submit">
		        	<button type="submit" class="btn btn-primary" >Submit</button>
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
	              url: "{{url('admin/getSubCategories')}}",
	              data: {id:id}
	          })
	          .done(function( msg ) {
	            select = document.getElementById('subcategory');
	            select.innerHTML = '';
	            var opt = document.createElement('option');
	            opt.value = '';
	            opt.innerHTML = 'Select Sub category';
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
	        });
    	}
  	}

	function selectPaper(ele){
    	subjectId = parseInt($(ele).val());
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
          	});
    	}
  	}

</script>
@stop