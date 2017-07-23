@extends('layouts.master')
@section('header-title')
  <title>V-edu - Best Place for Enhancing your Career |Vchip Technology</title>
@stop
@section('header-css')
	@include('layouts.home-css')
	<link href="{{ asset('css/v_career.css?ver=1.0')}}" rel="stylesheet"/>
	<link href="{{ asset('css/animate.min.css?ver=1.0')}}" rel="stylesheet"/>
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
				<img src="{{ asset('images/career.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip career" />
			</figure>
		</div>
		<div class="vchip-background-content">
        <h2 class="animated bounceInLeft">Digital Education</h2>
		</div>
	</div>
</section>
<section id="v_career" class="v_container v_bg_grey">
    <div class="container">
      <div class="row">
      @if(Session::has('message'))
        <div class="alert alert-success" id="message">
          <a class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ Session::get('message') }}
        </div>
      @endif
       <div class="col-md-12">
          <div class="v_career">
          <div class="font_size">
          <h2 class="v_h2_title">WE ARE HIRING<em> tutors for!</em></h2>
            <div class="rotating_text">
              <span class="span1">
                Data Science <br />
                Quantitative Aptitude <br />
                Reasoning<br />
                Android App Development
                </span>
            </div>
           </div>
           <div class="hr"></div><br />
          <h3 class="v_h3_title"><em>Performance of a company is directly proportional to his team...</em></h3><br />
            <p> V-edu have open, engaging, fun, employee-centric work environment with competitive compensation and rewards and fast-track programs for high potential employees. We are always be searching for new generation of entrepreneur. Our team is well balanced of both newly graduate and  experienced guys. We believe that newcomers known the exact market demand and our experienced guys fulfill it. We always welcome new ideas, technologically and ready to change. </p>

            <p>Our main motive is bridging between educational organization  and Industry along with Digital village. So our most of tutors are belong to industries.</p>
          </div>
        </div>
      </div>
      </div>
      </section>
      <section class="v_container">
      <div class="container">
      <div class="row">
            <div class="col-md-12" id="vchip_career_info">
              <div class="vchip_career_info-area">
               <div class="row">
                <div class="col-lg-6 col-md-6">
                <div class="vchip_career_info-left">
                  <div class="text-center">
                   <h2 class="v_h2_title">Features of V-edu</h2>
                  </div>
                  <ul class="vchip_list">
                    <li>Dynamic, healthy and happy Working Environment</li>
                    <li>Work on innovative and challenging topics</li>
                    <li>Innovative thinking inspires you to dream beyond boundaries</li>
                    <li>Involved in decision making , give filling of ownership</li>
                    <li>Your ideas and suggestions are always welcome</li>
                    <li>Great working culture</li>
                    <li>Make career while contributing learning and education</li>
                  </ul>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 animated bounceInRight" data-animate-effect="fadeInRight">
                <div class="vchip_career_info-right">
                <a id="mu-abtus-video" target="mutube-video">
                  <img src="{{ asset('images/career-1.jpg') }}" alt="vchip information">
                </a>
                </div>
              </div>
            </div>
          </div>
          </div>
      </div>
</div>
</section>
<section class="v_container v_bg_grey">
<div class="container ">
<div class="text-center">
  <h2 class="v_h2_title">Join us</h2>
  <hr class="section-dash-dark"/>
</div>

<div class="row " id="v_car">
<!-- col-8 aboutus -->
<div class="col-md-12">
<button class="accordion slideanim" title="DATA SCIENCE">DATA SCIENCE</button>
<div class="panel">
    <div class="panel-content">
        <p class="qualification">
              <strong>Qualification:</strong>Qualification BE/B.Tech(CS,IT,EXTC)
        </p>
        <p class="industryType">
              <strong>Industry Type:</strong>Education
              <strong>Functional Area:  </strong> Tutor
        </p>
        <p>• Minimum 5 year of industrial experience in the field of Data Analysis.</p>
        <p>• Having Exposure in Basic python, Python Data Analysis Library — pandas, SQL Server.</p>
        <p>• Good communication skills.</p>
        <p>• To participate in interactive discussion.</p>
        <p class="location"><strong>Location: </strong>Pune </p>
        <p><a class="btn btn-primary push-bottom" data-toggle="modal" data-target="#myModal" title="Apply Now">Apply Now</a>
        </p>

     </div>

</div>

<button class="accordion slideanim" title="QUANTITATIVE APTITUDE">QUANTITATIVE APTITUDE</button>
<div class="panel">
    <div class="panel-content">
        <p class="qualification">
              <strong>Qualification:</strong>Qualification MBA(From IIM)
        </p>
        <p class="industryType">
              <strong>Industry Type:</strong>Education
              <strong>Functional Area:  </strong> Tutor
        </p>
        <p>• Minimum 2 year of industrial experience in the field of Analysis </p>
        <p>• Having very good AIR in CAT exam.</p>
        <p>• Good communication skills.</p>
        <p>• To participate in interactive discussion.</p>
        <p class="location"><strong>Location: </strong>Pune </p>
        <p><a class="btn btn-primary push-bottom" data-toggle="modal" data-target="#myModal" title="Apply Now">Apply Now</a>
        </p>
     </div>

