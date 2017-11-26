@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Placement </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> User Dashboard</li>
      <li class="active">Placement </li>
    </ol>
  </section>
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div id="placement" class="">
      <div class="top mrgn_40_btm"">
        <div class="container">
          <div class="row">
            <div class="col-md-3 mrgn_10_btm" id="student">
              <select class="form-control" id="selected_student" name="student" onChange="showResult();">
                <option value="0"> Select User </option>
                 @if(count($students) > 0)
                  @foreach($students as $student)
                    @if(is_object($selectedStudent) && $selectedStudent->id == $student->id)
                      <option value="{{$student->id}}" selected="true"> {{$student->name}} </option>
                    @else
                      <option value="{{$student->id}}"> {{$student->name}} </option>
                    @endif
                  @endforeach
                @endif
              </select>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row text-center">
          <div class="mrgn_20_btm" id="video">
            <button type="button" class="btn btn-lg btn-primary btn-circle" title="Read" data-toggle="modal" data-placement="bottom"   href="#student_video" onClick="toggleVideo();"><i class="fa fa-book" >
              @if(is_object($selectedStudent) && $selectedStudent->recorded_video)
                Recorded Video of Student
              @else
                Video of Student is not uploaded
              @endif
            </i></button>
            <div id="student_video" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button class="close" data-dismiss="modal" onClick="toggleVideo('hide');">×</button>
                    <h2  class="modal-title">Recorded Video</h2>
                  </div>
                  <div class="modal-body">
                    <div id="iframe-video" class="iframe-container">
                      @if(is_object($selectedStudent) && $selectedStudent->recorded_video)
                         {!! $selectedStudent->recorded_video !!}
                      @else
                        Video of Student is not uploaded
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mrgn_20_btm" id="resume">
            <button type="button" class="btn btn-lg btn-primary btn-circle" title="Read" data-toggle="modal" data-placement="bottom"   href="#student_resume"><i class="fa fa-book" >
            @if(is_object($selectedStudent) && $selectedStudent->resume)
              Resume of Student
            @else
              Resume of Student is not uploaded
            @endif
            </i></button>
            <div id="student_resume" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button class="close" data-dismiss="modal">×</button>
                    <h2  class="modal-title">Resume</h2>
                  </div>
                  <div class="modal-body">
                    <div class="iframe-container">
                      @if(is_object($selectedStudent) && $selectedStudent->resume)
                        <iframe src="{{asset($selectedStudent->resume)}}" frameborder="0"></iframe>
                      @else
                        Resume of Student is not uploaded
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
<script type="text/javascript">


  function showResult(ele){
    var student = parseInt(document.getElementById('selected_student').value);
    $.ajax({
        method: "POST",
        url: "{{url('getStudentById')}}",
        data: {student_id:student}
    })
    .done(function( msg ) {
      var divVideo = document.getElementById('video');
      divVideo.innerHTML = '';
      var divResume = document.getElementById('resume');
      divResume.innerHTML = '';
      if(msg){
        var videoInnerHTML = '<button type="button" class="btn btn-lg btn-primary btn-circle" title="Read" data-toggle="modal" data-placement="bottom"   href="#student_video" onClick="toggleVideo();"><i class="fa fa-book" >';
        if(msg.recorded_video){
          videoInnerHTML += 'Recorded Video of Student';
        } else {
          videoInnerHTML += 'Video of Student is not uploaded';
        }
        videoInnerHTML += '</i></button><div id="student_video" class="modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal" onClick="toggleVideo(\'hide\');">×</button><h2  class="modal-title">Recorded Video</h2></div><div class="modal-body"><div class="iframe-container" id="iframe-video">';
        if(msg.recorded_video){
          videoInnerHTML += msg.recorded_video;
        } else {
          videoInnerHTML += 'Video of Student is not uploaded';
        }
        videoInnerHTML += '</div></div></div></div></div>';
        divVideo.innerHTML = videoInnerHTML;

        var resumeInnerHTML = '<button type="button" class="btn btn-lg btn-primary btn-circle" title="Read" data-toggle="modal" data-placement="bottom"   href="#student_resume"><i class="fa fa-book" >';
        if(msg.resume){
          resumeInnerHTML += 'Resume of Student';
        } else {
          resumeInnerHTML += 'Resume of Student is not uploaded';
        }
        resumeInnerHTML += '</i></button><div id="student_resume" class="modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">×</button><h2  class="modal-title">Resume</h2></div><div class="modal-body"><div class="iframe-container">';
        if(msg.resume){
          var url = "{{url('')}}/"+msg.resume;
          resumeInnerHTML += '<iframe src="'+url+'" frameborder="0"></iframe>';
        } else {
          resumeInnerHTML += 'Resume of Student is not uploaded';
        }
        resumeInnerHTML += '</div></div></div></div></div>';
        divResume.innerHTML = resumeInnerHTML;
      }

    });
  }

  function toggleVideo(state) {
    // if state == 'hide', hide. Else: show video
    var div = document.getElementById("iframe-video");
    if(div.getElementsByTagName("iframe").length > 0){
      var iframe = div.getElementsByTagName("iframe")[0].contentWindow;
      func = state == 'hide' ? 'pauseVideo' : 'playVideo';
      iframe.postMessage('{"event":"command","func":"' + func + '","args":""}','*');
    }
  }
  $('#student_video').on('hide.bs.modal', function (e) {
    toggleVideo('hide')
  })
</script>
@stop
