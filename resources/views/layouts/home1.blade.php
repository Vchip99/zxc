@extends('layouts.master')
@section('header')
	@include('layouts.home-css')
	<link href="{{asset('css/scrolling-nav.css?ver=1.0')}}" rel="stylesheet"/>
	<link href="{{asset('css/slick.css?ver=1.0')}}" rel="stylesheet"/>
	@include('header.header_menu')
    @include('header.header_info')
@stop
@section('content')
    @include('layouts.sections')
    @include('layouts.testimonials')
@stop
@section('footer')
	@include('footer.footer')
@stop