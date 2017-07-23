@extends('layouts.master')
@section('header-css')
	@include('layouts.home-css')
	<style type="text/css">
		.fa_icon{
		  font-size: 80px;
		   border:2px solid transform;
		display:block;
		position:relative;
		-webkit-transition:all .4s linear;
		transition:all .4s linear;
		}
		.fa_icon:hover{
		 -ms-transform:scale(1.2);
		-webkit-transform:scale(1.2);
		transform:scale(1.2);
		transition:all .4s linear;
		}
	</style>
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
				<img src="{{ asset('images/benifit-of-course.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip benifits of live course" />
			</figure>
		</div>
		<div class="vchip-background-content">
            <h2>Digital Education</h2>
		</div>
	</div>
</section>
<section class="v_container text-center">
  	<h2 class="v_h2_title">Benifits of Live Courses</h2>
    <hr class="section-dash-dark"/>
</section>
<section id="live-technology" class="v_container v_bg_grey ">
	<div class="container text-center mrgn_60_btm ">
		<div class="fa_icon slideanim"><i class="fa fa-user" aria-hidden="true"></i></div>
		<div class="">
			<h2 class="v_h2_title">PERSONALIZED LIVE TEACHING</h2>
		  <hr class="section-dash-dark "/>
		   <p>This ensures a student gets the entire attention of a dedicated teacher and learns at his/her pace. The interaction between the teacher and students is strong and two-way, which is also monitored technologically for further improvement (if needed). The student is encouraged to ask doubts/questions unlike in a crowded classroom. And the student is much comfortable in asking his/her doubts on Vedantu than anywhere else, as teachers instill questioning skills along with imparting theoretical knowledge. Enhanced engagement between student and teacher on Vedantu guarantees better understanding and recall for the student; unlike in a classroom where teacher-student engagement is difficult to measure. And of course, the student is learning from the comfort of his/her own home – Learning happens best when the student's mind is relaxed! </p>
		</div>
	</div>
</section>
<section id="betterThanRecCourse" class="v_container ">
	<div class="container text-center mrgn_60_btm">
		<div class="fa_icon slideanim"><i class="fa fa-refresh benefits-icon" aria-hidden="true"></i></div>
		<div>
		  <h2 class="v_h2_title">BETTER THAN  RECORDED LECTURES</h2>
		  <hr class="section-dash-dark"/>
		  <p>The MOOCS revolution claimed to ensure that quality education is available to all and this will solve the maladies that the education system suffers from. However, the basic problem has not been solved- that of assembly line education production. The ensuing 'One Size Fits All solution' is still completely against the very ethos of personalized education. </p>
		</div>
	</div>
</section>
<section id="saveOnTime" class="v_container v_bg_grey">
	<div class="container text-center mrgn_60_btm">
	 	<div class="fa_icon slideanim"><i class="fa fa-clock-o benefits-icon" aria-hidden="true"></i></div>
		<div>
		  <h2 class="v_h2_title">SAVE ON TIME</h2>
		  <hr class="section-dash-dark"/>
		   <p>Now every student can get world class coaching support for professional courses. He or she can completely bypass this wasteful travel time and energy, and focus only on studies from the comfort of home. Time saved is time earned for more productive relaxing activities- a hobby, relaxing with family, or that rare commodity for every student- plain rest! </p>
		</div>
	</div>
</section>
<section id="anytimeAnywhereLern" class="v_container ">
	<div class="container text-center mrgn_60_btm">
		<div class="fa_icon slideanim"><i class="fa fa-sun-o benefits-icon" aria-hidden="true"></i></div>
		<div>
		  <h2 class="v_h2_title">ANYTIME ANYWHERE LEARNING</h2>
		  <hr class="section-dash-dark"/>
		  <p>The conventional classroom method mandates that learning has to be limited to a place and constrained by time. We believe, learning should not be time bound. Nor should teaching be. Vedantu breaks the shackles of time and place, liberating learning and teaching from the limits of time and location. Now even late at night, or early in the morning, a student has the liberty to reach out for guidance and help... and teachers at Vedantu will be all excited to lend their helping hands!</p>
		</div>
	</div>
</section>
<section id="safe" class="v_container v_bg_grey">
	<div class="container text-center mrgn_60_btm">
		<div class="fa_icon slideanim"><i class="fa fa-expeditedssl benefits-icon" aria-hidden="true"></i></div>
		<div>
		  <h2 class="v_h2_title">SAFETY</h2>
		  <hr class="section-dash-dark"/>
		  <p>We are sure, by now you'll agree with us on the technological advancement that our RDX is working on to bring democratization and personalization in education. We have raised the bar even a step higher for ourselves and introduced LIVE learning on Android mobile devices. Yes, the students can now take full-fledged LIVE sessions on their mobile/tablet devices. We have now broken the shackles of time, place, and devices for learning. </p>
		</div>
	</div>
</section>
@stop
@section('footer')
@include('footer.footer')
@stop