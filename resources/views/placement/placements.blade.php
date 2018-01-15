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
.ckeditor-list-style ul{
  list-style: none !important;

}
.ckeditor-list-style ul li:before{
  content: "\f192" !important;
  font-family: FontAwesome !important;
  display: inline-block;
  font-size: 20px;
  color: #339999;
  margin-left: -20px;
  margin-right: 5px;
  width: 20px;
}

table {
  width: 100%;
  border-collapse: collapse;
}
/* Zebra striping */
tr:nth-of-type(odd) {
  background: #eee;
}
th {
  background: #333;
  color: white;
  font-weight: bold;
}
td, th {
  padding: 6px;
  border: 1px solid #ccc;
  text-align: left;
}
/*
Max width before this PARTICULAR table gets nasty
This query will take effect for any screen smaller than 760px
and also iPads specifically.
*/
@media
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {

  /* Force table to not be like tables anymore */
  table, thead, tbody, th, td, tr {
    display: block;
  }

  /* Hide table headers (but not display: none;, for accessibility) */
  thead tr {
    position: absolute;
    top: -9999px;
    left: -9999px;
  }

  tr { border: 1px solid #ccc; }

  td {
    /* Behave  like a "row" */
    border: none;
    border-bottom: 1px solid #eee;
    position: relative;
    padding-left: 5%;
  }
.name{text-align: center;
font-weight: bold;}
  td:before {
    /* Now like a table header */
    position: absolute;
    /* Top/left values mimic padding */
    top: 6px;
    left: 6px;
    width: 45%;
    padding-right: 10px;
    white-space: nowrap;
  }


/*student record*/
/*  #student-record td:nth-of-type(1):before { content: "Company Name :";  font-weight: bolder;}
  #student-record td:nth-of-type(2):before { content: "Job Description :"; font-weight: bolder;}
  #student-record td:nth-of-type(3):before { content: "Mock Test :"; font-weight: bolder;}
  #student-record td:nth-of-type(4):before { content: "Apply :"; font-weight: bolder;}*/
}


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
ul.table_list{ margin-left: -10px; }
@media(max-width: 768px){
ul.table_list{ margin-left: -30px; }
}
  .modal-header h2{font-size: 15px; font-weight: bold; color:#e91e63; }
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
          <select id="area" class="form-control mrgn_20_btm" name="area" data-toggle="tooltip" title="Area" onChange="selectCompany(this);" required>
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
      <div class="col-sm-9 col-sm-push-3 data" id="placement-box">
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
                    <div class=""> Apply Job </div>
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
                      <div class="panel-body ckeditor-list-style">
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
                        <p><b>Official Website :</b> {{ (is_object($companyDetails))?$companyDetails->website:NULL}}</p>
                      </div>
                    </div>
                  </div>
                   <!-- end about -->
                  <div id="tab_2" class="tab-pane fade ">
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">Selection Process:</h4>
                      </div>
                      <div class="panel-body ckeditor-list-style">
                       <p >{!! (is_object($placementProcess))?$placementProcess->selection_process:NULL !!}</p>
                      </div>
                    </div>
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">Academic Criteria:</h4>
                      </div>
                      <div class="panel-body ckeditor-list-style">{!! (is_object($placementProcess))?$placementProcess->academic_criteria:NULL !!}
                      </div>
                    </div>
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">Pattern of Written Exam:</h4>
                      </div>
                      <div class="panel-body ckeditor-list-style">
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
                      <div class="panel-body ckeditor-list-style"> {!! (is_object($placementProcess))?$placementProcess->hr_questions:NULL !!}
                      </div>
                    </div>
                    <div class="panel panel-default">
                      <div class="panel-heading "> <h4 class="panel-title">Apply directly at</h4></div>
                      <div class="panel-body ckeditor-list-style">
                      <a href="{!! (is_object($placementProcess))?$placementProcess->job_link:NULL !!}" target="_blank">Click Here </a>
                      </div>
                    </div>
                    <div class="panel panel-default">
                      <div class="panel-heading link"><a href="{!! (is_object($companyDetails))?$companyDetails->mock_test_link:NULL !!}">Sample Paper</a>
                      </div>
                    </div>
                    <span>NOTE: </span> all information provide about {{ (is_object($companyDetails))?$companyDetails->company->name:'company'}} is not directly come from {{ (is_object($companyDetails))?$companyDetails->company->name:'company'}} . we put this information from net.
                    <br/><br/>
                    <div class="">
                      <div class=" with-nav-tabs">
                        <div class="">
                          <div class="comment-meta">
                            @if(is_object($companyDetails))
                            <span id="like_{{$companyDetails->placement_company_id}}" class="first-like">
                              @if( isset($likesCount[$companyDetails->placement_company_id]) && isset($likesCount[$companyDetails->placement_company_id]['user_id'][$currentUser]))
                                   <i id="company_like_{{$companyDetails->placement_company_id}}" data-company_id="{{$companyDetails->placement_company_id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"> Like </i>
                                   <span id="like1-bs3">{{count($likesCount[$companyDetails->placement_company_id]['like_id'])}}</span>
                              @else
                                   <i id="company_like_{{$companyDetails->placement_company_id}}" data-company_id="{{$companyDetails->placement_company_id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"> Like </i>
                                   <span id="like1-bs3">@if( isset($likesCount[$companyDetails->placement_company_id])) {{count($likesCount[$companyDetails->placement_company_id]['like_id'])}} @endif</span>
                              @endif
                            </span>
                            @endif
                            <span class="mrgn_5_left">
                            <i class="fa fa-comment-o" aria-hidden="true"></i>
                              @if(is_object(Auth::user()))
                                <a class="your-cmt" role="button" data-toggle="collapse" href="#replyToEpisode{{(is_object($companyDetails))?$companyDetails->id:NULL}}" aria-expanded="false" aria-controls="collapseExample">Comment</a>
                              @else
                                <a class="your-cmt" role="button" data-toggle="modal" data-placement="bottom" href="#loginUserModel">Comment</a>
                              @endif
                            </span>
                             <hr />
                            <div class="collapse replyComment" id="replyToEpisode{{(is_object($companyDetails))?$companyDetails->id:NULL}}" >
                                <div class="form-group">
                                  <textarea name="comment" id="comment" placeholder="Comment here.." class="form-control" rows="7"></textarea>
                                </div>
                                <input type="hidden" id="company_id" name="company_id" value="{{(is_object($companyDetails))?$companyDetails->placement_company_id:NULL}}">
                                <button type="button" id="replyToEpisode{{(is_object($companyDetails))?$companyDetails->id:NULL}}" class="btn btn-default" onclick="confirmPlacementProcessComment(this);" title="Send" >
                                  <span class="hidden-lg fa fa-share" aria-hidden="true"></span>
                                  <div class="hidden-sm">Send</div>
                                </button>
                                <button type="button" class="btn btn-default" data-id="replyToEpisode{{(is_object($companyDetails))?$companyDetails->id:NULL}}" onclick="cancleReply(this);" title="Cancle">
                                  <span class="hidden-lg fa fa-times-circle" aria-hidden="true"></span>
                                  <div class="hidden-sm">Cancle</div>
                                </button>
                            </div>
                          </div>
                        </div>
                        <div class="panel-body">
                          <div class="tab-content">
                            <div class="tab-pane fade in active" id="questions" style="padding: 15px !important;">
                              <div class="post-comments ">
                                <div class="row">
                                   <div class=" ">
                                    <div class="box-body chat" id="chat-box">
                                      @if(count( $comments) > 0)
                                        @foreach($comments as $comment)
                                          <div class="item" id="showComment_{{$comment->id}}">
                                            @if(!empty($comment->user->photo))
                                              <img src="{{ asset($comment->user->photo)}} " class="img-circle" alt="User Image">
                                            @else
                                              <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
                                            @endif
                                            <div class="message">
                                              @if(is_object(Auth::user()) && (Auth::user()->id == $comment->user_id))
                                              <div class="dropdown pull-right">
                                                <button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                  @if(Auth::user()->id == $comment->user_id)
                                                    <li><a id="{{$comment->id}}" onclick="confirmCommentDelete(this);" data-comment_id="{{$comment->id}}" data-company_id="{{$companyDetails->placement_company_id}}">Delete</a></li>
                                                  @endif
                                                  @if(Auth::user()->id == $comment->user_id)
                                                    <li><a id="{{$comment->id}}" onclick="editComment(this);">Edit</a></li>
                                                  @endif
                                                </ul>
                                              </div>
                                              @endif
                                                <a class="SubCommentName">{{ $comment->user->name }}</a>
                                                <div class="more" id="editCommentHide_{{$comment->id}}">{!! $comment->body !!}</div>
                                                  <div class="form-group hide" id="editCommentShow_{{$comment->id}}" >
                                                    <textarea class="form-control" name="comment" id="comment_{{$comment->id}}" rows="3" >{!! $comment->body !!}</textarea>
                                                    <button class="btn btn-primary" onclick="updateComment(this);" data-comment_id="{{$comment->id}}" data-company_id="{{$companyDetails->placement_company_id}}">Update</button>
                                                    <button type="button" class="btn btn-default" id="{{$comment->id}}" onclick="cancleComment(this);">Cancle</button>
                                                  </div>
                                              </div>
                                              <div class="comment-meta reply-1">
                                                <span id="cmt_like_{{$comment->id}}" >
                                                  @if( isset($commentLikesCount[$comment->id]) && isset($commentLikesCount[$comment->id]['user_id'][$currentUser]))
                                                       <i id="comment_like_{{$comment->id}}" data-company_id="{{$companyDetails->placement_company_id}}" data-comment_id="{{$comment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                                                       <span id="like1-bs3">{{count($commentLikesCount[$comment->id]['like_id'])}}</span>
                                                  @else
                                                       <i id="comment_like_{{$comment->id}}" data-company_id="{{$companyDetails->placement_company_id}}" data-comment_id="{{$comment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                                                       <span id="like1-bs3">@if( isset($commentLikesCount[$comment->id])) {{count($commentLikesCount[$comment->id]['like_id'])}} @endif</span>
                                                  @endif
                                                </span>
                                               <span class="mrgn_5_left">
                                                @if(is_object(Auth::user()))
                                                  <a class="" role="button" data-toggle="collapse" href="#replyToComment{{$comment->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
                                                @else
                                                  <a role="button" data-toggle="modal" data-placement="bottom" href="#loginUserModel">reply</a>
                                                @endif
                                              </span>
                                              <span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> {{$comment->updated_at->diffForHumans()}}</span>
                                              <div class="collapse replyComment" id="replyToComment{{$comment->id}}">
                                                  <div class="form-group">
                                                    <label for="subcomment">Your Sub Comment</label>
                                                      <textarea name="subcomment" id="subcomment_{{$companyDetails->placement_company_id}}_{{$comment->id}}" class="form-control" rows="3"></textarea>
                                                  </div>
                                                  <button class="btn btn-default" onclick="confirmSubmitReplytoComment(this);" data-comment_id="{{$comment->id}}" data-company_id="{{$companyDetails->placement_company_id}}">Send</button>
                                                  <button type="button" class="btn btn-default" data-id="replyToComment{{$comment->id}}" onclick="cancleReply(this);">Cancle</button>
                                              </div>
                                            </div>
                                          </div>
                                          @if(count( $comment->children ) > 0)
                                            @include('placement.comments', ['comments' => $comment->children, 'parent' => $comment->id, 'companyId' => $companyDetails->placement_company_id])
                                          @endif
                                        @endforeach
                                      @endif
                                    </div>
                                  </div>
                              </div>
                            </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <input type="hidden" name="user_id" id="user_id" value="{{ (is_object(Auth::user()))?Auth::user()->id:NULL}}">
                  <!-- end pp -->
                  <div id="dropdown1-tab" class="tab-pane fade ">
                      <div class="panel panel-default clickable">
                        @if(count($placementExperiances) > 0)
                          @foreach($placementExperiances as $placementExperiance)
                            <div class="panel panel-default container-fluid slideanim">
                              <div class="panel-heading row">
                                <p class="ellipsed"> <a class="uppercase v_p_heding " href="{{url('placementExperiance')}}/{{$placementExperiance->id}}" target="_blank"> {{$placementExperiance->title}}</a>
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
                              <div class="panel-body mrgn_10_top_btm more ckeditor-list-style">
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
                                  <input type="hidden" name="company_id" value="" id="post_company_id">
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
                          <div class="panel-heading clickable collapsed" data-toggle="collapse" data-target="#post{{$placementFaq->id}}" >
                            <h4 class="panel-title">{{$placementFaq->question}}
                            </h4>
                          </div>
                          <div class="cmt-parent panel-collapse collapse" id="post{{$placementFaq->id}}">{!! $placementFaq->answer !!}</div>
                        </div>
                      @endforeach
                    @else
                      No faq available.
                    @endif
                  </div>
                  <!-- end FAQ -->
                  <div id="tab_4" class="tab-pane fade ">
                     <div class="panel panel-default">
                        <div class="panel-body" style="padding: 0px;">
                          <table  class="" id="student-record">
                            <thead>
                              <tr>
                                <th>Company Name</th>
                                <th>Job Description</th>
                                <th>Mock Test</th>
                                <th>Apply</th>
                              </tr>
                            </thead>
                            <tbody>
                              @if(count($applyJobs) > 0)
                                @foreach($applyJobs as $applyJob)
                                  <tr>
                                    <td class="name"><b>{{ $applyJob->company }}</b></td>
                                    <td> {!! mb_strimwidth( $applyJob->job_description , 0, 400, '...') !!}</br>
                                         <a type="button" class="btn btn-info btn-circle btn-xs" title="Read" data-toggle="modal" data-placement="bottom" href="#company_{{$applyJob->id }}">Read More</a>
                                    </td>
                                    <td>
                                      <a class="btn btn-primary btn-xs delet-bt delet-btn" href="{{ $applyJob->mock_test }}">Mock Test</a>
                                    </td>
                                    <td>
                                     <a class="btn btn-primary btn-xs delet-bt delet-btn" href="{{ $applyJob->job_url }}" target="_blank">Apply</a>
                                    </td>
                                  </tr>
                                @endforeach
                              @else
                                <tr><td colspan="4">No data available.</td></tr>
                              @endif
                            </tbody>
                          </table>
                        </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
         </div>
      </div>
      @if(count($applyJobs) > 0)
        @foreach($applyJobs as $applyJob)
          <div id="company_{{ $applyJob->id }}" class="modal fade" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content" style="background-color: white;">
                <div class="modal-header">
                  <button class="close" data-dismiss="modal">×</button>
                  <h2  class="modal-title">{{ $applyJob->company }}</h2>
                </div>
                <div class="modal-body">{!! $applyJob->job_description !!}</div>
              </div>
            </div>
          </div>
        @endforeach
      @endif
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
        <!-- <div class="advertisement-area">
            <span class="pull-right create-add"><a href="{{ url('createAd') }}"> Create Ad</a></span>
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
        </div> -->
        <div class="advertisement-area">
            <span class="pull-right create-add"><a href="{{ url('createAd') }}"> Create Ad</a></span>
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
                  <a class="img-course-box" href="http://ssgmce.org/Default.aspx?ReturnUrl=%2f" target="_blank">
                    <img src="{{ asset('images/logo/ssgmce-logo.jpg') }}" alt="SSGMCE"  class="img-responsive" />
                  </a>
                  <div class="course-box-content">
                    <h4 class="course-box-title" title="SSGMCE" data-toggle="tooltip" data-placement="bottom">
                      <a href="http://ssgmce.org/Default.aspx?ReturnUrl=%2f" target="_blank">SSGMCE</a>
                    </h4>
                    <p class="more"> SSGMCE</p>
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
  <input type="hidden" id="current_date" name="current_date" value="{{ date('Y-m-d') }}">
  <form id="company_form" action="{{url('placements')}}" method="POST" style="display: none;">
      {{ csrf_field() }}
      <input type="hidden" id="area_company_id" name="company_id" value="">
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
      showMore();
    });
  </script>
