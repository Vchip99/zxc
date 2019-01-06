@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/exam.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
  .padding-body{
    padding: 15px !important;
  }
  @media screen and (max-width: 768px) {
   .col-md-6{
          width: 45%;
          float: left;
          margin-left: 20px;
     }
   }
   @media screen and (max-width: 320px) {
    .data-sm .container, .data-sm .container .fluid-row{
      padding-left: 0px !important;
    }
    .data-sm .btn3d{
      min-width: 150px !important;
    }
   }
   @media screen and (max-width: 527px) {
   .col-md-6{
          width: 100%;
          float: left;
           margin-left: 0px;
     }
   }
    .label-primary{margin-right: 2px;margin-left: 2px;}
    .divider {
      border-color: grey;
      border-style: solid;
      border-width: 0 0 1px;
      height: 10px;
      line-height: 20px;
      text-align:center;
      overflow: visible;
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
  <section class="v_container ">
   <div class="container">
    <div class="row label-primary">
      <div class="col-md-8  col-md-offset-2  ">
        <div class="row text-center">
          <div class="col-md-6 col-sm-6  col-xs-12 mrgn_10_top_btm  ">
            <select class="form-control" id="category" name="category" onChange="selectSubcategory(this);" title="Category">
              <option>Select Category</option>
              @if(count($testCategories) > 0)
                @foreach($testCategories as $testCategory)
                  <option value="{{$testCategory->id}}"> {{$testCategory->name}} </option>
                @endforeach
              @endif
              @if(count($payableTestCategories) > 0)
                @foreach($payableTestCategories as $payableTestCategory)
                  <option value="{{$payableTestCategory->id}}"> {{$payableTestCategory->name}} </option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="col-md-6 col-sm-6  col-xs-12 mrgn_10_top_btm">
            <select class="form-control" id="subcategory" name="subcategory" onChange="selectPanel();" title="Sub Category">
                <option>Select Sub Category</option>
              </select>
          </div>
        </div>
      </div>
    </div>
    <div>
      <h3 class="divider">
        <span></span>
      </h3>
      <div class="divider">
        <span></span>
      </div>
    </div>
   </div>
  </section>
  <section>
    <div class="container exam-panel" id="subjects">
      @if(count($testSubjects)>0)
        @foreach($testSubjects as $testSubject)
          <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default" >
              <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title" title="{{ $testSubject->name }}">
                  <a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject{{$testSubject->id}}" aria-expanded="true" aria-controls="collapseOne">
                    <i class="more-less glyphicon glyphicon-plus"></i>
                    {{ $testSubject->name }}
                  </a>
                </h4>
              </div>
              <div id="subject{{$testSubject->id}}" class="panel-collapse collapse panel-lg" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                  <table class="table data-lg">
                    <thead>
                      <tr>
                          <th>Test Number</th>
                          <th>Start test</th>
                          <th>Result</th>
                          <th>Date to Active</th>
                          <th>Date to Inactive</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($testSubjectPapers[$testSubject->id] as $testSubjectPaper)
                        @if(in_array($testSubjectPaper->id, $alreadyGivenPapers))
                          <tr style="background-color: #b3c2dc;overflow: auto;">
                        @else
                          <tr style="overflow: auto;">
                        @endif
                            <td class=" ">{{ $testSubjectPaper->name }}</td>
                            <td class=" ">
                              @if($currentDate < $testSubjectPaper->date_to_active)
                                  <button disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button>
                              @elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
                                <button disabled="true" data-toggle="tooltip" title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button>
                              @else
                                <button onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->category_id}}" data-subcategory="{{$testSubjectPaper->sub_category_id}}"  data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                              @endif
                            </td>
                            <td class=" ">
                              @if($currentDate < $testSubjectPaper->date_to_active)
                                  <button disabled="true" data-toggle="tooltip" title="Result will enabled after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
                              @elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
                                <form id="showUserTestResult_{{$testSubjectPaper->id}}" method="POST" action="{{ url('showUserTestResult') }}">
                                  {{ csrf_field() }}
                                  <input type="hidden" name="paper_id" value="{{$testSubjectPaper->id}}">
                                  <input type="hidden" name="category_id" value="{{$testSubjectPaper->category_id}}">
                                  <input type="hidden" name="subcategory_id" value="{{$testSubjectPaper->sub_category_id}}">
                                  <input type="hidden" name="subject_id" value="{{$testSubject->id}}">
                                </form>
                                <button onClick="showUserTestResult(this);" data-paper_id="{{$testSubjectPaper->id}}" data-toggle="tooltip" title="Result!"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
                              @else
                                <button disabled="true" data-toggle="tooltip" title="Result will enabled after test given."><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
                              @endif
                            </td>
                            <td class=" ">{{ $testSubjectPaper->date_to_active }}</td>
                            <td class=" ">{{ $testSubjectPaper->date_to_inactive }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <div class="data-sm">
                    @if(isset($testSubjectPapers[$testSubject->id]))
                      @foreach($testSubjectPapers[$testSubject->id] as $testSubjectPaper)
                            <div class=" panel panel-info" >
                              @if(in_array($testSubjectPaper->id, $alreadyGivenPapers))
                                <div class="toggle panel-heading" data-toggle="paper{{$testSubjectPaper->id}}" style="background-color: #b3c2dc;">
                              @else
                                <div class="toggle panel-heading" data-toggle="paper{{$testSubjectPaper->id}}">
                              @endif
                              {{$testSubjectPaper->name}}<span class="col-xs-2 pull-right"><i class="fa fa-chevron-down pull-right"></i></span>
                              </div>
                                <div id="paper{{$testSubjectPaper->id}}" class="panel-body" style="padding:2px 0px;">
                                  <div class="container">
                                      <div class="fluid-row">
                                        <ul class="">
                                          <li id="startTest">
                                            @if($currentDate < $testSubjectPaper->date_to_active)
                                              <button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button>
                                            @elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
                                              <button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" >Start</span></button>
                                            @else
                                              <button class="btn-magick btn-sm btn3d" onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->category_id}}" data-subcategory="{{$testSubjectPaper->sub_category_id}}"  data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true"></span>Start</button>
                                            @endif
                                          </li>
                                          <li>
                                            @if($currentDate < $testSubjectPaper->date_to_active)
                                              <button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will enabled after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
                                            @elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
                                              <form id="showUserTestResult_{{$testSubjectPaper->id}}" method="POST" action="{{ url('showUserTestResult') }}">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="paper_id" value="{{$testSubjectPaper->id}}">
                                                <input type="hidden" name="category_id" value="{{$testSubjectPaper->category_id}}">
                                                <input type="hidden" name="subcategory_id" value="{{$testSubjectPaper->sub_category_id}}">
                                                <input type="hidden" name="subject_id" value="{{$testSubject->id}}">
                                              </form>
                                              <button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="{{$testSubjectPaper->id}}" data-toggle="tooltip" title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
                                            @else
                                              <button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will enabled after test given."><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
                                            @endif
                                          </li>
                                          <li>
                                            <button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-calendar"></span> {{ $testSubjectPaper->date_to_active }}</button>
                                          </li>
                                          <li>
                                            <button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-calendar"></span> {{ $testSubjectPaper->date_to_inactive }}</button>
                                          </li>
                                        </ul>
                                      </div>
                                  </div>
                                </div>
                            </div>
                          @endforeach
                        @endif
                    </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      @else
        <div class="panel-heading" role="tab" id="headingOne">
          <h4 class="panel-title">
              No test papers are available.
          </h4>
        </div>
      @endif
    </div>
  </section>
  <script type="text/javascript">
    $('li#onlineTest').addClass('active');

    function startTest(ele){
      var windowHeight = screen.height;
      var windowWidth = screen.width;
      var popup_window =window.open("", 'My Window', 'height='+windowHeight+'px !important,width='+windowWidth+'px !important');
      var paper = parseInt($(ele).data('paper'));
      var subject = parseInt($(ele).data('subject'));
      var category = parseInt($(ele).data('category'));
      var subcategory = parseInt($(ele).data('subcategory'));
      var userId = parseInt(document.getElementById('user_id').value);
      if(0 < userId){
        $.ajax({
                method: "POST",
                url: "{{url('setClientUserSessions')}}",
                data: {paper:paper, subject:subject, category:category, subcategory:subcategory}
            })
            .done(function( msg ) {
              if( msg ){
                popup_window.location = "{{ url('instructions')}}";
                popup_window.focus();
              }
            });
      } else {
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
                Cancel: function () {
                }
            }
          });
      }
    }

    function selectSubcategory(ele){
      id = parseInt($(ele).val());
      var userId = parseInt(document.getElementById('user_id').value);
      if( 0 < id && 0 < userId ){
        $.ajax({
                method: "POST",
                url: "{{url('getClientUserTestSubcategoriesBycategoryId')}}",
                data: {id:id, userId:userId}
            })
            .done(function( msg ) {
              select = document.getElementById('subcategory');
              select.innerHTML = '';
              var opt = document.createElement('option');
              opt.value = '';
              opt.innerHTML = 'Select Sub Category';
              select.appendChild(opt);
              if(msg['subcategories']){
                $.each(msg['subcategories'], function(idx, obj) {
                    var opt = document.createElement('option');
                    opt.value = obj.id;
                    opt.innerHTML = obj.name;
                    select.appendChild(opt);
                });
              }
            });
      }
    }
    function showUserTestResult(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'Do you want to see result?',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var paperId = parseInt($(ele).data('paper_id'));
                      document.getElementById('showUserTestResult_'+paperId).submit();
                    }
                },
                Cancle: function () {
                }
            }
          });
    }

    function selectPanel(argument) {
      var cat = parseInt($('select#category').val());
      var subcat = parseInt($('select#subcategory').val());
      var userId = parseInt(document.getElementById('user_id').value);
      if( 0 < cat && 0 < subcat ){
        $.ajax({
                method: "POST",
                url: "{{url('getRegisteredSubjectsAndPapersByCatIdBySubcatId')}}",
                data: {cat:cat, subcat:subcat, userId:userId}
            })
            .done(function( msg ) {
              divEle = document.getElementById('subjects');
              divEle.innerHTML = '';
              if(undefined !== msg['subjects'] && 0 < msg['subjects'].length) {
                $.each(msg['subjects'], function(ind, obj){
                  var subId = obj.id;

                  var mainPanelDiv = document.createElement('div');
                  mainPanelDiv.className = "panel-group";
                  mainPanelDiv.setAttribute('role','tablist');
                  mainPanelDiv.setAttribute('aria-multiselectable','true');
                  mainPanelDiv.id = 'headingOne';

                  var defaultPanelDiv = document.createElement('div');
                  defaultPanelDiv.className = "panel panel-default";

                  var firstMainDiv = document.createElement('div');
                  firstMainDiv.className = "panel-heading";
                  firstMainDiv.setAttribute('role','tab');
                  firstMainDiv.id = 'headingOne';

                  var h4Ele = document.createElement('h4');
                  h4Ele.className = 'panel-title';
                  h4Ele.title = obj.name;
                  h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject'+ subId +'" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-plus"></i>'+ obj.name + '</a>';
                  firstMainDiv.appendChild(h4Ele);
                  defaultPanelDiv.appendChild(firstMainDiv);

                  var secondMainDiv = document.createElement('div');
                  secondMainDiv.id = 'subject'+ subId;
                  secondMainDiv.className = "panel-collapse collapse panel-lg";
                  secondMainDiv.setAttribute('role','tabpanel');
                  secondMainDiv.setAttribute('aria-labelledby','headingOne');

                  var tableDiv = document.createElement('div');
                  tableDiv.className = 'panel-body';

                  var tableEle = document.createElement('table');
                  tableEle.className = "table data-lg";
                  if (undefined !== msg['papers'][subId] && msg['papers'][subId].length) {
                    var tableHead = document.createElement('thead');
                    var tableTr = document.createElement('tr');
                    var trInnerhtml = '';
                    trInnerhtml += '<th>Test Name</th>';
                    trInnerhtml += '<th>Start Test</th>';
                    trInnerhtml += '<th>Result</th>';
                    trInnerhtml += '<th>Date to Active</th>';
                    trInnerhtml += '<th>Date to Inactive</th>';
                    tableTr.innerHTML = trInnerhtml;
                    tableHead.appendChild(tableTr);
                    tableEle.appendChild(tableHead);
                  }
                  var tableBody = document.createElement('tbody');
                  if (undefined !== msg['papers'][subId] && msg['papers'][subId].length) {
                    $.each(msg['papers'][subId], function(ind, obj){
                      var tbodyTr = document.createElement("tr");
                      if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
                        tbodyTr.setAttribute('style','background-color: #b3c2dc;');

                        var divInnerHtml = '';
                        divInnerHtml += '<td class=" ">'+ obj.name+'</td>';
                        if(msg['currentDate'] < obj.date_to_active){
                          divInnerHtml += '<td id="startTest"><button disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
                        } else {
                          if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
                            divInnerHtml += '<td id="startTest"><button disabled="true" data-toggle="tooltip" title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
                          } else {
                            divInnerHtml += '<td id="startTest"><button onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.subject_id +'" data-category="'+ obj.category_id +'" data-subcategory="'+ obj.sub_category_id+'" data-toggle="tooltip" title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
                          }
                        }
                        if(msg['currentDate'] < obj.date_to_active){
                          divInnerHtml += '<td class=" ">';
                          divInnerHtml += '<button disabled="true" data-toggle="tooltip" title="Result will display after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
                          divInnerHtml += '</td>';
                        } else if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
                          var testUrl = "{{ url('showUserTestResult') }}";
                          var csrf_token = '{{ csrf_field() }}';
                          divInnerHtml += '<td class=" ">';
                          divInnerHtml += '<form id="showUserTestResult_'+obj.id+'" method="POST" action="'+testUrl+'">';
                          divInnerHtml += csrf_token;
                          divInnerHtml +='<input type="hidden" name="paper_id" value="'+obj.id+'"><input type="hidden" name="category_id" value="'+ obj.category_id +'"><input type="hidden" name="subcategory_id" value="'+ obj.sub_category_id+'"><input type="hidden" name="subject_id" value="'+ obj.subject_id +'"></form>';
                          divInnerHtml += '<button onClick="showUserTestResult(this);" data-paper_id="'+obj.id+'" data-toggle="tooltip" title="Result!"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
                          divInnerHtml += '</td>';
                        } else {
                          divInnerHtml += '<td class=" ">';
                          divInnerHtml += '<button disabled="true" data-toggle="tooltip" title="Result will display after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
                          divInnerHtml += '</td>';
                        }
                        divInnerHtml += '<td class=" ">'+ obj.date_to_active +'</td>';
                        divInnerHtml += '<td class=" ">'+ obj.date_to_inactive +'</td>';

                        tbodyTr.innerHTML = divInnerHtml;
                        tableBody.appendChild(tbodyTr);
                        tableEle.appendChild(tableBody);
                      }
                    });
                  } else {
                    tableBody.innerHTML = 'No papers are available..';
                    tableEle.appendChild(tableBody);
                  }
                  tableDiv.appendChild(tableEle);

                  var mainSmallDiv = document.createElement('div');
                  mainSmallDiv.className = "data-sm";

                  if (undefined !== msg['papers'][subId] && msg['papers'][subId].length) {
                    $.each(msg['papers'][subId], function(ind, obj){
                      var panelDiv = document.createElement('div');
                      panelDiv.className = 'panel panel-info';

                      var panelHeadingDiv = document.createElement('div');
                      panelHeadingDiv.className = 'toggle panel-heading';
                      panelHeadingDiv.setAttribute('data-toggle','paper'+obj.id);
                      if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
                        panelHeadingDiv.setAttribute('style','background-color: #b3c2dc;');

                        panelHeadingDiv.innerHTML = obj.name;

                        var spanEle = document.createElement('span');
                        spanEle.className = 'col-xs-2 pull-right';
                        spanEle.innerHTML = '<i class="fa fa-chevron-down pull-right"></i>';
                        panelHeadingDiv.appendChild(spanEle);

                        panelDiv.appendChild(panelHeadingDiv);

                        var panelContentDiv = document.createElement('div');
                        panelContentDiv.id = 'paper'+obj.id;
                        panelContentDiv.className = 'panel-body';

                        var containerDiv = document.createElement('div');
                        containerDiv.className='container';

                        var rowDiv = document.createElement('div');
                        rowDiv.className = 'fluid-row';

                        var ulDiv = document.createElement('ul');
                        ulDivInnerHtml = '';
                        if(msg['currentDate'] < obj.date_to_active){
                          ulDivInnerHtml += '<li id="startTest"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Test will be enabled on date to active."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
                        } else {
                          if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
                            ulDivInnerHtml += '<li id="startTest"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
                          } else {
                            ulDivInnerHtml += '<li id="startTest"><button class="btn-magick btn-sm btn3d" onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.subject_id +'" data-category="'+ obj.category_id +'" data-subcategory="'+ obj.sub_category_id+'" data-toggle="tooltip" title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
                          }
                        }
                        if(msg['currentDate'] < obj.date_to_active){
                            ulDivInnerHtml += '<li class=" ">';
                            ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will display after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
                            ulDivInnerHtml += '</li>';
                        } else if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
                            var testUrl = "{{ url('showUserTestResult') }}";
                            var csrf_token = '{{ csrf_field() }}';
                            ulDivInnerHtml += '<li class=" ">';
                            ulDivInnerHtml += '<form id="showUserTestResult_'+obj.id+'" method="POST" action="'+testUrl+'">';
                            ulDivInnerHtml += csrf_token;
                            ulDivInnerHtml +='<input type="hidden" name="paper_id" value="'+obj.id+'"><input type="hidden" name="category_id" value="'+ obj.category_id +'"><input type="hidden" name="subcategory_id" value="'+ obj.sub_category_id+'"><input type="hidden" name="subject_id" value="'+ obj.subject_id +'"></form>';
                            ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="'+obj.id+'" data-toggle="tooltip" title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
                            ulDivInnerHtml += '</li>';
                        } else {
                          ulDivInnerHtml += '<li class=" ">';
                          ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Result will display after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
                          ulDivInnerHtml += '</li>';
                        }
                        ulDivInnerHtml += '<li class=" "><button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-inr"></span>'+ obj.date_to_active +'</button></li><li class=" "><button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-inr"></span>'+ obj.date_to_inactive +'</button></li>';
                        ulDiv.innerHTML = ulDivInnerHtml;
                        rowDiv.appendChild(ulDiv);
                        containerDiv.appendChild(rowDiv);
                        panelContentDiv.appendChild(containerDiv);
                        panelDiv.appendChild(panelContentDiv);
                        mainSmallDiv.appendChild(panelDiv);
                      }
                    });
                  }
                  tableDiv.appendChild(mainSmallDiv);
                  secondMainDiv.appendChild(tableDiv);
                  defaultPanelDiv.appendChild(secondMainDiv);
                  mainPanelDiv.appendChild(defaultPanelDiv);
                  divEle.appendChild(mainPanelDiv);
                });
              } else {
                  var mainPanelDiv = document.createElement('div');
                  mainPanelDiv.className = "panel-group";
                  mainPanelDiv.setAttribute('role','tablist');
                  mainPanelDiv.setAttribute('aria-multiselectable','true');
                  mainPanelDiv.id = 'headingOne';

                  var defaultPanelDiv = document.createElement('div');
                  defaultPanelDiv.className = "panel panel-default";

                  var firstMainDiv = document.createElement('div');
                  firstMainDiv.className = "panel-heading";
                  firstMainDiv.setAttribute('role','tab');
                  firstMainDiv.id = 'headingOne'

                  var h4Ele = document.createElement('h4');
                  h4Ele.className = 'panel-title';
                  h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-plus"></i>No subjects are available.</a>';
                  firstMainDiv.appendChild(h4Ele);
                  defaultPanelDiv.appendChild(firstMainDiv);
                  mainPanelDiv.appendChild(defaultPanelDiv);
                  divEle.appendChild(mainPanelDiv);
              }
            $('[id^=paper]').hide();
            $('.toggle').click(function() {
              $input = $( this );
              $target = $('#'+$input.attr('data-toggle'));
              $target.slideToggle();
              if($input.find('.col-xs-2 i').attr('class')=="fa fa-chevron-down pull-right")
              {
               $input.find('.col-xs-2 i').removeClass('fa-chevron-down');
               $input.find('.col-xs-2 i').addClass('fa-chevron-up');
             }
             else if($input.find('.col-xs-2 i').attr('class')=="fa pull-right fa-chevron-up")
             {
               $input.find('.col-xs-2 i').removeClass('fa-chevron-up');
               $input.find('.col-xs-2 i').addClass('fa-chevron-down');
             }
             else if($input.find('.col-xs-2 i').attr('class')=="fa pull-right fa-chevron-down")
             {
               $input.find('.col-xs-2 i').removeClass('fa-chevron-down');
               $input.find('.col-xs-2 i').addClass('fa-chevron-up');
             }
           });
        });
      } else {
        divEle = document.getElementById('subjects');
        divEle.innerHTML = '';

        var mainPanelDiv = document.createElement('div');
        mainPanelDiv.className = "panel-group";
        mainPanelDiv.setAttribute('role','tablist');
        mainPanelDiv.setAttribute('aria-multiselectable','true');
        mainPanelDiv.id = 'headingOne';

        var defaultPanelDiv = document.createElement('div');
        defaultPanelDiv.className = "panel panel-default";

        var firstMainDiv = document.createElement('div');
        firstMainDiv.className = "panel-heading";
        firstMainDiv.setAttribute('role','tab');
        firstMainDiv.id = 'headingOne'

        var h4Ele = document.createElement('h4');
        h4Ele.className = 'panel-title';
        h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" href="" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-plus"></i>No subjects are available.</a>';
        firstMainDiv.appendChild(h4Ele);
        defaultPanelDiv.appendChild(firstMainDiv);
        mainPanelDiv.appendChild(defaultPanelDiv);
        divEle.appendChild(mainPanelDiv);
      }
    }
  </script>
