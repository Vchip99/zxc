@extends('layouts.master')
@section('header-css')
	@include('layouts.questions-js-css')
	 <style type="text/css">
	  .borderless td, .borderless th {
	    border: none !important;
	    margin-bottom: 20px;
	}
	.table-text{
	  font-weight: bolder;
	}
	table
	{
	    border-collapse:separate;
	    border-spacing:0 7px;
	}
	.borderless tr{margin-bottom:  40px !important;}
	.borderless td.table-value{
	  background-color: #ddd;
	  padding: 20px 10px;
	  text-align: center;
	  border-radius: 10px;
	  border: 1px solid red;
	}
	.btn-success{border-radius: 0px;}
	@media(max-width: 998px){.borderless{
	 padding-left: 100px;
	 padding-right: 30%;
	vertical-align: middle;}
	}
	@media(max-width: 600px){.borderless{
	 padding-left: 0px;
	 padding-right: 0%;
	vertical-align: middle;}
	}
</style>
@stop
@section('content')
     <section class="v_container v_bg_grey">
	 	<div class="container">
	   		<h2 class="v_h2_title text-center"> Your Quiz Results:</h2>
	   		<hr class="section-dash-dark"/>
	 	</div>
	 	<div class="container v-bg-gray">
	    	<div class="row col-md-5   col-md-offset-2 custyle ">
				<form class="form-horizontal" role="form" id='solution' method="post" action="{{url('solutions')}}">
				{{ csrf_field() }}
			    	<table class="table borderless">
			            <tr>
			                <td class="table-text">Right Answers:</td>
			                <td class="table-value">{{ (isset($result['right_answered']))? $result['right_answered']:''}}</td>
			            </tr>
			            <tr>
			                <td class="table-text">Wrong Answers:</td>
			                <td class="table-value">{{ (isset($result['wrong_answered']))? $result['wrong_answered']:''}}</td>
			            </tr>
			            <tr>
			                <td class="table-text">Unanswered Questions:</td>
			                <td class="table-value">{{ (isset($result['unanswered']))? $result['unanswered']:''}}</td>
			            </tr>
			             <tr>
			                <td class="table-text">Test Score:</td>
			                <td class="table-value">{{ (isset($result['marks']))? $result['marks']:''}}/{{$totalMarks}}</td>
			            </tr>
			             <tr>
			                <td class="table-text">Test Rank</td>
			                <td class="table-value">{{ $rank + 1}}/{{$totalRank}}</td>
			            </tr>
			    	</table>
			    	<div class="text-center">
						<button id="formButton" name="solution" type="submit" class="btn btn-success btn-lg">Solution</button>
						<button type="submit" class="btn btn-success btn-lg" onclick="window.close();" title="Close">Close</button>
					</div>
					<input type="hidden" id="category_id" name="category_id" value="{{$result['category_id']}}">
				    <input type="hidden" id="sub_category_id" name="sub_category_id" value="{{$result['subcat_id']}}">
				    <input type="hidden" id="subject_id" name="subject_id" value="{{$result['subject_id']}}">
				    <input type="hidden" id="paper_id" name="paper_id" value="{{$result['paper_id']}}">
				</form>
			</div>
		</div>
	</section>
<script type="text/javascript">
	var category = parseInt(document.getElementById('category_id').value);
			var subcategory = parseInt(document.getElementById('sub_category_id').value);
			var subject = parseInt(document.getElementById('subject_id').value);
			var paper = parseInt(document.getElementById('paper_id').value);
			var userId ="{{Auth::guard('clientuser')->user()->id}}";
	        window.onload = function()
		    {
		        window.opener.checkIsTestGiven(paper,subject,category,subcategory,userId);
		    }
</script>
@stop