<script type="text/javascript">
  $(document).on("click", "i[id^=company_like_]", function(e) {
      var companyId = $(this).data('company_id');
      var dislike = $(this).data('dislike');
      var userId = parseInt(document.getElementById('user_id').value);
      if( isNaN(userId)) {
        $('#loginUserModel').modal();
      } else {
        $.ajax({
            method: "POST",
            url: "{{url('likePlacementProcess')}}",
            data: {company_id:companyId, dis_like:dislike}
        })
        .done(function( msg ) {
          if( 'false' != msg ){
            var likeSpan = document.getElementById('like_'+companyId);
            likeSpan.innerHTML = '';
            if( 1 == dislike ){
              likeSpan.innerHTML +='<i id="company_like_'+companyId+'" data-company_id="'+companyId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"> Like </i>';
              likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
            } else {
              likeSpan.innerHTML +='<i id="company_like_'+companyId+'" data-company_id="'+companyId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"> Like </i>';
              likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
            }
          }
        });
      }
  });

  $(document).on("click", "i[id^=sub_comment_like_]", function(e) {
      var companyId = $(this).data('company_id');
      var commentId = $(this).data('comment_id');
      var subCommentId = $(this).data('sub_comment_id');
      var dislike = $(this).data('dislike');
      var userId = parseInt(document.getElementById('user_id').value);
       if( isNaN(userId)) {
        $('#loginUserModel').modal();
      } else {
        $.ajax({
            method: "POST",
            url: "{{url('likePlacementProcessSubComment')}}",
            data: {company_id:companyId, comment_id:commentId, sub_comment_id:subCommentId, dis_like:dislike}
        })
        .done(function( msg ) {
          if( 'false' != msg ){
              var likeSpan = document.getElementById('sub_cmt_like_'+subCommentId);
              likeSpan.innerHTML = '';
              if( 1 == dislike ){
                likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-company_id="'+companyId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              } else {
                likeSpan.innerHTML +='<i id="sub_comment_like_'+subCommentId+'" data-company_id="'+companyId+'" data-comment_id="'+commentId+'" data-sub_comment_id="'+subCommentId+'"  data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              }
        }
        });
      }
  });

  $(document).on("click", "i[id^=comment_like_]", function(e) {
      var companyId = $(this).data('company_id');
      var commentId = $(this).data('comment_id');
      var dislike = $(this).data('dislike');
      var userId = parseInt(document.getElementById('user_id').value);
      if( isNaN(userId)) {
        $('#loginUserModel').modal();
      } else {
        $.ajax({
            method: "POST",
            url: "{{url('likePlacementProcessComment')}}",
            data: {company_id:companyId, comment_id:commentId, dis_like:dislike}
        })
        .done(function( msg ) {
          if( 'false' != msg ){
              var likeSpan = document.getElementById('cmt_like_'+commentId);
              likeSpan.innerHTML = '';
              if( 1 == dislike ){
                likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-company_id="'+companyId+'" data-comment_id="'+commentId+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like" style= "margin-right:5px;"></i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              } else {
                likeSpan.innerHTML +='<i id="comment_like_'+commentId+'" data-company_id="'+companyId+'" data-comment_id="'+commentId+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like" style= "margin-right:5px;"></i>';
                likeSpan.innerHTML +='<span id="like1-bs3">'+ msg.length +'</span>';
              }
        }
        });
      }
  });

  function confirmCommentDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this comment, all sub comments of this comment will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var commentId = $(ele).data('comment_id');
                    var companyId = $(ele).data('company_id');
                    var userId = parseInt(document.getElementById('user_id').value);
                    $.ajax({
                        method: "POST",
                        url: "{{url('deletePlacementProcessComment')}}",
                        data: {company_id:companyId, comment_id:commentId}
                    })
                    .done(function( msg ) {
                      renderComments(msg, userId);
                    });
                  }
              },
              Cancle: function () {
              }
          }
        });
  }

  function confirmSubCommentDelete(ele){
    $.confirm({
        title: 'Confirmation',
        content: 'You want to delete this comment?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var commentId = $(ele).data('comment_id');
                    var subcommentId = $(ele).data('subcomment_id');
                    var companyId = $(ele).data('company_id');
                    var userId = parseInt(document.getElementById('user_id').value);
                    $.ajax({
                        method: "POST",
                        url: "{{url('deletePlacementProcessSubComment')}}",
                        data: {company_id:companyId, comment_id:commentId, subcomment_id:subcommentId}
                    })
                    .done(function( msg ) {
                      renderComments(msg, userId);
                    });
                  }
              },
              Cancle: function () {
              }
          }
        });
  }

  function updateComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var commentId = $(ele).data('comment_id');
    var companyId = $(ele).data('company_id');
    var comment = document.getElementById('comment_'+commentId).value;
    $.ajax({
        method: "POST",
        url: "{{url('updatePlacementProcessComment')}}",
        data: {company_id:companyId, comment_id:commentId, comment:comment}
    })
    .done(function( msg ) {
      renderComments(msg, userId);
    });

  }

  function updatePlacementProcessSubComment(ele){
      var commentId = $(ele).data('comment_id');
      var companyId = $(ele).data('company_id');
      var subcommentId = $(ele).data('subcomment_id');
      var subcomment = document.getElementById('updateSubComment_'+subcommentId).value;
      var userId = parseInt(document.getElementById('user_id').value);
      $.ajax({
          method: "POST",
          url: "{{url('updatePlacementProcessSubComment')}}",
          data: {company_id:companyId, comment_id:commentId, subcomment_id:subcommentId, subcomment:subcomment}
      })
      .done(function( msg ) {
        renderComments(msg, userId);
      });
  }

  function renderComments(msg, userId){
    var chatDiv = document.getElementById('chat-box');
    chatDiv.innerHTML = '';
    var commentLikesCount = msg['commentLikesCount'];
    var subcommentLikesCount = msg['subcommentLikesCount'];
    arrayComments = [];
    $.each(msg['comments'], function(idx, obj) {
      arrayComments[idx] = obj;
    });
    var sortedArray = arrayComments.reverse();
    $.each(sortedArray, function(idx, obj) {
      if(false == $.isEmptyObject(obj)){
        var mainCommentDiv = document.createElement('div');
        mainCommentDiv.className = 'item';
        mainCommentDiv.id = 'showComment_'+obj.id;

        var commentImage = document.createElement('img');
        if(obj.user_image){
          var imageUrl =  "{{ asset('') }}"+obj.user_image;
        } else {
          var imageUrl = "{{ asset('images/user1.png') }}";
        }
        commentImage.setAttribute('src',imageUrl);
        mainCommentDiv.appendChild(commentImage);

        var commentMessageDiv = document.createElement('div');
        commentMessageDiv.className = 'message';
        if( userId == obj.user_id ){
          var commentEditDeleteDiv = document.createElement('div');
          commentEditDeleteDiv.className = 'dropdown pull-right';
          editDeleteInnerHtml = '<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>';
          editDeleteInnerHtml += '<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';
          if( userId == obj.user_id ){
            editDeleteInnerHtml += '<li><a id="'+obj.id+'" data-comment_id="'+obj.id+'" data-company_id="'+obj.company_id+'" onclick="confirmCommentDelete(this);">Delete</a></li>';
          }
          if( userId == obj.user_id ){
            editDeleteInnerHtml += '<li><a id="'+obj.id+'" onclick="editComment(this);">Edit</a></li>';
          }
          editDeleteInnerHtml += '</ul>';
          commentEditDeleteDiv.innerHTML = editDeleteInnerHtml;
          commentMessageDiv.appendChild(commentEditDeleteDiv);
        }

        var ancUserNameDiv = document.createElement('a');
        ancUserNameDiv.className = 'SubCommentName';
        ancUserNameDiv.innerHTML = obj.user_name;
        commentMessageDiv.appendChild(ancUserNameDiv);

        var pCommentBodyDiv = document.createElement('p');
        pCommentBodyDiv.className = 'more';
        pCommentBodyDiv.id = 'editCommentHide_'+obj.id;
        pCommentBodyDiv.innerHTML = obj.body; //'{!! '+obj.body+' !!}';
        commentMessageDiv.appendChild(pCommentBodyDiv);

        var divUpdateComment = document.createElement('div');
        divUpdateComment.className = 'form-group hide';
        divUpdateComment.id = 'editCommentShow_'+obj.id;
        divUpdateComment.innerHTML = '<textarea class="form-control" name="comment" id="comment'+ obj.id +'" rows="3">'+ obj.body+'</textarea><button class="btn btn-primary" data-comment_id="'+ obj.id +'" data-company_id="'+ obj.company_id +'" onclick="updateComment(this);">Update</button><button type="button" class="btn btn-default" id="'+ obj.id +'" onclick="cancleComment(this);">Cancle</button>';
        commentMessageDiv.appendChild(divUpdateComment);
        mainCommentDiv.appendChild(commentMessageDiv);

        var commentReplyDiv = document.createElement('div');
        commentReplyDiv.className = 'comment-meta reply-1';

        var spanCommenReply = document.createElement('span');
        spanCommenReply.id = 'cmt_like_'+obj.id;
        var spanCommenInnerHtml = '';
        if( commentLikesCount[obj.id] && commentLikesCount[obj.id]['user_id'][userId]){
          spanCommenInnerHtml +='<i id="comment_like_'+obj.id+'" data-company_id="'+obj.company_id+'" data-comment_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
          spanCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(commentLikesCount[obj.id]['like_id']).length +'</span>';
        } else {
          spanCommenInnerHtml +='<i id="comment_like_'+obj.id+'" data-company_id="'+obj.company_id+'" data-comment_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
          if(commentLikesCount[obj.id]){
            spanCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(commentLikesCount[obj.id]['like_id']).length +'</span>';
          }
        }
        spanCommenReply.innerHTML = spanCommenInnerHtml;
        commentReplyDiv.appendChild(spanCommenReply);

        var spanCommenReplyButton = document.createElement('span');
        spanCommenReplyButton.className = 'mrgn_5_left';
        spanCommenReplyButton.innerHTML = '<a class="" role="button" data-toggle="collapse" href="#replyToComment'+obj.id+'" aria-expanded="false" aria-controls="collapseExample">reply</a>';
        commentReplyDiv.appendChild(spanCommenReplyButton);

        var spanCommenReplyDate = document.createElement('span');
        spanCommenReplyDate.className = 'text-muted time-of-reply';
        spanCommenReplyDate.innerHTML = '<i class="fa fa-clock-o"></i>'+ obj.updated_at;
        commentReplyDiv.appendChild(spanCommenReplyDate);

        var subCommenDiv = document.createElement('div');
        subCommenDiv.className = 'collapse replyComment';
        subCommenDiv.id = 'replyToComment'+obj.id;
        subCommenDiv.innerHTML = '<div class="form-group"><label for="subcomment">Your Sub Comment</label><textarea name="subcomment" class="form-control" rows="3"  id="subcomment_'+obj.company_id+'_'+obj.id+'" ></textarea></div><button type="button" class="btn btn-default" onclick="confirmSubmitReplytoComment(this);" data-comment_id="'+obj.id+'" data-company_id="'+obj.company_id+'" >Send</button>';
        commentReplyDiv.appendChild(subCommenDiv);
        mainCommentDiv.appendChild(commentReplyDiv);
        chatDiv.appendChild(mainCommentDiv);
        if( obj['subcomments'] ){
          if(false == $.isEmptyObject(obj['subcomments'])){
            var commentUserId = obj.user_id;
            showSubComments(obj['subcomments'], chatDiv, subcommentLikesCount, userId, commentUserId);
          }
        }
      }
    });
    showMore();
  }

  function showSubComments(subcomments, commentchatDiv, subcommentLikesCount, userId, commentUserId){
    if(false == $.isEmptyObject(subcomments)){
      arraySubComments = [];
      $.each(subcomments, function(idx, obj) {
        arraySubComments[idx] = obj;
      });
      var sortedArray = arraySubComments.reverse();
      $.each(sortedArray, function(idx, obj) {
        if(false == $.isEmptyObject(obj)){
          var mainSubCommentDiv = document.createElement('div');
          mainSubCommentDiv.className = 'item replySubComment-1';

          var subcommentImage = document.createElement('img');
          if(obj.user_image){
            var subcommentImageUrl = "{{ asset('') }}"+obj.user_image;
          } else {
            var subcommentImageUrl = "{{ asset('images/user1.png') }}";
          }
          subcommentImage.setAttribute('src',subcommentImageUrl);
          mainSubCommentDiv.appendChild(subcommentImage);

          var subCommentMessageDiv = document.createElement('div');
          subCommentMessageDiv.className = 'message';
          subCommentMessageDiv.id = 'subcomment_'+obj.id;
          if( userId == obj.user_id || userId == commentUserId){
            var subcommentEditDeleteDiv = document.createElement('div');
            subcommentEditDeleteDiv.className = 'dropdown pull-right';
            editDeleteInnerHtml = '<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>';
            editDeleteInnerHtml += '<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">';
            if(  userId == obj.user_id || userId == commentUserId){
              editDeleteInnerHtml += '<li><a id="'+obj.placement_process_comment_id+'_'+obj.id+'" onclick="confirmSubCommentDelete(this);"  data-subcomment_id="'+obj.id+'" data-comment_id="'+obj.placement_process_comment_id+'" data-company_id="'+obj.company_id+'">Delete</a></li>';
            }
            if( userId == obj.user_id ){
              editDeleteInnerHtml += '<li><a id="'+obj.id+'" onclick="editSubComment(this);">Edit</a></li>';
            }
            editDeleteInnerHtml += '</ul>';
            subcommentEditDeleteDiv.innerHTML = editDeleteInnerHtml;
            subCommentMessageDiv.appendChild(subcommentEditDeleteDiv);
          }

          var pSubcommentBodyDiv = document.createElement('p');
          var ancUserNameDiv = document.createElement('a');
          ancUserNameDiv.className = 'SubCommentName';
          ancUserNameDiv.innerHTML = obj.user_name;
          pSubcommentBodyDiv.appendChild(ancUserNameDiv);

          var spanSubCommentBodyDiv = document.createElement('span');
          spanSubCommentBodyDiv.className = 'more';
          spanSubCommentBodyDiv.id = 'editSubCommentHide_'+obj.id;
          spanSubCommentBodyDiv.innerHTML = obj.body; //'{!! '+obj.body+' !!}';
          pSubcommentBodyDiv.appendChild(spanSubCommentBodyDiv);
          subCommentMessageDiv.appendChild(pSubcommentBodyDiv);

          var divUpdateSubComment = document.createElement('div');
          divUpdateSubComment.className = 'form-group hide';
          divUpdateSubComment.id = 'editSubCommentShow_'+obj.id;

          divUpdateSubComment.innerHTML = '<textarea class="form-control" name="comment" id="updateSubComment_'+ obj.id +'" rows="3">'+ obj.body+'</textarea><button class="btn btn-primary"  data-subcomment_id="'+ obj.id +'" data-comment_id="'+ obj.placement_process_comment_id +'" data-company_id="'+ obj.company_id +'" onclick="updatePlacementProcessSubComment(this);">Update</button><button type="button" class="btn btn-default" id="'+ obj.id +'" onclick="cancleSubComment(this);">Cancle</button></div></form>';
          subCommentMessageDiv.appendChild(divUpdateSubComment);
          mainSubCommentDiv.appendChild(subCommentMessageDiv);

          var subcommentReplyDiv = document.createElement('div');
          subcommentReplyDiv.className = 'comment-meta reply-1';

          var spanCommenReply = document.createElement('span');
          spanCommenReply.id = 'sub_cmt_like_'+obj.id;
          var spanSubCommenInnerHtml = '';
          if( subcommentLikesCount[obj.id] && subcommentLikesCount[obj.id]['user_id'][userId]){
            spanSubCommenInnerHtml +='<i id="sub_comment_like_'+obj.id+'" data-company_id="'+obj.company_id+'" data-comment_id="'+obj.placement_process_comment_id+'"  data-sub_comment_id="'+obj.id+'" data-dislike="1" class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>';
            spanSubCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(subcommentLikesCount[obj.id]['like_id']).length +'</span>';
          } else {
            spanSubCommenInnerHtml +='<i id="sub_comment_like_'+obj.id+'" data-company_id="'+obj.company_id+'" data-comment_id="'+obj.placement_process_comment_id+'" data-sub_comment_id="'+obj.id+'" data-dislike="0" class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>';
            if(subcommentLikesCount[obj.id]){
              spanSubCommenInnerHtml +='<span id="like1-bs3">'+ Object.keys(subcommentLikesCount[obj.id]['like_id']).length +'</span>';
            }
          }
          spanCommenReply.innerHTML = spanSubCommenInnerHtml;
          subcommentReplyDiv.appendChild(spanCommenReply);

          var spanSubCommenReplyButton = document.createElement('span');
          spanSubCommenReplyButton.className = 'mrgn_5_left';
          spanSubCommenReplyButton.innerHTML = '<a class="" role="button" data-toggle="collapse" href="#replySubComment'+obj.placement_process_comment_id+'-'+obj.id+'" aria-expanded="false" aria-controls="collapseExample">reply</a>';
          subcommentReplyDiv.appendChild(spanSubCommenReplyButton);

          var spanSubCommenReplyDate = document.createElement('span');
          spanSubCommenReplyDate.className = 'text-muted time-of-reply';
          spanSubCommenReplyDate.innerHTML = '<i class="fa fa-clock-o"></i>'+ obj.updated_at;
          subcommentReplyDiv.appendChild(spanSubCommenReplyDate);

          var createSubCommenDiv = document.createElement('div');
          createSubCommenDiv.className = 'collapse replyComment';
          createSubCommenDiv.id = 'replySubComment'+obj.placement_process_comment_id+'-'+obj.id;
          createSubCommenDivInnerHTML = '<div class="form-group"><label for="subcomment">Your Sub Comment</label>';
          if( userId != obj.user_id ){
            createSubCommenDivInnerHTML += '<textarea name="subcomment" class="form-control" rows="3" id="createSubComment_'+ obj.id +'" >'+obj.user_name+'</textarea>';
          } else {
            createSubCommenDivInnerHTML += '<textarea name="subcomment" class="form-control" rows="3" id="createSubComment_'+ obj.id +'"></textarea>';
          }
          createSubCommenDivInnerHTML += '</div><button class="btn btn-default" onclick="confirmSubmitReplytoSubComment(this);" data-subcomment_id="'+ obj.id +'" data-comment_id="'+ obj.placement_process_comment_id +'" data-company_id="'+ obj.company_id +'" >Send</button><button class="btn btn-default" data-id="replySubComment'+ obj.placement_process_comment_id +'-'+ obj.id +'" onclick="cancleReply(this);">Cancle</button>';
          createSubCommenDiv.innerHTML = createSubCommenDivInnerHTML;
          subcommentReplyDiv.appendChild(createSubCommenDiv);
          mainSubCommentDiv.appendChild(subcommentReplyDiv);
          commentchatDiv.appendChild(mainSubCommentDiv);
        }
      });
    }
  }

  function confirmPlacementProcessComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var companyId = parseInt(document.getElementById('company_id').value);
    var comment = document.getElementById('comment').value;
    document.getElementById('comment').value = '';
    if(0 < userId && comment.length > 0){
      $.ajax({
          method: "POST",
          url: "{{url('createPlacementProcessComment')}}",
          data: {company_id:companyId, comment:comment}
      })
      .done(function( msg ) {
        document.getElementById($(ele).attr('id')).classList.remove("in");

        renderComments(msg, userId);
      });

    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    } else if( comment.length == 0){
      $.alert({
        title: 'Alert!',
        content: 'Please enter something in a comment. ',
      });
    }
  }

  function confirmSubmitReplytoSubComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
        var commentId = $(ele).data('comment_id');
        var companyId = $(ele).data('company_id');
        var subcommentId = $(ele).data('subcomment_id');
        var subcomment = document.getElementById('createSubComment_'+subcommentId).value;

        $.ajax({
            method: "POST",
            url: "{{url('createPlacementProcessSubComment')}}",
            data: {company_id:companyId, comment_id:commentId, subcomment:subcomment, subcomment_id:subcommentId}
        })
        .done(function( msg ) {
          renderComments(msg, userId);
        });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }

  function confirmSubmitReplytoComment(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    if(0 < userId){
        var commentId = $(ele).data('comment_id');
        var companyId = $(ele).data('company_id');
        var subcomment = document.getElementById('subcomment_'+companyId+'_'+commentId).value;
        $.ajax({
            method: "POST",
            url: "{{url('createPlacementProcessSubComment')}}",
            data: {company_id:companyId, comment_id:commentId, subcomment:subcomment}
        })
        .done(function( msg ) {
          renderComments(msg, userId);
        });
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
    }
  }

  function cancleReply(ele){
    var id = $(ele).data('id');
    document.getElementById(id).classList.remove("in");
  }

  function editComment(ele){
    var id = $(ele).attr('id');
    document.getElementById('editCommentHide_'+id).classList.add("hide");
    document.getElementById('editCommentShow_'+id).classList.remove("hide");
  }

  function cancleComment(ele){
    var id = $(ele).attr('id');
    document.getElementById('editCommentHide_'+id).classList.remove("hide");
    document.getElementById('editCommentShow_'+id).classList.add("hide");
  }

  function editSubComment(ele){
    var id = $(ele).attr('id');
    document.getElementById('editSubCommentHide_'+id).classList.add("hide");
    document.getElementById('editSubCommentShow_'+id).classList.remove("hide");
  }

  function cancleSubComment(ele){
    var id = $(ele).attr('id');
    document.getElementById('editSubCommentHide_'+id).classList.remove("hide");
    document.getElementById('editSubCommentShow_'+id).classList.add("hide");
  }

  function confirmSubmit(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var areaId = parseInt(document.getElementById('area1').value);
    var companyId = parseInt(document.getElementById('company1').value);
    var questionLength = CKEDITOR.instances.question.getData().length;

    if(0 < userId && 0 < areaId && 0 < companyId && questionLength > 0){
        var area = document.getElementById('post_area_id');
        area.value= areaId;
        var company = document.getElementById('post_company_id');
        company.value= companyId;
        formId = $(ele).attr('id');
        form = document.getElementById(formId);
        form.submit();
    } else if( isNaN(userId)) {
      $('#loginUserModel').modal();
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
    document.getElementById('area_company_id').value = company;
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

  function showMore(){
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
  }
</script>
@stop