</div>
<button class="accordion slideanim" title="REASONING">REASONING</button>
<div class="panel">
    <div class="panel-content">
        <p class="qualification">
              <strong>Qualification:</strong>Qualification MBA(From IIM)
        </p>
        <p class="industryType">
              <strong>Industry Type:</strong>Education
              <strong>Functional Area:  </strong> Tutor
        </p>
        <p>• Minimum 2 year of industrial experience in the field of Analysis </p>
        <p>• Having very good AIR in CAT exam.</p>
        <p>• Good communication skills.</p>
        <p>• To participate in interactive discussion.</p>
        <p class="location"><strong>Location: </strong>Pune </p>
        <p><a class="btn btn-primary push-bottom" data-toggle="modal" data-target="#myModal" title="Apply Now">Apply Now</a>
        </p>
     </div>

</div>
<button class="accordion slideanim" title="ANDROID APP DEVELOPMENT">ANDROID APP DEVELOPMENT</button>
<div class="panel">
    <div class="panel-content">
        <p class="qualification">
              <strong>Qualification:</strong>Qualification BE/B.Tech(CS,IT,EXTC)
        </p>
        <p class="industryType">
              <strong>Industry Type:</strong>Education
              <strong>Functional Area:  </strong> Tutor
        </p>
        <p>• Minimum 5 year of industrial experience in the field of Android app development </p>
        <p>• Good communication skills.</p>
        <p>• To participate in interactive discussion.</p>
        <p class="location"><strong>Location: </strong>Pune </p>
        <p><a class="btn btn-primary push-bottom" data-toggle="modal" data-target="#myModal" title="Apply Now">Apply Now</a>
        </p>
     </div>

</div>
         <!--Application  Form  -->
        <div class="modal fade" id="myModal" role="dialog">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="v_h2_title text-center ">Application Form</h2>
              </div>
              <div class="modal-body">
               <form class="form-horizontal" method="post" action="{{ url('sendMail')}}" enctype="multipart/form-data">
                {{ csrf_field()}}
                <fieldset>
                  <legend class="text-center">Vchip</legend>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="firstname">Subject:</label>
                  <div class="col-md-4">
                    <select class="form-control" name="subject" required>
                      <option value=""> Select Subject ...</option>
                      <option value="Data Science"> Data Science </option>
                      <option value="Quantitative Apptitude"> Quantitative Apptitude </option>
                      <option value="Reasoning"> Reasoning </option>
                      <option value="Android App Development"> Android App Development </option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="firstname">First name</label>   <div class="col-md-4">
                    <input id="firstname" name="firstname" type="text" placeholder="first name" class="form-control input-md" required="">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="lastname">Last name</label>
                  <div class="col-md-4">
                    <input id="lastname" name="lastname" type="text" placeholder="last name" class="form-control input-md" required="">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="company">Company</label>
                  <div class="col-md-4">
                    <input id="company" name="company" type="text" placeholder="company" class="form-control input-md" required="">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="email">Email</label>
                  <div class="col-md-4">
                    <input id="email" name="email" type="text" placeholder="email" class="form-control input-md" required="">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="address1">Address 1</label>
                  <div class="col-md-4">
                    <input id="address1" name="address1" type="text" placeholder="Address1" class="form-control input-md" required="">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="address2">Address 2</label>
                  <div class="col-md-4">
                    <input id="address2" name="address2" type="text" placeholder="Address2" class="form-control input-md">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="city">City</label>
                  <div class="col-md-4">
                    <input id="city" name="city" type="text" placeholder="city" class="form-control input-md" required="">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="zip">Zip Code</label>
                  <div class="col-md-4">
                    <input id="zip" name="zip" type="text" placeholder="Zip Code" class="form-control input-md" required="">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="country">Country</label>
                  <div class="col-md-4">
                    <input id="country" name="country" type="text" placeholder="Country" class="form-control input-md" required="">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="phone">Phone</label>
                  <div class="col-md-4">
                    <input id="phone" name="phone" type="text" placeholder="Phone" class="form-control input-md" required="">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="gender">Gender</label>
                  <div class="col-md-4">
                    <label class="radio-inline" for="gender-0">
                      <input type="radio" name="gender" id="gender-0" value="0" checked="checked">
                      Male
                    </label>
                    <label class="radio-inline" for="gender-1">
                      <input type="radio" name="gender" id="gender-1" value="1">
                      Female
                    </label>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="selectbasic">Resume</label>
                  <div class="col-md-4">
                    <input type="file" name="resume" class="form-control">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-4 control-label" for="submit"></label>
                  <div class="col-md-4">
                    <button id="submit" name="submit" class="btn btn-primary" title="SUBMIT">SUBMIT</button>
                  </div>
                </div>
              </fieldset>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-info" data-dismiss="modal" title="Close">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!--ens Application  Form  -->
</div>
</div>
</div>

</section>
@stop
@section('footer')
	@include('footer.footer')
	<script src="{{ asset('js/form.js') }}"></script>
@stop