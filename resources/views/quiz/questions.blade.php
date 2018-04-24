@extends('layouts.master')
@section('header-css')
	@include('layouts.questions-js-css')
	<style type="text/css">
	.top-btn-align, .bottom-btn-align{text-align: right;
	}
	@media  (min-width: 993px) {
		.hidden-lg { display: none; }
	}
	@media  (max-width: 992px) {
		.hidden-sm { display: none; }
		.top-btn-align, .bottom-btn-align{text-align: center;}
	}
	img{
		position: relative;
		outline: none;
		/*width: 100%;*/
		max-width: 100%;
		height: auto;
		/*border: 1px solid red;*/
	}
	@media  (max-width: 478px) {
		.btn-inline{ display:inline-block; vertical-align: middle;}
		.btn-sq-sm{margin-left: 20%;}
	}
	.answer{
		padding-left: 20px !important;
	}
	</style>
@stop
@section('content')
	<div class="content">
     	<div class="container">
			<div class="row">
				<form class="form-horizontal" role="form" id='quiz_form' method="post" action="{{ url('quiz-result') }}">
					{{ csrf_field() }}
			    	<div class ="col-sm-9">
						<div class="panel panel-info" >
							@if(count($results['questions']) > 0)
							    <div align="left" style="background:#ADD8E6">
							    	@foreach($results['questions'] as $index => $question)
							    		@if(isset($sections[$index]) && count($results['questions'][$index]) > 0)
								   			<a class="section btn btn-default" id="{{ $sections[$index]->name }}" data-session_id="{{ $sections[$index]->id }}" style="width:100px;">{{ $sections[$index]->name }}</a>
								   			<input type="hidden" id="show-{{ $sections[$index]->name }}" name="show-{{ $sections[$index]->name }}" value="1" />
								   		@else
								   			<input type="hidden" id="show-{{ $sections[$index]->name }}" name="show-{{ $sections[$index]->name }}" value="0" />
								   		@endif
								   		<input type="hidden" id="duration-{{ $sections[$index]->id }}" value="{{ $sections[$index]->duration }}" />
							    	@endforeach
								</div>
							    <div class="panel-heading" title="Calculator" style="background:#ADD8E6" align="right">
							    	@if(1 == $paper->show_calculator)
							        <a class= "btn btn-success" target="popup" onclick="window.open('http://web2.0calc.com/widgets/horizontal/?options=%7B%22angular%22%3A%22deg%22%2C%22options%22%3A%22hide%22%2C%22menu%22%3A%22show%22%7D','name','width=600,height=400')"><i class="fa fa-calculator hidden-lg" aria-hidden="true" ></i><div class="hidden-sm hidden-xs">Calculator</div></a>&emsp;
							        @endif
							        <a class="btn btn-primary" title="Useful Data" role="button" data-toggle="modal" data-target="#useful_data"><i class="fa fa-book hidden-lg" aria-hidden="true"></i><div class="hidden-sm">Useful Data</div></a>&emsp;
							        @foreach($sections as $index => $section)
										<button type="button" class="btn hide" title="Time" id="timer_{{$section->id}}"><i class="fa fa-clock-o hidden-lg" aria-hidden="true"></i> <b ><span class="hidden-sm">Timer:</span> <span id="timer_duration_{{$section->id}}">{{$section->duration}} </span></b></button >
										<input type="hidden" id="timer_changed_{{$section->id}}" data-section="{{$section->name}}" value="{{$section->duration}}">
									@endforeach
								</div>
								@foreach($sections as $index => $section)
									<div id="section_{{ $section->name }}" class="hide">
									@if(isset($results['questions']) && count($results['questions'][$section->id]) > 0)
									@foreach($results['questions'][$section->id] as $index => $result)
										@if( $index == 0)
											<div class='cont' id='question_{{$result->id}}' value='{{$result->id}}'>
												<div align="right" style="background-color: yellow">
													<span>Marks for correct answer: {{$result->positive_marks}} |
													Negative Marks: <span style="color: red">{{$result->negative_marks}}</span></span>&emsp;&emsp;&emsp;
												</div>
										@else
							  				<div class='cont hide' id='question_{{$result->id}}' value='{{$result->id}}'>
							  					<div align="right" style="background-color: yellow">
													<span>Marks for correct answer: {{$result->positive_marks}} |
													Negative Marks: <span style="color: red">{{$result->negative_marks}}</span></span>&emsp;&emsp;&emsp;
												</div>
							  			@endif
											<div class="bg-warning" style="height:400px" >
										        <div class="panel-body"  >
													<div id='question{{$result->id}}' >
														<p class="questions" id="qname{{$result->id}}">
															@if(!empty($result->common_data))
																<b>Common Data:</b><br/>
																<span style="padding-left: 5px;">{!! $result->common_data !!}</span><hr/>
															@endif
															<span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
															{!! $result->name !!}
														</p>
														@if( 1 == $result->question_type )
															<div class="row answer">A.<input type="radio" value="1" class="radio1 radio1_{{$result->id}}" id="radio1_{{$result->id}}" name="{{$result->id}}" />
																{!! $result->answer1 !!}
															</div>
															<div class="row answer">B.<input type="radio" value="2" class="radio1 radio1_{{$result->id}}" id="radio2_{{$result->id}}" name="{{$result->id}}" />
																{!! $result->answer2 !!}
															</div>
															<div class="row answer">C.<input type="radio" value="3" class="radio1 radio1_{{$result->id}}" id="radio3_{{$result->id}}" name="{{$result->id}}" />
																{!! $result->answer3 !!}
															</div>
															<div class="row answer">D.<input type="radio" value="4" class="radio1 radio1_{{$result->id}}" id="radio4_{{$result->id}}" name="{{$result->id}}" />
																{!! $result->answer4 !!}
															</div>
															@if(isset( $result->answer5 ) && !empty( $result->answer5 ))
																<div class="row answer">E.<input type="radio" value="5" class="radio1 radio1_{{$result->id}}" id="radio5_{{$result->id}}" name="{{$result->id}}" />
																{!! $result->answer5 !!}
																</div>
															@endif
															@if(isset( $result->answer6 ) && !empty( $result->answer6 ))
																<div class="row">F.<input type="radio" value="6" class="radio1 radio1_{{$result->id}}" id="radio6_{{$result->id}}" name="{{$result->id}}" />
																{!! $result->answer6 !!}
																</div>
															@endif
															<input type="radio" checked='checked' style='display:none' value="unsolved" id='radio7_{{$result->id}}' name='{{$result->id}}' />
														@else
															<input type="number" class="form-control numpad answer" id="numpad_{{$result->id}}" data-id="{{$result->id}}" name="{{$result->id}}" placeholder="Enter a number">
														@endif
															<p class="hide" id="timeout_{{$result->id}}"> {{$section->name}} section is time out</p>
														<br />
													</div>
												</div>
										 	</div>
											<div style="background:#ADD8E6" align ="right" class="actionButtons">
												<br />
								                <button id ="pre_{{$result->id}}"  value='{{$result->id}}' data-prev_ques="{{isset($results['questions'][$section->id][$index-1])?$results['questions'][$section->id][$index-1]->id:0}}" class='prev btn' title='Previous' type='button' ><i class='fa fa-arrow-circle-left hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Previous</div></button>
												<button id ="next_{{$result->id}}" value='{{$result->id}}' data-next_ques="{{isset($results['questions'][$section->id][$index+1])?$results['questions'][$section->id][$index+1]->id:0}}" class='next btn btn-success' title='Next' type='button'><i class='fa fa-arrow-circle-right hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Next</div></button>&emsp;
								                <button id ="clear_{{$result->id}}" value='{{$result->id}}' class='clear btn btn-success'  type='button'>clear</button>&emsp;
								                <button id ="mark_{{$result->id}}" value='{{$result->id}}' data-next_ques="{{isset($results['questions'][$section->id][$index+1])?$results['questions'][$section->id][$index+1]->id:0}}" class='mark btn btn-success' title='Mark for Review' type='button'><i class='fa fa-check-square-o hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Mark for Review</div></button>
											</div>
										</div>
									@endforeach
									@endif
									</div>
								@endforeach
							@else
								No questions are available.
							@endif
						</div>
					</div>
					<div class ="col-sm-3">
					  	<div class="panel panel-info">
			             	<div class="panel-heading">Questions Palette</div>
					   		<div class="panel-body">
					     		<table class="table" >
					     		@if(count($results['questions']) > 0)
						     		@foreach($sections as $index => $section)
								  	<tr id="{{$section->name}}_palette">
								  		<div class="row">
				                   			<div class="col-lg-12">
								  				<td height="200px"   overflow = "scroll" >
											  	<div class="bg-warning" style="height:300px" >
											      	<p id = "id1"></p>
											      	@if(isset($results['questions']) && count($results['questions'][$section->id]) > 0)
												    	@foreach($results['questions'][$section->id] as $index => $q)
													 		<button type="button" id ="id_{{$q->id}}" data-type="{{$section->name}}" class="button1 btn btn-sq-xs btn-info" value="{{$q->id}}"  title='{{$index+1}}'>{{$index+1}}</button>
												      	@endforeach
											      	@endif
											    </div>
												</td>
											</div>
								  		</div>
								  	</tr>
								  	@endforeach
				                    <tr >
									    <div class="row" >
											<div class="col-lg-12"  >
												<td>
											  		<p>
														<a data-path="{{ url('instructions1') }}" class="btn btn-sq-sm btn-primary load-ajax-modal" role="button" data-toggle="modal" data-target="#dynamic-modal">Instruction</a>
														<a class="btn btn-sq-sm btn-success" role="button" data-toggle="modal" data-target="#user-profile">Profile</a >
														<button id="btn1" type="button" class="btn btn-sq-sm btn-warning next btn btn-success" onClick="submitForm();">Submit</button >
														<a class="btn btn-sq-sm btn-danger load-ajax-modal" role="button" data-toggle="modal" data-target="#questions">Que paper</a >
											  		</p>
											  	</td>
											</div>
										</div>
								  	</tr>
							  	@else
								<tr >
								    <div class="row" >
										<div class="col-lg-12"  >
											<td>
										  		<p >
													<button id="btn1" type="button" class="btn btn-sq-sm btn-warning next btn btn-success" onclick="window.close();">Close</button >
										  		</p>
										  	</td>
										</div>
									</div>
							  	</tr>
							  	@endif
								</table>
						  	</div>
					    </div>
					</div>
				    <input type="hidden" id="category_id" name="category_id" value="{{$paper->test_category_id}}">
				    <input type="hidden" id="sub_category_id" name="sub_category_id" value="{{$paper->test_sub_category_id}}">
				    <input type="hidden" id="subject_id" name="subject_id" value="{{$paper->test_subject_id}}">
				    <input type="hidden" id="paper_id" name="paper_id" value="{{$paper->id}}">
					<input type="hidden" name="verification_code" value="{{$checkVerificationCode}}">

				</form>
			</div>
		    <input type="hidden" id="all_sections" value="">
			<input type="hidden" id="selected_section" value="">
			<input type="hidden" id="previous_section" value="">
			<input type="hidden" name="time_out_by" id="time_out_by" value="{{$paper->time_out_by}}">
			<input type="hidden" id="paper_time" value="{{$paper->time}}">
    	</div>
	   <div class="modal modal-wide fade" id="dynamic-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	      <div class="modal-dialog">
	      	<div class="modal-content  modal-lg">
	      	<div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal">Close</button>
	        </div>
	          <div class="modal-body" >
				 @include('layouts.instructions_in_questions')
	          </div>
	        </div>
	      </div>
	    </div>
	    <div class="modal modal-wide fade" id="useful_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	      <div class="modal-dialog">
	        <div class="modal-content">
	        	<div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal">Close</button>
		        </div>
	          	<div class="modal-body">
					@include('layouts.useful_data')
	          	</div>
	        </div>
	      </div>
	    </div>
	    <div class="modal modal-wide fade" id="questions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	      <div class="modal-dialog">
	        <div class="modal-content">
	        	<div class="modal-header">
		          	<button type="button" class="close" data-dismiss="modal">Close</button>
		        </div>
	          <div class="modal-body" >

	          </div>
	        </div>
	      </div>
	    </div>
	    <div class="modal modal-wide fade" id="user-profile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	      <div class="modal-dialog">
	        <div class="modal-content  modal-sm">
	        	<div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal">Close</button>
		        </div>
	          	<div class="modal-body text-center">
					<input type="hidden" name="mode"  id="mode" value=""/>
					<img src="{{ asset('images/user/user1.png') }}" style="width: 100px; height: 100px;" />
					<br/>
					<span><b>Name :</b> </span>{{Auth::user()->name}}
					<br/>
					<span><b>Email :</b> </span>{{Auth::user()->email}}
	          	</div>
	        </div>
	      </div>
	    </div>
