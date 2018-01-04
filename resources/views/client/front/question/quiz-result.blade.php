@extends('layouts.master')
@section('header-css')
	@include('layouts.questions-js-css')
<style type="text/css">
/*===== Not The CSS :P =====*/
/*@import url('https://fonts.googleapis.com/css?family=Open+Sans:400,700');
*/body {
  /*background: rgb(35, 51, 64);*/
}

#wrapper{
  position: relative;
  top: 80px;
  width: 404px;
}
.center {
  left: 50%;
  -webkit-transform: translate( -50% );
  -ms-transform: translate( -50% );
  transform: translate( -50% );
}

/*===== The CSS =====*/
.progress{
  width: 200px;
  height: auto;
  background:#fff;
  border:none;
  box-shadow: none;
}
.progress .track, .progress .fill{
  fill: rgba(0, 0, 0, 0);
  stroke-width: 8;
  transform: rotate(90deg)translate(0px, -80px);
}
.progress .track{
  stroke: #eee;
}
.progress .fill {
  stroke: rgb(255, 255, 255);
  stroke-dasharray: 219.99078369140625;
  stroke-dashoffset: -219.99078369140625;
  transition: stroke-dashoffset 1s;
}

.progress.blue .fill {
  stroke: #049dff;
}
.progress.green .fill {
  stroke: #1abc9c;
}
.progress.yellow .fill {
  stroke: #fdba04;
}
.progress .value, .progress .text {
  font-family: 'Open Sans';
  /*fill: rgb(255, 255, 255);*/
  text-anchor: middle;
}
.progress .text {
  font-size: 12px;
}
.noselect {
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    cursor: default;
}
.box {
  position: relative;
  border-radius: 3px;
  background: #ffffff;
  border-top: 3px solid #d2d6de;
  margin-bottom: 20px;
  width: 100%;
  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
}
.box.box-primary {
  border-top-color: #3c8dbc;
}
.box.box-info {
  border-top-color: #00c0ef;
}
.box.box-danger {
  border-top-color: #dd4b39;
}
.box.box-warning {
  border-top-color: #f39c12;
}
.box.box-success {
  border-top-color: #00a65a;
}
.box.box-default {
  border-top-color: #d2d6de;
}
.box.collapsed-box .box-body,
.box.collapsed-box .box-footer {
  display: none;
}
.box .nav-stacked > li {
  border-bottom: 1px solid #f4f4f4;
  margin: 0;
}
.box .nav-stacked > li:last-of-type {
  border-bottom: none;
}
.box.height-control .box-body {
  max-height: 300px;
  overflow: auto;
}
.box .border-right {
  border-right: 1px solid #f4f4f4;
}
.box .border-left {
  border-left: 1px solid #f4f4f4;
}
.box.box-solid {
  border-top: 0;
}
.box.box-solid > .box-header .btn.btn-default {
  background: transparent;
}
.box.box-solid > .box-header .btn:hover,
.box.box-solid > .box-header a:hover {
  background: rgba(0, 0, 0, 0.1);
}
.box.box-solid.box-default {
  border: 1px solid #d2d6de;
}
.box.box-solid.box-default > .box-header {
  color: #444444;
  background: #d2d6de;
  background-color: #d2d6de;
}
.box.box-solid.box-default > .box-header a,
.box.box-solid.box-default > .box-header .btn {
  color: #444444;
}
.box.box-solid.box-primary {
  border: 1px solid #3c8dbc;
}
.box.box-solid.box-primary > .box-header {
  color: #ffffff;
  background: #3c8dbc;
  background-color: #3c8dbc;
}
.box.box-solid.box-primary > .box-header a,
.box.box-solid.box-primary > .box-header .btn {
  color: #ffffff;
}
.box.box-solid.box-info {
  border: 1px solid #00c0ef;
}
.box.box-solid.box-info > .box-header {
  color: #ffffff;
  background: #00c0ef;
  background-color: #00c0ef;
}
.box.box-solid.box-info > .box-header a,
.box.box-solid.box-info > .box-header .btn {
  color: #ffffff;
}
.box.box-solid.box-danger {
  border: 1px solid #dd4b39;
}
.box.box-solid.box-danger > .box-header {
  color: #ffffff;
  background: #dd4b39;
  background-color: #dd4b39;
}
.box.box-solid.box-danger > .box-header a,
.box.box-solid.box-danger > .box-header .btn {
  color: #ffffff;
}
.box.box-solid.box-warning {
  border: 1px solid #f39c12;
}
.box.box-solid.box-warning > .box-header {
  color: #ffffff;
  background: #f39c12;
  background-color: #f39c12;
}
.box.box-solid.box-warning > .box-header a,
.box.box-solid.box-warning > .box-header .btn {
  color: #ffffff;
}
.box.box-solid.box-success {
  border: 1px solid #00a65a;
}
.box.box-solid.box-success > .box-header {
  color: #ffffff;
  background: #00a65a;
  background-color: #00a65a;
}
.box.box-solid.box-success > .box-header a,
.box.box-solid.box-success > .box-header .btn {
  color: #ffffff;
}
.box.box-solid > .box-header > .box-tools .btn {
  border: 0;
  box-shadow: none;
}
.box.box-solid[class*='bg'] > .box-header {
  color: #fff;
}
.box .box-group > .box {
  margin-bottom: 5px;
}
.box .knob-label {
  text-align: center;
  color: #333;
  font-weight: 100;
  font-size: 12px;
  margin-bottom: 0.3em;
}
.box > .overlay,
.overlay-wrapper > .overlay,
.box > .loading-img,
.overlay-wrapper > .loading-img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
.box .overlay,
.overlay-wrapper .overlay {
  z-index: 50;
  background: rgba(255, 255, 255, 0.7);
  border-radius: 3px;
}
.box .overlay > .fa,
.overlay-wrapper .overlay > .fa {
  position: absolute;
  top: 50%;
  left: 50%;
  margin-left: -15px;
  margin-top: -15px;
  color: #000;
  font-size: 30px;
}
.box .overlay.dark,
.overlay-wrapper .overlay.dark {
  background: rgba(0, 0, 0, 0.5);
}
.box-header:before,
.box-body:before,
.box-footer:before,
.box-header:after,
.box-body:after,
.box-footer:after {
  content: " ";
  display: table;
}
.box-header:after,
.box-body:after,
.box-footer:after {
  clear: both;
}
.box-header {
  color: #444;
  display: block;
  padding: 10px;
  position: relative;
}
.box-header.with-border {
  border-bottom: 1px solid #f4f4f4;
}
.collapsed-box .box-header.with-border {
  border-bottom: none;
}
.box-header > .fa,
.box-header > .glyphicon,
.box-header > .ion,
.box-header .box-title {
  display: inline-block;
  font-size: 18px;
  margin: 0;
  line-height: 1;
}

