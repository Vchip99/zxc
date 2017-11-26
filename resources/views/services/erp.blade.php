@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu - Digital Education, Online Courses & eLearning |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{asset('css/service.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/themify-icons/themify-icons.css?ver=1.0')}}" rel="stylesheet"/>

@stop
@section('header-js')
  @include('layouts.home-js')
@stop
@section('content')
  @include('header.header_menu')
  <section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single" >
      <div class="vchip-background-img">
        <figure>
          <img src="{{ asset('images/clg-erp.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="contact us" />
        </figure>
      </div>
      <div class="vchip-background-content">
      </div>
    </div>
  </section>
  <section id="" class="v_container v_bg_grey">
     <h2 class="v_h2_title text-center"> Collages & ERP</h2>
     <hr class="section-dash-dark "/>
      <div class="container">
        <p>Vchip-edu is working on digital education platform namely Vchip-edu 1.0. Its the digital education platform design for all who wish to learn. Our main motive is to deliver quality education in villages and remote areas along with urban area to fulfill the dream of Digital Village. At first we are focusing on Engineering and management collages because we have lots of literature review on that.
          </p>
          <p>
          We provide our all the service at free of cost  to college for non-commercial purpose only. We are mostly focusing on bridging a gap between industries and colleges/educational organizations.
          </p>
      </div>
  </section>
  <section id="" class="v_container " >
    <div class="container">
      <div class="row">
        <div class="col-md-6 mrgn_20_btm">
         <h3 class="v_h3_title "> How to Use it:</h3>
            <ul class="user-list">
                <li class=""><span class="" ><i class="ti-check-box"></i> </span>  <h3>Sign up     </h3>    </li>
              <li class=""><span class="" ><i class="ti-check-box"></i> </span>  <h3>Verify your email id</h3>    </li>
              <li class=""><span class="" ><i class="ti-check-box"></i> </span>  <h3>Sign in</h3>    </li>
              <li class=""><span class="" ><i class="ti-check-box"></i> </span>  <h3>Go to Digital Education and select the required one</h3>    </li>
              <li class=""><span class="" ><i class="ti-check-box"></i> </span>  <h3>Enjoy the Digital Education platform</h3>    </li>
            </ul>
      </div>
        <div class="col-md-6 ">
          <div class="embed-responsive embed-responsive-16by9">
               <iframe width="560" height="315" src="https://www.youtube.com/embed/tAZDiJxIRZk" frameborder="0" allowfullscreen></iframe>
          </div>
      </div>
      </div>
    </div>
  </section>
  <section  class="v_container v_bg_grey feature">
     <h2 class="v_h2_title text-center">Advantage to Colleges</h2>
     <hr class="section-dash-dark mrgn_60_btm"/>
      <div class="container">
        <div class="row">
            <div class=" col-md-6">
                <div class="media box-1 wow fadeInUp animated" data-wow-duration="500ms" data-wow-delay="300ms">
                    <div class="media-left">
                        <div class="icon">
                            <i class="ti-direction-alt "></i>
                        </div>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">Bridging the gap between industries and educational organizations</h4>
                        <p>We provide aptitude test series for students of second year and above. Also, toppers will get a certificate of appreciation and gift vouchers.
                           Online course for aptitude by experts.
                        Throughout practice of placement procedure in the form of screening test (aptitude and technical) at first stage, and then conduction of interviews on skype by industry experts. The whole procedure will be designed according to the recruitment pattern of different companies.
                           Also, various interviews of 3rd year and final year students are shared on our cloud. These videos are provided to the recruiters according to their required profile.
                          <Our main goal is to bridge the gap between industries and educational organizations.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="media box-1 wow fadeInUp animated" data-wow-duration="500ms" data-wow-delay="300ms">
                    <div class="media-left">
                        <div class="icon">
                            <i class="ti-hand-point-up"></i>
                        </div>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">Students data on single click (ERP)</h4>
                        <p> Manage all required data of students (ERP) like performance in test series, attendance, assignments, notifications, performance in online certified courses, etc.
                         Regular assignments to the students and their submission. Suggestions and corrections can be asked by lecturers and students can resubmit the assignments.  Analytical reports to examine himself/herself better. Graphical reports to easily understand their performance.  Quality interaction with parents and students.</p>
                    </div>
                </div>
            </div>
            <div class=" col-md-6">
                <div class="media box-2 wow fadeInUp animated" data-wow-duration="500ms" data-wow-delay="300ms">
                    <div class="media-left">
                        <div class="icon">
                            <i class="ti-desktop"></i>
                        </div>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"> Conduction of online unit tests</h4>
                        <p> In general academic examinations, students mostly deal with subjective problems but after the completion of UG, they have to face a lot of objective tests. So, we conduct one objective test per semester according to the university syllabus. Also, all the offline unit tests can be converted to online tests if the college is interested.</p>
                    </div>
                </div>
            </div>
            <div class=" col-md-6">
                <div class="media box-2 wow fadeInUp animated" data-wow-duration="500ms" data-wow-delay="300ms">
                    <div class="media-left">
                        <div class="icon">
                            <i class="ti-crown"></i>
                        </div>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"> Internship in Vchip Technology and our partner companies</h4>
                        <p> Provide in-campus internships to the students. We also provide all the online supports from Vchip’s headquarter. We will also train your few faculties so that they can guide the students at their level only. Any further difficulties will be solved by our experts.  Some live sessions conducted by industrial experts.Internships can be converted into full-time job.</p>
                    </div>
                </div>
            </div>
            <div class=" col-md-6">
                <div class="media box-3 wow fadeInUp animated" data-wow-duration="500ms" data-wow-delay="300ms">
                    <div class="media-left">
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">Interaction with industrial experts</h4>
                          <p>  Live discussions on allotted time period with our experts. Motivational seminar by CEO, CTOs, Directors and Founder of successful start-ups</p>
                    </div>
                </div>
            </div>
            <div class=" col-md-6">
                <div class="media box-3 wow fadeInUp animated" data-wow-duration="500ms" data-wow-delay="300ms">
                    <div class="media-left">
                        <div class="icon">
                            <i class="ti-money"></i>
                        </div>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">Sponsor final year project </h4>
                          <p> We conduct interviews and select some groups for sponsorship from Vchip Technology and our partner companies. A full time job can also be offered according to their performance.</p>
                    </div>
                </div>
            </div>
            <div class=" col-md-6">
                <div class="media box-3 wow fadeInUp animated" data-wow-duration="500ms" data-wow-delay="300ms">
                    <div class="media-left">
                        <div class="icon">
                            <i class="ti-vector "></i>
                        </div>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">Workshops on latest Technology</h4>
                        <p> We will take live workshop on emerging technologies in fields of VLSI, IoT, Embedded etc. </p>
                    </div>
                </div>
            </div>
          </div>
      </div>
  </section>
  <section id="partner" class="v_container ">
     <h2 class="v_h2_title text-center">Be Partner with us</h2>
     <hr class="section-dash-dark "/>
      <div class="container">
        <p>All the above mention facilities are totally free to use. We are focusing on bridging a gap between industries and education organizations or between industry experts and students. So, we are signing MoU with well establish industries, start-ups, educational organizations and colleges.
        </p>
        <br/>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-md-6 col-sm-6 mou-table">
            <table class="table ">
              <tbody>
                <tr>
                    <th>Pattern of MoU to colleges</th>
                    <td><button class="btn btn-primary " data-toggle="modal" data-target="#mouCollege"> Click Here </button>
                    </td>
                </tr>
                <tr>
                    <th>Pattern of MoU to companies</th>
                    <td><button class="btn btn-primary  " data-toggle="modal" data-target="#movCompanies"> Click Here </button></td>
                </tr>
                <tr>
                    <th>About Vchip Technology</th>
                    <td> <button class="btn btn-primary " data-toggle="modal" data-target="#movVchip"> Click Here </button></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
  </section>
  <!-- modal for patterner of MoU to collage -->
  <div class="modal fade" id="mouCollege" tabindex="-1" role="dialog" aria-labelledby="myModal3Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="background-color: white;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title text-center" id="myModal3Label">Vchip Technology
          <img src="{{ asset('images/logo/logo.jpg')}}" align="vchip Technology" class="logo-img "></h4>
        </div>
        <div class="modal-body">
          <div class="text-center mov-head">
                <h2>Memorandum of Understanding</h2>
                <p>Between </p>
                <h2>Partner 1</h2>
                 <p>   and</p>
                <h2>Vchip Technology pvt. Ltd.</h2>
          </div>
          <p>This Memorandum of Understanding (MoU) sets for the terms and understanding between the <b>Partner1</b> and the <b>Vchip Technology pvt. Ltd.</b>  to access <b>Digital education</b> platform.</p>
          <b>Purpose</b>
          <br/>
          <br/>
          <p>We, <b>Vchip Technology</b>  working on educational platform namely <b>Vchip-edu </b> with primary motive of bridging the gap between educational organizations/institutes and well established companies/start-ups along with the concept of digital village.</p>

          <p>We will provide our <b>Vchip-edu</b> platform to your organization so that your students can access our services, courses and test series related to the placements along with workshops on emerging technologies, internships, ERP software at free of cost and other professional courses at free/minimal cost.</p>

          <b>Vchip-edu platform includes  </b>
          <br/>
          <br/>

          <b>I. Concentration on Placement drives</b>
            <ul>
              <li>We provide aptitude test series for students of second year and above. Also, toppers will get a certificate of appreciation and gift vouchers.</li>
              <li>Online course for aptitude by experts.</li>
              <li>Throughout practice of placement procedure in the form of screening test (aptitude and technical) at first stage, and then conduction of interviews on skype by industry experts. The whole procedure will be designed according to the recruitment pattern of different companies.</li>
              <li>Also, various interviews of 3rd year and final year students are shared on our cloud. These videos are provided to the recruiters according to their required profile.</li>
              <li>Our main goal is to bridge the gap between industries and educational organizations.</li>
            </ul>
          <br/>
          <b>II. Students data on single click (ERP)</b>
          <ul>
            <li>Manage all required data of students (ERP) like performance in test series, attendance, assignments, notifications, performance in online certified courses, etc.</li>
            <li>Regular assignments to the students and their submission. Suggestions and corrections can be asked by lecturers and students can resubmit the assignments. </li>
            <li>Analytical reports to examine himself/herself better.</li>
            <li>Graphical reports to easily understand their performance. </li>
            <li>Quality interaction with parents and students.</li>
          </ul>
          <br/>
          <b>III. Conduction of online unit tests</b>
            <ul>
              <li>In general academic examinations, students mostly deal with subjective problems but after the completion of UG, they have to face a lot of objective tests. So, we conduct one objective test per semester according to the university syllabus. </li>
              <li>Also, all the offline unit tests can be converted to online tests if the college is interested.</li>
            </ul>
          <br/>
          <b>IV. Workshops on latest Technology</b>
          <ul>
              <li>We will take live workshop on emerging technologies in fields of VLSI, IoT, Embedded etc.</li>
          </ul>
          <br/>
          <b>V. Internship in Vchip Technology and our partner companies</b>
          <ul>
          <li>Provide in-campus internships to the students. We also provide all the online supports from Vchip’s headquarter. We will also train your few faculties so that they can guide the students at their level only. Any further difficulties will be solved by our experts. </li>
          <li>Some live sessions conducted by industrial experts.</li>
          <li>Internships can be converted into full-time job.</li>
          </ul>
          <br/>
          <b>VI. Sponsor final year project  </b>
          <ul>
          <li>We conduct interviews and select some groups for sponsorship from Vchip Technology and our partner companies.</li>
          <li>A full time job can also be offered according to their performance.</li>
          </ul>
          <br/>
          <b>VII. Courses for nationalize exams</b>
          <ul>
          <li>Online courses for nationalized exams like GATE, CAT.</li>
          <li>Direction for preparation of nationalized exams like GATE, IES, CAT, GRE, etc.</li>
          </ul>
          <br/>
          <b>VIII. Interaction with industrial experts</b>
          <ul>
          <li>Live discussions on allotted time period with our experts.</li>
          <li>Motivational seminar by CEO, CTOs, Directors and Founder of successful start-ups.</li>
          </ul>

          <p><b>Note: </b> We provide all the above facilities at free of cost in the form of online support. You are likely to be charged only when you want any of our experts personally to be at your organization.</p>
          <br/>
          <b>What we expect from your side:</b>
          <ul>
            <li>One coordinator from your side. So that there can be a proper communication between your organization and Vchip Technology.</li>
            <li>Proper arrangements for video recording.</li>
            <li>Contact details of your students. We will strictly use them to send “only educational” related emails.</li>
            <li>By signing MoU, your organization agrees to Vchip Technology to use of name, URL, photos of your organization for promotional purposes on Website of Vchip Technology and in other promotional material.</li>
            <li>Proper provision of accommodation and meal to our experts and representatives when they will be at your campus regarding the work of your organization. </li>
          </ul>
          <b>Note:</b>
          <ul>
            <li>We are providing access of our Vchip-edu platform to your students only.</li>
            <li>You can’t use our platform for any commercial purpose. Please, don’t take any charge from your students on the name of Vchip-edu.</li>
          </ul>

          <b>Duration</b>
          <br/><br/>

          <p>This MoU is at-will and may be modified by mutual consent of authorized officials from any partner. This MoU shall become effective upon signature by the authorized officials from the both partners and will remain in effect until modified or terminated by any one of the partners by mutual consent. This MoU is in effect for one year after signing by both the partners.</p>
        </div>
      </div>
    </div>
  </div>
