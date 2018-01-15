@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu – Be partner with Vchip Technology</title>
@stop
@section('header-css')
	@include('layouts.home-css')
<link id="cpswitch" href="{{ asset('css/hover.css?ver=1.0')}}" rel="stylesheet" />
<link href="{{ asset('css/v_career.css?ver=1.0')}}" rel="stylesheet"/>
<style type="text/css">
  .tile
{
  width:100%;
  height:200px;
  margin:10px;
  background-color:#fff;
  display:inline-block;
  background-size:cover;
  position:relative;
  cursor:pointer;
  transition: all 0.4s ease-out;
  box-shadow: 0px 35px 77px -17px rgba(0,0,0,0.44);
  overflow:hidden;
  color:white;
  font-family:'Roboto';

}
.tile img
{
  height:100%;
  width:100%;
  position:absolute;
  top:0;
  left:0;
  z-index:0;
  transition: all 0.4s ease-out;
}
.tile .text
{
   z-index:99;
  position:absolute;
  /*padding:30px;*/
  height:calc(100% - 60px);
}
.tile h1
{
  font-weight:300;
  margin:0;
  text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
  color:#e91e63;
  background-color: rgba(255,255,255,.35);
  padding:0px;
  font-size: 15px;
  top: 0px !important;
   box-shadow: 5px 5px 25px 0 rgba(46,61,73,.2);
}
.tile h2
{
  font-weight:100;
  margin:20px 0 0 0;
  font-style:italic;
   transform: translateX(200px);
}
.tile p
{
  font-weight:300;
  margin:20px 0 0 0;
  line-height: 25px;
/*   opacity:0; */
  transform: translateX(-200px);
  transition-delay: 0.2s;
}
.animate-text
{
  padding: 50px 0px 50px 45px;
  opacity:0;
  transition: all 0.6s ease-in-out;
}
.animate-text>a
{
font-weight: bolder;
   box-shadow: 5px 5px 25px 0 rgba(46,61,73,.2);

}
.tile:hover
{
/*   background-color:#99aeff; */
box-shadow: 0px 35px 77px -17px rgba(0,0,0,0.64);
  transform:scale(1.05);
}
.tile:hover img
{
  opacity: 0.2;
}
.tile:hover .animate-text
{
  transform:translateX(0);
  opacity:1;
}
@media (min-width: 500px) and (max-width: 764px){
.tile {
     width: 60%;
     margin-left: 20%;
  }
}
@media screen and (max-width: 388px){
   .tile {
     width: 100%;
  }
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
				<img src="{{ asset('images/partner.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip partners" />
			</figure>
		</div>
		<div class="vchip-background-content">
      <h2 class="animated bounceInLeft">Digital Education</h2>
    </div>
  </div>
</section>
<section id="" class="v_container v_bg_grey">
  <div class="container ">
    @if(Session::has('message'))
        <div class="alert alert-success" id="message">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ Session::get('message') }}
        </div>
      @endif
    <div class="row text-center">
      <h2 class="v_h2_title">Our Partners</h2>
      <hr class="section-dash-dark"/>
      <h3 class="v_h3_title ">We Believe that working together always increases productivity...</h3>
      <p>An integral part of Vchip-edu’s partners includes our mentors, funders and franchise holders. Vchip-edu works with companies of same interest, governments, nonprofits and other organizations, institutes, mentors, funders and franchise holders to make an educated world and digital villages. We are partners not only for business, but also for better educated society. By supporting to each others we have made stronger community.</p>
    </div>
</section>
<section class="v_container">
    <div class="container" >
      	<div class="row">
	        <div class="col-md-8 col-md-offset-2 text-center ">
	          <h2 class="v_h2_title">OUR PARTNERS</h2>
	          <hr class="section-dash-dark"/>
	          <h3 class="v_h3_title ">Happy partners...Successful adventure.</h3>
	        </div>
	        <div class="row our_customer">
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim">
              <div class="tile">
                    <img src="{{ asset('images/our-partner/vchip-tech-logo.jpg')}}" alt="SSGMCE"/>
                    <div class="text">
                      <h1 title="Vchip Tech">Vchip Tech</h1>
                      <p class="animate-text">
                        <a class="info" href="https://vchiptech.com/" target="_blank">Learn More
                           <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </a>
                      </p>
                    </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim">
              <div class="tile">
                    <img src="{{ asset('images/our-partner/lasthour_logo.jpg')}}" alt="Last Hours Tech"/>
                    <div class="text">
                      <h1 title="Last Hours Tech">Last Hours Tech</h1>
                      <p class="animate-text">
                        <a class="info" href="http://lasthourstech.com/" target="_blank">Learn More
                           <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </a>
                      </p>
                    </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim">
              <div class="tile">
                    <img src="" alt="Pinnaculum Infotech Pvt. Ltd"/>
                    <div class="text">
                      <h1 title="Pinnaculum Infotech">Pinnaculum Infotech</h1>
                      <p class="animate-text">
                        <a class="info" href="http://pinnaculuminfotech.com/" target="_blank">Learn More
                           <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </a>
                      </p>
                    </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim">
              <div class="tile">
                    <img class="" src="{{ asset('images/our-partner/axis-bank.jpg')}}" alt="Axis Bank">
                    <div class="text">
                      <h1 title="Axis Bank">Axis Bank</h1>
                      <p class="animate-text">
                        <a class="info" href="https://www.axisbank.com/" target="_blank">Learn More
                           <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </a>
                      </p>
                    </div>
              </div>
            </div>
 			</div>
		</div>
	</div>
