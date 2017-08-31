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
		width: 100%;
		max-width: 100%;
		height: auto;
		/*border: 1px solid red;*/
	}
	@media  (max-width: 478px) {
		.btn-inline{ display:inline-block; vertical-align: middle;}
		.btn-sq-sm{margin-left: 20%;}

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
				@if(isset($results['questions']))
				<div align="left" style="background:#ADD8E6">
			   	  @if(isset($results['questions']) && isset($results['questions'][0]) && count($results['questions'][0]) > 0)
				   		<a class="btn btn-default" id="tech1" style="width:100px;">Technical</a>
				   		<input type="hidden" id="show-tech" name="show-tech" value="1" />
			   		@else
			   			<input type="hidden" id="show-tech" name="show-tech" value="0" />
			   		@endif
			   		@if(isset($results['questions']) && isset($results['questions'][1]) && count($results['questions'][1]) > 0)
				   		<a class="btn btn-primary" id="apt1" style="width:100px;">Aptitude</a>
				   		<input type="hidden" id="show-apt" name="show-apt" value="1" />
			   		@else
			   			<input type="hidden" id="show-apt" name="show-apt" value="0" />
			   		@endif
				</div>
			    <div class="panel-heading" title="Calculator" style="background:#ADD8E6" align="right">
			        <a class= "btn btn-success" target="popup" onclick="window.open('http://web2.0calc.com/widgets/horizontal/?options=%7B%22angular%22%3A%22deg%22%2C%22options%22%3A%22hide%22%2C%22menu%22%3A%22show%22%7D','name','width=600,height=400')"><i class="fa fa-calculator hidden-lg" aria-hidden="true" ></i><div class="hidden-sm hidden-xs">Calculator</div></a>&emsp;
			        <a class="btn btn-primary" title="Useful Data" role="button" data-toggle="modal" data-target="#useful_data"><i class="fa fa-book hidden-lg" aria-hidden="true"></i><div class="hidden-sm">Useful Data</div></a>&emsp;
					<button type="button" class="btn" title="Time"><i class="fa fa-clock-o hidden-lg" aria-hidden="true"></i> <b class="hidden-sm">Timer : </b><span id='timer'></span></button >&emsp;&emsp;
				</div>

				<div id="tech" class="hide">
					@if(isset($results['questions']) && isset($results['questions'][0]) && count($results['questions'][0]) > 0)
					@foreach($results['questions'][0] as $index => $result)
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
										<span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
										{!! $result->name !!}
									</p>
									@if( 1 == $result->question_type )
										<div class="row">A.<input type="radio" value="1" class="radio1 radio1_{{$result->id}}" id="radio1_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer1 !!}
										</div>
										<div class="row">B.<input type="radio" value="2" class="radio1 radio1_{{$result->id}}" id="radio2_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer2 !!}
										</div>
										<div class="row">C.<input type="radio" value="3" class="radio1 radio1_{{$result->id}}" id="radio3_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer3 !!}
										</div>
										<div class="row">D.<input type="radio" value="4" class="radio1 radio1_{{$result->id}}" id="radio4_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer4 !!}
										</div>
										@if(isset( $result->answer5 ) && !empty( $result->answer5 ))
											<div class="row">E.<input type="radio" value="5" class="radio1 radio1_{{$result->id}}" id="radio5_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer5 !!}
										@endif
										@if(isset( $result->answer6 ) && !empty( $result->answer6 ))
											<div class="row">F.<input type="radio" value="6" class="radio1 radio1_{{$result->id}}" id="radio6_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer6 !!}
										@endif
										<input type="radio" checked='checked' style='display:none' value="unsolved" id='radio7_{{$result->id}}' name='{{$result->id}}' />
									@else
										<input type="number" class="form-control numpad" id="numpad_{{$result->id}}" data-id="{{$result->id}}" name="{{$result->id}}" placeholder="Enter a number">
									@endif
								</div>

								<br />
							</div>
						</div>
						<div style="background:#ADD8E6" align ="right">
							<br />
			                <button id ="pre_{{$result->id}}"  value='{{$result->id}}' data-prev_ques="{{isset($results['questions'][0][$index-1])?$results['questions'][0][$index-1]->id:0}}" class='prev btn' title='Previous' type='button' ><i class='fa fa-arrow-circle-left hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Previous</div></button>
							<button id ="next_{{$result->id}}" value='{{$result->id}}' data-next_ques="{{isset($results['questions'][0][$index+1])?$results['questions'][0][$index+1]->id:0}}" class='next btn btn-success' title='Next' type='button'><i class='fa fa-arrow-circle-right hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Next</div></button>&emsp;
			                <button id ="clear_{{$result->id}}" value='{{$result->id}}' class='clear btn btn-success'  type='button'>clear</button>&emsp;
			                <button id ="mark_{{$result->id}}" value='{{$result->id}}' data-next_ques="{{isset($results['questions'][0][$index+1])?$results['questions'][0][$index+1]->id:0}}" class='mark btn btn-success' title='Mark for Review' type='button'><i class='fa fa-check-square-o hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Mark for Review</div></button>
						</div>
						</div>
					@endforeach
					@endif
				</div>
				<div id="apt" class="hide">
					@if(isset($results['questions']) && isset($results['questions'][1]) && count($results['questions'][1]) > 0)
					@foreach($results['questions'][1] as $index => $result)
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
										<span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
										{!! $result->name !!}
									</p>
									@if( 1 == $result->question_type )
										<div class="row">A.<input type="radio" value="1" class="radio1 radio1_{{$result->id}}" id="radio1_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer1 !!}
										</div>
										<div class="row">B.<input type="radio" value="2" class="radio1 radio1_{{$result->id}}" id="radio2_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer2 !!}
										</div>
										<div class="row">C.<input type="radio" value="3" class="radio1 radio1_{{$result->id}}" id="radio3_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer3 !!}
										</div>
										<div class="row">D.<input type="radio" value="4" class="radio1 radio1_{{$result->id}}" id="radio4_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer4 !!}
										</div>
										@if(isset( $result->answer5 ) && !empty( $result->answer5 ))
											<div class="row">E.<input type="radio" value="5" class="radio1 radio1_{{$result->id}}" id="radio5_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer5 !!}
										@endif
										@if(isset( $result->answer6 ) && !empty( $result->answer6 ))
											<div class="row">F.<input type="radio" value="6" class="radio1 radio1_{{$result->id}}" id="radio6_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer6 !!}
										@endif
										<input type="radio" checked='checked' style='display:none' value="unsolved" id='radio7_{{$result->id}}' name='{{$result->id}}' />
									@else
										<input type="number" class="form-control numpad" id="numpad_{{$result->id}}" data-id="{{$result->id}}" name="{{$result->id}}" placeholder="Enter a number">
									@endif
								</div>
								<br />
							</div>
						</div>
						<div style="background:#ADD8E6" align ="right">
							<br/>
							<button id ="pre_{{$result->id}}" value='{{$result->id}}' data-prev_ques="{{isset($results['questions'][1][$index-1])?$results['questions'][1][$index-1]->id:0}}" class='prev btn' title='Previous' type='button'><i class='fa fa-arrow-circle-left hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Previous</div></button>
							<button id ="next_{{$result->id}}" value='{{$result->id}}' data-next_ques="{{isset($results['questions'][1][$index+1])?$results['questions'][1][$index+1]->id:0}}" class='next btn btn-success' title='Next' type='button'><i class='fa fa-arrow-circle-right hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Next</div></button>&emsp;
			                <button id ="clear_{{$result->id}}" value='{{$result->id}}' class='clear btn btn-success' type='button'>clear</button>&emsp;
			                <button id ="mark_{{$result->id}}" value='{{$result->id}}' data-next_ques="{{isset($results['questions'][1][$index+1])?$results['questions'][1][$index+1]->id:0}}" class='mark btn btn-success' type='button'><i class='fa fa-check-square-o hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Mark for Review</div></button>
						</div>
						</div>
					@endforeach
					@endif
				</div>
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
		     		@if(isset($results['questions']))
				  	<tr id="apt_palette">
				  		<div class="row">
                   			<div class="col-lg-12">
				  				<td height="200px"   overflow = "scroll" >
							  	<div class="bg-warning" style="height:300px" >
							      	<p id = "id1"></p>
							      	@if(isset($results['questions']) && isset($results['questions'][1]) && count($results['questions'][1]) > 0)
								    	@foreach($results['questions'][1] as $index => $q)
									 		<button type="button" id ="id_{{$q->id}}" data-type="apt" class="button1 btn btn-sq-xs btn-info" value="{{$q->id}}" >{{$index+1}}</button>
								      	@endforeach
							      	@endif
							    </div>
								</td>
							</div>
				  		</div>
				  	</tr>
				  	<tr id="tech_palette" class="hide">
				  		<div class="row">
                   			<div class="col-lg-12">
				  				<td height="200px"   overflow = "scroll" >
							  	<div class="bg-warning" style="height:300px" >
							      	<p id = "id2"></p>
							      	@if(isset($results['questions']) && isset($results['questions'][0]) && count($results['questions'][0]) > 0)
										@foreach($results['questions'][0] as $index => $q)
									 		<button type="button" id ="id_{{$q->id}}" data-type="tech" class="button1 btn btn-sq-xs btn-info" value="{{$q->id}}" >{{$index+1}}</button>
								      	@endforeach
							      	@endif
							    </div>
								</td>
							</div>
				  		</div>
				  	</tr>
                    <tr >
					    <div class="row" >
							<div class="col-lg-12"  >
							<td>
							  <p>
								<a data-path="{{ url('instructions1') }}" class="btn btn-sq-sm btn-primary load-ajax-modal" role="button" data-toggle="modal" data-target="#dynamic-modal">Instruction
								</a>
								<a class="btn btn-sq-sm btn-success" role="button" data-toggle="modal" data-target="#user-profile">Profile</a >
								<button id="btn1" type="button" class="btn btn-sq-sm btn-warning next btn btn-success" onClick="submitForm();">Submit
								</button >
								<a class="btn btn-sq-sm btn-danger load-ajax-modal" role="button" data-toggle="modal" data-target="#questions">Que paper
									</a >
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
								<button id="btn1" type="button" class="btn btn-sq-sm btn-warning next btn btn-success" onclick="window.close();">Close
									</button >
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
	</form>
    </div>
   <div class="modal modal-wide fade" id="dynamic-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
      	<div class="modal-content">
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
				<img src="{{ asset('images/user/user.png') }}" style="width: 100px; height: 100px;" />
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
		$('tr#tech_palette > td > div > button:first').css('background', 'red');
		$('tr#apt_palette > td > div > button:first').css('background', 'red');

		//deciding as 1st is apti session or technical session
		var showTechSection = document.getElementById('show-tech').value;
		var showAptSection = document.getElementById('show-apt').value;

		if(showTechSection == 1) {
			techSection();
		}  else if(0 == showTechSection && 1 == showAptSection){
			aptSection();
		}
	});

		$(document).on('click', '#tech1', techSection);

		function techSection(){
			$('#tech1').removeClass('btn-default').addClass('btn-primary');
			$('#apt1').removeClass('btn-primary').addClass('btn-default');
			$('tr#tech_palette').removeClass('hide');
			$('tr#apt_palette').addClass('hide');
			$('div#tech').removeClass('hide');
			$('div#apt').addClass('hide');
			$('.cont').addClass('hide');
			$('div#tech > div:first').removeClass('hide');
			nex = $('tr#tech_palette > td > div > button:first').val();
		};

		$(document).on('click', '#apt1', aptSection);

		function aptSection(){
			$('#apt1').removeClass('btn-default').addClass('btn-primary');
			$('#tech1').removeClass('btn-primary').addClass('btn-default');
			$('tr#tech_palette').addClass('hide');
			$('tr#apt_palette').removeClass('hide');
			$('div#apt').removeClass('hide');
			$('div#tech').addClass('hide');
			$('.cont').addClass('hide');
			$('div#apt > div:first').removeClass('hide');
			nex = $('tr#apt_palette > td > div > button:first').val();
		};

		//if radio button click
		$(document).on("click",".radio1",function(){
			z=parseInt($(this).attr('name'));
			if($('#radio1_'+z).prop("checked") || $('#radio2_'+z).prop("checked") || $('#radio3_'+z).prop("checked") || $('#radio4_'+z).prop("checked") || $('#radio5_'+z).prop("checked") || $('#radio6_'+z).prop("checked") ){
				$('#id_'+z).css('background', 'green');
				}
			});

		// next question
		$(document).on("click",".next",function(){
		    last=parseInt($(this).attr('value'));
		    nex = parseInt($(this).data('next_ques'));
		    if( 'question_'+last == $('div#apt > div:last').attr('id')){
		    	nex= $('div#apt > div:first').attr('value');
		    }
		    if('question_'+last == $('div#tech > div:last').attr('id')){
		    	nex= $('div#tech > div:first').attr('value');
		    }

				if($('#radio1_'+last).prop("checked") || $('#radio2_'+last).prop("checked") || $('#radio3_'+last).prop("checked") || $('#radio4_'+last).prop("checked") || $('#radio5_'+last).prop("checked") || $('#radio6_'+last).prop("checked") || $('#numpad_'+last).val() ){
				$('#id_'+last).css('background', 'green');
				}
			    else{
					$('#id_'+last).css('background', 'red');
				}

				if($('#radio1_'+nex).prop("checked") || $('#radio2_'+nex).prop("checked") || $('#radio3_'+nex).prop("checked") || $('#radio4_'+nex).prop("checked") || $('#radio5_'+nex).prop("checked") || $('#radio6_'+nex).prop("checked") || $('#numpad_'+nex).val() ){
				$('#id_'+nex).css('background', 'green');
				}
			    else{
					$('#id_'+nex).css('background', 'red');
				}

		    $('#question_'+last).addClass('hide');
		    $('#question_'+nex).removeClass('hide');

		});

		$('div#apt > div:first button:first').prop('disabled', true);
		$('div#tech > div:first button:first').prop('disabled', true);


		// previous question
		$(document).on("click",".prev",function(){
		    last=parseInt($(this).attr('value'));
		    nex = parseInt($(this).data('prev_ques'));
			if($('#radio1_'+last).prop("checked") || $('#radio2_'+last).prop("checked") || $('#radio3_'+last).prop("checked") || $('#radio4_'+last).prop("checked") || $('#radio5_'+last).prop("checked") || $('#radio6_'+last).prop("checked") || $('#numpad_'+last).val() ){
			$('#id_'+last).css('background', 'green');
			}
		    else{
				$('#id_'+last).css('background', 'red');
			}

			if($('#radio1_'+nex).prop("checked") || $('#radio2_'+nex).prop("checked") || $('#radio3_'+nex).prop("checked") || $('#radio4_'+nex).prop("checked") || $('#radio5_'+nex).prop("checked") || $('#radio6_'+nex).prop("checked") || $('#numpad_'+nex).val() ){
				$('#id_'+nex).css('background', 'green');
				}
			    else{
					$('#id_'+nex).css('background', 'red');
				}

		    $('#question_'+last).addClass('hide');
		    $('#question_'+nex).removeClass('hide');
		});

		// mark question
		$(document).on('click','.mark',function(){
		    last=parseInt($(this).attr('value'));
		    nex = parseInt($(this).data('next_ques'));

		   if( 'question_'+last == $('div#apt > div:last').attr('id')){
		    	nex= $('div#apt > div:first').attr('value');
		    }
		    if('question_'+last == $('div#tech > div:last').attr('id')){
		    	nex= $('div#tech > div:first').attr('value');
		    }

			if($('#radio1_'+last).prop("checked") || $('#radio2_'+last).prop("checked") || $('#radio3_'+last).prop("checked") || $('#radio4_'+last).prop("checked") || $('#radio5_'+last).prop("checked") || $('#radio6_'+last).prop("checked") || $('#numpad_'+last).val() ){
			$('#id_'+last).css('background', '#8A2BE2');
			}
		    else{
				$('#id_'+last).css('background', '#8A2BE2');
			}

			if($('#radio1_'+nex).prop("checked") || $('#radio2_'+nex).prop("checked") || $('#radio3_'+nex).prop("checked") || $('#radio4_'+nex).prop("checked") || $('#radio5_'+nex).prop("checked") || $('#radio6_'+nex).prop("checked") || $('#numpad_'+nex).val() ){
				$('#id_'+nex).css('background', 'green');
				}
			    else{
					$('#id_'+nex).css('background', 'red');
				}

			$('#question_'+last).addClass('hide');
		    $('#question_'+nex).removeClass('hide');
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

			if( typeof(nex)  == "undefined"){
				if( $('div#tech').hasClass('hide') ){
					nex = $('tr#apt_palette > td > div > button:first').val();
				} else {
					nex = $('tr#tech_palette > td > div > button:first').val();
				}
			}

				last=nex;
			    nex=parseInt($(this).attr('value'));

				if($('#radio1_'+last).prop("checked") || $('#radio2_'+last).prop("checked") || $('#radio3_'+last).prop("checked") || $('#radio4_'+last).prop("checked") || $('#radio5_'+last).prop("checked") || $('#radio6_'+last).prop("checked") || $('#numpad_'+last).val()){
				$('#id_'+last).css('background', 'green');
				}
			    else{
					$('#id_'+last).css('background', 'red');
				}

				if($('#radio1_'+nex).prop("checked") || $('#radio2_'+nex).prop("checked") || $('#radio3_'+nex).prop("checked") || $('#radio4_'+nex).prop("checked") || $('#radio5_'+nex).prop("checked") || $('#radio6_'+nex).prop("checked") || $('#numpad_'+nex).val() ){
				$('#id_'+nex).css('background', 'green');
				}
			    else{
					$('#id_'+nex).css('background', 'red');
				}

				$('#question_'+last).addClass('hide');
			    $('#question_'+nex).removeClass('hide');

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


		var c = {{$paper->time}};
        var t;
        timedCount();

        function timedCount() {

        	var hours = parseInt( c / 3600 ) % 24;
        	var minutes = parseInt( c / 60 ) % 60;
        	var seconds = c % 60;

        	var result = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds  < 10 ? "0" + seconds : seconds);


        	$('#timer').html(result);
            if(c == 0 ){
            	// setConfirmUnload(false);
                $("#quiz_form").submit();
            }
            c = c - 1;
            t = setTimeout(function(){ timedCount() }, 1000);
        }

</script>

@stop