@extends('mentor.dashboard.dashboard')
@section('dashboard_header')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
@stop
@section('module_title')
  <section class="content-header">
    <h1> Profile </h1>
  </section>
  <style type="text/css">
    @media screen and (max-width: 500px) {
      .v-container .container, .list-group .list-group-item, .col-xs-12, .col-xs-9,.col-md-offset-2, .col-xs-10{
        padding-left: 0px !important;
        padding-right: 0px !important;
      }
    }
  </style>
@stop
@section('dashboard_content')
	<div class="content-wrapper tab-content" >
    <div id="profile" class="tab-pane active">
      <div class="container">
        <div class="row">
          <div class="col-md-7 col-md-offset-2">
            <div class="panel panel-default">
              <div class="panel-heading">
                @if(Session::has('message'))
                  <div class="alert alert-success" id="message">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      {{ Session::get('message') }}
                  </div>
                @endif
                @if(count($errors) > 0)
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
                @endif
                <a class="btn-top pull-right"  href="#edit-all" class="btn btn-primary btn-success pull-right" data-toggle="modal" style="position: absolute;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit All</a>
                  <div id="edit-all" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button class="close" data-dismiss="modal">×</button>
                          <h2  class="modal-title">Edit Profile</h2>
                        </div>
                        <div class="modal-body">
                          <div class="">
                            <form action="{{url('mentor/updateProfile')}}" method="POST" enctype="multipart/form-data">
                              {{ method_field('PUT') }}
                              {{ csrf_field() }}
                              <fieldset>
                                <div class="form-group row">
                                  <label>Name:</label>
                                  <input class="form-control" placeholder="name" name="name" type="text" value="{{$loginUser->name}}" required>
                                </div>
                                <div class="form-group row">
                                  <label>Designation:</label>
                                  <input class="form-control" placeholder="Designation" name="designation" type="text" value="{{$loginUser->designation}}" required>
                                </div>
                                <div class="form-group row">
                                  <label>Area:</label>
                                  <select class="form-control" name="area" required onChange="selectSkill(this);">
                                    <option> Select Area</option>
                                    @if(count($mentorAreas) > 0)
                                      @foreach($mentorAreas as $mentorArea)
                                        @if($loginUser->mentor_area_id == $mentorArea->id)
                                          <option value="{{$mentorArea->id}}" selected>{{$mentorArea->name}}</option>
                                        @else
                                          <option value="{{$mentorArea->id}}">{{$mentorArea->name}}</option>
                                        @endif
                                      @endforeach
                                    @endif
                                  </select>
                                </div>
                                <div class="form-group row">
                                  <label>Skills:</label>
                                  <div id="skillList">
                                    @if(count($mentorSkills) > 0)
                                      @foreach($mentorSkills as $mentorSkill)
                                        @if(in_array($mentorSkill->id,$mentorSkillArr))
                                          <input type="checkbox" name="skills[]" multiple value="{{$mentorSkill->id}}" checked> {{$mentorSkill->name}}
                                        @else
                                          <input type="checkbox" name="skills[]" multiple value="{{$mentorSkill->id}}"> {{$mentorSkill->name}}
                                        @endif
                                      @endforeach
                                    @endif
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label>Photo:</label>
                                  <input class="form-control" placeholder="Photo" name="photo" type="file">
                                  <label>Existing Photo:</label> {{basename($loginUser->photo)}}
                                </div>
                                <div class="form-group row">
                                  <label>LinkedIn:</label>
                                  <input class="form-control" placeholder="LinkedIn" name="linked_in" type="text" value="{{$loginUser->linked_in}}" >
                                </div>
                                <div class="form-group row">
                                  <label>Twitter:</label>
                                  <input class="form-control" placeholder="Twitter" name="twitter" type="text" value="{{$loginUser->twitter}}" >
                                </div>
                                <div class="form-group row">
                                  <label>Youtube:</label>
                                  <input class="form-control" placeholder="Youtube" name="youtube" type="text" value="{{$loginUser->youtube}}" >
                                </div>
                                <div class="form-group row">
                                  <label>Facebook:</label>
                                  <input class="form-control" placeholder="Facebook" name="facebook" type="text" value="{{$loginUser->facebook}}" >
                                </div>
                                <div class="form-group row">
                                  <label>Fees/Hour:</label>
                                  <input class="form-control" placeholder="Fees" name="fees" type="text" value="{{$loginUser->fees}}" >
                                </div>
                                <div class="form-group row">
                                  <label>About:</label>
                                  <textarea name="about" id="about" placeholder="About here.." class="form-control" rows="3">{!! $loginUser->about !!}</textarea>
                                  <script type="text/javascript">
                                    CKEDITOR.replace('about');
                                    CKEDITOR.config.width="100%";
                                    CKEDITOR.config.height="auto";
                                    CKEDITOR.on('dialogDefinition', function (ev) {
                                        var dialogName = ev.data.name,
                                            dialogDefinition = ev.data.definition;
                                        if (dialogName == 'image') {
                                            var onOk = dialogDefinition.onOk;
                                            dialogDefinition.onOk = function (e) {
                                                var width = this.getContentElement('info', 'txtWidth');
                                                width.setValue('100%');
                                                var height = this.getContentElement('info', 'txtHeight');
                                                height.setValue('400');
                                                onOk && onOk.apply(this, e);
                                            };
                                        }
                                    });
                                  </script>
                                </div>
                                <div class="form-group row">
                                  <label>Experiance:</label>
                                  <textarea name="experiance" id="experiance" placeholder="Experiance here.." class="form-control" rows="3">{!! $loginUser->experiance !!}</textarea>
                                  <script type="text/javascript">
                                    CKEDITOR.replace('experiance');
                                    CKEDITOR.config.width="100%";
                                    CKEDITOR.config.height="auto";
                                    CKEDITOR.on('dialogDefinition', function (ev) {
                                        var dialogName = ev.data.name,
                                            dialogDefinition = ev.data.definition;
                                        if (dialogName == 'image') {
                                            var onOk = dialogDefinition.onOk;
                                            dialogDefinition.onOk = function (e) {
                                                var width = this.getContentElement('info', 'txtWidth');
                                                width.setValue('100%');
                                                var height = this.getContentElement('info', 'txtHeight');
                                                height.setValue('400');
                                                onOk && onOk.apply(this, e);
                                            };
                                        }
                                    });
                                  </script>
                                </div>
                                <div class="form-group row">
                                  <label>Achievement:</label>
                                  <textarea name="achievement" id="achievement" placeholder="Achievement here.." class="form-control" rows="3">{!! $loginUser->achievement !!}</textarea>
                                  <script type="text/javascript">
                                    CKEDITOR.replace('achievement');
                                    CKEDITOR.config.width="100%";
                                    CKEDITOR.config.height="auto";
                                    CKEDITOR.on('dialogDefinition', function (ev) {
                                        var dialogName = ev.data.name,
                                            dialogDefinition = ev.data.definition;
                                        if (dialogName == 'image') {
                                            var onOk = dialogDefinition.onOk;
                                            dialogDefinition.onOk = function (e) {
                                                var width = this.getContentElement('info', 'txtWidth');
                                                width.setValue('100%');
                                                var height = this.getContentElement('info', 'txtHeight');
                                                height.setValue('400');
                                                onOk && onOk.apply(this, e);
                                            };
                                        }
                                    });
                                  </script>
                                </div>
                                <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                                <button class="btn btn-info" type="submit">Submit</button>
                              </fieldset>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div  align="center" style="background-image: url('{{ url('images/user/profile-bg.jpg')}}');"">
                    @if(is_file($loginUser->photo) || (!empty($loginUser->photo) && false == preg_match('/userStorage/',$loginUser->photo)))
                      <img alt="User Pic" style="max-height: 200px !important;" src="{{ url($loginUser->photo)}}" id="profile-image1" class="user-prof img-responsive">
                    @else
                      <img alt="User Pic" src="{{ url('images/user/user1.png')}}" id="profile-image1" class="img-circle img-responsive">
                    @endif
                  </div>
              </div>
              <ul class="list-group">
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-3 "><b>Name</b></div>
                        <div class="col-xs-9 pull-left">{{$loginUser->name}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-2">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-3 "><b>Email</b></div>
                        <div class="col-xs-9 pull-left">{{$loginUser->email}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-3" data-toggle="detail-3">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-3 "><b>Mobile</b></div>
                        <div class="col-xs-9 pull-left">{{$loginUser->mobile}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-3 "><b> Designation</b></div>
                         <div class="col-xs-9 ">{{$loginUser->designation}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-3 "><b> Area</b></div>
                         <div class="col-xs-9 ">
                         @if($loginUser->mentor_area_id > 0)
                            {{$loginUser->mentorArea->name}}
                          @endif
                         </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-3 "><b> Skills</b></div>
                         <div class="col-xs-9 ">{{$skills}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-3 "><b> LinkedIn</b></div>
                         <div class="col-xs-9 ">{{$loginUser->linked_in}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-3 "><b> Twitter</b></div>
                         <div class="col-xs-9 ">{{$loginUser->twitter}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-3 "><b> Youtube</b></div>
                         <div class="col-xs-9 ">{{$loginUser->youtube}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-3 "><b> Facebook</b></div>
                         <div class="col-xs-9 ">{{$loginUser->facebook}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-3 "><b> Fees/Hour</b></div>
                         <div class="col-xs-9 ">
                          @if($loginUser->fees > 0)
                            Rs. {{$loginUser->fees}}
                          @else
                            Rs. 0
                          @endif
                          </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-3 "><b> About</b></div>
                         <div class="col-xs-9 ">{!! $loginUser->about !!}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-3 "><b> Experiance</b></div>
                         <div class="col-xs-9 ">{!! $loginUser->experiance !!}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-3 "><b> Achievement</b></div>
                         <div class="col-xs-9 ">{!! $loginUser->achievement !!}</div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
       </div>
    </div>
  </div>
  <input type="hidden" id="user_type" value="{{$loginUser->user_type}}">
<script type="text/javascript">
  function selectSkill(ele){
    var areaId = $(ele).val();
    if(areaId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('getMentorSkillsByAreaId')}}",
        data: {area:areaId}
      })
      .done(function( msg ) {
        var skillList = document.getElementById('skillList');
        skillList.innerHTML = '';
        if(msg.length > 0){
          $.each(msg, function(idx,obj){
            skillList.innerHTML +=' <input type="checkbox" name="skills[]" multiple value="'+obj.id+'"> '+obj.name;
          });
        }
      });
    }
  }


</script>
@stop