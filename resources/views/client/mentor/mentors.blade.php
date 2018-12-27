@extends('client.mentor.master')
@section('title')
  <title>Mentor</title>
@stop
@section('header-css')
  <link href="{{asset('css/nav_footer.css?ver=1.0')}}" rel="stylesheet"/>
  <style>
    .memberinfo{
      margin:10px;
    }
    .image{
      height:150px;
      width:150px;
    }
    .topcontent{
      padding-top:20px;
    }
    /* For Horizontal Scrolling */
    .scrolling-wrapper {
      overflow-x: scroll;
      overflow-y: hidden;
      white-space: nowrap;
      -webkit-overflow-scrolling: touch;
    }
    .scrolling-wrapper .card {
      display: inline-block;
    }
    /* end For Horizontal Scrolling */
  </style>
@stop
@section('content')
    @include('client.mentor.header_menu')
    <section class="container" style="margin-top: 100px;">
      <h1 style="text-align: center;">Connect with Expert to Become Expert</h1><br>
      <p style="text-align:justify;">Experience has its own importance. Connect with expert and learn from their experiences. We believe that learning from others experiences is the best way to learn with in short period and become expert. Sometime we stuck at some point and you might required days or months or years to clear the concepts, at that time it best to take advice from experts, because they already went through  similar condition.</p>
    </section><br>
    <section>
      <div class="container">
        <div class="row">
          <div class="col-sm-9">
            <div style="display: inline-block;">
              <select class="form-control"  style="min-width: 170px;">
                <option value="">Select Area</option>
                <option value="1">All</option>
                <option value="2">IT</option>
                <option value="2">IoT</option>
              </select>
            </div>&nbsp;&nbsp;
            <div style="display: inline-block;">
              <select class="form-control"  style="min-width: 170px;">
                <option value="">Select Skill</option>
                <option value="1">All</option>
                <option value="2">HTML</option>
              </select>
            </div><br><br>
            <div style="border:1px solid black;">
              <div class="row memberinfo" >
                <div class="col-md-4">
                  <div>
                    <img src="{{url('images/mark.jpg')}}" alt="member" class="image img-circle">
                  </div><br>
                  <div><strong>Reviews: </strong></div>
                  <div><strong>Fees: 1000 /Hour </strong></div>
                </div>
                <div class="col-md-8 topcontent">
                  <p><strong>Name:</strong> Vishesh Agrawal</p>
                  <p><strong>Designation:</strong> CEO, Vchip Technology Pvt Ltd.</p>
                  <p><strong>Education:</strong> M-Tech, IIT Kharagpur</p>
                  <p><strong>Key Areas:</strong> #Mentoring #AI #Machine Learning #Programing Languages #Higher Education Consultant</p>
                  <p><strong>Social Media:</strong></p>
                  <a class="btn btn-primary" href="{{ url('mentorinfo') }}">View Profile</a>
                  <a class="btn btn-primary" href="{{ url('mentorinfo') }}">Message</a>
                </div>
              </div>
            </div><br>
            <div style="border:1px solid black;">
              <div class="row memberinfo" >
                <div class="col-md-4">
                  <div>
                    <img src="{{url('images/mark.jpg')}}" alt="member" class="image img-circle">
                  </div><br>
                  <div><strong>Reviews: </strong></div>
                  <div><strong>Fees: 1000 /Hour </strong></div>
                </div>
                <div class="col-md-8 topcontent">
                  <p><strong>Name:</strong> Vishesh Agrawal</p>
                  <p><strong>Designation:</strong> CEO, Vchip Technology Pvt Ltd.</p>
                  <p><strong>Education:</strong> M-Tech, IIT Kharagpur</p>
                  <p><strong>Key Areas:</strong> #Mentoring #AI #Machine Learning #Programing Languages #Higher Education Consultant</p>
                  <p><strong>Social Media:</strong></p>
                  <a class="btn btn-primary" href="{{ url('mentorinfo') }}">View Profile</a>
                </div>
              </div>
            </div><br>
            <div style="border:1px solid black;">
              <div class="row memberinfo" >
                <div class="col-md-4">
                  <div>
                    <img src="{{url('images/mark.jpg')}}" alt="member" class="image img-circle">
                  </div><br>
                  <div><strong>Reviews: </strong></div>
                  <div><strong>Fees: 1000 /Hour </strong></div>
                </div>
                <div class="col-md-8 topcontent">
                  <p><strong>Name:</strong> Vishesh Agrawal</p>
                  <p><strong>Designation:</strong> CEO, Vchip Technology Pvt Ltd.</p>
                  <p><strong>Education:</strong> M-Tech, IIT Kharagpur</p>
                  <p><strong>Key Areas:</strong> #Mentoring #AI #Machine Learning #Programing Languages #Higher Education Consultant</p>
                  <p><strong>Social Media:</strong></p>
                  <a class="btn btn-primary" href="{{ url('mentorinfo') }}">View Profile</a>
                </div>
              </div>
            </div><br>
            <div style="border:1px solid black;">
              <div class="row memberinfo" >
                <div class="col-md-4">
                  <div>
                    <img src="{{url('images/mark.jpg')}}" alt="member" class="image img-circle">
                  </div><br>
                  <div><strong>Reviews: </strong></div>
                  <div><strong>Fees: 1000 /Hour </strong></div>
                </div>
                <div class="col-md-8 topcontent">
                  <p><strong>Name:</strong> Vishesh Agrawal</p>
                  <p><strong>Designation:</strong> CEO, Vchip Technology Pvt Ltd.</p>
                  <p><strong>Education:</strong> M-Tech, IIT Kharagpur</p>
                  <p><strong>Key Areas:</strong> #Mentoring #AI #Machine Learning #Programing Languages #Higher Education Consultant</p>
                  <p><strong>Social Media:</strong></p>
                  <a class="btn btn-primary" href="{{ url('mentorinfo') }}">View Profile</a>
                </div>
              </div>
            </div><br>
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
    </section>
  <section class="container">
    <h4><strong>Simillar Mentors:</strong></h4>
      <div class="scrolling-wrapper">
        <div class="card">
          <a href="{{ url('mentorinfo') }}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo') }}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo') }}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo') }}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo') }}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo') }}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo') }}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo') }}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
      </div>
  </section>
@stop
@section('footer')
  @include('client.mentor.footer')
@stop