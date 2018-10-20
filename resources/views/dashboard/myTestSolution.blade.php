@extends('dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/style.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
    .navbar-nav > li > a {
        border-left: none !important;
      }
      footer {
          padding: 0%;
      }
      .top-btn-align, .bottom-btn-align{text-align: right;
    }
    @media  (min-width: 993px) {
      .hidden-lg { display: none; }
    }
    @media  (max-width: 992px) {
      .hidden-sm { display: none; }
      .top-btn-align, .bottom-btn-align{text-align: center;}
    }
    img{
      position: relative;
      outline: none;
      /*width: 100%;*/
      max-width: 100%;
      height: auto;
      /*border: 1px solid red;*/
    }
    @media  (max-width: 478px) {
      .btn-inline{ display:inline-block; vertical-align: middle;}
      .btn-sq-sm{margin-left: 20%;}

    }
    .answer{
      padding-left: 20px !important;
    }
    .btn {
      border-radius: 3px !important;
      margin: 5px;
    }
    .btn-default {
      background-color: #3c8dbc;
      border-color: #367fa9;
      color: white;
    }
    .bg-warning {
      background-color: white;
    }
    tr:nth-of-type(odd) {
      background-color: white;
    }
    .btn-primary {
      width: 100px !important;
    }
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Test </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test</li>
      <li class="active">My Test</li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="content" oncontextmenu="return false;" >
    <div class="container">
      <div class="row">
        <div class ="col-sm-9">
          <div class="panel panel-info" >
            @if(count($results['questions']) > 0)
              <div align="left" style="background:#ADD8E6">
                @foreach($results['questions'] as $index => $question)
                  @if(isset($sections[$index]) && count($results['questions'][$index]) > 0)
                    <a class="section btn btn-primary" id="{{ $sections[$index]->name }}" style="min-width:100px !important;max-width:200px !important;">{{ $sections[$index]->name }}</a>
                    <input type="hidden" id="show-{{ $sections[$index]->name }}" name="show-{{ $sections[$index]->name }}" value="1" />
                  @else
                    <input type="hidden" id="show-{{ $sections[$index]->name }}" name="show-{{ $sections[$index]->name }}" value="0" />
                  @endif
                @endforeach
              </div>
              <div class="panel-heading" title="Calculator" style="background:#ADD8E6" align="right">
                @if(1 == $paper->show_calculator)
                  <a class= "btn btn-success" target="popup" onclick="window.open('http://web2.0calc.com/widgets/horizontal/?options=%7B%22angular%22%3A%22deg%22%2C%22options%22%3A%22hide%22%2C%22menu%22%3A%22show%22%7D','name','width=600,height=400')"><i class="fa fa-calculator hidden-lg" aria-hidden="true" ></i><div class="hidden-sm hidden-xs">Calculator</div></a>&emsp;
                @endif
                <a class="btn btn-default" title="Useful Data" role="button" data-toggle="modal" data-target="#useful_data"><i class="fa fa-book hidden-lg" aria-hidden="true"></i><div class="hidden-sm">Useful Data</div></a>&emsp;
              </div>
              @foreach($sections as $index => $section)
                <div id="{{ $section->name }}" class="hide">
                @if(isset($results['questions']) && count($results['questions'][$section->id]) > 0)
                  @foreach($results['questions'][$section->id] as $index => $result)
                    @if( $index == 0)
                      <div class='cont' id='question_{{$result->id}}'>
                    @else
                        <div class='cont hide' id='question_{{$result->id}}'>
                      @endif
                    <div class="bg-warning" style="height:400px" >
                          <div class="panel-body"  >
                        <div id='question{{$result->id}}' >
                          <p class="questions" id="qname{{$result->id}}">
                            @if(!empty($result->common_data))
                              <span style="padding-left: 5px;">{!! $result->common_data !!}</span></br>
                            @endif
                            <span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
                            {!! $result->name !!}
                          </p>
                          @if( 1 == $result->question_type )
                            <div class="row answer">A.<input type="radio" value="1" class="radio1 radio1_{{$result->id}}" id="radio1_{{$result->id}}" name="{{$result->id}}" />
                              {!! $result->answer1 !!}
                            </div>
                            <div class="row answer">B.<input type="radio" value="2" class="radio1 radio1_{{$result->id}}" id="radio2_{{$result->id}}" name="{{$result->id}}" />
                              {!! $result->answer2 !!}
                            </div>
                            <div class="row answer">C.<input type="radio" value="3" class="radio1 radio1_{{$result->id}}" id="radio3_{{$result->id}}" name="{{$result->id}}" />
                              {!! $result->answer3 !!}
                            </div>
                            <div class="row answer">D.<input type="radio" value="4" class="radio1 radio1_{{$result->id}}" id="radio4_{{$result->id}}" name="{{$result->id}}" />
                              {!! $result->answer4 !!}
                            </div>
                            @if(!empty($result->answer5))
                            <div class="row answer">E.<input type="radio" value="5" class="radio1 radio1_{{$result->id}}" id="radio5_{{$result->id}}" name="{{$result->id}}" />
                              {!! $result->answer5 !!}
                            </div>
                            @endif
                            <input type="radio" checked='checked' style='display:none' value="unsolved" id='radio7_{{$result->id}}' name='{{$result->id}}' />
                          @else
                            <input type="number" class="form-control numpad" id="numpad_{{$result->id}}" data-id="{{$result->id}}" name="{{$result->id}}" placeholder="Enter a number" readonly="true">
                          @endif
                        </div>
                      </div>
                    </div>
                    <div style="background:#ADD8E6" align ="right">
                      <button id='{{$result->id}}' data-prev_ques="{{isset($results['questions'][$section->id][$index-1])?$results['questions'][$section->id][$index-1]->id:0}}" class='prev btn' title='Previous' type='button' ><i class='fa fa-arrow-circle-left hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Previous</div></button>
                      <button id='{{$result->id}}' data-next_ques="{{isset($results['questions'][$section->id][$index+1])?$results['questions'][$section->id][$index+1]->id:0}}" class='next btn btn-success' title='Next' type='button'><i class='fa fa-arrow-circle-right hidden-lg' aria-hidden='true'></i><div class='hidden-sm'>Next</div></button>&emsp;
                    </div>
                    <br/>
                    <div class="panel-heading" style="background:#ADD8E6" >Solution</div>
                      <div class="panel-body">
                        <br/>
                      <b><h4>Correct Answer:
                      @if(0 == $result->question_type)
                        {{$result->min}} to {{$result->max}}
                              @else
                                @if($result->answer==1)
                          A
                                  @elseif($result->answer==2)
                                    B
                                  @elseif($result->answer==3)
                                    C
                                  @else
                                    D
                                  @endif
                              @endif
                              </h4></b><br/>
                              <b><h4>Your Answer:
                      @if(0 == $result->question_type)
                        @if( isset($userResults[$result->id]))
                          {!! $userResults[$result->id]->user_answer !!}
                                  @elseif(!isset($userResults[$result->id]))
                          unsolved(New Question)
                                  @else
                                    unsolved
                                  @endif
                              @else
                                @if( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 1)
                          A
                                  @elseif( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 2)
                                    B
                                  @elseif( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 3)
                                    C
                                  @elseif( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 4)
                                    D
                                  @elseif( isset($userResults[$result->id]) && $userResults[$result->id]->user_answer == 5)
                                    E
                                  @elseif(!isset($userResults[$result->id]))
                          unsolved(New Question)
                                  @else
                                    unsolved
                                  @endif
                              @endif
                              </h4></b><br/>
                      <b><h4>Solution: </h4></b><br/><br/> {!! $result->solution !!}
                              <br/>
                      </div>
                    </div>
                  @endforeach
                @endif
                </div>
              @endforeach
            @else
              No questions are available.
            @endif
          </div>
        </div>
        <div class ="col-sm-3">
          <div class="panel panel-info">
            <div class="panel-heading">Questions Palette</div>
            <div class="panel-body">
              <table class="table" >
                @foreach($sections as $index => $section)
                <tr id="{{$section->name}}_palette">
                  <div class="row">
                    <div class="col-lg-12">
                      <td height="200px"   overflow = "scroll" >
                        <div class="bg-warning" style="height:300px" >
                            <p id = "id1"></p>
                            @if(isset($results['questions']) && count($results['questions'][$section->id]) > 0)
                            @foreach($results['questions'][$section->id] as $index => $q)
                            @if(!isset($userResults[$q->id]) || 'unsolved' == $userResults[$q->id]->user_answer)
                              <button type="button" id ="id_{{$q->id}}" data-type="{{$section->name}}" class="button1 btn btn-sq-xs btn-info" value="{{$q->id}}"  title='{{$index+1}}'>{{$index+1}}</button>
                            @elseif(isset($userResults[$q->id]) && ($q->answer == $userResults[$q->id]->user_answer || (0 == $q->question_type && $userResults[$q->id]->user_answer >= $q->min && $userResults[$q->id]->user_answer <= $q->max)))
                              <button type="button" id ="id_{{$q->id}}" data-type="{{$section->name}}" class="button1 btn btn-sq-xs" value="{{$q->id}}"  title='{{$index+1}}' style="background-color: green;">{{$index+1}}</button>
                            @else
                              <button type="button" id ="id_{{$q->id}}" data-type="{{$section->name}}" class="button1 btn btn-sq-xs" value="{{$q->id}}"  title='{{$index+1}}' style="background-color: red;">{{$index+1}}</button>
                            @endif
                              @endforeach
                            @endif
                        </div>
                      </td>
                    </div>
                  </div>
                </tr>
                @endforeach
                <tr >
                  <div class="row" >
                    <div class="col-lg-12"  >
                      <td>
                        <p >
                        <button class="btn btn-sq-sm btn-danger load-ajax-modal" role="button" data-toggle="modal" data-target="#questions" title="Question paper">Que paper
                          </button >
                        <a href="{{url('downloadQuestions')}}/{{$paper->test_category_id}}/{{$paper->test_sub_category_id}}/{{$paper->test_subject_id}}/{{$paper->id}}" class="btn btn-default" id="myBtn" title="Download Question paper">Download</a>
                        </p>
                      </td>
                    </div>
                  </div>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
          <input type="hidden" id="category_id" name="category_id" value="{{$paper->test_category_id}}">
          <input type="hidden" id="sub_category_id" name="sub_category_id" value="{{$paper->test_sub_category_id}}">
          <input type="hidden" id="subject_id" name="subject_id" value="{{$paper->test_subject_id}}">
          <input type="hidden" id="paper_id" name="paper_id" value="{{$paper->id}}">
          <input type="hidden" id="all_sections" value="">
          <input type="hidden" id="selected_section" value="">
         <div class="modal fade" id="useful_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
            <div class="modal-dialog">
              <div class="modal-content model-sm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">Close</button>
                </div>
                <div class="modal-body">
                  @include('layouts.useful_data')
                </div>
              </div>
            </div>
         </div>
          <div class="modal fade" id="questions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog show-questions">
              <div class="modal-content model-lg">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">Close</button>
                </div>
                <div class="modal-body" >
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    $('.load-ajax-modal').click(function(){
      var category = parseInt(document.getElementById('category_id').value);
      var subcategory = parseInt(document.getElementById('sub_category_id').value);
      var subject = parseInt(document.getElementById('subject_id').value);
      var paper = parseInt(document.getElementById('paper_id').value);
        $.ajax({
              method: "POST",
              url: "{{url('getQuestions')}}",
              data: {paper:paper, subject:subject, category:category, subcategory:subcategory}
          })
          .done(function( msg ) {
            if( msg ){
          $('#questions div.modal-body').html(msg);
            }
          });
    });

    $( document ).ready(function() {
      var sections = document.getElementsByClassName("section");
      $.each(sections, function(idx, obj) {
          var id = $(obj).attr('id');
          if( 0 == idx){
            $('a#'+id).removeClass('btn-default').addClass('btn-primary');
            $('tr#'+id+'_palette').removeClass('hide');
            $('div#'+id).removeClass('hide');
            document.getElementById('all_sections').value = id;
          } else {
            $('a#'+id).removeClass('btn-primary').addClass('btn-default');
            $('tr#'+id+'_palette').addClass('hide');
            $('div#'+id).addClass('hide');
            document.getElementById('all_sections').value = document.getElementById('all_sections').value +','+id;
          }
      });

      $(document).on('click', '.section', function(){
        var allSections= document.getElementById('all_sections').value;
        var selectedSection = $(this).attr('id');
        $.each(allSections.split(','), function(idx, obj) {
          if(selectedSection == obj){
            showSelectedSection(obj);
            document.getElementById('selected_section').value = obj;
          } else {
            hideUnSelectedSection(obj);
          }
          $('div#'+obj+' > div:first button:first').prop('disabled', true);
        });
      });

      function showSelectedSection(sect){
        $('#'+sect).removeClass('btn-default').addClass('btn-primary');
        $('tr#'+sect+'_palette').removeClass('hide');
        $('div#'+sect).removeClass('hide');
        $('div#'+sect+' > div').addClass('hide');
        $('div#'+sect+' > div:first').removeClass('hide');
      }

      function hideUnSelectedSection(sect){
        $('#'+sect).removeClass('btn-primary').addClass('btn-default');
        $('tr#'+sect+'_palette').addClass('hide');
        $('div#'+sect).addClass('hide');
        $('div#'+sect+' > div').addClass('hide');
      }

      // next question
      $(document).on("click",".next",function(){
          last=parseInt($(this).attr('id'));
          nex = parseInt($(this).data('next_ques'));
          if( nex > 0){
            var allSections= document.getElementById('all_sections').value;
          var selectedSection = document.getElementById('selected_section').value;
          $.each(allSections.split(','), function(idx, obj) {
            if(selectedSection == obj){
              if( 'question_'+nex == $('div#'+obj+' > div:last').attr('id')){
                  $('button#'+nex+'.next').prop('disabled', true);
                  $('button#'+nex+'.mark').prop('disabled', true);
                }
            } else {
              if( 'question_'+nex == $('div#'+obj+' > div:last').attr('id')){
                  $('button#'+nex+'.next').prop('disabled', true);
                  $('button#'+nex+'.mark').prop('disabled', true);
                }
            }
          });

            $('#question_'+last).addClass('hide');
            $('#question_'+nex).removeClass('hide');
        } else {
          $('button#'+last+'.next').prop('disabled', true);
          $('button#'+last+'.prev').prop('disabled', true);
        }
      });

      // previous question
      $(document).on("click",".prev",function(){
          last=parseInt($(this).attr('id'));
          nex = parseInt($(this).data('prev_ques'));
          if(nex > 0){
            $('#question_'+last).addClass('hide');
            $('#question_'+nex).removeClass('hide');
          }
      });

      $(document).on('click','.button1',function(){
        var questionId = parseInt($(this).attr('value'));
        var section = $(this).attr('data-type');
        $.each($('div#'+section+' > .cont'), function(idx, obj) {
          var divId = $(obj).attr('id');
          if('question_'+questionId == divId){
            $('div#question_'+questionId).removeClass('hide');
          } else {
            $('div#'+divId).addClass('hide');
          }
        });
      });
    });
  </script>
@stop