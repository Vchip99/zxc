@extends('layouts.master')
@section('content')
<style type="text/css">
	.watermark {
  width: 300px;
  height: 100px;
  display: block;
  position: relative;
}

.watermark::after {
  content: "";
 background:url(https://www.google.co.in/images/srpr/logo11w.png);
  opacity: 0.2;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  position: absolute;
  z-index: -1;
}
</style>
<div class="row">

	@if( isset($questions[0]) && count($questions[0]) > 0)
	<a class="btn btn-primary" style="width:100px;">Technical</a>
	@foreach($questions[0] as $index => $question)
	<div class="panel-body">
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
	<div class="panel-body">
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