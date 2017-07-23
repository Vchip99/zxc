@extends('layouts.master')
@section('header-css')
	@include('layouts.questions-js-css')
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
			   			<a class="btn btn-default" id="tech" style="width:100px;">Technical</a>
			   			<input type="hidden" id="show-tech" name="show-tech" value="1" />
			   		@else
			   			<input type="hidden" id="show-tech" name="show-tech" value="0" />
			   		@endif
			   		@if(isset($results['questions']) && isset($results['questions'][1]) && count($results['questions'][1]) > 0)
			   			<a class="btn btn-primary" id="apt" style="width:100px;">Aptitude</a>
			   			<input type="hidden" id="show-apt" name="show-apt" value="1" />
			   		@else
			   			<input type="hidden" id="show-apt" name="show-apt" value="0" />
			   		@endif
				</div>
			    <div class="panel-heading" style="background:#ADD8E6" align="right">
			        <a class= "btn btn-success" style="width:160px;" target="popup" onclick="window.open('http://web2.0calc.com/widgets/horizontal/?options=%7B%22angular%22%3A%22deg%22%2C%22options%22%3A%22hide%22%2C%22menu%22%3A%22show%22%7D','name','width=600,height=400')">Scientific Calculator</a>&emsp;
			        <a class="btn btn-sq-sm btn-primary" role="button" data-toggle="modal" data-target="#useful_data">Useful Data
					</a>&emsp;
					<button type="button" style="width:200px;"><b>Timer : <span id='timer'></span></b></button >&emsp;&emsp;
				</div>
				<div id="tech" class="hide">
					@if(isset($results['questions']) && isset($results['questions'][0]) && count($results['questions'][0]) > 0)
					@foreach($results['questions'][0] as $index => $result)
						@if( $index == 0)
							<div class="cont" id="question_{{$result->id}}">
								<div align="right" style="background-color: yellow">
									<span>Marks for correct answer: {{$result->positive_marks}} |
									Negative Marks: <span style="color: red">{{$result->negative_marks}}</span></span>&emsp;&emsp;&emsp;
								</div>
						@else
			  				<div class="cont hide" id="question_{{$result->id}}">
			  					<div align="right" style="background-color: yellow">
									<span>Marks for correct answer: {{$result->positive_marks}} |
									Negative Marks: <span style="color: red">{{$result->negative_marks}}</span></span>&emsp;&emsp;&emsp;
								</div>
			  			@endif
						<div class="bg-warning" style="height:400px" >
					        <div class="panel-body"  >
								<div id="question{{$result->id}}">
									<p class="questions" id="qname{{$result->id}}">
										<span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
										{!! $result->name !!}
									</p>
									@if( 1 == $result->question_type )
										<div class="row">A.<input type="radio" value="1" class="radio1_{{$result->id}}" id="radio1_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer1 !!}
										</div>
										<div class="row">B.<input type="radio" value="2" class="radio1_{{$result->id}}" id="radio2_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer2 !!}
										</div>
										<div class="row">C.<input type="radio" value="3" class="radio1_{{$result->id}}" id="radio3_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer3 !!}
										</div>
										<div class="row">D.<input type="radio" value="4" class="radio1_{{$result->id}}" id="radio4_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer4 !!}
										</div>
										@if(isset( $result->answer5 ) && !empty( $result->answer5 ))
											<input type="radio" value="5" class="radio1_{{$result->id}}" id="radio5_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer5 !!}
										@endif
										@if(isset( $result->answer6 ) && !empty( $result->answer6 ))
											<input type="radio" value="6" class="radio1_{{$result->id}}" id="radio6_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer6 !!}
										@endif
										<input type="radio" checked="checked" style="display:none" value="unsolved" id="radio7_{{$result->id}}" name="{{$result->id}}"/>
									@else
										<input type="number" class="form-control" id="numpad_{{$result->id}}" data-id="{{$result->id}}" name="{{$result->id}}" placeholder="Enter a number">
									@endif
								</div>
								<br/>
							</div>
						</div>
						<div style="background:#ADD8E6" align ="right">
							<br/>
			                <button id="{{$result->id}}" data-prev_ques="{{isset($results['questions'][0][$index-1])?$results['questions'][0][$index-1]->id:0}}" class="prev btn" style="width:84px;"" type="button">Previous</button>
							<button id="{{$result->id}}" data-next_ques="{{isset($results['questions'][0][$index+1])?$results['questions'][0][$index+1]->id:0}}" class="next btn btn-success" style="width:58px;" type="button">Next</button>&emsp;
			                <button id="{{$result->id}}" class="clear btn btn-success" style="width:125px;" type="button">clear response</button>&emsp;
			                <button id="{{$result->id}}" data-next_ques="{{isset($results['questions'][0][$index+1])?$results['questions'][0][$index+1]->id:0}}" class="mark btn btn-success" style="width:206px;" type="button">Mark for Review and Next</button>
						</div>
						</div>
					@endforeach
					@endif
				</div>
				<div id="apt" class="hide">
					@if(isset($results['questions']) && isset($results['questions'][1]) && count($results['questions'][1]) > 0)
					@foreach($results['questions'][1] as $index => $result)
						@if( $index == 0)
							<div class="cont" id="question_{{$result->id}}">
								<div align="right" style="background-color: yellow">
									<span>Marks for correct answer: {{$result->positive_marks}} |
									Negative Marks: <span style="color: red">{{$result->negative_marks}}</span></span>&emsp;&emsp;&emsp;
								</div>
						@else
			  				<div class="cont hide" id="question_{{$result->id}}">
			  					<div align="right" style="background-color: yellow">
									<span>Marks for correct answer: {{$result->positive_marks}} |
									Negative Marks: <span style="color: red">{{$result->negative_marks}}</span></span>&emsp;&emsp;&emsp;
								</div>
			  			@endif
						<div class="bg-warning" style="height:400px" >
					        <div class="panel-body"  >
								<div id="question{{$result->id}}">
									<p class="questions" id="qname{{$result->id}}">
										<span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
										{!! $result->name !!}
									</p>
									@if( 1 == $result->question_type )
										<div class="row">A.<input type="radio" value="1" class="radio1_{{$result->id}}" id="radio1_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer1 !!}
										</div>
										<div class="row">B.<input type="radio" value="2" class="radio1_{{$result->id}}" id="radio2_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer2 !!}
										</div>
										<div class="row">C.<input type="radio" value="3" class="radio1_{{$result->id}}" id="radio3_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer3 !!}
										</div>
										<div class="row">D.<input type="radio" value="4" class="radio1_{{$result->id}}" id="radio4_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer4 !!}
										</div>
										@if(isset( $result->answer5 ) && !empty( $result->answer5 ))
											<input type="radio" value="5" class="radio1_{{$result->id}}" id="radio5_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer5 !!}
										@endif
										@if(isset( $result->answer6 ) && !empty( $result->answer6 ))
											<input type="radio" value="6" class="radio1_{{$result->id}}" id="radio6_{{$result->id}}" name="{{$result->id}}" />
											{!! $result->answer6 !!}
										@endif
										<input type="radio" checked='checked' style='display:none' value="unsolved" id="radio7_{{$result->id}}" name="{{$result->id}}"/>
									@else
											<input type="number" class="form-control" id="numpad_{{$result->id}}" data-id="{{$result->id}}" name="{{$result->id}}" placeholder="Enter a number">
									@endif
								</div>
								<br/>
							</div>
						</div>
						<div style="background:#ADD8E6" align ="right">
							<br/>
							<button id="{{$result->id}}" data-prev_ques="{{isset($results['questions'][1][$index-1])?$results['questions'][1][$index-1]->id:0}}" class="prev btn" style="width:84px;" type="button">Previous</button>
							<button id="{{$result->id}}" data-next_ques="{{isset($results['questions'][1][$index+1])?$results['questions'][1][$index+1]->id:0}}" class="next btn btn-success" style="width:58px;" type="button">Next</button>&emsp;
			                <button id="{{$result->id}}" class="clear btn btn-success" style="width:125px;" type="button">clear response</button>&emsp;
			                <button id="{{$result->id}}" data-next_ques="{{isset($results['questions'][1][$index+1])?$results['questions'][1][$index+1]->id:0}}" class="mark btn btn-success" style="width:206px;" type="button">Mark for Review and Next</button>
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
									 		<button type="button" id ="id_{{$q->id}}" data-type="apt" class="button1 btn btn-sq-xs btn-info" value="{{$q->id}}"  title="{{$q->id}}">{{$index+1}}</button>
								      	@endforeach
								      	@endif
								    </div>
									</td>
								</div>
					  		</div>
					  	</tr>
					  	<tr id="tech_palette" class="cont hide">
					  		<div class="row">
	                   			<div class="col-lg-12">
					  				<td height="200px"   overflow = "scroll" >
								  	<div class="bg-warning" style="height:300px" >
								      	<p id = "id1"></p>
								      	@if(isset($results['questions']) && isset($results['questions'][0]) && count($results['questions'][0]) > 0)
										@foreach($results['questions'][0] as $index => $q)
									 		<button type="button" id ="id_{{$q->id}}" data-type="tech" class="button1 btn btn-sq-xs btn-info" value="{{$q->id}}"  title="{{$q->id}}">{{$index+1}}</button>
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
									<a class="btn btn-sq-sm btn-primary" role="button" data-toggle="modal" data-target="#dynamic-modal">Instruction
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
    <div class="modal modal-wide fade" id="user-profile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
      <div class="modal-dialog">
        <div class="modal-content">
        	<div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal">Close</button>
	        </div>
          	<div class="modal-body">
				<input type="hidden" name="mode"  id="mode" value=""/>
				<img src="{{ asset('images/avatar.png') }}" width="110" />
				<br/>
				<span><b>Name :</b> </span>{{Auth::guard('clientuser')->user()->name}}
				<br/>
				<span><b>Email :</b> </span>{{Auth::guard('clientuser')->user()->email}}
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
<style type="text/css">
    @media screen and (min-height: 768px) {
        .modal-dialog {
          height: 80%; /* New width for default modal */
          width: 80%;
        }
    }
