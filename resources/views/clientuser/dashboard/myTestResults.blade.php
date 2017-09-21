@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Test Results </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Test</li>
      <li class="active"> My Test Results </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div class="">
      <div class="container">
        <div class="row">
          <div class="mrgn_20_btm">
              <div class="col-sm-4 mrgn_10_btm">
                <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" title="Category">
                  <option value="">Select Category ...</option>
                  @if(count($categories) > 0)
                    @foreach($categories as $category)
                      <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                  @endif
                </select>
              </div>
              <div class="col-sm-4">
                <select id="subcategory" class="form-control" name="subcategory" onChange="showResult(this);" title="Sub Category">
                  <option value="">Select Sub Category ...</option>
                </select>
              </div>
          </div>
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
               RESULT
              </div>
              <div class="panel-body">
                <table  class="" id="dataTables-example">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Subject</th>
                      <th>Paper</th>
                      <th>Marks</th>
                      <th>Rank</th>
                    </tr>
                  </thead>
                  <tbody  id="test-result">
                    @if(count($results) > 0)
                      @foreach($results as $index => $result)
                        <tr class="">
                          <td>{{$index + 1}}</td>
                          <td>{{$result->subject->name}}</td>
                          <td>{{$result->paper->name}}</td>
                          <td class="center">{{$result->test_score}} / {{$result->totalMarks()['totalMarks']}}</td>
                          <td class="center">{{$result->rank()}}</td>
                        </tr>
                      @endforeach
                    @elseif(0 == count($results))
                      <tr class="">
                        <td colspan="5">No result for selected user.</td>
                      </tr>
                    @endif
                  </tbody>
                </table>
               </div>
            </div>
          </div>
          <div class="col-lg-12" id="bar-chart">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
                 RESULT
              </div>
              <div class="panel-body">
                <div class="barchart-Wrapper">
                  <div class="barchart-TimeCol">
                    @foreach($barchartLimits as $barchartLimit)
                      <div class="barchart-Time">
                        <span class="barchart-TimeText">{{$barchartLimit}}</span>
                      </div>
                    @endforeach
                  </div>
                  <div class="barChart-Container">
                    <div class="barchart" id="barchart">
                      @if(count($results) > 0)
                        @foreach($results as $index => $result)
                          <div class="barchart-Col">
                            <div class="barchart-Bar" style="height: {{$result->totalMarks()['percentage']}}%;" title="{{$result->subject->name}}-{{$result->paper->name}}" attr-height="{{$result->totalMarks()['percentage']}}%"></div>
                            <div class="barchart-BarFooter " title="{{$result->paper->name}}">{{$result->paper->name}}</div>
                          </div>
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
<script type="text/javascript">

 function showResult(ele){
    var subcategory = parseInt(document.getElementById('subcategory').value);
    var category = parseInt(document.getElementById('category').value);
    var student = parseInt(document.getElementById('user_id').value);
    $.ajax({
            method: "POST",
            url: "{{url('showUserTestResultsByCategoryBySubcategoryByUserId')}}",
            data: {category:category,subcategory:subcategory,student:student}
        })
        .done(function( msg ) {
          body = document.getElementById('test-result');
          body.innerHTML = '';
          barchart = document.getElementById('barchart');
          barchart.innerHTML = '';
          if( 0 < msg['scores'].length){
            $.each(msg['scores'], function(idx, obj) {
                var eleTr = document.createElement('tr');
                var eleIndex = document.createElement('td');
                eleIndex.innerHTML = idx + 1;
                eleTr.appendChild(eleIndex);

                var eleSubject = document.createElement('td');
                eleSubject.innerHTML = obj.subject;
                eleTr.appendChild(eleSubject);

                var elePaper = document.createElement('td');
                elePaper.innerHTML = obj.paper;
                eleTr.appendChild(elePaper);

                var eleScore = document.createElement('td');
                eleScore.innerHTML = obj.test_score+'/'+msg['marks'][obj.id]['totalMarks'];
                eleTr.appendChild(eleScore);

                var eleRank = document.createElement('td');
                eleRank.innerHTML = msg['ranks'][obj.id];
                eleTr.appendChild(eleRank);
                body.appendChild(eleTr);

                var eleMainDiv = document.createElement('div');
                eleMainDiv.className = 'barchart-Col';
                eleInnerHtml = '<div class="barchart-Bar" style="height:'+ msg['marks'][obj.id]['percentage']+ '%;" title="'+obj.subject+'-'+obj.paper+'" attr-height="'+ msg['marks'][obj.id]['percentage']+ '%"></div>';
                eleInnerHtml += '<div class="barchart-BarFooter " title="'+obj.paper+'">'+obj.paper+'</div>';
                eleMainDiv.innerHTML = eleInnerHtml;
                barchart.appendChild(eleMainDiv);
            });
          } else {
            var eleTr = document.createElement('tr');
            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = 'No result!';
            eleIndex.setAttribute('colspan', '5');
            eleTr.appendChild(eleIndex);
            body.appendChild(eleTr);
          }
    });
  }

  function selectSubcategory(ele){
    id = parseInt($(ele).val());
    var userId = parseInt(document.getElementById('user_id').value);
    document.getElementById('test-result').innerHTML = '';
    document.getElementById('barchart').innerHTML = '';
    if( 0 < id && 0 < userId ){
      $.ajax({
              method: "POST",
              url: "{{url('getOnlineTestSubCategories')}}",
              data: {id:id, userId:userId}
          })
          .done(function( msg ) {
            select = document.getElementById('subcategory');
            select.innerHTML = '';
            var opt = document.createElement('option');
          opt.value = '';
          opt.innerHTML = 'Select Sub Category ...';
          select.appendChild(opt);
          if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
          });
    }
  }
</script>
@stop