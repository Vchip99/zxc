@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Test Results </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-group"></i> Users Info</li>
      <li class="active">Test Results </li>
    </ol>
  </section>
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="">
            <input type="hidden" id="login_User_Type" name="login_User_Type" value="{{Auth::user()->user_type}}">
            @if(4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
            <div class="col-md-3 mrgn_10_btm" id="showUsers">
              <select class="form-control" id="user" name="user_type" onChange="resetDepartments();" required="true">
                <option value="0">Select User Type</option>
                <option value="2" @if(is_object($selectedStudent) && '2' == $selectedStudent->user_type) selected="true" @endif >Student</option>
                <option value="3" @if(is_object($selectedStudent) && '3' == $selectedStudent->user_type) selected="true" @endif >Lecturer</option>
                @if(5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
                  <option value="4" @if(is_object($selectedStudent) && '4' == $selectedStudent->user_type) selected="true" @endif >Hod</option>
                @endif
              </select>
            </div>
            @endif
              @if(5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
                <div class="col-md-3 mrgn_10_btm">
                  <select class="form-control" id="dept" onChange="resetYear(this);">
                    <option value="0"> Select Department </option>
                    @if(count($collegeDepts) > 0)
                      @foreach($collegeDepts as $collegeDept)
                        @if(is_object($selectedStudent) && $selectedStudent->college_dept_id == $collegeDept->id)
                          <option value="{{$collegeDept->id}}" selected="true">{{$collegeDept->name}}</option>
                        @else
                          <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                        @endif
                      @endforeach
                    @endif
                  </select>
                </div>
              @endif
              @if((is_object($selectedStudent) && 2 == $selectedStudent->user_type) || 3 == Auth::user()->user_type)
                <div class="col-md-3 mrgn_10_btm" id="div_year">
              @else
                <div class="col-md-3 mrgn_10_btm hide" id="div_year">
              @endif
                  <select class="form-control" id="selected_year" name="year" onChange="showStudents(this);">
                    <option value="0"> Select Year </option>
                    <option value="1" @if(is_object($selectedStudent) &&'1' == $selectedStudent->year) selected="true" @endif >First Year</option>
                    <option value="2" @if(is_object($selectedStudent) &&'2' == $selectedStudent->year) selected="true" @endif >Second Year</option>
                    <option value="3" @if(is_object($selectedStudent) &&'3' == $selectedStudent->year) selected="true" @endif >Third Year</option>
                    <option value="4" @if(is_object($selectedStudent) &&'4' == $selectedStudent->year) selected="true" @endif >Fourth Year</option>
                  </select>
                </div>

              <div class="col-md-3 ">
                <select class="form-control" id="student" onChange="showResult(this);">
                  <option value="0">Select User </option>
                  @if(is_object($selectedStudent) && count($students) > 0)
                    @foreach($students as $student)
                      @if(is_object($selectedStudent) && $selectedStudent->year == $student->year)
                        @if($selectedStudent->id == $student->id)
                          <option value="{{$student->id}}" selected="true">{{$student->name}}</option>
                        @else
                          <option value="{{$student->id}}">{{$student->name}}</option>
                        @endif
                      @endif
                    @endforeach
                  @endif
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="mrgn_20_btm">
              <div class="col-md-3 mrgn_10_btm">
               <select class="form-control" id="category" id="category" name="category" title="Category" onChange="selectSubcategory(this);">
                <option value="0">Select Category</option>
                @if(count($categories) > 0)
                  @foreach($categories as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                  @endforeach
                @endif
               </select>
              </div>
              <div class="col-md-3 ">
               <select class="form-control" id="subcategory" name="subcategory" title="Sub Category" onChange="showResultWithCategorySubCategory(this);">
                <option value="0">Select Sub Category</option>
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
                    @if(is_object($selectedStudent) && count($results) > 0)
                      @foreach($results as $index => $result)
                        <tr class="">
                          <td>{{$index + 1}}</td>
                          <td>{{$result->subject->name}}</td>
                          <td>{{$result->paper->name}}</td>
                          <td class="center">{{$result->test_score}} / {{$result->totalMarks()['totalMarks']}}</td>
                          <td class="center">{{$result->rank($selectedStudent->college_id)}}</td>
                        </tr>
                      @endforeach
                    @elseif(is_object($selectedStudent) && 0 == count($results))
                      <tr class="">
                        <td colspan="5">No result for selected user.</td>
                      </tr>
                    @else
                      <tr class="">
                        <td colspan="5">Select year and student to see result.</td>
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
                            <div class="barchart-Bar" style="height: {{$result->totalMarks()['percentage']}}%;" title="{{$result->totalMarks()['percentage']}}%" attr-height="{{$result->totalMarks()['percentage']}}%"></div>
                            <div class="barchart-BarFooter " title="{{$result->subject->name}}-{{$result->paper->name}}">{{$result->subject->name}}-{{$result->paper->name}}</div>
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

  function resetYear(){
    var user_type = parseInt(document.getElementById('user').value);
    if(3 == user_type || 4 == user_type){
      showStudents();
    } else {
      document.getElementById('selected_year').value = 0;
    }
    document.getElementById('student').value = 0;
    document.getElementById('category').value = 0;
    document.getElementById('subcategory').value = 0;
    document.getElementById('test-result').innerHTML = '';
    document.getElementById('barchart').innerHTML = '';
  }

  function resetDepartments(){
    var user_type = parseInt(document.getElementById('user').value);
    var login_User_Type = parseInt(document.getElementById('login_User_Type').value);

    if(document.getElementById('dept')){
      document.getElementById('dept').value = 0;
    }
    document.getElementById('student').value = 0;
    document.getElementById('category').value = 0;
    document.getElementById('subcategory').value = 0;
    document.getElementById('test-result').innerHTML = '';
    document.getElementById('barchart').innerHTML = '';
    if(2 == user_type){
      document.getElementById('div_year').classList.remove('hide');
      document.getElementById('selected_year').value = 0;
    }
    if(3 == user_type || 4 == user_type){
      document.getElementById('div_year').classList.add('hide');
      document.getElementById('selected_year').value = 0;
    }
    if(4 == login_User_Type && 3 == user_type){
      showStudents();
    } else {
      resetUser();
    }
  }
  function showStudents(){
    if(document.getElementById('user')){
      var user_type = parseInt(document.getElementById('user').value);
    } else {
      var login_User_Type = parseInt(document.getElementById('login_User_Type').value);
      if(3 == login_User_Type){
        var user_type = 2;
      } else {
        var user_type = 0;
      }
    }
    if(document.getElementById('selected_year')){
      var year = parseInt(document.getElementById('selected_year').value);
    } else {
      var year = 0;
    }
    if(document.getElementById("dept")){
        var department = parseInt(document.getElementById("dept").value);
    } else {
        var department = 0;
    }
    document.getElementById('category').value = 0;
    document.getElementById('subcategory').value = 0;
    document.getElementById('test-result').innerHTML = '';
    document.getElementById('barchart').innerHTML = '';

    $.ajax({
          method: "POST",
          url: "{{url('showStudentsByDepartmentByYear')}}",
          data: {year:year,department:department,user_type:user_type}
      })
      .done(function( msg ) {
        select = document.getElementById('student');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '0';
        opt.innerHTML = 'Select User';
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
  function showResultWithCategorySubCategory(){
    var subcategory = parseInt(document.getElementById('subcategory').value);
    var category = parseInt(document.getElementById('category').value);
    var student = parseInt(document.getElementById('student').value);
    if(document.getElementById('selected_year')){
      var year = parseInt(document.getElementById('selected_year').value);
    } else {
      var year = 0;
    }
    if(document.getElementById("dept")){
        var department = parseInt(document.getElementById("dept").value);
    } else {
        var department = 0;
    }
    if(student > 0){
      renderResult(category,subcategory,student,year,department);
    } else {
      body = document.getElementById('test-result');
      body.innerHTML = '';
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = 'Select User!';
      eleIndex.setAttribute('colspan', '5');
      eleTr.appendChild(eleIndex);
      body.appendChild(eleTr);
    }
  }

  function showResult(ele){
    document.getElementById('category').value = 0;
    document.getElementById('subcategory').value = 0;
    var subcategory = parseInt(document.getElementById('subcategory').value);
    var category = parseInt(document.getElementById('category').value);
    var student = parseInt(document.getElementById('student').value);
    if(document.getElementById('selected_year')){
      var year = parseInt(document.getElementById('selected_year').value);
    } else {
      var year = 0;
    }
    if(document.getElementById("dept")){
        var department = parseInt(document.getElementById("dept").value);
    } else {
        var department = 0;
    }
    if(student > 0){
      renderResult(category,subcategory,student,year,department);
    } else {
      body = document.getElementById('test-result');
      body.innerHTML = '';
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = 'Select User!';
      eleIndex.setAttribute('colspan', '5');
      eleTr.appendChild(eleIndex);
      body.appendChild(eleTr);
    }
  }

  function renderResult(category,subcategory,student,year,department){
    $.ajax({
              method: "POST",
              url: "{{url('showTestResults')}}",
              data: {category:category,subcategory:subcategory,student:student,year:year,department:department}
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
                  eleInnerHtml = '<div class="barchart-Bar" style="height:'+ msg['marks'][obj.id]['percentage']+ '%;" title="'+ msg['marks'][obj.id]['percentage']+ '%" attr-height="'+ msg['marks'][obj.id]['percentage']+ '%"></div>';
                  eleInnerHtml += '<div class="barchart-BarFooter " title="'+obj.subject+'-'+obj.paper+'">'+obj.subject+'-'+obj.paper+'</div>';
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
    if( 0 < id ){
      $.ajax({
            method: "POST",
            url: "{{url('getSubCategories')}}",
            data: {id:id}
        })
        .done(function( msg ) {
          select = document.getElementById('subcategory');
          select.innerHTML = '';
          var opt = document.createElement('option');
          opt.value = '0';
          opt.innerHTML = 'Select Sub Category';
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

  function resetUser(){
    select = document.getElementById('student');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '0';
    opt.innerHTML = 'Select User';
    select.appendChild(opt);
  }

</script>
@stop