<script type="text/javascript">
	// Set NumPad defaults for jQuery mobile.
	// These defaults will be applied to all NumPads within this document!
	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" />';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="width: 80%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	// Instantiate NumPad once the page is ready to be shown
	$(document).ready(function(){
		$('#quiz_form .form-control').numpad();
	});
</script>

<script type="text/javascript">
	// Getting all question when click on Que Paper
	$('.load-ajax-modal').click(function(){
	    var category = parseInt(document.getElementById('category_id').value);
		var subcategory = parseInt(document.getElementById('sub_category_id').value);
		var subject = parseInt(document.getElementById('subject_id').value);
		var paper = parseInt(document.getElementById('paper_id').value);
	    $.ajax({
            method: "POST",
            url: "{{url('getQuestions')}}",
            data: {paper:paper, subject:subject, category:category, subcategory:subcategory}
        })
        .done(function( msg ) {
        	if( msg ){
				$('#questions div.modal-body').html(msg);
        	}
        });
	});

	function submitForm(){
		$.confirm({
          title: 'Confirmation',
          content: 'Are you sure you want to submit answers?',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                    	document.getElementById('quiz_form').submit();
                    }
                },
                Cancle: function () {
                }
            }
      	});
	}
	//initially 1st button is red
	$( document ).ready(function() {
		var sections = document.getElementsByClassName("section");
		timeOutBy = document.getElementById('time_out_by').value;

		$.each(sections, function(idx, obj) {
	    	var id = $(obj).attr('id');
	    	var sessionId = $(obj).data('session_id');
		    if( 0 == idx){
		    	$('a#'+id).removeClass('btn-default').addClass('btn-primary');
		    	$('tr#'+id+'_palette').removeClass('hide');
		    	$('div#section_'+id).removeClass('hide');
		    	document.getElementById('all_sections').value = id;
			    $('button#timer_'+sessionId).removeClass('hide');
		    	if(0 == timeOutBy){
			    	counter = parseInt(document.getElementById('timer_duration_'+sessionId).innerText);
			    	tc = sessionTimedCount(counter,sessionId);
			    } else {
			    	var c = document.getElementById('paper_time').value;
        			paperTimedCount(c,sessionId);
			    }
		    	document.getElementById('selected_section').value = id;
		    	document.getElementById('previous_section').value = id;
		    } else {
		    	$('a#'+id).removeClass('btn-primary').addClass('btn-default');
		    	$('tr#'+id+'_palette').addClass('hide');
		    	$('div#section_'+id).addClass('hide');
		    	document.getElementById('all_sections').value = document.getElementById('all_sections').value +','+id;
		    }
		    $('tr#'+id+'_palette > td > div > button:first').css('background', 'red');
		});

		$(document).on('click', '.section', function(){
			var allSections= document.getElementById('all_sections').value;
			var selectedSection = $(this).attr('id');
			var sessionId = $(this).data('session_id');
			$('.cont').addClass('hide');
			document.getElementById('previous_section').value = document.getElementById('selected_section').value;
			document.getElementById('selected_section').value = selectedSection;
			var previousSessionId = $('#'+document.getElementById('previous_section').value).data('session_id');
			if(0 == timeOutBy){
				clearTimeout(tc);
				counter = parseInt(document.getElementById('timer_changed_'+sessionId).value);
				tc = sessionTimedCount(counter,sessionId);
			}
			$.each(allSections.split(','), function(idx, obj) {
				if(selectedSection == obj){
					showSelectedSection(obj);
					document.getElementById('selected_section').value = obj;
				} else {
					hideUnSelectedSection(obj);
				}
				$('div#'+obj+' > div:first button:first').prop('disabled', true);
			});
		});

		function showSelectedSection(sect){
			$('#'+sect).removeClass('btn-default').addClass('btn-primary');
			$('tr#'+sect+'_palette').removeClass('hide');
			$('div#section_'+sect).removeClass('hide');
			$('div#section_'+sect+' > div').addClass('hide');
			$('div#section_'+sect+' > div:first').removeClass('hide');
			if(0 == timeOutBy){
				var sessionId = $('#'+sect).data('session_id');
				$('button#timer_'+sessionId).removeClass('hide');
			}
		}

		function hideUnSelectedSection(sect){
			$('#'+sect).removeClass('btn-primary').addClass('btn-default');
			$('tr#'+sect+'_palette').addClass('hide');
			$('div#section_'+sect).addClass('hide');
			$('div#section_'+sect+' > div').addClass('hide');
			if(0 == timeOutBy){
				var sessionId = $('#'+sect).data('session_id');
				$('button#timer_'+sessionId).addClass('hide');
			}
		}

		//if radio button click
		$(document).on("click",".radio1",function(){
			z=parseInt($(this).attr('name'));
			if($('#radio1_'+z).prop("checked") || $('#radio2_'+z).prop("checked") || $('#radio3_'+z).prop("checked") || $('#radio4_'+z).prop("checked") || $('#radio5_'+z).prop("checked") || $('#radio6_'+z).prop("checked") ){
				$('#id_'+z).css('background', 'green');
			}
		});

		// next question
		$(document).on("click",".next",function(){
		    questionId=parseInt($(this).attr('value'));
		    nex = parseInt($(this).data('next_ques'));
		    if( nex == 0){
		    	var allSections= document.getElementById('all_sections').value;
				$.each(allSections.split(','), function(idx, obj) {
					if( 'question_'+questionId == $('div#section_'+obj+' > div:last').attr('id')){
				    	nex= $('div#section_'+obj+' > div:first').attr('value');
				    }
				});
			}
		    $('#question_'+questionId).addClass('hide');
		    $('#question_'+nex).removeClass('hide');

			if($('#radio1_'+questionId).prop("checked") || $('#radio2_'+questionId).prop("checked") || $('#radio3_'+questionId).prop("checked") || $('#radio4_'+questionId).prop("checked") || $('#radio5_'+questionId).prop("checked") || $('#radio6_'+questionId).prop("checked") || $('#numpad_'+questionId).val() ){
				$('#id_'+questionId).css('background', 'green');
			} else {
				$('#id_'+questionId).css('background', 'red');
			}
			if($('#radio1_'+nex).prop("checked") || $('#radio2_'+nex).prop("checked") || $('#radio3_'+nex).prop("checked") || $('#radio4_'+nex).prop("checked") || $('#radio5_'+nex).prop("checked") || $('#radio6_'+nex).prop("checked") || $('#numpad_'+nex).val() ){
				$('#id_'+nex).css('background', 'green');
			} else {
				$('#id_'+nex).css('background', 'red');
			}
		});

		// previous question
		$(document).on("click",".prev",function(){
		    questionId=parseInt($(this).attr('value'));
		    prev = parseInt($(this).data('prev_ques'));
		    if( prev == 0){
		    	var allSections= document.getElementById('all_sections').value;
				$.each(allSections.split(','), function(idx, obj) {
					if( 'question_'+questionId == $('div#section_'+obj+' > div:first').attr('id')){
				    	prev= $('div#section_'+obj+' > div:last').attr('value');
				    }
				});
			}
			$('#question_'+questionId).addClass('hide');
		    $('#question_'+prev).removeClass('hide');

			if($('#radio1_'+questionId).prop("checked") || $('#radio2_'+questionId).prop("checked") || $('#radio3_'+questionId).prop("checked") || $('#radio4_'+questionId).prop("checked") || $('#radio5_'+questionId).prop("checked") || $('#radio6_'+questionId).prop("checked") || $('#numpad_'+questionId).val() ){
				$('#id_'+questionId).css('background', 'green');
			}
		    else{
				$('#id_'+questionId).css('background', 'red');
			}
			if($('#radio1_'+prev).prop("checked") || $('#radio2_'+prev).prop("checked") || $('#radio3_'+prev).prop("checked") || $('#radio4_'+prev).prop("checked") || $('#radio5_'+prev).prop("checked") || $('#radio6_'+prev).prop("checked") || $('#numpad_'+prev).val() ){
				$('#id_'+prev).css('background', 'green');
			}
		    else{
				$('#id_'+prev).css('background', 'red');
			}
		});

		// mark question
		$(document).on('click','.mark',function(){
		    last=parseInt($(this).attr('value'));
		    nex = parseInt($(this).data('next_ques'));

		    var allSections= document.getElementById('all_sections').value;
			$.each(allSections.split(','), function(idx, obj) {
				if( 'question_'+last == $('div#section_'+obj+' > div:last').attr('id')){
			    	nex= $('div#section_'+obj+' > div:first').attr('value');
			    }
			});

			$('#question_'+last).addClass('hide');
		    $('#question_'+nex).removeClass('hide');

			if($('#radio1_'+last).prop("checked") || $('#radio2_'+last).prop("checked") || $('#radio3_'+last).prop("checked") || $('#radio4_'+last).prop("checked") || $('#radio5_'+last).prop("checked") || $('#radio6_'+last).prop("checked") || $('#numpad_'+last).val() ){
				$('#id_'+last).css('background', '#8A2BE2');
			} else {
				$('#id_'+last).css('background', '#8A2BE2');
			}

			if($('#radio1_'+nex).prop("checked") || $('#radio2_'+nex).prop("checked") || $('#radio3_'+nex).prop("checked") || $('#radio4_'+nex).prop("checked") || $('#radio5_'+nex).prop("checked") || $('#radio6_'+nex).prop("checked") || $('#numpad_'+nex).val() ){
				$('#id_'+nex).css('background', 'green');
			} else {
				$('#id_'+nex).css('background', 'red');
			}
		});

		// clear result
		$(document).on('click','.clear',function(){
		    zxc=parseInt($(this).attr('value'));
			$('#radio1_'+zxc).prop('checked', false);
			$('#radio2_'+zxc).prop('checked', false);
			$('#radio3_'+zxc).prop('checked', false);
			$('#radio4_'+zxc).prop('checked', false);
			$('#radio5_'+zxc).prop('checked', false);
			$('#radio6_'+zxc).prop('checked', false);
			$('#radio7_'+zxc).prop('checked', true);
			$('#numpad_'+zxc).val('');
			$('#id_'+zxc).css('background', 'red');
		});

		$(document).on('click','.button1',function(){
		    var questionId = parseInt($(this).attr('value'));
			var section = $(this).attr('data-type');
			$.each($('div#section_'+section+' > .cont'), function(idx, obj) {
				var divId = $(obj).attr('id');
				if('question_'+questionId == divId){
					$('div#question_'+questionId).removeClass('hide');
				} else {
					$('div#'+divId).addClass('hide');
				}
			});
			if($('#radio1_'+questionId).prop("checked") || $('#radio2_'+questionId).prop("checked") || $('#radio3_'+questionId).prop("checked") || $('#radio4_'+questionId).prop("checked") || $('#radio5_'+questionId).prop("checked") || $('#radio6_'+questionId).prop("checked") || $('#numpad_'+questionId).val()){
				$('#id_'+questionId).css('background', 'green');
			} else {
				$('#id_'+questionId).css('background', 'red');
			}
		});

		//if value put value in num pad then make button green
		$(".numpad").click(function(){
			zx=parseInt($(this).attr('name'));
			$(document).on("click",".done",function(){
				if($('#numpad_'+zx).val()){
				 $('#id_'+zx).css('background', 'green');
				}
				else{
					$('#id_'+zx).css('background', 'red');

				}
			});
		});

        function paperTimedCount(c, timerId) {
        	var hours = parseInt( c / 3600 ) % 24;
        	var minutes = parseInt( c / 60 ) % 60;
        	var seconds = c % 60;

        	var result = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds  < 10 ? "0" + seconds : seconds);

        	$('#timer_duration_'+timerId).html(result);
            if(c == 0 ){
            	// setConfirmUnload(false);
                $("#quiz_form").submit();
            }
            c = c - 1;
            t = setTimeout(function(){ paperTimedCount(c, timerId) }, 1000);
        }

        function sessionTimedCount(counter, timerId) {
        	var hours = parseInt( counter / 3600 ) % 24;
        	var minutes = parseInt( counter / 60 ) % 60;
        	var seconds = counter % 60;
        	var result = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds  < 10 ? "0" + seconds : seconds);

        	$('#timer_duration_'+timerId).html(result);
            if(counter == 0 ){
                var section = $('#timer_changed_'+timerId).data('section');
                $.each($('div#section_'+section+' > .cont > div.actionButtons > button'), function(idx, obj) {
                	$(obj).prop('disabled', true);
                });
                $.each($('div#section_'+section+' > .cont'), function(idx, obj) {
                	var questionId = $(obj).attr('value');
                	$('#question'+questionId+' > .answer').addClass('hide');
                	$('p#timeout_'+questionId).removeClass('hide');
                });
                $.each($('tr#'+section+'_palette > td > div > button'), function(idx, obj){
                	$(obj).prop('disabled', true);
                });

                clearTimeout(counter);
                var sections = document.getElementsByClassName("section");
                var totalTime = 0;
				$.each(sections, function(idx, obj) {
			    	var sessionId = $(obj).data('session_id');
			    	totalTime += parseInt(document.getElementById('timer_changed_'+sessionId).value);
			    });
			    if(totalTime == 0){
			    	$("#quiz_form").submit();
			    }
            }
            if(counter > 0 ){
		        counter = counter - 1;
		        document.getElementById('timer_changed_'+timerId).value = counter;
		        tc = setTimeout(function(){ sessionTimedCount(counter, timerId) }, 1000);
		    }
        }
    });
</script>

@stop