</section>
<section id="v_section" class=" v_container v_bg_grey">
  <div class="container">
    <div class="row">
      <div class="text-center">
       <h1 class="v_hedling ">BE OUR PARTNERS</h1>
       <hr class="section-dash-dark" />
       <h3 class="v_h3_title text-muted "><em>Happy partners...Successful adventure.</em></h3>
     </div>
     <div class="col-md-12 mrgn_30_top">
      <button class="accordion slideanim" title="ANGEL INVESTOR">ANGEL INVESTOR</button>
      <div class="panel">
        <div class="panel-content ">
          <ul>
           <li>We are looking for Angel Investor so we can concentrate more and more toward quality rather that currency flow at the initial stage and also we can pay well to our team so they will be always with us. </li>
           <h3 class="v_h3_title"> Vchip-edu is best place to invest because:</h3>
           <ul class="vchip_list">
             <li> Education is one of the most growing business in the world.</li>
             <li> Vchip-edu is working on IoT base both software and hardware solution for education sector. </li>
             <li> We have done lots of research on what’s beneficial to our students for their long term future.</li>
             <li> Optimize solution.</li>
             <li> First time digital villages concept in India.</li>
             <li> Concept of learn with fun. </li>
             <li> It is also helpful for bridging between educational organizations/institutes and industries.</li>
             <li> Our education platform provides industrial touch along with quality education. </li>
           </ul>
         </ul>
         <h4 class="v_h4_subtitle">So, Vchip-edu is one of the prominent place to invest.</h4>
         <p><a class="btn btn-apply btn-primary push-bottom flot-left" href="#" data-toggle="modal" data-target="#myModal" title="Apply Now">Apply Now</a></p>
       </div>


     </div>

     <button class="accordion slideanim" title="FRANCHISE HOLDER">FRANCHISE HOLDER</button>
     <div class="panel">
      <div class="panel-content ">
        <h2 class="v_h2_title text-center">Vchip-edu, leaders in on-line education platform.</h2>
        <p>Vchip-edu offers the customize on-line education platform to the students, industrial personal by the quality teaching, optimistic materials. Vchip-edu has introduced the innovative products live learn with fun, Vchip-education, Vchip-connect, Vchip-pendrive, Vchip-cloud. </p>
        <p>The Franchise concept is the most trusted and successful business model. It help to grow the business across the world ie. win-win condition to all of us along with our customers.  Vchip-edu offers a complete business format to the franchisees including support in setting up and operating the center. In general our Franchisees will have:</p>
        <ul class=" vchip_list">
          <li> Opportunity to work with the leader in Education field. </li>
          <li> Access to the brand Vchip-edu.</li>
          <li> Access to all of our platform.</li>
          <li> Exposure to standard business process, with highest level of ethical standards.</li>
          <li> Opportunity to do something for society.</li>
          <li> Access to research papers, study material and faculty orientation programs.</li>
          <li> Guidance and motivation to do business. </li>
        </ul>
        <h3 class="v_h3_title"> Advantages of being a Vchip-edu Franchisee</h3>
        <ul class="vchip_list float-left">
          <li><a>Lower risks:</a> On-line education is the one of the most growing business in the world. Our team take care of your business so it takes off smoothly.</li>
          <li><a>Business Plan:</a> We will provide the basic business plan and know-how to operate the center.</li>
          <li><a>Beyond the conventional ways:</a> Vchip-edu provides business module different than that of conventional business module. So your services to your students become unique.</li>
          <li><a>Training:</a> All new partners and their team will undergo the basic training to deal with all the platform of the Vchip-edu. Specific modules like marketing and counseling training will be available on request.</li>
          <li><a>Quality and optimistic products and Services:</a> Vchip-edu has our unique, innovative and optimistic products.  such as Learn with fun, Vchip-education, Vchip-connect, Vchip-pendrive, Vchip-cloud. </li>
        </ul>
        <h4 class="v_h4_subtitle">Vchip-edu franchisee would have access to all the products and services offered by us.
          So, Vchip-edu is one of the prominent place to gather and build prominent products.</h4>
          <p><a class="btn btn-apply btn-primary push-bottom" href="#" data-toggle="modal" data-target="#myModal" title="Apply Now">Apply Now</a></p>
        </div>

      </div>
      <button class="accordion slideanim" title="MENTORS">MENTORS</button>
      <div class="panel">
        <div class="panel-content">
          <p>We believes that if you want to win the race then only your speed in not enough but also you should have proper direction. So before starting any adventure we at first discuss our plan with our mentors, industry leaders who are working in same fields.
            All the industry leaders in the field of education, professors are most welcome to become mentor of Vchip-edu. </p>
            <p><a class="btn btn-apply btn-primary push-bottom" href="#" data-toggle="modal" data-target="#myModal" title="Apply Now">Apply Now</a></p>
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
                      <option value="Angel Investor"> Angel Investor </option>
                      <option value="Franchise Holder"> Franchise Holder </option>
                      <option value="Mentors"> Mentors </option>
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
                <!-- <div class="form-group">
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
                </div> -->
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
	<script>
		var acc = document.getElementsByClassName("accordion");
		var i;

		for (i = 0; i < acc.length; i++) {
			acc[i].onclick = function() {
				this.classList.toggle("active");
				var panel = this.nextElementSibling;
				if (panel.style.maxHeight){
					panel.style.maxHeight = null;
				} else {
					panel.style.maxHeight = panel.scrollHeight + "px";
				}
			}
		}
	</script>
@stop