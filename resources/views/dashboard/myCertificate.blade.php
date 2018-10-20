@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> My Certificate </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses</li>
      <li class="active">My Certificate </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
	<div class="container">
  		<img class="img-responsive " src="{{url('/images/certificate.jpg')}}" alt="">
  	</div>
@stop