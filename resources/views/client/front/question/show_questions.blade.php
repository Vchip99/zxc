@extends('layouts.master')
@section('content')
<style type="text/css">
input, p{display: inline;  }
input{padding:  10px !important;}
p img{margin-top: 30px;}
</style>
<div class="row">

	@if( !empty($questions[0]) && count($questions[0]) > 0)
		<a class="btn btn-primary" style="width:100px;" title="Technical">Technical</a>
		@foreach($questions[0] as $index => $question)
			<div class="panel-body">
				<div >
					<p class="questions" >
						<span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
						{!! $question->name !!}
					</p>
					<p>
					@if( 1 == $question->question_type )
						<div class="row">A.<input type="radio" value="1" class="radio1 radio1_{{$question->id}}" id="radio1_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
							{!! $question->answer1 !!}
						</div>
						<div class="row">B.<input type="radio" value="2" class="radio1 radio1_{{$question->id}}" id="radio2_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
							{!! $question->answer2 !!}
						</div>
						<div class="row">C.<input type="radio" value="3" class="radio1 radio1_{{$question->id}}" id="radio3_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
							{!! $question->answer3 !!}
						</div>
						<div class="row">D.<input type="radio" value="4" class="radio1 radio1_{{$question->id}}" id="radio4_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
							{!! $question->answer4 !!}
						</div>
					@else
						<input type="number" class="form-control numpad" id="numpad_{{$question->id}}" data-id="{{$question->id}}" name="{{$question->id}}" placeholder="Enter a number" readonly="true">
					@endif
					</p>
				</div>
			</div>
		@endforeach
	@endif
	@if( !empty($questions[1]) && count($questions[1]) > 0)
		<a class="btn btn-primary" style="width:100px;" title="Aptitude">Aptitude</a>
		@foreach($questions[1] as $index => $question)
			<div class="panel-body">
				<div >
					<p class="questions" >
						<span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
						{!! $question->name !!}
					</p>
					<p>
					@if( 1 == $question->question_type )
						<div class="row">A.<input type="radio" value="1" class="radio1 radio1_{{$question->id}}" id="radio1_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
							{!! $question->answer1 !!}
						</div>
						<div class="row">B.<input type="radio" value="2" class="radio1 radio1_{{$question->id}}" id="radio2_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
							{!! $question->answer2 !!}
						</div>
						<div class="row">C.<input type="radio" value="3" class="radio1 radio1_{{$question->id}}" id="radio3_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
							{!! $question->answer3 !!}
						</div>
						<div class="row">D.<input type="radio" value="4" class="radio1 radio1_{{$question->id}}" id="radio4_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
							{!! $question->answer4 !!}
						</div>
					@else
						<input type="number" class="form-control numpad" id="numpad_{{$question->id}}" data-id="{{$question->id}}" name="{{$question->id}}" placeholder="Enter a number" readonly="true">
					@endif
					</p>
				</div>
			</div>
		@endforeach
	@endif
</div>
@stop