</style>

<script type="text/javascript">
	$('.load-ajax-modal').click(function(){

	    $.ajax({
	        type : 'POST',
	        url : "{{ url('getQuestions')}}",

	        success: function(result) {
	            $('#questions div.modal-body').html(result);
	        }
	    });
	});

	function submitForm(){
		if(confirm('Are you sure you want to submit answers?')){
			var form = document.getElementById('quiz_form');
			form.submit();
		}
	}
	$( document ).ready(function() {
		var showTechSection = document.getElementById('show-tech').value;
		var showAptSection = document.getElementById('show-apt').value;

		if( 1 == showTechSection && 1 == showAptSection ){
			$('div#tech').removeClass('hide');
			techSection();
		} else if(1 == showTechSection && 0 == showAptSection){
			$('div#tech').removeClass('hide');
			techSection();
		} else if(0 == showTechSection && 1 == showAptSection){
			$('div#apt').removeClass('hide');
			aptSection();
		}

		$(document).on('click', '#tech', techSection);

		function techSection(){
			$('#tech').removeClass('btn-default').addClass('btn-primary');
			$('#apt').removeClass('btn-primary').addClass('btn-default');
			$('tr#tech_palette').removeClass('hide');
			$('tr#apt_palette').addClass('hide');
			$('div#tech').removeClass('hide');
			$('div#apt').addClass('hide');
		}

		$(document).on('click', '#apt', aptSection);

		function aptSection(){
			$('#apt').removeClass('btn-default').addClass('btn-primary');
			$('#tech').removeClass('btn-primary').addClass('btn-default');
			$('tr#tech_palette').addClass('hide');
			$('tr#apt_palette').removeClass('hide');
			$('div#apt').removeClass('hide');
			$('div#tech').addClass('hide');
		}
		// next question
		$(document).on("click",".next",function(){
		    last=parseInt($(this).attr('id'));
		    nex = parseInt($(this).data('next_ques'));
		    if( nex > 0){
			    if( 'question_'+nex == $('div#apt > div:last').attr('id')){
			    	$('button#'+nex+'.next').prop('disabled', true);
			    	$('button#'+nex+'.mark').prop('disabled', true);
			    }
			    if( 'question_'+nex == $('div#tech > div:last').attr('id')){
			    	$('button#'+nex+'.next').prop('disabled', true);
			    	$('button#'+nex+'.mark').prop('disabled', true);
			    }

			    if( null == document.getElementById('radio1_'+last)) {
		    		if( $('#numpad_'+last).val()){
		    			$('#id_'+last).css('background', 'green');
		    		} else {
						$('#id_'+last).css('background', 'red');
		    		}
			    } else {
					if($('#radio1_'+last).prop("checked") || $('#radio2_'+last).prop("checked") || $('#radio3_'+last).prop("checked") || $('#radio4_'+last).prop("checked") || $('#radio5_'+last).prop("checked") || $('#radio6_'+last).prop("checked")){
					$('#id_'+last).css('background', 'green');
					}
				    else{
						$('#id_'+last).css('background', 'red');
					}
				}

			    $('#question_'+last).addClass('hide');
			    $('#question_'+nex).removeClass('hide');
			} else {
				$('button#'+last+'.next').prop('disabled', true);
				$('button#'+last+'.prev').prop('disabled', true);
				$('#id_'+last).css('background', 'red');
			}

		});
		$('div#apt > div:first button:first').prop('disabled', true);
		$('div#tech > div:first button:first').prop('disabled', true);


		// previous question
		$(document).on("click",".prev",function(){
		    last=parseInt($(this).attr('id'));
		    nex = parseInt($(this).data('prev_ques'));
			if($('#radio1_'+last).prop("checked") || $('#radio2_'+last).prop("checked") || $('#radio3_'+last).prop("checked") || $('#radio4_'+last).prop("checked") || $('#radio5_'+last).prop("checked") || $('#radio6_'+last).prop("checked")){
			$('#id_'+last).css('background', 'green');
			}
		    else{
				$('#id_'+last).css('background', 'red');
			}
		    $('#question_'+last).addClass('hide');
		    $('#question_'+nex).removeClass('hide');
		});

		// mark question
		$(document).on('click','.mark',function(){
		    last=parseInt($(this).attr('id'));
		    nex = parseInt($(this).data('next_ques'));
		    if( nex > 0){
			    // if( null == document.getElementById('question_'+nex)){
			    // 	nex = 1;
			    // }
			    if( 'question_'+nex == $('div#apt > div:last').attr('id')){
			    	$('button#'+nex+'.mark').prop('disabled', true);
			    	$('button#'+nex+'.next').prop('disabled', true);
			    }
			    if( 'question_'+nex == $('div#tech > div:last').attr('id')){
			    	$('button#'+nex+'.mark').prop('disabled', true);
			    	$('button#'+nex+'.next').prop('disabled', true);
			    }

				if($('#radio1_'+last).prop("checked") || $('#radio2_'+last).prop("checked") || $('#radio3_'+last).prop("checked") || $('#radio4_'+last).prop("checked") || $('#radio5_'+last).prop("checked") || $('#radio6_'+last).prop("checked")){
				$('#id_'+last).css('background', '#8A2BE2');
				}
			    else{
					$('#id_'+last).css('background', '#8A2BE2');
				}

				$('#question_'+last).addClass('hide');
			    $('#question_'+nex).removeClass('hide');
			} else {
				$('button#'+last+'.mark').prop('disabled', true);
				$('#id_'+last).css('background', '#8A2BE2');
			}
		});

		// clear result
		$(document).on('click','.clear',function(){
		    last=parseInt($(this).attr('id'));
			$('#radio1_'+last).prop('checked', false);
			$('#radio2_'+last).prop('checked', false);
			$('#radio3_'+last).prop('checked', false);
			$('#radio4_'+last).prop('checked', false);
			$('#radio5_'+last).prop('checked', false);
			$('#radio6_'+last).prop('checked', false);
			});

		$(document).on('click','.button1',function(){
			if( typeof(nex)  == "undefined"){
				if( false == $('div#apt').hasClass('hide') ){
					nex = $('tr#apt_palette > td > div > button:first').val();
				} else {
					nex = $('tr#tech_palette > td > div > button:first').val();
				}
			}

		  	// if( typeof(nex)  !== "undefined"){
				last=nex;
			    nex=parseInt($(this).attr('value'));

				if($('#radio1_'+last).prop("checked") || $('#radio2_'+last).prop("checked") || $('#radio3_'+last).prop("checked") || $('#radio4_'+last).prop("checked") || $('#radio5_'+last).prop("checked") || $('#radio6_'+last).prop("checked")){
				$('#id_'+last).css('background', 'green');
				}
			    else{
					$('#id_'+last).css('background', 'red');
				}
				$('#question_'+last).addClass('hide');
			    $('#question_'+nex).removeClass('hide');
			    lastEle = document.getElementById('id_'+last);
			    if($(lastEle).attr('data-type') != $(this).attr('data-type')){
			    	if( 'tech' == $(this).attr('data-type')){
			    		techFirstQue = $('tr#tech_palette > td > div > button:first').val();
			    		if(techFirstQue !== $(this).val()){
			    			$('#question_'+techFirstQue).addClass('hide');
			    		}
			    		aptFirstQue = $('tr#apt_palette > td > div > button:first').val();
			    		$('#question_'+aptFirstQue).removeClass('hide');
			    	}
			    	if( 'apt' == $(this).attr('data-type')){
			    		aptFirstQue = $('tr#apt_palette > td > div > button:first').val();
			    		currentBtnVal = $(this).val();
			    		if(aptFirstQue !== currentBtnVal ){
			    			$('#question_'+aptFirstQue).addClass('hide');
			    		}
			    		techFirstQue = $('tr#tech_palette > td > div > button:first').val();
			    		$('#question_'+techFirstQue).removeClass('hide');
			    	}
			    }
			// }
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


         // Prevent accidental navigation away
		// setConfirmUnload(true);
	 //    function setConfirmUnload(on)
	 //    {
	 //        window.onbeforeunload = on ? unloadMessage : null;
	 //    }
	 //    function unloadMessage()
	 //    {
	 //        return 'Your Answered Questions are resetted zero, Please select stay on page to continue your Quiz';
	 //    }

		// $(document).on('click', 'button:submit',function(){
	 //      setConfirmUnload(false);
	 //      window.onbeforeunload =  function(event) {
	 //      event.returnValue = "Are you sure";
	 //    }

	 //    });



	});
</script>

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
		// $('#text-basic').numpad();
		// $.each($('input[id^="numpad_"]'),function(key,val){
		//   $(val).numpad();
		// });
		$('#quiz_form .form-control').numpad();

	});
</script>
@stop