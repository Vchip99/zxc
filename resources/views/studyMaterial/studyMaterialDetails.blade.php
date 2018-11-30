@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu – Be partner with Vchip Technology</title>
@stop
@section('header-css')
	@include('layouts.home-css')
<link id="cpswitch" href="{{ asset('css/hover.css?ver=1.0')}}" rel="stylesheet" />
<link href="{{ asset('css/v_career.css?ver=1.0')}}" rel="stylesheet"/>
<style type="text/css">
  a.list-group-item:hover{
    color: #f4645f;
  }
  h1 {
    font-size: 48px;
    font-weight: 200;
  }
</style>
@stop
@section('header-js')
	@include('layouts.home-js')
@stop
@section('content')
@include('header.study_material_menu',compact('categories','subcategories'))
<div class="container_fluid" style="padding-top: 100px; padding-bottom: 50px;">
  <div class="row">
    <div class="col-md-2">
      <div id="MainMenu">
        <div class="list-group panel">
          @if(count($subjects) > 0)
            <b> {{$subcategoryName}}</b>
            @foreach($subjects as $subjectId => $subject)
              <a href="#{{$subjectId}}" class="list-group-item" data-toggle="collapse" data-parent="#MainMenu">{{$subject}}  <i class="fa fa-caret-down"></i></a>
              @if(count($topics) > 0)
                <div class="collapse" id="{{$subjectId}}">
                  @foreach($topics[$subjectId] as $topicId => $topic)
                    <a href="{{ url('study-material')}}/{{$subcategoryId}}/{{$subject}}/{{$topicId}}" class="list-group-item" style="color: #f4645f;">{{$topic}}</a>
                  @endforeach
                </div>
              @endif
            @endforeach
          @endif
        </div>
      </div>
    </div>
    <div class="col-md-10">
      <h1 align="center">{{$topicName}}</h1>
      <hr>
      {!! $topicContent !!}
    </div>
  </div>
</div>
@stop
@section('footer')
	@include('footer.footer')
@stop