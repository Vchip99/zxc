@extends('layouts.master')
@section('header-title')
<title>V-Cloud – Access Data of your Students just by click |Vchip-edu</title>
@stop
@section('header-css')
@include('layouts.home-css')
<link href="{{asset('css/solution.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('header-js')
@include('layouts.home-js')
@stop
@section('content')
@include('header.header_menu')
<section id="vchip-background" class="mrgn_60_btm">
	<div class="vchip-background-single">
		<div class="vchip-background-img">
			<figure>
				<img src="{{ asset('images/v-cloude.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip cloud"/>
			</figure>
		</div>
		<div class="vchip-background-content">
	      <h2 class="animated bounceInLeft">Digital Education</h2>
	    </div>
	</div>
</section>
<section id="" class="v_container v_bg_grey">
	<div class="container ">
		<div class="row">
			<div class="col-md-9">
				<h2 class="v_h2_title text-center">V-cloud</h2>
				<p>
					Vchip-edu provides cloud storage to which pupils can send (hand in) documents, photographs and other created work. This means that lecturer can easily view, edit, save, mark and print students’ work that they have created on V-connect platform.
					<br/>
					V-education’s V-Cloud is secure cloud storage which is used in conjunction with V-connect in class teaching.
				</p>
			</div>
			<div class="col-md-3">
				<div class="vchip-right-sidebar ">
					<h3 class="v_h3_title text-center">Solutions</h3>
					<ul class="vchip_list">
						<li title="V-education"><a href="{{ url('vEducation') }}">V-education</a></li>
				        <li title="V-connect"><a href="{{ url('vConnect') }}">V-connect</a></li>
				        <li title="V-pendrive"><a href="{{ url('vPendrive') }}">V-pendrive</a></li>
				        <li title="V-cloud"><a href="{{ url('vCloud') }}">V-cloud</a></li>
					</ul>
				</div>

			</div>
		</div>
	</section>
	<section class="v_container ">
		<div class="container">
			<div class="row">
				<div class="col-md-5">
					<h2 class="v_h2_title mrgn_50_top_btm text-center">Classwork Storage</h2>
					<p>
						V-cloud is an innovative solution for storing and managing classwork created by students. V-cloud automatically receives students work done on V-connect platform, including documents and photos, all in a secure online folder. Teachers can easily locate student-created work, and can view, edit, save, print or send it back to them, all from a simple web page.
					</p>
				</div>
				<div class="col-md-7 text-center">
				<div>
						<div id="" class="pd_img img-container">
							<img class="img-responsive img-hover" src="{{ asset('images/solution/cloud/cloud.png')}}" alt="vchip cloud"  />
						</div>
					</div>
				</div>
			</div>
			<div class="row mrgn_30_top">
				<div class="col-md-7 text-center">
				<div>
						<div id="" class="pd_img img-container">
							<img class="img-responsive img-hover " src="{{ asset('images/solution/cloud/cloud-1.png')}}" alt="vchip cloud"  />
						</div>
					</div>
				</div>
				<div class="col-md-5">
					<h2 class="v_h2_title text-center">Features of V-cloud</h2>
					<p>
						Twenty gigabytes (20 Gb) of V-cloud storage we give at first to every organization. That cloud is use to store data come from V-connect platform. As your students grow, V-cloud can expand to accommodate their needs with further storage upgrades available starting at only $ 20 per annum for 20Gb of secure storage.

					</p>
				</div>
			</div>
		</div>
	</section>
	<section id="" class="v_container v_bg_grey">
		<div class="container ">
			<div class="row">
				<h2 class="v_h2_title ">Advantages of V-cloud storage</h2>
				<ul class="custom-list-style">
					<li >Highly secure database </li>
					<li>256 Bit encryption </li>
					<li>Disaster recovery </li>
					<li> Managed back up and recovery </li>
					<li>Dedicated storage  </li>
				</ul>
			</div>
		</div>
	</section>
	@stop
	@section('footer')
	@include('footer.footer')
	@stop