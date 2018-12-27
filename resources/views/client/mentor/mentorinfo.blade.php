@extends('client.mentor.master')
@section('title')
  <title>Vchip Mentor</title>
@stop
@section('header-css')
  <!-- <link href="{{asset('css/nav_footer.css?ver=1.0')}}" rel="stylesheet"/> -->
  <style>
    .memberinfotop{
      margin-top: 100px;
    }
    .memberinfo{
      margin:10px;
    }
    .image{
      height:220px;
      width:200px;
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
  <div class="container">
    <div class="row">
      <div class="col-md-10 memberinfotop col-md-offset-1">
        <div style="border:1px solid black;">
          <div class="row memberinfo" >
            <div class="col-md-4">
              <div>
                <img src="{{ asset('images/mark.jpg') }}" alt="member" class="image img-circle">
              </div>
            </div>
            <div class="col-md-8 topcontent">
              <p><strong>Name:</strong> Vishesh Agrawal</p>
              <p><strong>Designation:</strong> CEO, Vchip Technology Pvt Ltd.</p>
              <p><strong>Education:</strong> M-Tech, IIT Kharagpur</p>
              <p><strong>Key Skills:</strong> #Mentoring #AI #Machine Learning #Programing Languages #Higher Education Consultant</p>
              <p><strong>Social Media:</strong></p>
              <p><strong>Reviews: </strong></p>
              <p><strong>Fees: 1000 /Hour </strong></p>
              <button class="btn btn-primary">Massage</button>
            </div>
          </div>
        </div><br>
        <div style="border:1px solid black;">
          <div class="row memberinfo">
            <h4><strong>About Vishesh:</strong></h4>
            <p>Vishesh Agrawal was born on 27-April-1989 at Sawlapur (Small village in Amravati District, Maharashtra, India). Vishesh had completed his primary education in his village only. In 10th, he was topped in his village, so his father send him district place (Amravati) for higher education. Vishesh had completed his engineering from Amravati university in Electronics & Telecommunication. In his engineering he heard about IIT and everyone wanna go to it. So Vishesh also search more about IIT and he realized that IIT is the best place for enhancing the knowledge and also best for extra curriculum activities. So, he had prepared for GATE exam score good marks (AIR: 105) and got admission in IIT Kharagpur.</p>
          </div>
        </div><br>
        <div style="border:1px solid black;">
          <div class="row memberinfo">
            <h4><strong>Experiance:</strong></h4>
            <p>After completion of his education, he has started to work toward his dream Digital Village. Vishesh believes that for Digitalizations of India at first we have to digitalized villages.</p>
          </div>
        </div><br>
        <div style="border:1px solid black;">
          <div class="row memberinfo">
            <h4><strong>Achievements:</strong></h4>
            <p>When we see toward journey of Vishesh Agrawal then its look like Zero to Hero. As Vishesh has started his primary education in small village and completed his post graduation from IIT (Top most university of India) and today he is a CEO of one of the growing and respectable industry in Education and Agriculture sector namely Vchip Technology Pvt Ltd. After completion of his M-tech he has started GATE institute namely GATE THE Direction, saved money and build network in educational field so it would be helpful for Vchip Technology. He had started Vchip Technology by himself with the help of his family and friends. So, he always give credential and time to his family and friends. Vishesh’s father is always be with him for his crazy decisions and support him in his difficult time. Also some of his friends are directly or indirectly with him at the crucial times like foundation of the base of Vchip Technology.</p>
          </div>
        </div><br>
        <h4><strong>Simillar Mentors:</strong></h4>
        <div class="scrolling-wrapper">
          <div class="card">
            <a href="{{ url('mentorinfo')}}">
              <div class="panel">
                <div>
                  <img src="{{ asset('images/mark.jpg') }}" alt="member" class="" />
                </div>
                <p><b>Name: Mark Zukarbarg</b></p>
                <p><strong>Reviews: </strong></p>
              </div>
            </a>
          </div>
          <div class="card">
            <a href="{{ url('mentorinfo')}}">
              <div class="panel">
                <div>
                  <img src="{{ asset('images/mark.jpg') }}" alt="member" class="" />
                </div>
                <p><b>Name: Mark Zukarbarg</b></p>
                <p><strong>Reviews: </strong></p>
              </div>
            </a>
          </div>
          <div class="card">
            <a href="{{ url('mentorinfo')}}">
              <div class="panel">
                <div>
                  <img src="{{ asset('images/mark.jpg') }}" alt="member" class="" />
                </div>
                <p><b>Name: Mark Zukarbarg</b></p>
                <p><strong>Reviews: </strong></p>
              </div>
            </a>
          </div>
          <div class="card">
            <a href="{{ url('mentorinfo')}}">
              <div class="panel">
                <div>
                  <img src="{{ asset('images/mark.jpg') }}" alt="member" class="" />
                </div>
                <p><b>Name: Mark Zukarbarg</b></p>
                <p><strong>Reviews: </strong></p>
              </div>
            </a>
          </div>
          <div class="card">
            <a href="{{ url('mentorinfo')}}">
              <div class="panel">
                <div>
                  <img src="{{ asset('images/mark.jpg') }}" alt="member" class="" />
                </div>
                <p><b>Name: Mark Zukarbarg</b></p>
                <p><strong>Reviews: </strong></p>
              </div>
            </a>
          </div>
          <div class="card">
            <a href="{{ url('mentorinfo')}}">
              <div class="panel">
                <div>
                  <img src="{{ asset('images/mark.jpg') }}" alt="member" class="" />
                </div>
                <p><b>Name: Mark Zukarbarg</b></p>
                <p><strong>Reviews: </strong></p>
              </div>
            </a>
          </div>
          <div class="card">
            <a href="{{ url('mentorinfo')}}">
              <div class="panel">
                <div>
                  <img src="{{ asset('images/mark.jpg') }}" alt="member" class="" />
                </div>
                <p><b>Name: Mark Zukarbarg</b></p>
                <p><strong>Reviews: </strong></p>
              </div>
            </a>
          </div>
          <div class="card">
            <a href="{{ url('mentorinfo')}}">
              <div class="panel">
                <div>
                  <img src="{{ asset('images/mark.jpg') }}" alt="member" class="" />
                </div>
                <p><b>Name: Mark Zukarbarg</b></p>
                <p><strong>Reviews: </strong></p>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
@section('footer')
  @include('client.mentor.footer')
@stop