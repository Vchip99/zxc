@extends('layouts.master')
@section('content')
<style>.watermark {
    position: absolute;
    opacity: 0.25;
    font-size: 50px;
    width: 100%;
    text-align: center;
    z-index: 1000;
    color: grey;
}</style>
<div class="row">

	@if( isset($questions[0]) && count($questions[0]) > 0)
	<a class="btn btn-primary" style="width:100px;">Technical</a>
	@foreach($questions[0] as $index => $question)
	<div class="panel-body"><span class="watermark">Vchip Technology</span>
		<div >
			<p class="questions" >
				<span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
				{!! $question->name !!}
			</p>
			@if( 1 == $question->question_type )
				<div class="row">A. {!! $question->answer1 !!}
				</div>
				<div class="row">B. {!! $question->answer2 !!}
				</div>
				<div class="row">C. {!! $question->answer3 !!}
				</div>
				<div class="row">D. {!! $question->answer4 !!}
				</div>
			@else
				<input type="number" class="form-control numpad" id="numpad_{{$question->id}}" data-id="{{$question->id}}" name="{{$question->id}}" placeholder="Enter a number" readonly="true">
			@endif
		</div>
	</div>
	@endforeach
	@endif
	@if( isset($questions[1]) && count($questions[1]) > 0)
	<a class="btn btn-primary" style="width:100px;">Aptitude</a>
	@foreach($questions[1] as $index => $question)
	<div class="panel-body"><span class="watermark">Vchip Technology</span>
		<div >
			<p class="questions" >
				<span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
				{!! $question->name !!}
			</p>
			<p>
			@if( 1 == $question->question_type )
				<div class="row">A. {!! $question->answer1 !!}
				</div>
				<div class="row">B. {!! $question->answer2 !!}
				</div>
				<div class="row">C. {!! $question->answer3 !!}
				</div>
				<div class="row">D. {!! $question->answer4 !!}
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