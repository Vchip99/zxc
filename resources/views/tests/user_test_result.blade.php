@extends('layouts.master')
@section('header-title')
  <title>Online Test Series for GATE, CAT, Aptitude |V-edu</title>
@stop
@section('header-css')
	@include('layouts.home-css')
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
	  background-color: #fff;
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
@section('header-js')
	@include('layouts.home-js')
@stop
@section('content')
	@include('header.header_menu')
	<section id="vchip-background" class="mrgn_60_btm">
	  <div class="vchip-background-single">
	    <div class="vchip-background-img">
	      <figure>
	        <img src="{{asset('images/exam.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip Exam" />
	      </figure>
	    </div>
	    <div class="vchip-background-content">
	      <h2>Digital Education</h2>
	    </div>
	  </div>
	</section>
	<section class="v_container v_bg_grey">
	 	<div class="container">
	   		<h2 class="v_h2_title text-center"> Your Quiz Results:</h2>
	   		<hr class="section-dash-dark"/>
	 	</div>
	 	<div class="container v-bg-gray">
	    	<div class="row col-md-5   col-md-offset-2 custyle ">
	    		<form class="form-horizontal" role="form" id='solution' method="post" action="{{url('showUserTestSolution')}}">
				{{ csrf_field() }}
			    	<table class="table borderless">
			            <tr>
			                <td class="table-text">Right Answers:</td>
			                <td class="table-value">{{$score->right_answered}}</td>
			            </tr>
			            <tr>
			                <td class="table-text">Wrong Answers:</td>
			                <td class="table-value">{{$score->wrong_answered}}</td>
			            </tr>
			            <tr>
			                <td class="table-text">Unanswered Questions:</td>
			                <td class="table-value">{{$score->unanswered}}</td>
			            </tr>
			             <tr>
			                <td class="table-text">Test Score:</td>
			                <td class="table-value">{{$score->test_score}}/{{$totalMarks}}</td>
			            </tr>
			             <tr>
			                <td class="table-text">Test Rank</td>
			                <td class="table-value">{{ $rank + 1}}/{{$totalRank}}</td>
			            </tr>
			            <input type="hidden" name="score_id" value="{{$score->id}}">
						<input type="hidden" name="subject_id" value="{{$score->subject_id}}">
						<input type="hidden" name="paper_id" value="{{$score->paper_id}}">
						<input type="hidden" name="user_id" value="{{$score->user_id}}">
			    	</table>
			    	<div class="text-center">
						<button id="formButton" name="solution" type="submit" class="btn btn-success btn-lg">Solution</button>
					</div>
				</form>
			</div>
		</div>
	</section>
@stop
@section('footer')
	@include('footer.footer')
@stop