<!-- modal for about vchip tech -->
  <div class="modal fade" id="movCompanies" tabindex="-1" role="dialog" aria-labelledby="myModal3Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="background-color: white;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title text-center" id="myModal3Label">Pattern of MoU to companies</h4>
        </div>
        <div class="modal-body">
          <div class="text-center mov-head">
                <h2>MEMORANDUM OF UNDERSTANDING (MOU)</h2>
                <p>Between </p>
                <h2>Partner 1</h2>
                 <p>   and</p>
                <h2>Vchip Technology pvt. Ltd.</h2>
          </div>
            <p>This document constitutes an agreement between Partner 1 and Vchip Technology with the prior motive of bridging a gap between industries and educational organizations/colleges. We Vchip Technology, is IoT based privately held company. We are working on education sector, so quality education should reach to villages/ rural areas along with urban areas.</p>
            <br/>
            <b>1. Objective:</b>

            <p>The objective of this MoU is to bridging a gap between industries and educational organizations/ colleges. We believe that not only students having dream companies but also companies having their dream employees. So that, we are connecting with industries and colleges, so right person should reach to right place. </p>
            <br/>
            <b>We Provide:</b>
            <ul>
              <li>We  provide students/employee as per your required profile for sponsor projects, internship and for part-time & full-time job.</li>
              <li>We conduct their aptitude test, followed by TI and PI and finally send profiles of pre selected students/candidates along with their performance in detail. Also we send video recording of interviews of  pre selected students/candidates. </li>
            </ul>

            <p><b>Note:</b> All the above services we provide at free of cost.</p>
            <br/>
            <b>What We Aspects:</b>
            <ul>
              <li>Industrial relationship with your company/organization. </li>
              <li>A hour of your industrial experts per month for interaction/conference with our students. For interaction/conference link is available on our platform, your industrial experts can access it from anywhere. </li>
            </ul>

            <b>General Terms of MOU</b>
            <ul>
              <li>Duration of MOU: This MOU shall be operational upon signing and will have an initial duration of one year. All activities conducted before this date within the vision of the joint collaboration will be deemed to fall under this MOU.</li>

              <li>Coordination: In order to carry out and fulfill the aims of this agreement, each party will appoint an appropriate person(s) to represent its organization and to coordinate the implementation of activities.</li>

              <li>Technical Support: All the technical support will be provided by Vchip Technology for selection of candidate to interaction with our students. </li>

              <li>Confidentiality: Each party agrees that it shall not, at any time, after executing the activities of this MOU, disclose any information in relation to these activities or the affairs of business or method of carrying on the business of the other without consent of both parties.</li>

              <li>Termination of MOU: The partnership covered by this MOU shall terminate upon completion of the agreed upon period. The agreement may also be terminated with a written one month notice from either side. In the event of non-compliance or breach by one of the parties of the obligations binding upon it, the other party may terminate the agreement with immediate effect.</li>

              <li>Extension of Agreement: The MOU may be extended provided the parties agree upon, and can provide the necessary resources.</li>

              <li>Communications: All notice, demands and other communication under this agreement in connection herewith shall be written in English language and shall be sent to the last known address, e-mail, or fax of the concerned party. Any notice shall be effective from the date on which it reaches the other party.</li>

              <li>Addendum: Any Addendum to this MOU shall be in writing and signed by both parties.</li>
            </ul>
            <br/>

            <b>Contact Information</b>
            <br/><br/>
            <b>Partner 1:</b>
            <br/>
            <br/>
            <p>Vchip Design pvt. Ltd.<br/>
            Gitanjali colony, <br/>
            Rajyog society,<br/>
            Warje, Pune -411 058.<br/>
            Phone no.:7722078597<br/>
            email:info@vchiptech.com</p>
            <div class="row">
              <div class="col-md-6">
                <b>Partner1    </b><br/>
                <b>CEO </b>
              </div>
              <div class="col-md-6 pull-right">
                <b> Vishesh Agrawal</b><br/>
                <b> CEO</b><br/>
                <b>    Vchip Technology</b>
              </div>
            </div>
        </div>
        </div>
    </div>
  </div>
