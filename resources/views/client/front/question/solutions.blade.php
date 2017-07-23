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

	    	<div class ="col-sm-9">
			<div class="panel panel-info" >
				@if(isset($results['questions']))
			    <div align="left" style="background:#ADD8E6">
			   		@if(isset($results['questions']) && isset($results['questions'][0]) && count($results['questions'][0]) > 0)
			   			<a class="btn btn-default" id="tech" style="width:100px;" title="Technical">Technical</a>
			   			<input type="hidden" id="show-tech" name="show-tech" value="1" />
			   		@else
			   			<input type="hidden" id="show-tech" name="show-tech" value="0" />
			   		@endif
			   		@if(isset($results['questions']) && isset($results['questions'][1]) && count($results['questions'][1]) > 0)
			   			<a class="btn btn-primary" id="apt" style="width:100px;" title="Aptitude">Aptitude</a>
			   			<input type="hidden" id="show-apt" name="show-apt" value="1" />
			   		@else
			   			<input type="hidden" id="show-apt" name="show-apt" value="0" />
			   		@endif
				</div>
			    <div class="panel-heading" style="background:#ADD8E6" align="right">
			        <a class= "btn btn-success" target="popup" onclick="window.open('http://web2.0calc.com/widgets/horizontal/?options=%7B%22angular%22%3A%22deg%22%2C%22options%22%3A%22hide%22%2C%22menu%22%3A%22show%22%7D','name','width=600,height=400')"><i class="fa fa-calculator hidden-lg" aria-hidden="true" ></i><div class="hidden-sm hidden-xs">Calculator</div></a>&emsp;
			        <a class="btn btn-primary" title="Useful Data" role="button" data-toggle="modal" data-target="#useful_data"><i class="fa fa-book hidden-lg" aria-hidden="true"></i><div class="hidden-sm">Useful Data</div></a>&emsp;
				</div>

				<div id="tech" class="hide">
					@if(isset($results['questions']) && isset($results['questions'][0]) && count($results['questions'][0]) > 0)
					@foreach($results['questions'][0] as $index => $result)
						@if( $index == 0)
							<div class='cont' id='question_{{$result->id}}'>
						@else
			  				<div class='cont hide' id='question_{{$result->id}}'>
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
										<input type="radio" checked='checked' style='display:none' value="unsolved" id='radio7_{{$result->id}}' name='{{$result->id}}' />
									@else
										<input type="number" class="form-control numpad" id="numpad_{{$result->id}}" data-id="{{$result->id}}" name="{{$result->id}}" placeholder="Enter a number" readonly="true">
									@endif
								</div>
							</div>
						</div>
						<div style="background:#ADD8E6" align ="right">
							<button id='{{$result->id}}' data-prev_ques="{{isset($results['questions'][0][$index-1])?$results['questions'][0][$index-1]->id:0}}" class='prev btn' title='Previous' type='button' ><i class='fa fa-arrow-circle-left hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Previous</div></button>
							<button id='{{$result->id}}' data-next_ques="{{isset($results['questions'][0][$index+1])?$results['questions'][0][$index+1]->id:0}}" class='next btn btn-success' title='Next' type='button'><i class='fa fa-arrow-circle-right hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Next</div></button>&emsp;
						</div>
						<br/>
						<div class="panel-heading" style="background:#ADD8E6" >Solution</div>
					    <div class="panel-body">
						  	<br/>
							<b><h4>Correct Answer:
								@if(0 == $result->question_type)
									{{$result->min}} to {{$result->max}}
				                @else
				                	@if($result->answer==1)
										A
				                    @elseif($result->answer==2)
				                    	B
				                    @elseif($result->answer==3)
				                    	C
				                    @else
				                    	D
				                    @endif
				                @endif
		                	</h4></b><br/>
		                	<b><h4>Your Answer:
								@if(0 == $result->question_type)
									@if( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer >= $result->min && $userResults[$result->id]->user_answer <= $result->max)
										{!! $userResults[$result->id]->user_answer !!}
				                    @else
				                    	unsolved
				                    @endif
				                @else
				                	@if( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 1)
										A
				                    @elseif( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 2)
				                    	B
				                    @elseif( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 3)
				                    	C
				                    @elseif( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 4)
				                    	D
				                    @else
				                    	unsolved
				                    @endif
				                @endif
		                	</h4></b><br/>
						  <b><h4>Solution:</b><br/><br/> {!! $result->solution !!}</h4>
		                  <br/>
					    </div>
						</div>
					@endforeach
					@endif
				</div>
				<div id="apt" class="">
					@if(isset($results['questions']) && isset($results['questions'][1]) && count($results['questions'][1]) > 0)
					@foreach($results['questions'][1] as $index => $result)
						@if( $index == 0)
							<div class='cont' id='question_{{$result->id}}'>
						@else
			  				<div class='cont hide' id='question_{{$result->id}}'>
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
										<input type="radio" checked='checked' style='display:none' value="unsolved" id='radio7_{{$result->id}}' name='{{$result->id}}' />
									@else
										<input type="number" class="form-control numpad" id="numpad_{{$result->id}}" data-id="{{$result->id}}" name="{{$result->id}}" placeholder="Enter a number" readonly="true">
									@endif
								</div>
							</div>
						</div>
						<div style="background:#ADD8E6" align ="right">
							<button id='{{$result->id}}' data-prev_ques="{{isset($results['questions'][1][$index-1])?$results['questions'][1][$index-1]->id:0}}" class='prev btn' title='Previous' type='button' ><i class='fa fa-arrow-circle-left hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Previous</div></button>
							<button id='{{$result->id}}' data-next_ques="{{isset($results['questions'][1][$index+1])?$results['questions'][1][$index+1]->id:0}}" class='next btn btn-success' title='Next' type='button'><i class='fa fa-arrow-circle-right hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Next</div></button>&emsp;
						</div>
						<br/>
						<div class="panel-heading" style="background:#ADD8E6" >Solution</div>
					    <div class="panel-body">
						  	<br/>
							<b><h4>Correct Answer:
								@if(0 == $result->question_type)
									{{$result->min}} to {{$result->max}}
				                @else
				                	@if($result->answer==1)
										A
				                    @elseif($result->answer==2)
				                    	B
				                    @elseif($result->answer==3)
				                    	C
				                    @else
				                    	D
				                    @endif
				                @endif
		                	</h4></b><br/>
		                	<b><h4>Your Answer:
								@if(0 == $result->question_type)
									@if( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer >= $result->min && $userResults[$result->id]->user_answer <= $result->max)
										{!! $userResults[$result->id]->user_answer !!}
				                    @else
				                    	unsolved
				                    @endif
				                @else
				                	@if( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 1)
										A
				                    @elseif( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 2)
				                    	B
				                    @elseif( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 3)
				                    	C
				                    @elseif( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 4)
				                    	D
				                    @else
				                    	unsolved
				                    @endif
				                @endif
		                	</h4></b><br/>
						  <b><h4>Solution:</b><br/><br/> {!! $result->solution !!}</h4>
		                  <br/>
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
								 		<button type="button" id ="id_{{$q->id}}" data-type="apt" class="button1 btn btn-sq-xs btn-info" value="{{$q->id}}">{{$index+1}}</button>
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
								 		<button type="button" id ="id_{{$q->id}}" data-type="tech" class="button1 btn btn-sq-xs btn-info" value="{{$q->id}}"  title='{{$q->id}}'>{{$index+1}}</button>
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
							  <p >
								<button class="btn btn-sq-sm btn-danger load-ajax-modal" role="button" data-toggle="modal" data-target="#questions" title="Que paper">Que paper
									</button >
								<button id="btn1" type="button" class="btn btn-sq-sm btn-warning next btn btn-success" onclick="window.close();" title="Close">Close
								</button >
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
								<button id="btn1" type="button" class="btn btn-sq-sm btn-warning next btn btn-success" onclick="window.close();"  title="Close">Close
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
     </div>
	<input type="hidden" id="category_id" name="category_id" value="{{$score->category_id}}">
	    <input type="hidden" id="sub_category_id" name="sub_category_id" value="{{$score->subcat_id}}">
	    <input type="hidden" id="subject_id" name="subject_id" value="{{$score->subject_id}}">
	    <input type="hidden" id="paper_id" name="paper_id" value="{{$score->paper_id}}">
   <div class="modal fade" id="useful_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
      <div class="modal-dialog">
        <div class="modal-content model-sm">
        	<div class="modal-header">
	          	<button type="button" class="close" data-dismiss="modal">Close</button>
	        </div>
          <div class="modal-body">
			 @include('layouts.useful_data')
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="questions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog show-questions">
        <div class="modal-content model-lg">
        	<div class="modal-header">
	          	<button type="button" class="close" data-dismiss="modal">Close</button>
	        </div>
          <div class="modal-body" >

          </div>
        </div>
      </div>
    </div>

<script type="text/javascript">
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

			    $('#question_'+last).addClass('hide');
			    $('#question_'+nex).removeClass('hide');
			} else {
				$('button#'+last+'.next').prop('disabled', true);
				$('button#'+last+'.prev').prop('disabled', true);
			}

		});
		$('div#apt > div:first button:first').prop('disabled', true);
		$('div#tech > div:first button:first').prop('disabled', true);


		// previous question
		$(document).on("click",".prev",function(){
		    last=parseInt($(this).attr('id'));
		    nex = parseInt($(this).data('prev_ques'));

		    $('#question_'+last).addClass('hide');
		    $('#question_'+nex).removeClass('hide');
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
	});
</script>
@stop
