@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Subject  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Subject </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
    <div class="form-group row">
      @if(5 == Auth::user()->user_type)
        <div class="col-md-3 mrgn_10_btm">
          <select class="form-control" id="department" name="department" title="department" onChange="resetYear(this);">
            <option value="">Select Department</option>
            @if(count($allDepts) > 0)
              @foreach($allDepts as $deptId => $departmentName)
                <option value="{{ $deptId }}">{{ $departmentName }}</option>
              @endforeach
            @endif
          </select>
        </div>
        <div class="col-md-3 mrgn_10_btm">
          <select class="form-control" id="year" name="year" required title="year" onChange="getSubjects(this);">
            <option value="">Select Year</option>
            <option value="1">First </option>
            <option value="2">Second </option>
            <option value="3">Third </option>
            <option value="4">Fourth </option>
          </select>
        </div>
      @endif
      <div>
        <a href="{{url('college/'.Session::get('college_user_url').'/createCollegeSubject')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Subject">Add New Subject</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table id="collegeSubjects">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Subject </th>
          <th>Years</th>
          <th>Departments</th>
          <th>Created By </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody id="collegeSubjectsTable">
        @if(count($subjects) > 0)
          @foreach($subjects as $index => $subject)
          <tr style="overflow: auto;">
            <td>{{$index + $subjects->firstItem()}}</td>
            <td>{{$subject->name}}</td>
            <td>
              @php
                $years = explode(',', $subject->years);
              @endphp
              @if(count($years) > 0)
                @foreach($years as $index => $year)
                  @if(0 == $index)
                    @if( 1 == $year)
                      First
                    @elseif( 2 == $year)
                      Second
                    @elseif( 3 == $year)
                      Third
                    @elseif( 4 == $year)
                      Fourth
                    @endif
                  @else
                    @if( 1 == $year)
                      ,First
                    @elseif( 2 == $year)
                      ,Second
                    @elseif( 3 == $year)
                      ,Third
                    @elseif( 4 == $year)
                      ,Fourth
                    @endif
                  @endif
                @endforeach
              @endif
            </td>
            <td>
              @php
                $depts = explode(',', $subject->college_dept_ids);
              @endphp
              @if(count($depts) > 0)
                @foreach($depts as $index => $dept)
                  @if(0 == $index)
                    {{$allDepts[$dept]}}
                  @else
                    ,{{$allDepts[$dept]}}
                  @endif
                @endforeach
              @endif
            </td>
            <td>{{$subject->user}}</td>
            <td>
              @if($subject->lecturer_id == Auth::User()->id || 4 == Auth::User()->user_type || 5 == Auth::User()->user_type)
              <a href="{{url('college/'.Session::get('college_user_url').'/collegeSubject')}}/{{$subject->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$subject->name}}" />
                </a>
              @endif
            </td>
            <td>
              @if($subject->lecturer_id == Auth::User()->id || 4 == Auth::User()->user_type || 5 == Auth::User()->user_type)
                <a id="{{$subject->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$subject->name}}" />
                </a>
                <form id="deleteSubject_{{$subject->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCollegeSubject')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="subject_id" value="{{$subject->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="7">No subjects are created by you.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $subjects->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">
  function confirmDelete(ele){
    $.confirm({
      title: 'Confirmation',
      content: 'If you delete this subject, then all associated attendance, offline paper and its marks, topics, assignments and its answers will be deleted for associated departments and years.',
      type: 'red',
      typeAnimated: true,
      buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-red',
                action: function(){
                  var id = $(ele).attr('id');
                  formId = 'deleteSubject_'+id;
                  document.getElementById(formId).submit();
                }
            },
            Cancel: function () {
            }
        }
      });
  }

  function resetYear(){
    document.getElementById('year').selectedIndex = '';
  }

  function getSubjects(){
    var year = document.getElementById('year').value;
    var department = document.getElementById('department').value;
    $.ajax({
        method: "POST",
        url: "{{url('getCollegeSubjectsByDeptIdByYear')}}",
        data: {department:department,year:year}
    })
    .done(function( result ) {
      body = document.getElementById('collegeSubjectsTable');
      body.innerHTML = '';
        if(result['subjects'].length > 0){
          $.each(result['subjects'], function(idx, subject) {
            var eleTr = document.createElement('tr');
            eleTr.setAttribute("style","overflow: auto;");

            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = idx + 1;
            eleTr.appendChild(eleIndex);

            var eleSubject = document.createElement('td');
            eleSubject.innerHTML = subject.name;
            eleTr.appendChild(eleSubject);

            var eleYear = document.createElement('td');
            var years = subject.years.split(',');
            if(years.length > 0){
              $.each(years,function(idx,year){
                if(0 == idx){
                  if(1 == year){
                    eleYear.innerHTML = 'First';
                  } else if(2 == year){
                    eleYear.innerHTML = 'second';
                  } else if(3 == year){
                    eleYear.innerHTML = 'Third';
                  } else if(4 == year){
                    eleYear.innerHTML = 'Fourth';
                  }
                } else {
                  if(1 == year){
                    eleYear.innerHTML += ','+'First';
                  } else if(2 == year){
                    eleYear.innerHTML += ','+'second';
                  } else if(3 == year){
                    eleYear.innerHTML += ','+'Third';
                  } else if(4 == year){
                    eleYear.innerHTML += ','+'Fourth';
                  }
                }
              });
            }
            eleTr.appendChild(eleYear);

            var depts = subject.college_dept_ids.split(',');
            var eleDepts = document.createElement('td');
            if(depts.length > 0){
              $.each(depts,function(idx,dept){
                if(0 == idx){
                  eleDepts.innerHTML = result['depts'][dept];
                } else {
                  eleDepts.innerHTML += ','+result['depts'][dept];
                }
              });
            }
            eleTr.appendChild(eleDepts);

            var eleCreatedBy = document.createElement('td');
            eleCreatedBy.innerHTML = subject.user;
            eleTr.appendChild(eleCreatedBy);

            var url = "{{url('college/'.Session::get('college_user_url').'/collegeSubject')}}/"+subject.id+"/edit";
            var imageSrc = "{{asset('images/edit1.png')}}";
            var eleRemark = document.createElement('td');
            eleRemark.innerHTML = '<a href="'+ url +'" ><img src="'+imageSrc+'" width=\'30\' height=\'30\' title=" Edit " /></a>';
            eleTr.appendChild(eleRemark);


            var url = "{{url('college/'.Session::get('college_user_url').'/deleteCollegeSubject')}}";
            var imageSrc = "{{asset('images/delete2.png')}}";
            var csrfField = '{{ csrf_field() }}';
            var deleteMethod ='{{ method_field("DELETE") }}';
            var eleDelete = document.createElement('td');
            eleDelete.innerHTML = '<a id="'+ subject.id+'" onclick="confirmDelete(this);" ><img src="'+imageSrc+'" width=\'30\' height=\'30\' title=" Delete  " /></a>';
            eleDelete.innerHTML += '<form id="deleteTopic_'+ subject.id+'" action="'+url+'" method="POST" style="display: none;">'+csrfField+''+deleteMethod+'<input type="hidden" name="subject_id" value="'+ subject.id+'"></form>';

            eleTr.appendChild(eleDelete);

            body.appendChild(eleTr);
          });
        } else {
          var eleTr = document.createElement('tr');
          eleTr.setAttribute("style","overflow: auto;");
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = 'No subjects are created.';
          eleIndex.setAttribute("colspan","7");
          eleTr.appendChild(eleIndex);
          body.appendChild(eleTr);
        }
    });
  }

</script>
@stop