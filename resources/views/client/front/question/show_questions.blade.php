@extends('layouts.master')
@section('content')
<style type="text/css">
input, p{display: inline;  }
input{padding:  10px !important;}
p img{margin-top: 30px;}

}
</style>
<style>
	.watermark {
	    position: absolute;
	    opacity: 0.25;
	    font-size: 100px;
	    width: 100%;
	    text-align: center;
	    z-index: 1000;
	    color: grey;
	}
	.answer{
		padding-left: 20px !important;
	}
</style>
<div class="row"  >
	@if(count($sections) > 0)
		@foreach($sections as $index => $section)
			@if( isset($questions[$section->id]) && count($questions[$section->id]) > 0)
			<a class="btn btn-primary" style="width:100px;">{{ $section->name }}</a>
				@foreach($questions[$section->id] as $index => $question)
					<div class="panel-body"><span class="watermark">{{ $clientSubdomain }}</span>
						<div >
							<p class="questions">
								@if(!empty($question->common_data))
									<b>Common Data:</b><br/>
									<span style="padding-left: 5px;">{!! $question->common_data !!}</span><hr/>
								@endif
								<span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
								{!! $question->name !!}
							</p>
							@if( 1 == $question->question_type )
								<div class="row answer">A. {!! $question->answer1 !!}
								</div>
								<div class="row answer">B. {!! $question->answer2 !!}
								</div>
								<div class="row answer">C. {!! $question->answer3 !!}
								</div>
								<div class="row answer">D. {!! $question->answer4 !!}
								</div>
								@if(!empty($question->answer5))
								<div class="row answer">E. {!! $question->answer5 !!}
								</div>
								@endif
							@else
								<input type="number" class="form-control numpad" id="numpad_{{$question->id}}" data-id="{{$question->id}}" name="{{$question->id}}" placeholder="Enter a number" readonly="true">
							@endif
						</div>
					</div>
				@endforeach
			@endif
		@endforeach
	@endif
</div>
@stop