@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Offline Paper  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Offline Paper </li>
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
          <select class="form-control" id="year" name="year" required title="year" onChange="getOfflinePapers(this);">
            <option value="">Select Year</option>
            <option value="1">First </option>
            <option value="2">Second </option>
            <option value="3">Third </option>
            <option value="4">Fourth </option>
          </select>
        </div>
      @endif
      <div id="addTopicDiv">
        <a id="addTopic" href="{{url('college/'.Session::get('college_user_url').'/createCollegeOfflinePaper')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Offline Paper">Add New Offline Paper</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="collegeOfflinePaper">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Paper </th>
          <th>Subject </th>
          <th>Department </th>
          <th>Year </th>
          <th>Marks </th>
          <th>Created By </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody id="collegeOfflinePaperTable">
        @if(count($papers) > 0)
          @foreach($papers as $index => $paper)
          <tr style="overflow: auto;">
            <td>{{$index + $papers->firstItem()}}</td>
            <td>{{$paper->name}}</td>
            <td>{{$allSubjects[$paper->college_subject_id]}}</td>
            <td>{{$allCollegeDepts[$paper->college_dept_id]}}</td>
            <td>
              @if(1 == $paper->year)
                First
              @elseif(2 == $paper->year)
                Second
              @elseif(3 == $paper->year)
                Third
              @elseif(4 == $paper->year)
                Fourth
              @endif
            </td>
            <td>{{$paper->marks}}</td>
            <td>{{$paper->user}}</td>
            <td>
              @if($paper->created_by == Auth::user()->id || (4 == Auth::user()->user_type || 5 == Auth::user()->user_type))
              <a href="{{url('college/'.Session::get('college_user_url').'/collegeOfflinePaper')}}/{{$paper->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$paper->name}}" />
                </a>
              @endif
            </td>
            <td>
              @if($paper->created_by == Auth::user()->id || (4 == Auth::user()->user_type || 5 == Auth::user()->user_type))
                <a id="{{$paper->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$paper->name}}" />
                </a>
                <form id="deleteOfflinePaper_{{$paper->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteCollegeOfflinePaper')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="paper_id" value="{{$paper->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="9">No Offline Papers are created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $papers->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

  function confirmDelete(ele){
    $.confirm({
      title: 'Confirmation',
      content: 'If you delete this paper, marks of this paper for students will be deleted.',
      type: 'red',
      typeAnimated: true,
      buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-red',
                action: function(){
                  var id = $(ele).attr('id');
                  formId = 'deleteOfflinePaper_'+id;
                  document.getElementById(formId).submit();
                }
            },
            Cancle: function () {
            }
        }
      });
  }

  function resetYear(){
    document.getElementById('year').selectedIndex = '';
  }

  function getOfflinePapers(){
    var year = document.getElementById('year').value;
    var department = document.getElementById('department').value;
    $.ajax({
        method: "POST",
        url: "{{url('getCollegeOfflinePapersByDeptIdByYear')}}",
        data: {department:department,year:year}
    })
    .done(function( result ) {
      body = document.getElementById('collegeOfflinePaperTable');
      body.innerHTML = '';
        if(result['papers'].length > 0){
          $.each(result['papers'], function(idx, paper) {
            var eleTr = document.createElement('tr');
            eleTr.setAttribute("style","overflow: auto;");

            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = idx + 1;
            eleTr.appendChild(eleIndex);

            var elePaper = document.createElement('td');
            elePaper.innerHTML = paper.name;
            eleTr.appendChild(elePaper);

            var eleSubject = document.createElement('td');
            eleSubject.innerHTML = paper.subject;
            eleTr.appendChild(eleSubject);

            var eleDept = document.createElement('td');
            eleDept.innerHTML = result['depts'][paper.college_dept_id];
            eleTr.appendChild(eleDept);

            var eleYear = document.createElement('td');
            if(1 == paper.year){
              eleYear.innerHTML = 'First';
            } else if(2 == paper.year){
              eleYear.innerHTML = 'second';
            } else if(3 == paper.year){
              eleYear.innerHTML = 'Third';
            } else if(4 == paper.year){
              eleYear.innerHTML = 'Fourth';
            }
            eleTr.appendChild(eleYear);

            var eleMarks = document.createElement('td');
            eleMarks.innerHTML = paper.marks;
            eleTr.appendChild(eleMarks);

            var eleCreatedBy = document.createElement('td');
            eleCreatedBy.innerHTML = paper.user;
            eleTr.appendChild(eleCreatedBy);

            var url = "{{url('college/'.Session::get('college_user_url').'/collegeOfflinePaper')}}/"+paper.id+"/edit";
            var imageSrc = "{{asset('images/edit1.png')}}";
            var eleRemark = document.createElement('td');
            eleRemark.innerHTML = '<a href="'+ url +'" ><img src="'+imageSrc+'" width=\'30\' height=\'30\' title=" Edit " /></a>';
            eleTr.appendChild(eleRemark);


            var url = "{{url('college/'.Session::get('college_user_url').'/deleteCollegeOfflinePaper')}}";
            var imageSrc = "{{asset('images/delete2.png')}}";
            var csrfField = '{{ csrf_field() }}';
            var deleteMethod ='{{ method_field("DELETE") }}';
            var eleDelete = document.createElement('td');
            eleDelete.innerHTML = '<a id="deleteOfflinePaper_'+ paper.id+'" onclick="confirmDelete(this);" ><img src="'+imageSrc+'" width=\'30\' height=\'30\' title=" Delete  " /></a>';
            eleDelete.innerHTML += '<form id="deletepaper_'+ paper.id+'" action="'+url+'" method="POST" style="display: none;">'+csrfField+''+deleteMethod+'<input type="hidden" name="paper_id" value="'+ paper.id+'"></form>';

            eleTr.appendChild(eleDelete);

            body.appendChild(eleTr);
          });
        } else {
          var eleTr = document.createElement('tr');
          eleTr.setAttribute("style","overflow: auto;");
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = 'No Offline Papers are created.';
          eleIndex.setAttribute("colspan","9");
          eleTr.appendChild(eleIndex);
          body.appendChild(eleTr);
        }
    });
  }

</script>
@stop