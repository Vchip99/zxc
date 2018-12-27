@extends('mentor.front.master')
@section('title')
  <title>FAQ</title>
@stop
@section('header-css')
  <link href="{{asset('css/nav_footer.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
  .btn-apply{
      width:100px;
      padding: 10px 30px;
      border-radius: 20px;
    }
    button.accordion {
        background-color: #eee;
        color: #0077b3;
        cursor: pointer;
        width: 100%;
        border-left: 3px solid #0077b3;
        border-radius: 10px;
        text-align: left;
        outline: none;
        font-size: 15px;
        transition: 0.4s;
        padding: 12px 20px 12px 10px;
       font-size: 1.1em;
    }
    button.accordion:hover{
          background-color:#0077b3;
          color: white;
    }

    button.accordion:after {
        content: '\002B';/* Unicode character for "plus" sign (+) */
        font-weight: bold;
        float: right;
        margin-left: 5px;
    }

    button.accordion.active:after {
        content: "\2212";/* Unicode character for "minus" sign (-) */
    }
    div.panel {
        padding:0px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.2s ease-out;
        margin:10px;
    }
    div.panel .panel-content strong{
      color: #1E90FF;
      margin-right: 10px;
    }
</style>
@stop
@section('content')
  @include('mentor.front.header_menu')
  <div class="container" style="margin-top: 100px;">
    <div class="row">
      <div class="col-sm-9">
        <h1 style="text-align: center; font-size: 30px; "><b>FAQ</b></h1>
        <button class="accordion">WHAT IS THE NEED OF MENTORING</button>
        <div class="panel">
          <div class="panel-content">
            <p>As an entrepreneur, the importance of execution of ideas are as equally important as having great ideas with us. The path of execution will be well tested, if we go with mentor. To making certain decision on new products, partners, a mentor can guide us from their own experiences and journey of adventure.</p>
            <p>Here are some of advantage of having mentor with us:</p>
            <ul>
              <li>Mentors provide information and knowledge form their experiences, which prevent the common mistakes done by others in same field.</li>
              <li>Mentors can see our week points that often we cannot and provide appropriate solution for improvements.</li>
              <li>Mentors increases our personal and professional growth by 10x.</li>
              <li>Mentors encourage us.</li>
              <li>Presence of mentors provides strength to take strong decisions. </li>
              <li>Mentors teach, how to generate necessary boundaries and discipline.</li>
              <li>We can ask for opinion to mentors for our ideas.</li>
              <li>Mentors are one of the best advisers.</li>
              <li>Mentors are priceless in more ways than one.</li>
            </ul>
            <p>So we believe that mentor needed to every one at every stage of life. Mentors help us to improve our concepts by their experiences </p>
          </div>
        </div>
        <button class="accordion">HOW TO BECOME MENTOR</button>
        <div class="panel">
          <div class="panel-content">
            <p>Sign-up as Mentor, our team member will contact you for verification, after successfully completion of verification, we will approve your profile.</p>
          </div>
        </div>
        <button class="accordion">CAN I FACE TO FACE MEET TO MENTOR</button>
        <div class="panel">
          <div class="panel-content">
            <p>Yes, if mentor agree for face to face meeting they you can. Date, time and location will be fix by mentor or it is depend on your mutual understanding.</p>
          </div>
        </div>
        <button class="accordion">WHICH WAY IS IT BETTER TO CONSULT WITH MENTOR, ONLINE OR PERSONAL MEET</button>
        <div class="panel">
          <div class="panel-content">
            <p>Actually personal meeting with mentor is better but most of interaction done online because of time and location constraints.</p>
          </div>
        </div>
        <button class="accordion">IS THIS MENTORING ONE TO ONE OR IN GROUP</button>
        <div class="panel">
          <div class="panel-content">
            <p>Generally it is one to one mentoring, but if mentor agree to connect with group then you can add your friends also.</p>
          </div>
        </div>
        <button class="accordion">IS WE HAVE TO DO PAYMENT ON THIS PLATFORM</button>
        <div class="panel">
          <div class="panel-content">
            <p>No, you can pay directly to mentor by the source provided by mentors (Like: Paytm, Google pay, bank account etc.).</p>
          </div>
        </div>
        <button class="accordion">CAN WE MASSAGE TO MENTOR WITHOUT ANY PAYMENT</button>
        <div class="panel">
          <div class="panel-content">
            <p>Yes, you can send them massage.</p>
          </div>
        </div>
        <button class="accordion">IS FEES OF MENTOR IS NEGOTIABLE </button>
        <div class="panel">
          <div class="panel-content">
            <p>Yes, you can request to minimize the fees to mentor.  Final decision on fees can be taken by mentor only.</p>
          </div>
        </div>
        <button class="accordion">IF I DIDN’T SATISFY, THEN DO YOU REFUND FEE</button>
        <div class="panel">
          <div class="panel-content">
            <p>Yes, If mentor don’t connect with you at pre-schedule time then only we refund the money. If you don’t satisfy with the mentoring then you can review to mentor accordingly. No one mentor afford to degrade their reviews because most of the mentees, select mentor on the basis of reviews.</p>
          </div>
        </div>
        <button class="accordion">AS WE ARE DIRECTLY PAYING TO MENTORS THEN WHAT IS ADVANTAGE OF VCHIP-EDU</button>
        <div class="panel">
          <div class="panel-content">
            <p>This service is totally free of cost from Vchip-edu, with the prior motive of connecting students with right mentor so they can pursue their dream. We earn from advertisement.</p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="advertisement-area">
          <a class="pull-right create-add" href="{{$addUrl}}" target="_blank">Create Ad</a>
        </div>
        <br/>
        @if(count($ads) > 0)
          @foreach($ads as $ad)
            <div class="add-1">
              <div class="course-box">
                <a class="img-course-box" href="{{ $ad->website_url }}" target="_blank">
                  <img src="{{asset($ad->logo)}}" alt="{{ $ad->company }}"  class="img-responsive" />
                </a>
                <div class="course-box-content">
                  <h4 class="course-box-title" title="{{ $ad->company }}" data-toggle="tooltip" data-placement="bottom">
                    <a href="{{ $ad->website_url }}" target="_blank">{{ $ad->company }}</a>
                  </h4>
                  <p class="more"> {{ $ad->tag_line }}</p>
                </div>
              </div>
            </div>
          @endforeach
        @endif
        @if(count($ads) < 3)
          @for($i = count($ads)+1; $i <=3; $i++)
            @if(1 == $i)
              <div class="add-1">
                <div class="course-box">
                  <a class="img-course-box" href="http://www.ssgmce.org" target="_blank">
                    <img src="{{ asset('images/logo/ssgmce-logo.jpg') }}" alt="Mauli College of Engineering Shegaon"  class="img-responsive" />
                  </a>
                  <div class="course-box-content">
                    <h4 class="course-box-title" title="Shri Sant Gajanan Maharaj College of Engineering" data-toggle="tooltip" data-placement="bottom">
                      <a href="http://www.ssgmce.org/" target="_blank">Shri Sant Gajanan Maharaj College of Engineering</a>
                    </h4>
                    <p class="more"> Shri Sant Gajanan Maharaj College of Engineering</p>
                  </div>
                </div>
              </div>
            @elseif(2 == $i)
              <div class="add-1">
                <div class="course-box">
                  <a class="img-course-box" href="http://ghrcema.raisoni.net/" target="_blank">
                    <img src="{{ asset('images/logo/ghrcema_logo.png') }}" alt="G H RISONI"  class="img-responsive" />
                  </a>
                  <div class="course-box-content">
                    <h4 class="course-box-title" title="G H RISONI" data-toggle="tooltip" data-placement="bottom">
                      <a href="http://ghrcema.raisoni.net/" target="_blank">G H RISONI</a>
                    </h4>
                    <p class="more"> G H RISONI</p>
                  </div>
                </div>
              </div>
            @elseif(3 == $i)
              <div class="add-1">
                <div class="course-box">
                  <a class="img-course-box" href="http://hvpmcoet.in/" target="_blank">
                    <img src="{{ asset('images/logo/hvpm.jpg') }}" alt="HVPM"  class="img-responsive" />
                  </a>
                  <div class="course-box-content">
                    <h4 class="course-box-title" title="HVPM" data-toggle="tooltip" data-placement="bottom">
                      <a href="http://hvpmcoet.in/" target="_blank">HVPM College of Engineer And Technology</a>
                    </h4>
                    <p class="more"> HVPM College of Engineer And Technology</p>
                  </div>
                </div>
              </div>
            @endif
          @endfor
        @endif
      </div>
    </div>
  </div>
@stop
@section('footer')
    @include('mentor.front.footer')
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
