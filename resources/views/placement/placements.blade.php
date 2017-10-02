@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |V-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/placement.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/comment.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
  .advertisement {
    position:relative;
    overflow:hidden;
}
    .caption {
    position:absolute;
    top:0;
    right:0;
    background:rgba(66, 139, 202, 0.75);
    width:100%;
    height:100%;
    padding:2%;
    display: none;
    text-align:center;
    color:#fff !important;
    z-index:2;
}
.caption p{ margin-top: 35%;}
@media(min-width: 548px) and(max-width: 768px)
{
  .caption p{ margin-top: 10%;}
}
.caption a{ font-weight: bolder; }
.add-link{font-weight: bolder;}

.ellipsed {
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}
 .sponsored a, .create-add a{color:#A9A9A9;
    font-weight: bolder;
  }
    .ellipsed {
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}
p.ellipsed{
  cursor: pointer;
}
  .v_p_heding{font-weight: bolder;}
 .panel-heading img {
  width: 30px;
  height: 30px;
  float: left;
  border: 2px solid #d2d6de;
  padding: 1px;
}
.username{
margin-left: 10px;
margin-right: 10px;

}
.username {
  font-size: 16px;
  font-weight: 600;
  color:#b6b6b6;
}
.fa-calendar-o{ font-weight: bolder;
margin-right: 5px;
}
.date{
color: #b6b6b6;
}
</style>
@stop
@section('header-js')
  @include('layouts.home-js')
    <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
@stop
@section('content')
	@include('header.header_menu')
  <section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single">
      <div class="vchip-background-img">
        <figure>
          <img src="{{asset('images/placement-bg.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip courses" />
        </figure>
      </div>
      <div class="vchip-background-content">
          <h2 class="animated bounceInLeft">Digital Education</h2>
        </div>
    </div>
  </section>