<script >
  function toggleIcon(e) {
      $(e.target)
      .prev('.panel-heading')
      .find(".more-less")
      .toggleClass('glyphicon-plus glyphicon-minus');
    }
  $('.panel-group').on('hidden.bs.collapse', toggleIcon);
  $('.panel-group').on('shown.bs.collapse', toggleIcon);

    $(document).ready(function() {
      $('[id^=paper]').hide();
      $('.toggle').click(function() {
        $input = $( this );
        $target = $('#'+$input.attr('data-toggle'));
        $target.slideToggle();
        if($input.find('.col-xs-2 i').attr('class')=="fa fa-chevron-down pull-right")
        {
         $input.find('.col-xs-2 i').removeClass('fa-chevron-down');
         $input.find('.col-xs-2 i').addClass('fa-chevron-up');
       }
       else if($input.find('.col-xs-2 i').attr('class')=="fa pull-right fa-chevron-up")
       {
         $input.find('.col-xs-2 i').removeClass('fa-chevron-up');
         $input.find('.col-xs-2 i').addClass('fa-chevron-down');
       }
       else if($input.find('.col-xs-2 i').attr('class')=="fa pull-right fa-chevron-down")
       {
         $input.find('.col-xs-2 i').removeClass('fa-chevron-down');
         $input.find('.col-xs-2 i').addClass('fa-chevron-up');
       }

     });
    });
</script>
@stop