<!-- modal for about vchip tech -->
  <div class="modal fade" id="movVchip" tabindex="-1" role="dialog" aria-labelledby="myModal3Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="background-color: white;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title text-center" id="myModal3Label">About Vchip Technology </h4>
        </div>
        <div class="modal-body">

            <p>We are working on digital education platform namely V-edu 1.0 <a target="_blank" href="https://vchipedu.com">(https://vchipedu.com).</a> Its the digital education platform design for all who wish to learn. Our main motive is to deliver quality education in villages and remote areas along with urban area to fulfill the dream of Digital Village. At first we are focusing on Engineering collages because we have lots of literature review on that. </p>

            <p>We will provide all the basic need at free of cost like in engineering collages most of students (90%+) are prepare for placement. We will digitally provide all the required for placement at free of cost.</p>
            <br/>
            <br/>
            <b>Why Vchip Technology:</b>
            <ul>
              <li>Vchip Technology has purpose of educated society. Its not only business but much more than it. Its all about betterness of society. </li>
              <li> Vchip Technology has CEO, who is very dedicated about his dream and attitude of never give up always be helpful to Vchip.</li>
              <li>Vchip has proper time-lime to work and never think about plan B.</li>
            </ul>
            <br/>
            <br/>

            <b>About Vchip Technology:</b>
            <ul>
              <li>Vchip Technology is newly born IoT base company.</li>
              <li>Vchip Technology is working in Education and Agriculture field. Because childhood of our CEO went in villages and He faced lost of problem in his primary education. In villages quality of education is very poor so, at that time only he decided that I would contribute in education field to improve the quality of education in villages and remote areas along with urban area. Also, He is son of farmer, so obviously he wanna work for farmer. He believe that, India is country of villages so development of India is directly proportional to development of villages.</li>
              <li>Vchip Technology believe that <b>Better society is best place to live and Educated society is better than best</b>.</li>
              <li>We will provide IoT base solution in Education and Agriculture field. Currently we are working on Software part. In next year, we will start working in Hardware field and Networking.</li>
              <li>Currently we have team of 4 guys at our Pune office.</li>
              <li>Has lots of IITian connected with us for courses/test series/project/research paper generation.</li>
              <li>Discussion going on with IIT Kharagpur to become partner with Vchip Technology.</li>
            </ul>
            <br/>
            <br/>
            <br/>
            <b>Thanks and Regard,</b><br/>
            <b>Vchip Technology Team</b>
        </div>
      </div>
    </div>
  </div>
@stop
@section('footer')
  @include('footer.footer')
@stop