<!-- Start course section -->
<section>
  <div class="container ">
    <div class="row">
      <div class="col-sm-3 hidden-div">
        <div class="control mrgn_20_top_btm" >
          <select id="area" class="form-control" name="area" data-toggle="tooltip" title="Area" onChange="selectCompany(this);" required>
            <option value="0">Select Area</option>
            @if(count($placementAreas) > 0)
              @foreach($placementAreas as $placementArea)
                @if($selectedArea == $placementArea->id)
                  <option value="{{$placementArea->id}}" selected="true">{{$placementArea->name}}</option>
                @else
                  <option value="{{$placementArea->id}}">{{$placementArea->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
          <select id="company" class="form-control" name="company" data-toggle="tooltip" title="Company" onChange="getPlacementCompany(this);" required>
            <option value="0">Select Company</option>
            @if(count($placementCompanies) > 0)
              @foreach($placementCompanies as $placementCompany)
                @if($selectedCompany == $placementCompany->id)
                  <option value="{{$placementCompany->id}}" selected="true">{{$placementCompany->name}}</option>
                @else
                  <option value="{{$placementCompany->id}}">{{$placementCompany->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
        </div>
      </div>
      <div class="col-sm-9 col-sm-push-3 data">
         <div class="portlet box grey-cascade">
            <div class="portlet-title">
              <ul class="nav nav-tabs nav-tabs-lg pull-left">
                <li class="active" title="About">
                  <a data-toggle="tab" href="#tab_1"
                    aria-expanded="true">
                    <div class="" align="center">About</div>
                  </a>
                </li>
                <li class="" title="Placement Process">
                  <a data-toggle="tab" href="#tab_2"
                    aria-expanded="true">
                    <div class="hidden-xs">
                      Placement Process
                    </div>
                    <div class="hidden-md hidden-lg hidden-sm">
                      PP
                    </div>
                  </a>
                </li>
                <li role="presentation" class="dropdown" title="Experience"  >
                  <a href="#" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown" aria-controls="myTabDrop1-contents" data-toggle="tab" href="#tab_4"
                    aria-expanded="true" >
                    <span class="text">Experience</span>
                    <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1" id="myTabDrop1-contents">
                    <li>
                      <a href="#dropdown1-tab" tabindex="-1" role="tab"  data-toggle="tab" aria-controls="dropdown1">
                        <span>Candidate Exp.</span>
                      </a>
                    </li>
                    <li>
                      <a href="#dropdown2-tab" tabindex="-1" role="tab" data-toggle="tab" aria-controls="dropdown2">
                        <span>Share your Exp.</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="" title="Frequently Ask Question">
                  <a data-toggle="tab" href="#tab_3"
                    aria-expanded="true">
                    <div class="">  FAQ  </div>
                  </a>
                </li>
                <li class="" title="Mock Test">
                  <a data-toggle="tab" href="#tab_4"
                    aria-expanded="true">
                    <div class="">  Mock Test  </div>

                  </a>
                </li>
              </ul>
            </div>
            <div class="portlet-body">
              <div class="tabbable">
                <div class="tab-content">
                  <div id="tab_1" class="tab-pane fade in active">
                   <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">{{ (is_object($companyDetails))?$companyDetails->company->name:'Company'}}</h4>
                      </div>
                      <div class="panel-body">
                       <p>{!! (is_object($companyDetails))?$companyDetails->about_company:NULL !!}</p>
                       <br/>
                       <p><b>Industry:</b>  {{ (is_object($companyDetails))?$companyDetails->industry_type:NULL}}</p>
                       <p><b>Founded:</b>   {{ (is_object($companyDetails))?$companyDetails->founded_year:NULL  }}</p>
                       <p><b>Founder:</b>   {{ (is_object($companyDetails))?$companyDetails->founder_name:NULL  }}</p>
                       <p><b>Headquarters:</b>  {{ (is_object($companyDetails))?$companyDetails->headquarters:NULL }}</p>
                       <!-- <p><b>Area served</b> Worldwide</p> -->
                       <p><b>Key people:</b> {{(is_object($companyDetails))?$companyDetails->ceo:NULL}}</p>
                       <b>Products:</b>
                       <ul class="custom-list-style">
                        @if(is_object($companyDetails) && !empty($companyDetails->products))
                          @foreach(explode(',',$companyDetails->products) as $product)
                            <li>{{ $product }}</li>
                          @endforeach
                        @endif
                        </ul>
                        <p><b>official Website :</b> {{ (is_object($companyDetails))?$companyDetails->website:NULL}}</p>
                      </div>
                    </div>
                  </div>
                   <!-- end about -->
                  <div id="tab_2" class="tab-pane fade ">
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">Selection Process:</h4>
                      </div>
                      <div class="panel-body">
                       <p class="bullet_ul">{!! (is_object($placementProcess))?$placementProcess->selection_process:NULL !!}</p>
                      </div>
                    </div>
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">Academic Criteria:</h4>
                      </div>
                      <div class="panel-body">{!! (is_object($placementProcess))?$placementProcess->academic_criteria:NULL !!}
                      </div>
                    </div>
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">Pattern of Written Exam:</h4>
                      </div>
                      <div class="panel-body">
                          <table class="table table-striped table-bordered">
                            <tbody>
                              <tr>
                                <td><strong>Testing Area</strong></td>
                                <td><strong>No. of questions</strong></td>
                                <td><strong>Duration</strong></td>
                              </tr>
                              @if(count($examPatterns) > 0)
                                @foreach($examPatterns as $examPattern)
                                  <tr>
                                    <td>{{$examPattern->testing_area}}</td>
                                    <td>{{$examPattern->no_of_question}}</td>
                                    <td>{{$examPattern->duration}}</td>
                                  </tr>
                                @endforeach
                              @endif
                            </tbody>
                          </table>
                          <br/>

                        <b>Aptitude:</b>{!! (is_object($placementProcess))?$placementProcess->aptitude_syllabus:NULL !!}
                      </div>
                    </div>
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">HR Interview</h4>
                      </div>
                      <div class="panel-body"> {!! (is_object($placementProcess))?$placementProcess->hr_questions:NULL !!}
                      </div>
                    </div>
                    <div class="panel panel-default">
                      <div class="panel-heading "> <h4 class="panel-title">Apply directly at</h4></div>
                      <div class="panel-body">
                      <a href="{!! (is_object($placementProcess))?$placementProcess->job_link:NULL !!}" target="_blank">Click Here </a>
                      </div>
                    </div>
                    <div class="panel panel-default">
                      <div class="panel-heading link"><a href="{!! (is_object($companyDetails))?$companyDetails->mock_test_link:NULL !!}">Sample Paper</a>
                      </div>
                    </div>
                    <span>NOTE</span> all information provide about BHEL is not directly come from BHEL . we put this information from net.
                    <br/><br/>
                    <!-- <div class="">
                      <div class=" replyComment sub-cmt" id="replyComment">
                        <form>
                          <div class="form-group">
                            <label for="comment">Your Comment</label>
                            <textarea name="comment" class="form-control" rows="4"></textarea>
                          </div>
                          <button type="submit" class="btn btn-default">Send</button>
                        </form>
                      </div>
                      <div class="">
                        <div class="box-body chat " id="chat-box">
                          <div class="item">
                              <img src="img/user/user1.png" alt="User Image" />
                              <div class="message">
                                <div class="dropdown pull-right">
                                  <button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                  </button>
                                  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a href="#">Delete</a></li>
                                    <li><a href="#">Edit</a></li>
                                  </ul>
                                </div>
                                <p class="more"><a href="" class="SubCommentName">Mike Doe</a>
                                  I would like to meet you to discuss the latest news about
                                  the arrival of the new theme. They say it is going to be one the
                                  best themes on the market</p>
                              </div>
                              <div class="comment-meta reply-1">
                                <span>
                                   <i id="like1" class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                                   <span id="like1-bs3"></span>
                                </span>
                                <span class="mrgn_5_left">
                                  <a href="#replyComment" role="button" >reply</a>
                                </span>
                                <span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> 2:15</span>
                              </div>
                          </div>
                          <div class="item replySubComment-1">
                            <img src="img/user/user1.png" alt="User Image" />
                            <div class="message">
                              <div class="dropdown pull-right">
                                <button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                  <li><a href="#">Delete</a></li>
                                  <li><a href="#">Edit</a></li>
                                </ul>
                              </div>
                              <p class="more"><a href="" class="SubCommentName">Namu</a>
                                reply to sub comment</p>
                            </div>
                            <div class="comment-meta reply-1">
                              <span>
                               <i id="like1" class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                               <span id="like1-bs3"></span>
                              </span>
                              <span class="mrgn_5_left">
                                 <a href="#replyComment" role="button" >reply</a>
                              </span>
                              <span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> 3hr</span>
                            </div>
                          </div>
                          <div class="item replySubComment-1">
                            <img src="img/user/user1.png" alt="User Image" />
                            <div class="message">
                              <div class="dropdown pull-right">
                                <button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu3">
                                  <li><a href="#">Delete</a></li>
                                  <li><a href="#">Edit</a></li>
                                </ul>
                              </div>
                              <p class="more"><a href="" class="SubCommentName">Sid</a>
                                reply to sub comment</p>
                            </div>
                            <div class="comment-meta reply-1">
                                <span>
                                 <i id="like1" class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                                 <span id="like1-bs3"></span>
                                </span>
                                <span class="mrgn_5_left">
                                  <a href="#replyComment" role="button" >reply</a>
                                </span>
                                <span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> 23hr</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> -->
                  </div>
                  <input type="hidden" name="user_id" id="user_id" value="{{ (is_object(Auth::user()))?Auth::user()->id:NULL}}">
                  <!-- end pp -->
                  <div id="dropdown1-tab" class="tab-pane fade ">
                      <div class="panel panel-default clickable">
                        @if(count($placementExperiances) > 0)
                          @foreach($placementExperiances as $placementExperiance)
                            <div class="panel panel-default container-fluid slideanim">
                              <div class="panel-heading row">
                                <p class="ellipsed"> <a class="uppercase v_p_heding " href="{{url('blogComment')}}/{{$placementExperiance->id}}" target="_blank"> {{$placementExperiance->title}}</a>
                                </p>
                                <figcaption class="blog-by">
                                  <span>
                                    @if(!empty($placementExperiance->user->photo))
                                      <img src="{{ asset($placementExperiance->user->photo)}} " class="img-circle" alt="User Image">
                                    @else
                                      <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
                                    @endif
                                  </span>
                                  <span class="username">{{$placementExperiance->user->name}}</span>
                                  <span class="date"><i class="fa fa-calendar-o"></i><span> {{ $placementExperiance->created_at->format('M d , Y') }}</span></span>
                                </figcaption>
                              </div>
                              <div class="panel-body mrgn_10_top_btm more">
                                {!! $placementExperiance->question !!}
                              </div>
                              <div class="panel-footer row">
                                <div class="col-xs-12">
                                  <i class="fa fa-comments" aria-hidden="true"><a href="{{url('placementExperiance')}}/{{$placementExperiance->id}}" target="_blank"> Leave a comment</a></i>
                                </div>
                              </div>
                            </div>
                          @endforeach
                        @else
                          No experiance is available.
                        @endif
                      </div>
                  </div>
                  <!--end Candidate Exp. -->
                  <div id="dropdown2-tab" class="tab-pane fade ">
                      <div class="row">
                          <div class="col-md-4">
                            <select id="area1" class="form-control mrgn_20_top_btm " name="area" data-toggle="tooltip" title="Area" onChange="selectCompany(this);" required>
                              <option value="0">Select Area</option>
                              @if(count($placementAreas) > 0)
                                @foreach($placementAreas as $placementArea)
                                  @if($selectedArea == $placementArea->id)
                                    <option value="{{$placementArea->id}}" selected="true">{{$placementArea->name}}</option>
                                  @else
                                    <option value="{{$placementArea->id}}">{{$placementArea->name}}</option>
                                  @endif
                                @endforeach
                              @endif
                            </select>
                          </div>
                          <div class="col-md-4">
                            <select id="company1" class="form-control mrgn_20_top_btm" name="company" data-toggle="tooltip" title="Company" required>
                              <option value="0">Select Company</option>
                              @if(count($placementCompanies) > 0)
                                @foreach($placementCompanies as $placementCompany)
                                  @if($selectedCompany == $placementCompany->id)
                                    <option value="{{$placementCompany->id}}" selected="true">{{$placementCompany->name}}</option>
                                  @else
                                    <option value="{{$placementCompany->id}}">{{$placementCompany->name}}</option>
                                  @endif
                                @endforeach
                              @endif
                            </select>
                          </div>
                      </div>
                      <div class="row padding_10_left_right">
                      <div class="widget-area   ">
                          <div class="status-upload">

                              <form action="{{url('createPlacementExperiance')}}" method="POST" id="createPost">
                                {{csrf_field()}}
                                 <div class="input-group">
                                    <span class="input-group-addon">Title</span>
                                    <input id="title" type="text" class="form-control" name="title" placeholder="Add Title Here">
                                  </div>
                                   <textarea name="question" placeholder="post here" type="text" id="question" required></textarea>
                                  <script type="text/javascript">
                                    CKEDITOR.replace( 'question', { enterMode: CKEDITOR.ENTER_BR } );
                                    CKEDITOR.config.width="100%";
                                    CKEDITOR.config.height="auto";
                                    CKEDITOR.on('dialogDefinition', function (ev) {

                                        var dialogName = ev.data.name,
                                            dialogDefinition = ev.data.definition;

                                        if (dialogName == 'image') {
                                            var onOk = dialogDefinition.onOk;

                                            dialogDefinition.onOk = function (e) {
                                                var width = this.getContentElement('info', 'txtWidth');
                                                width.setValue('100%');//Set Default Width

                                                var height = this.getContentElement('info', 'txtHeight');
                                                height.setValue('auto');////Set Default height

                                                onOk && onOk.apply(this, e);
                                            };
                                        }
                                    });
                                  </script>
                                  <input type="hidden" name="area" value="" id="post_area_id">
                                  <input type="hidden" name="company" value="" id="post_company_id">
                                  <button type="button" class="btn btn-success btn-circle text-uppercase" onclick=" confirmSubmit(this);" id="createPost" title="Share"><i class="fa fa-share"></i> Share</button>
                              </form>
                          </div>
                      </div>
                      </div>
                  </div>
                  <!--  -->
                  <div id="tab_3" class="tab-pane ">
                    @if(count($placementFaqs) > 0)
                      @foreach($placementFaqs as $placementFaq)
                        <div class="panel panel-default">
                          <div class="panel-heading clickable">
                            <h4 class="panel-title">{{$placementFaq->question}}</h4>
                            <span class="pull-right "><i class="fa fa-chevron-up" aria-hidden="true"></i></span>
                          </div>
                          <div class="panel-body">{!! $placementFaq->answer !!}</div>
                        </div>
                      @endforeach
                    @else
                      No faq available.
                    @endif
                  </div>
                  <!-- end FAQ -->
                   <div id="tab_4" class="tab-pane fade ">
                   <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">Mock Test</h4>
                      </div>
                      <div class="panel-body">
                       <p><b>Vchip-edu</b> provide Online mock tests with same pattern as company follow at totally free of cost</p>
                        <a href="{!! (is_object($companyDetails))?$companyDetails->mock_test_link:NULL !!}" class="btn btn-primary" target="_blank"> Start Test</a>
                      </div>
                    </div>
                  </div>
                  <!-- end Mock Test -->
                </div>
              </div>
            </div>
         </div>
      </div>
      <div class="col-sm-3 col-sm-pull-9">
        <div class=" hidden-div1">
          <select id="area2" class="form-control mrgn_20_btm" name="area" data-toggle="tooltip" title="Area" onChange="selectCompany(this);" required>
            <option value="0">Select Area</option>
            @if(count($placementAreas) > 0)
              @foreach($placementAreas as $placementArea)
                @if($selectedArea == $placementArea->id)
                  <option value="{{$placementArea->id}}" selected="true">{{$placementArea->name}}</option>
                @else
                  <option value="{{$placementArea->id}}">{{$placementArea->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
          <select id="company2" class="form-control mrgn_20_top_btm" name="company" data-toggle="tooltip" title="Company" onChange="getPlacementCompany(this);"  required>
            <option value="0">Select Company</option>
            @if(count($placementCompanies) > 0)
              @foreach($placementCompanies as $placementCompany)
                @if($selectedCompany == $placementCompany->id)
                  <option value="{{$placementCompany->id}}" selected="true">{{$placementCompany->name}}</option>
                @else
                  <option value="{{$placementCompany->id}}">{{$placementCompany->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
        </div>
        <div class="advertisement-area">
            <span class="pull sponsored"><a href=""> Sponsored</a></span>
            <span class="pull-right create-add"><a href=""> Create Advert</a></span>
        </div>
        <br/>
        <div class="add-1">
          <div class="course-box advertisement">
           <a href="http://www.gatethedirection.com/" target="_blank"  title="Gate The Direction">
           <div class="caption">
                <p class=" btn btn-default">View Details</p>
            </div>
            </a>
            <a class="img-course-box" href="v-kit-project.html">
              <img src="{{ asset('images/logo/gatetheDirection.png') }}" alt="Gate The Direction"  class="img-responsive" />
            </a>
            <div class="course-box-content">
              <h4 class="course-box-title" title="GATE THE DIRECTION" data-toggle="tooltip" data-placement="bottom">
                <a href="http://localhost/EDUCATION/final-website(php)/gate/index.php"  class="add-tital ellipsed">GATE THE DIRECTION</a>
              </h4>
              <p class="ellipsed"> "Started by IITain so you become IITain"</p>
            </div>
          </div>
         </div>
        <div class="add-2">
          <div class="course-box advertisement">
            <a href="http://kaizenn.org/" target="_blank"  title="kaizen coaching classes">
            <div class="caption">
                <p class=" btn btn-default">View Details</p>
            </div>
            </a>
            <a class="img-course-box" href="http://kaizenn.org/">
              <img src="{{ asset('images/logo/kaizen.jpg') }}" alt="Kaizen classes"  class="img-responsive" />
            </a>
            <div class="course-box-content">
              <h4 class="course-box-title" title="kaizen coaching classes" data-toggle="tooltip" data-placement="bottom">
                <a href="http://kaizenn.org/" class="add-tital ellipsed">KAIZEN COACHING CLASSES</a>
              </h4>
              <p class="ellipsed"> "Leading the success in "Banking Exams""</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <input type="hidden" id="current_date" name="current_date" value="{{ date('Y-m-d') }}">
  <form id="company_form" action="{{url('placements')}}" method="POST" style="display: none;">
      {{ csrf_field() }}
      <input type="hidden" id="company_id" name="company_id" value="">
  </form>
</section>
@stop
@section('footer')
	@include('footer.footer')
  <script type="text/javascript" src="{{ asset('js/togleForFilterBy.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/scrolling-nav.js')}}"></script>
  <script type="text/javascript">
    $( document ).ready(function() {
      $("[rel='tooltip']").tooltip();

      $('.advertisement').hover(
          function(){
              $(this).find('.caption').slideDown(250); //.fadeIn(250)
          },
          function(){
              $(this).find('.caption').slideUp(250); //.fadeOut(205)
          }
      );

      var showChar = 400;
      var ellipsestext = "...";
      var moretext = "Read more";
      var lesstext = "less";
      $('.more').each(function() {
        var content = $(this).html();

        if(content.length > showChar) {

          var c = content.substr(0, showChar);
          var h = content.substr(0, content.length);
          var html = '<div class="zxc">'+ c + '<span style="color:#01bafd; margin-left:5px;">' + ellipsestext+ '</span><br /><a href="" class="morelink" style="color:#01bafd";>' + moretext + '</a></div><div class="zxc1" style="display:none;">'+ h + '<br /><a href="" class="morelink1" style="color:#01bafd";>' + lesstext + '</a></div>';

          $(this).html(html);
        }

      });

      $(".morelink").click(function(){
        $(this).closest('.zxc').toggle();
        $(this).closest('.zxc').siblings('.zxc1').toggle();
        return false;
      });
      $(".morelink1").click(function(){
        $(this).closest('.zxc1').toggle();
        $(this).closest('.zxc1').siblings('.zxc').toggle();
        return false;
      });

  });
  </script>
<script type="text/javascript">

 function confirmSubmit(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var areaId = parseInt(document.getElementById('area1').value);
    var companyId = parseInt(document.getElementById('company1').value);
    var questionLength = CKEDITOR.instances.question.getData().length;
// alert(questionLength);
    if(0 < userId && 0 < areaId && 0 < companyId && questionLength > 0){
        var area = document.getElementById('post_area_id');
        area.value= areaId;
        var company = document.getElementById('post_company_id');
        company.value= companyId;
        formId = $(ele).attr('id');
        form = document.getElementById(formId);
        form.submit();
    } else if( isNaN(userId)) {
      $.confirm({
        title: 'Confirmation',
        content: 'Please login first. Click "Ok" button to login.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    window.location="{{url('/home')}}";
                  }
              },
              Cancle: function () {
              }
          }
        });
    }else if( areaId == 0)  {
      $.alert({
        title: 'Alert!',
        content: 'Please select area.',
      });
    }else if( companyId == 0 ) {
      $.alert({
        title: 'Alert!',
        content: 'Please select company.',
      });
    } else if( questionLength == 0){
      $.alert({
        title: 'Alert!',
        content: 'Please enter something in a question. ',
      });
    }
  }
  function getPlacementCompany(ele){
    company = parseInt($(ele).val());
    document.getElementById('company_id').value = company;
    document.getElementById('company_form').submit();
  }

  function selectCompany(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getPlacementCompaniesByAreaForFront')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('company');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = 0;
        opt.innerHTML = 'Select Company';
        select.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
        select1 = document.getElementById('company1');
        select1.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = 0;
        opt.innerHTML = 'Select Company';
        select1.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select1.appendChild(opt);
          });
        }
        select2 = document.getElementById('company2');
        select2.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = 0;
        opt.innerHTML = 'Select Company';
        select2.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select2.appendChild(opt);
          });
        }
      });
    }
  }
</script>
@stop