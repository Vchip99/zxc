@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> User Test Result </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-group"></i> Users Info </li>
      <li class="active"> User Test Result </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="college" name="college" onChange="showDepartments();">
                <option value="0"> Select College </option>
                @if(is_object($selectedStudent) && 'other' == $selectedStudent->college_id)
                  <option value="other" selected="true">Other</option>
                @else
                  <option value="other">Other</option>
                @endif
                @if(count($colleges) > 0)
                  @foreach($colleges as $college)
                    @if(is_object($selectedStudent) && $selectedStudent->college_id == $college->id)
                      <option value="{{$college->id}}" selected="true">{{$college->name}}</option>
                    @else
                      <option value="{{$college->id}}">{{$college->name}}</option>
                    @endif
                  @endforeach
                @endif
              </select>
            </div>
            @if(is_object($selectedStudent) && ('other' == $selectedStudent->college_id || 5 == $selectedStudent->user_type || 6 == $selectedStudent->user_type))
              <div class="col-md-3 mrgn_10_btm hide" id="dept">
            @else
              <div class="col-md-3 mrgn_10_btm" id="dept">
            @endif
              <select class="form-control" id="selected_dept" name="departemnt" onChange="resetYear();">
                <option value="0"> Select Departemnt </option>
                @if(is_object($selectedStudent) && count($collegeDepts) > 0)
                  @foreach($collegeDepts as $collegeDept)
                    @if($selectedStudent->college_dept_id == $collegeDept->id)
                      <option value="{{$collegeDept->id}}" selected="true"> {{$collegeDept->name}} </option>
                    @else
                      <option value="{{$collegeDept->id}}"> {{$collegeDept->name}} </option>
                    @endif
                  @endforeach
                @endif
              </select>
            </div>
            @if(is_object($selectedStudent) && ('other' == $selectedStudent->college_id || 4 == $selectedStudent->user_type || 5 == $selectedStudent->user_type || 6 == $selectedStudent->user_type))
              <div class="col-md-3 mrgn_10_btm hide" id="showYears">
            @else
              <div class="col-md-3 mrgn_10_btm" id="showYears">
            @endif
              <select class="form-control" id="selected_year" name="year" onChange="showStudents(this.value);">
                <option value="0"> Select Year </option>
                  <option value="1" @if(is_object($selectedStudent) &&'1' == $selectedStudent->year) selected="true" @endif >First Year</option>
                  <option value="2" @if(is_object($selectedStudent) &&'2' == $selectedStudent->year) selected="true" @endif >Second Year</option>
                  <option value="3" @if(is_object($selectedStudent) &&'3' == $selectedStudent->year) selected="true" @endif >Third Year</option>
                  <option value="4" @if(is_object($selectedStudent) &&'4' == $selectedStudent->year) selected="true" @endif >Fourth Year</option>
              </select>
            </div>
            <div class="col-md-3 mrgn_10_btm" id="student">
              <select class="form-control" id="selected_student" name="student" onChange="showResult();">
                <option value="0"> Select User </option>
                 @if(is_object($selectedStudent) && count($students) > 0)
                  @foreach($students as $student)
                    @if($selectedStudent->year == $student->year)
                      @if($selectedStudent->id == $student->id)
                        <option value="{{$student->id}}" selected="true"> {{$student->name}} </option>
                      @else
                        <option value="{{$student->id}}"> {{$student->name}} </option>
                      @endif
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
             <select class="form-control" id="subcategory" name="subcategory" title="Sub Category" onChange="showResult();">
              <option value="0">Select Sub Category</option>
             </select>
            </div>
          </div>
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
                User Result
              </div>
              <div class="panel-body">
                <table>
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Subject</th>
                      <th>Paper</th>
                      <th>Marks</th>
                      <th>Rank</th>
                    </tr>
                  </thead>
                  <tbody id="test-result">
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
                        <td colspan="5">Select college,department, year and student to see result.</td>
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
  function resetYear(){
    document.getElementById('selected_year').value = 0;
    document.getElementById('selected_student').value = 0;
    document.getElementById('test-result').innerHTML = '';
    document.getElementById('barchart').innerHTML = '';
    document.getElementById('category').value = 0;
    unsetSubCategory();
    unsetStudent();
  }

  function showResult(ele){
    var subcategory = parseInt(document.getElementById('subcategory').value);
    var category = parseInt(document.getElementById('category').value);
    var college = parseInt(document.getElementById('college').value);
    var department = parseInt(document.getElementById('selected_dept').value);
    var student = parseInt(document.getElementById('selected_student').value);
    var year = parseInt(document.getElementById('selected_year').value);
    $.ajax({
            method: "POST",
            url: "{{url('admin/showUserTestResults')}}",
            data: {category:category,subcategory:subcategory,college:college,department:department,student:student,year:year}
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
            eleIndex.innerHTML = 'No result for selected user.';
            eleIndex.setAttribute('colspan', '5');
            eleTr.appendChild(eleIndex);
            body.appendChild(eleTr);
          }
    });
  }
  function showStudents(){
    var college = document.getElementById('college').value;
    var user_type = 2;
    var selected_dept = document.getElementById('selected_dept').value;
    var selected_year = document.getElementById('selected_year').value;
    document.getElementById('selected_student').value = 0;
    document.getElementById('test-result').innerHTML = '';
    document.getElementById('barchart').innerHTML = '';
    document.getElementById('category').value = 0;
    unsetSubCategory();

    if(user_type > 0){
      $.ajax({
        method: "POST",
        url: "{{url('admin/searchUsers')}}",
        data:{college_id:college, user_type:user_type, department_id:selected_dept, selected_year:selected_year}
      })
      .done(function( msg ) {
        select = document.getElementById('selected_student');
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
  }

  function showDepartments(){
    var college = document.getElementById('college').value;
    document.getElementById('dept').classList.remove('hide');
    document.getElementById('showYears').classList.remove('hide');

    document.getElementById('selected_dept').value = 0;
    document.getElementById('selected_year').value = 0;
    document.getElementById('test-result').innerHTML = '';
    document.getElementById('barchart').innerHTML = '';

    document.getElementById('category').value = 0;
    unsetSubCategory();
    unsetStudent();

    if(college > 0){
      $.ajax({
        method: "POST",
        url: "{{url('admin/getDepartments')}}",
        data:{college:college}
      })
      .done(function( msg ) {
        select = document.getElementById('selected_dept');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '0';
        opt.innerHTML = 'Select Department';
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
    } else {
      document.getElementById('dept').classList.add('hide');
      document.getElementById('showYears').classList.add('hide');
      showStudents();
    }
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
  function unsetSubCategory(){
    select = document.getElementById('subcategory');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '0';
    opt.innerHTML = 'Select Sub Category';
    select.appendChild(opt);
  }

  function unsetStudent(){
    select = document.getElementById('selected_student');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '0';
    opt.innerHTML = 'Select User';
    select.appendChild(opt);
  }
</script>
@stop