@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> User Video Url </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-group"></i> User Dashboard </li>
      <li class="active"> User Video Url </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          @if(Session::has('message'))
            <div class="alert alert-success" id="message">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ Session::get('message') }}
            </div>
          @endif
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="container admin_div">
            <div class="row">
              <label class="col-sm-2 col-form-label" for="category">User:</label>
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
              </div>
            </div>
              <form action="{{url('updateUserVideo')}}" method="POST">
              {{ method_field('PUT') }}
              <input type="hidden" id="student_id" name="student_id" value="{{($selectedStudent)?$selectedStudent->id:null}}">

              {{ csrf_field() }}
              <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="category">Video Url:</label>
                <div class="col-sm-3">
                  <input type="text" class="form-control" id="recorded_video" name="recorded_video" value="{{($selectedStudent)?$selectedStudent->recorded_video:null}}" placeholder="Enter Video Url" required="true">
                </div>
              </div>
              <div class="form-group row">
                <div class="col-sm-2" title="Submit">
                  <button type="submit" class="btn btn-primary" style="width: 100px;">Submit</button>
                </div>
              </div>
              </form>
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
        if(msg){
          document.getElementById('student_id').value = msg.id;
          document.getElementById('recorded_video').value = msg.recorded_video;
        } else {
          document.getElementById('student_id').value = 0;
          document.getElementById('recorded_video').value = '';
        }
    });
  }
</script>
@stop