@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Topic  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Assignment </li>
      <li class="active"> Manage Topic </li>
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
            @if(count($allCollegeDepts) > 0)
              @foreach($allCollegeDepts as $deptId => $departmentName)
                <option value="{{ $deptId }}">{{ $departmentName }}</option>
              @endforeach
            @endif
          </select>
        </div>
        <div class="col-md-3 mrgn_10_btm">
          <select class="form-control" id="year" name="year" required title="year" onChange="getTopics(this);">
            <option value="">Select Year</option>
            <option value="1">First </option>
            <option value="2">Second </option>
            <option value="3">Third </option>
            <option value="4">Fourth </option>
          </select>
        </div>
      @endif
      <div id="addTopicDiv">
        <a id="addTopic" href="{{url('college/'.Session::get('college_user_url').'/createAssignmentTopic')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Topic">Add New Topic</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="assignmentTopics">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Topic </th>
          <th>Subject </th>
          <th>Departments </th>
          <th>Years </th>
          <th>Created By </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody id="assignmentTopic">
        @if(count($topics) > 0)
          @foreach($topics as $index => $topic)
          <tr style="overflow: auto;">
            <td>{{$index + $topics->firstItem()}}</td>
            <td>{{$topic->name}}</td>
            <td>{{$allSubjects[$topic->college_subject_id]}}</td>
            @php
              $topicDepts = explode(',',$topic->college_dept_ids);
              $topicYears = explode(',',$topic->years);
            @endphp
            <td>
              @if(count($topicDepts) > 0)
                @foreach($topicDepts as $index => $topicDept)
                  @if(0 == $index)
                    {{$allCollegeDepts[$topicDept]}}
                  @else
                    ,{{$allCollegeDepts[$topicDept]}}
                  @endif
                @endforeach
              @endif
            </td>
            <td>
              @if(count($topicYears) > 0)
                @foreach($topicYears as $index => $year)
                  @if(0 == $index)
                    @if(1 == $year)
                      First
                    @elseif(2 == $year)
                      Second
                    @elseif(3 == $year)
                      Third
                    @elseif(4 == $year)
                      Fourth
                    @endif
                  @else
                    @if(1 == $year)
                      ,First
                    @elseif(2 == $year)
                      ,Second
                    @elseif(3 == $year)
                      ,Third
                    @elseif(4 == $year)
                      ,Fourth
                    @endif
                  @endif
                @endforeach
              @endif
            </td>
            <td>{{$topic->user}}</td>
            <td>
              @if($topic->lecturer_id == Auth::user()->id || (4 == Auth::user()->user_type || 5 == Auth::user()->user_type))
              <a href="{{url('college/'.Session::get('college_user_url').'/assignmentTopic')}}/{{$topic->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$topic->name}}" />
                </a>
              @endif
            </td>
            <td>
              @if($topic->lecturer_id == Auth::user()->id || (4 == Auth::user()->user_type || 5 == Auth::user()->user_type))
                <a id="{{$topic->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$topic->name}}" />
                </a>
                <form id="deleteTopic_{{$topic->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteAssignmentTopic')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="topic_id" value="{{$topic->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="8">No Topics are created by you.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $topics->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

  function resetYear(){
    document.getElementById('year').selectedIndex = '';
  }

  function getTopics(){
    var year = document.getElementById('year').value;
    var department = document.getElementById('department').value;
    $.ajax({
        method: "POST",
        url: "{{url('getAssignmentTopicsByDeptIdByYear')}}",
        data: {department:department,year:year}
    })
    .done(function( result ) {
      body = document.getElementById('assignmentTopic');
      body.innerHTML = '';
        if(result['topics'].length > 0){
          $.each(result['topics'], function(idx, topic) {
            var eleTr = document.createElement('tr');
            eleTr.setAttribute("style","overflow: auto;");

            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = idx + 1;
            eleTr.appendChild(eleIndex);

            var eleTopic = document.createElement('td');
            eleTopic.innerHTML = topic.name;
            eleTr.appendChild(eleTopic);

            var eleSubject = document.createElement('td');
            eleSubject.innerHTML = topic.subject;
            eleTr.appendChild(eleSubject);

            var depts = topic.college_dept_ids.split(',');
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

            var eleYear = document.createElement('td');
            var years = topic.years.split(',');
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

            var eleCreatedBy = document.createElement('td');
            eleCreatedBy.innerHTML = topic.user;
            eleTr.appendChild(eleCreatedBy);

            var url = "{{url('college/'.Session::get('college_user_url').'/assignmentTopic')}}/"+topic.id+"/edit";
            var imageSrc = "{{asset('images/edit1.png')}}";
            var eleRemark = document.createElement('td');
            eleRemark.innerHTML = '<a href="'+ url +'" ><img src="'+imageSrc+'" width=\'30\' height=\'30\' title=" Edit " /></a>';
            eleTr.appendChild(eleRemark);


            var url = "{{url('college/'.Session::get('college_user_url').'/deleteAssignmentTopic')}}";
            var imageSrc = "{{asset('images/delete2.png')}}";
            var csrfField = '{{ csrf_field() }}';
            var deleteMethod ='{{ method_field("DELETE") }}';
            var eleDelete = document.createElement('td');
            eleDelete.innerHTML = '<a id="'+ topic.id+'" onclick="confirmDelete(this);" ><img src="'+imageSrc+'" width=\'30\' height=\'30\' title=" Delete  " /></a>';
            eleDelete.innerHTML += '<form id="deleteTopic_'+ topic.id+'" action="'+url+'" method="POST" style="display: none;">'+csrfField+''+deleteMethod+'<input type="hidden" name="topic_id" value="'+ topic.id+'"></form>';

            eleTr.appendChild(eleDelete);

            body.appendChild(eleTr);
          });
        } else {
          var eleTr = document.createElement('tr');
          eleTr.setAttribute("style","overflow: auto;");
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = 'No Topics are created.';
          eleIndex.setAttribute("colspan","8");
          eleTr.appendChild(eleIndex);
          body.appendChild(eleTr);
        }
    });
  }

  function confirmDelete(ele){
    $.confirm({
      title: 'Confirmation',
      content: 'If you delete this topic, all associated assignments and its answers will be deleted.',
      type: 'red',
      typeAnimated: true,
      buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-red',
                action: function(){
                  var id = $(ele).attr('id');
                  formId = 'deleteTopic_'+id;
                  document.getElementById(formId).submit();
                }
            },
            Cancel: function () {
            }
        }
      });
  }
</script>
@stop