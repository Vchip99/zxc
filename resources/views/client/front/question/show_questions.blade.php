@extends('layouts.master')
@section('content')
<div class="row"  oncontextmenu="return false;">
	@if(count($sections) > 0)
		@foreach($sections as $index => $section)
			@if( isset($questions[$section->id]) && count($questions[$section->id]) > 0)
			<span class="btn btn-info" style="min-width:100px;max-width:200px;"><b>{{ $section->name }}</b></span>
				@foreach($questions[$section->id] as $index => $question)
					<div class="panel-body">
						<div >
							<p class="questions">
								@if(!empty($question->common_data))
									<b>Common Data:</b><br/>
									<span style="padding-left: 5px;">{!! preg_replace('/(<br \/>)+$/', '', $question->common_data); !!}</span><br/>
								@endif
								<br/>
								<span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
								{!! preg_replace('/(<br \/>)+$/', '', $question->name); !!}
							</p>
							<p>
							@if( 1 == $question->question_type )
								<div class="row answer">A. {!! preg_replace('/(<br \/>)+$/', '', $question->answer1); !!}
								</div>
								<div class="row answer">B. {!! preg_replace('/(<br \/>)+$/', '', $question->answer2); !!}
								</div>
								<div class="row answer">C. {!! preg_replace('/(<br \/>)+$/', '', $question->answer3); !!}
								</div>
								<div class="row answer">D. {!! preg_replace('/(<br \/>)+$/', '', $question->answer4); !!}
								</div>
								@if(!empty($question->answer5) && $optionCount > 4)
								<div class="row answer">E. {!! preg_replace('/(<br \/>)+$/', '', $question->answer5); !!}
								</div>
								@endif
							@else
								<input type="number" class="form-control numpad" id="numpad_{{$question->id}}" data-id="{{$question->id}}" name="{{$question->id}}" placeholder="Enter a number" readonly="true">
							@endif
						</p>
						</div>
					</div></br/>
				@endforeach
			@endif
		@endforeach
	@endif
</div>
@stop