.box-header .box-title {
  text-transform: uppercase;
  font-weight: bolder;
}
.box-header > .fa,
.box-header > .glyphicon,
.box-header > .ion {
  margin-right: 5px;
}
.box-header > .box-tools {
  position: absolute;
  right: 10px;
  top: 5px;
}
.box-header > .box-tools [data-toggle="tooltip"] {
  position: relative;
}
.box-header > .box-tools.pull-right .dropdown-menu {
  right: 0;
  left: auto;
}
.box-header > .box-tools .dropdown-menu > li > a {
  color: #444!important;
}
.btn-box-tool {
  padding: 5px;
  font-size: 12px;
  background: transparent;
  color: #97a0b3;
}
.open .btn-box-tool,
.btn-box-tool:hover {
  color: #606c84;
}
.btn-box-tool.btn:active {
  box-shadow: none;
}
.box-body {
  border-top-left-radius: 0;
  border-top-right-radius: 0;
  border-bottom-right-radius: 3px;
  border-bottom-left-radius: 3px;
  padding: 10px;
}
.no-header .box-body {
  border-top-right-radius: 3px;
  border-top-left-radius: 3px;
}
.box-body > .table {
  margin-bottom: 0;
}
.box-body .fc {
  margin-top: 5px;
}
.box-body .full-width-chart {
  margin: -19px;
}
.box-body.no-padding .full-width-chart {
  margin: -9px;
}
.box-body .box-pane {
  border-top-left-radius: 0;
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 3px;
}
.box-body .box-pane-right {
  border-top-left-radius: 0;
  border-top-right-radius: 0;
  border-bottom-right-radius: 3px;
  border-bottom-left-radius: 0;
}
.box-footer {
  border-top-left-radius: 0;
  border-top-right-radius: 0;
  border-bottom-right-radius: 3px;
  border-bottom-left-radius: 3px;
  border-top: 1px solid #f4f4f4;
  padding: 10px;
  background-color: #ffffff;
}

    .ans{
    line-height: 16px;
    font-size: 10px;
    width: 18px;
    height: 18px;
    position: relative;
    border-radius: 50%;
    text-align: center;
    border: 1px solid transparent;
    color: #fff;
    font-size: 100%;
    vertical-align: middle;
    display: inline-block;
  }
  .inline-block{display: inline-block !important;     margin-right: 10px!important}
    .ans-correct{background-color: #31ad4b;   }
    .ans-wrong{background-color: #e54a4a; }
    .ans-skipp{background-color: #51647a; }
  .ans-unseen{border-color: #51647a;
    background-color: #5bc0de;}
    .ans-score{background-color: #2e6da4;}
    .ans-rank{background-color:#fdba04;}
  .media-left i{
    /*font-weight: bolder;*/
    font-size: 30px;
    vertical-align: middle;
    color: #fff;
    padding: 10px;
    /*border-radius: 50%;*/
    border:1px solid #ddd;
  }
  .bg-blue{background-color:#049dff;}
  .bg-green{background-color:#1abc9c;}
  .bg-yellow{background-color:#fdba04;}
  .bg-pink{background-color:#ed687c;}
  .border-line {
        /*border-right: 1px solid #e1e8ed;*/
        border-bottom: 1px solid #e1e8ed;
  }

.name{color: #b1b6c1;}
@media(max-width: 991px){.sol-btn{margin-top: 15px;}
.prg-bar{margin-bottom: 50px;}}
.media-body{width: 00px !important;}
.progress-list li:not(:last-child) {
    border-bottom: 1px solid #e1e8ed;}
.progress-list li{   padding: 8px!important
}
  </style>
@stop
@section('content')
	<section class="v_container v_bg_grey">
	 	<div class="container">
	   		<h2 class="v_h2_title text-center"> Your Test Results:</h2>
	   		<hr class="section-dash-dark"/>
	 	</div>
		<div class="container v-bg-gray">
		    <div class="box box-solid">
		      <div class="box-header with-border">
		        <h3 class="box-title">Details</h3>
		      </div>
		      <div class="box-body text-center">
		        <div class="col-md-8">
		          <div class="inline-block">
		              <div class="inline-block ">
  		              <span class="ans ans-correct" title="Correct">
  		                  <i class="fa fa-check"></i>
  		              </span> {{ (isset($result['right_answered']))? $result['right_answered']:''}}
		              </div>
		              <div class="inline-block">
  		              <span class="ans ans-wrong" title="Incorrect">
  		                  <i class="fa fa-close"></i>
  		              </span> {{ (isset($result['wrong_answered']))? $result['wrong_answered']:''}}
		              </div>
		              <div class="inline-block">
		                  <span class="ans ans-unseen" title="Unattempt"></span> {{ (isset($result['unanswered']))? $result['unanswered']:''}}
		              </div>
		               <div class="inline-block">
		                  <span class="hidden-sm hidden-xs"><b> Score</b></span>
		                  <span class="ans ans-score hidden-md hidden-lg">
		                      <i class="fa fa-line-chart"></i>
		                  </span>
		                   {{$result['marks']}}/{{$totalMarks}}
		              </div>
		              @if(is_object(Auth::guard('clientuser')->user()))
		              	<div class="inline-block">
		                   <span class="hidden-sm hidden-xs"><b> Rank:</b></span>
		                  <span class="ans ans-rank hidden-md hidden-lg">
		                      <i class="fa fa-tachometer"></i>
		                  </span>
		                   {{ $rank + 1}}/{{$totalRank}}
		              	</div>
		              @endif
		             </div>
		          </div>
		        <div class="col-md-4 sol-btn" >
		           	@if( is_object($score) && 1 == $score->paper->show_solution)
				    	<form class="form-horizontal" role="form" id='solution' method="post" action="{{url('solutions')}}">
						{{ csrf_field() }}
						    <input type="hidden" id="category_id" name="category_id" value="{{$result['category_id']}}">
						    <input type="hidden" id="sub_category_id" name="sub_category_id" value="{{$result['subcat_id']}}">
						    <input type="hidden" id="subject_id" name="subject_id" value="{{$result['subject_id']}}">
						    <input type="hidden" id="paper_id" name="paper_id" value="{{$result['paper_id']}}">

					    	<div class="col-md-4 sol-btn" >
					     		<span><button class="btn btn-info  btn-sm" type="submit" >Solution</button></span>
					    	</div>
				    	</form>
				    @endif
				    <input type="hidden" id="category_id" name="category_id" value="{{$result['category_id']}}">
				    <input type="hidden" id="sub_category_id" name="sub_category_id" value="{{$result['subcat_id']}}">
				    <input type="hidden" id="subject_id" name="subject_id" value="{{$result['subject_id']}}">
				    <input type="hidden" id="paper_id" name="paper_id" value="{{$result['paper_id']}}">
				    	<div class="col-md-4 sol-btn" >
					     	<span><button type="submit" class="btn btn-success btn-sm" onclick="window.close();" title="Close">Close</button></span>
					    </div>
		        </div>
		      </div>
		    </div>
		    <div class="box box-solid">
		      <div class="box-header with-border">
		        <h3 class="box-title">Overall Progress Status</h3>
		      </div>
		      <div class="box-body text-center">
		         <div class="mrgn-top-10">
		            <div class="col-md-4 prg-bar">
		              <div class=" border-line">
		                <div class="media inline-block">
		                    <div class="media-left ">
		                        <i class="fa fa-signal bg-blue" aria-hidden="true"></i>
		                    </div>
		                    <div class="media-body media-middle number-text">
		                        <h3 class="media-heading mar-b0">
		                            <span class="text-gray-9 font-weight-normal ng-binding">{{$percentile}}</span>
		                            <span class="font-size-small">%</span>
		                        </h3>
		                        <p class="mar-b0 text-uppercase">percentile</p>
		                    </div>
		                </div>
		              </div>
		              <br/>
		              <svg class="progress blue noselect" data-progress="{{$percentile}}" x="0px" y="0px" viewBox="0 0 90 80">
		                <path class="track" d="M5,40a35,35 0 1,0 70,0a35,35 0 1,0 -70,0" /></path>
		                <path class="fill" d="M5,40a35,35 0 1,0 70,0a35,35 0 1,0 -70,0" /></path>
		                <text class="value" x="50%" y="55%">0%</text>
		              </svg>
		              <br/>
		                <ul class="list-unstyled progress-list">
		                	@if(is_object(Auth::guard('clientuser')->user()))
		                  	<li class="">
			                    <span class="name">Rank</span>
			                    <span class="pull-right text-ellipsis value ng-binding">{{$rank + 1}}</span>
			                </li>
			                @endif
		                  <li class="">
		                    <span class="name">Total Candidates</span>
		                    <span class="pull-right text-ellipsis value ng-binding">{{$totalRank}}</span>
		                  </li>
		                  <li class="">
		                    <span class="name">percentile</span>
		                    <span class="pull-right text-ellipsis value ng-binding">{{$percentile}}</span>
		                  </li>
		                </ul>
		            </div>
		            <div class="col-md-4 prg-bar">
		              <div class=" border-line">
		                 <div class="media inline-block">
		                      <div class="media-left ">
		                          <i class="fa fa-bullseye bg-green" aria-hidden="true"></i>
		                      </div>
		                      <div class="media-body media-middle number-text">
		                          <h3 class="media-heading mar-b0">
		                              <span class="text-gray-9 font-weight-normal ng-binding">{{ $accuracy }}</span>
		                              <span class="font-size-small">%</span>
		                          </h3>
		                          <p class="mar-b0 text-uppercase">ACCURACY</p>
		                      </div>
		                  </div>
		              </div>
		              <br/>
		              <svg class="progress green noselect" data-progress="{{ $accuracy }}" x="0px" y="0px" viewBox="0 0 90 80">
		                <path class="track" d="M5,40a35,35 0 1,0 70,0a35,35 0 1,0 -70,0" /></path>
		                <path class="fill" d="M5,40a35,35 0 1,0 70,0a35,35 0 1,0 -70,0" /></path>
		                <text class="value" x="50%" y="55%">0%</text>
		              </svg>
		              <br/>
		              <ul class="list-unstyled progress-list">
		                <li class="">
		                  <span class="name">Correct</span>
		                  <span class="pull-right text-ellipsis value ng-binding">{{ (isset($result['right_answered']))? $result['right_answered']:''}}</span>
		                </li>
		                <li class="">
		                  <span class="name">Incorrect</span>
		                  <span class="pull-right text-ellipsis value ng-binding">{{ (isset($result['wrong_answered']))? $result['wrong_answered']:''}}</span>
		                </li>
		                <li class="">
		                  <span class="name">Unattempt</span>
		                  <span class="pull-right text-ellipsis value ng-binding">{{ (isset($result['unanswered']))? $result['unanswered']:''}}</span>
		                </li>
		              </ul>
		            </div>
		          <div class="col-md-4 prg-bar">
		              <div class=" border-line">
		                <div class="media inline-block">
		                    <div class="media-left ">
		                        <i class="fa fa-percent bg-yellow" aria-hidden="true"></i>
		                    </div>
		                    <div class="media-body media-middle number-text">
		                        <h3 class="media-heading mar-b0">
		                            <span class="text-gray-9 font-weight-normal ng-binding">{{$percentage}}</span>
		                            <span class="font-size-small">%</span>
		                        </h3>
		                        <p class="mar-b0 text-uppercase">percentage</p>
		                    </div>
		                </div>
		              </div>
		              <br/>
		              <svg class="progress yellow noselect" data-progress="{{$percentage}}" x="0px" y="0px" viewBox="0 0 90 80">
		                <path class="track" d="M5,40a35,35 0 1,0 70,0a35,35 0 1,0 -70,0" /></path>
		                <path class="fill" d="M5,40a35,35 0 1,0 70,0a35,35 0 1,0 -70,0" /></path>
		                <text class="value" x="50%" y="55%">0%</text>
		              </svg>
		              <br/>
		              <ul class="list-unstyled progress-list">
		                <li class="">
                      <span class="name">Positive Marks</span>
                      <span class="pull-right text-ellipsis value ng-binding">{{$positiveMarks}}</span>
                    </li>
                    <li class="">
                      <span class="name">Negative Marks</span>
                      <span class="pull-right text-ellipsis value ng-binding">{{$negativeMarks}}</span>
                    </li>
                    <li class="">
                      <span class="name">Total Marks</span>
		                  <span class="pull-right text-ellipsis value ng-binding">{{$result['marks']}}</span>
		                </li>
		              </ul>
		            </div>
		          </div>
		       </div>
		    </div>
  	</div>
</section>
<script type="text/javascript">
	var category = parseInt(document.getElementById('category_id').value);
	var subcategory = parseInt(document.getElementById('sub_category_id').value);
	var subject = parseInt(document.getElementById('subject_id').value);
	var paper = parseInt(document.getElementById('paper_id').value);
	var userId ="{{(is_object(Auth::guard('clientuser')->user()))?Auth::guard('clientuser')->user()->id:0}}";

	var forEach = function (array, callback, scope) {
	  for (var i = 0; i < array.length; i++) {
	    callback.call(scope, i, array[i]);
	  }
	};
	window.onload = function(){
	  var max = -219.99078369140625;
	  forEach(document.querySelectorAll('.progress'), function (index, value) {
	  percent = value.getAttribute('data-progress');
	    value.querySelector('.fill').setAttribute('style', 'stroke-dashoffset: ' + ((100 - percent) / 100) * max);
	    value.querySelector('.value').innerHTML = percent + '%';
	  });
		if(userId > 0){
	    	window.opener.checkIsTestGiven(paper,subject,category,subcategory,userId);
		}
	}
</script>
@stop