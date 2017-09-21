@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Students Assignment  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Assignment </li>
      <li class="active"> Students Assignment </li>
    </ol>
  </section>
  <style type="text/css">
    .attachment-text{
      border: 1px solid  #d2d6de;
      margin-left: 50px;
    }

   .attachment {
        display: inline-block;
        margin-top: 10px;
          border: 2px solid  #d2d6de;
          cursor: pointer;
          margin-left: 50px;
          padding: 10px 10px;

      }
  .fa-download{margin-left: 10px;
    margin-right: 10px;}
    @media (max-width: 678px){
    .attachment {
          width: 180px;
          text-overflow: ellipsis !important;
          overflow: hidden;
           white-space: nowrap;
           margin-left: 50px;
      }
    }
    .direct-chat-text img,
    .direct-chat-text-left img{
      max-width: 60% !important;
      height: 200px !important;
      margin: 10px;

    }
    @media (max-width: 678px){
    .direct-chat-text img, .direct-chat-text-left img{
        width: 100% !important;
        height: 100% !important;
    }
    }
  </style>
@stop
@section('dashboard_content')
	&nbsp;
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  <div class="container">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="row">
      <div class="col-md-12 ">
        <div class="box box-primary direct-chat direct-chat-warning">
          <div class="box-header with-border">
            <div class="form-group row ">
              <label class="col-sm-2 col-form-label">Student Name:</label>
              <div class="col-sm-3">
                {{$student->name}}
              </div>
            </div>
            <div class="form-group row ">
              <label class="col-sm-2 col-form-label">Institute Course:</label>
              <div class="col-sm-3">
                {{$assignment->instituteCourse->name}}
              </div>
            </div>
            <div class="form-group row ">
              <label class="col-sm-2 col-form-label">Subject Name:</label>
              <div class="col-sm-3">
                {{$assignment->subject->name}}
              </div>
            </div>
            <div class="form-group row ">
              <label class="col-sm-2 col-form-label">Topic Name:</label>
              <div class="col-sm-3">
                {{$assignment->topic->name}}
              </div>
            </div>
            <div class="form-group row ">
              <label for="topic" class="col-sm-2 col-form-label">Assignment:</label>
              <div class="col-sm-10">
                  {!! $assignment->question !!}
              </div>
            </div>
              <div class="form-group row ">
                  <label class="col-sm-2 col-form-label" for="attached_link">Attachment:</label>
                  @if(!empty($assignment->attached_link))
                  <div class="col-sm-3 attachment btn-primary btn" >
                    <a  data-path="{{asset($assignment->attached_link)}}" data-toggle="modal" data-target="#dynamic-modal-{{$assignment->id}}" data-document_id="{{$assignment->id}}"  style="color: #fff;">
                      <i class="fa fa-download" ></i>
                      <strong class="ellipsed">{{basename($assignment->attached_link)}}</strong>
                    </a>
                  </div>
                  @endif
              </div>
              @if(!empty($assignment->attached_link))
              <div id="dynamic-modal-{{$assignment->id}}" class="modal fade" role="dialog">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button class="close" data-dismiss="modal">×</button>
                      <h2  class="modal-title">{{basename($assignment->attached_link)}}</h2>
                    </div>
                    <div class="modal-body">
                        <div class="iframe-container">
                          <iframe src="{{asset($assignment->attached_link)}}" frameborder="0"></iframe>
                        </div>
                    </div>
                    <div class="modal-footer ">
                      <a href="{{asset($assignment->attached_link)}}" download class="btn btn-primary download" id="myBtn" style="width: 90px !important;"><i class="fa fa-download" aria-hidden="true"></i></a>
                    </div>
                  </div>
                </div>
              </div>
              @endif
          </div>
          <div class="box-body">
            <div class="direct-chat-messages">
              @if(count($answers) > 0)
                @foreach($answers as $answer)
                  @if(!empty($answer->answer))
                    <div class="direct-chat-msg left">
                      <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name pull-left">{{ $answer->student->name }}</span>
                        <span class="direct-chat-timestamp ">{{ $answer->created_at->format('d M Y i a')}}</span>
                      </div>
                      @if(!empty($answer->student->photo))
                        <img class="direct-chat-img" src="{{ asset($answer->student->photo) }}" alt="User Image" />
                      @else
                        <img class="direct-chat-img" src="{{ url('images/user/user.png')}}" alt="User Image" />
                      @endif
                      <div class="direct-chat-text-left ">
                       {!! $answer->answer !!}
                      </div>
                      @if(!empty($answer->attached_link))
                        <div class="attachment" >
                           <a  data-path="{{asset($answer->attached_link)}}" data-toggle="modal" data-target="#dynamic-modal-answer-{{$answer->id}}" data-document_id="{{$answer->id}}" style="">
                              <i class="fa fa-download" ></i>
                              <strong class="ellipsed">{{basename($answer->attached_link)}}</strong>
                            </a>
                        </div>
                        @endif
                    </div>
                  @else
                    <div class="direct-chat-msg right">
                      <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name pull-right">{{ $answer->teacher->name }}</span>
                        <span class="direct-chat-timestamp ">{{ $answer->created_at->format('d M Y i a')}}</span>
                      </div>
                      @if(!empty($answer->teacher->photo))
                        <img class="direct-chat-img" src="{{ asset($answer->teacher->photo) }}" alt="User Image" />
                      @else
                        <img class="direct-chat-img" src="{{ url('images/user/user.png')}}" alt="User Image" />
                      @endif
                      <div class="direct-chat-text ">{!! $answer->teacher_comment !!}
                      </div>
                      @if(!empty($answer->attached_link))
                          <div class="attachment" style="float: right; margin-right: 50px;">
                           <a  data-path="{{asset($answer->attached_link)}}" data-toggle="modal" data-target="#dynamic-modal-answer-{{$answer->id}}" data-document_id="{{$answer->id}}" style="">
                              <i class="fa fa-download" ></i>
                              <strong class="ellipsed">{{basename($answer->attached_link)}}</strong>
                            </a>
                        </div>
                        @endif
                    </div>
                  @endif
                @endforeach
              @else
                No comments are available.
              @endif
            <hr>
            <form action="{{url('createAssignmentRemark')}}" method="POST" enctype="multipart/form-data">
              {{ csrf_field() }}
              <div class="form-group row @if ($errors->has('teacher_comment')) has-error @endif">
                <label for="teacher_comment" class="col-sm-2 col-form-label">Remark:</label>
                <div class="col-sm-10">
                  <textarea name="teacher_comment" cols="60" rows="4" id="teacher_comment" placeholder="Enter your comment" required>
                  </textarea>
                  <script type="text/javascript">
                    CKEDITOR.replace( 'teacher_comment', { enterMode: CKEDITOR.ENTER_BR } );
                  </script>
                </div>
              </div>
              <div class="form-group row @if ($errors->has('attached_link')) has-error @endif">
                <label class="col-sm-2 col-form-label" for="attached_link">Attachment:</label>
                <div class="col-sm-3">
                  <input type="file" class="form-control"  name="attached_link" id="attached_link" >
                </div>
              </div>
              <input type="hidden" name="assignment_question_id" value="{{$assignment->id}}">
              <input type="hidden" name="student_id" value="{{ $student->id }}">
              <input type="hidden" name="client_id" value="{{ Auth::guard('client')->user()->id }}">
              <input type="hidden" name="answer" value="">
              <div class="form-group row">
                <div class="offset-sm-2 col-sm-3" title="Submit">
                  <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
                </div>
              </div>
            </form>
            </div>
          </div>
          @if(count($answers) > 0)
            @foreach($answers as $answer)
              @if(!empty($answer->answer))
                  @if(!empty($answer->attached_link))
                      <div id="dynamic-modal-answer-{{$answer->id}}" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button class="close" data-dismiss="modal">×</button>
                              <h2  class="modal-title">{{basename($answer->attached_link)}}</h2>
                            </div>
                            <div class="modal-body">
                                <div class="iframe-container">
                                  <iframe src="{{asset($answer->attached_link)}}" frameborder="0"></iframe>
                                </div>
                            </div>
                            <div class="modal-footer ">
                              <a href="{{asset($answer->attached_link)}}" download class="btn btn-primary download" id="myBtn" style="width: 90px !important;"><i class="fa fa-download" aria-hidden="true"></i></a>
                            </div>
                          </div>
                        </div>
                      </div>
                    @endif
              @else
                  @if(!empty($answer->attached_link))
                      <div id="dynamic-modal-answer-{{$answer->id}}" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button class="close" data-dismiss="modal">×</button>
                              <h2  class="modal-title">{{basename($answer->attached_link)}}</h2>
                            </div>
                            <div class="modal-body">
                                <div class="iframe-container">
                                  <iframe src="{{asset($answer->attached_link)}}" frameborder="0"></iframe>
                                </div>
                            </div>
                            <div class="modal-footer ">
                              <a href="{{asset($answer->attached_link)}}" download class="btn btn-primary download" id="myBtn" style="width: 90px !important;"><i class="fa fa-download" aria-hidden="true"></i></a>
                            </div>
                          </div>
                        </div>
                      </div>
                    @endif
              @endif
            @endforeach
          @endif
        </div>
      </div>
    </div>

@stop