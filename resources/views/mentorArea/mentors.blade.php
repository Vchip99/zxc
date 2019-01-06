@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Mentors </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-graduation-cap"></i> Mentor </li>
      <li class="active"> Mentors </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div>
      <div class="col-sm-3">
        @if(count($areas) > 0)
          <select class="form-control" id="area" name="area" onChange="showMentorsByArea(this.value);">
            <option value=""> Select Area </option>
            <option value="All">All</option>
            @foreach($areas as $area)
              <option value="{{$area->id}}"> {{$area->name}} </option>
            @endforeach
          </select>
        @endif
      </div>
      <div class="col-sm-3">
        @if(count($skills) > 0)
          <select class="form-control" id="skill" name="skill" onChange="showMentorsBySkill(this.value);">
            <option value=""> Select Skill </option>
            <option value="All">All</option>
            @foreach($skills as $skill)
              <option value="{{$skill->id}}"> {{$skill->name}} </option>
            @endforeach
          </select>
        @endif
      </div>
      <br><br>
      <table class="table admin_table">
        <thead >
          <tr>
            <th>#</th>
            <th>Mentor</th>
            <th>Admin Approve</th>
            <th>Delete </th>
          </tr>
        </thead>
        <tbody id="allMentors">
          @if(count($mentors) > 0)
            @foreach($mentors as $index => $mentor)
            <tr style="overflow: auto;">
              <th scope="row">{{$index + $mentors->firstItem()}}</th>
              <td>{{$mentor->name}}</td>
              <td>
                @if(1 == $mentor->admin_approve)
                  <input type="checkbox" value="" data-mentor_id="{{$mentor->id}}" onclick="changeMentorApproveStatus(this);" checked="checked">
                @else
                  <input type="checkbox" value="" data-mentor_id="{{$mentor->id}}" onclick="changeMentorApproveStatus(this);" >
                @endif
              </td>
              <td>
              <a id="{{$mentor->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$mentor->name}}" />
                  </a>
                  <form id="deleteMentor_{{$mentor->id}}" action="{{url('admin/deleteMentor')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="mentor_id" value="{{$mentor->id}}">
                  </form>
              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="4">No mentor is created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{ $mentors->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
       $.confirm({
        title: 'Confirmation',
        content: 'If you delete mentor, then all info associated with this mentor will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteMentor_'+id;
                    document.getElementById(formId).submit();
                  }
              },
              Cancel: function () {
              }
          }
        });
    }

    function changeMentorApproveStatus(ele){
      var mentor = $(ele).data('mentor_id');
      if(mentor > 0){
        $.ajax({
          method:'POST',
          url: "{{url('admin/changeMentorApproveStatus')}}",
          data:{mentor_id:mentor}
        }).done(function( msg ) {
          window.location.reload();
        });
      }
    }

    function showMentorsByArea(areaId){
      $.ajax({
        method:'POST',
        url: "{{url('admin/getMentorsByAreaId')}}",
        data:{area_id:areaId}
      }).done(function( msg ) {
        body = document.getElementById('allMentors');
        body.innerHTML = '';
        if(msg['mentors'].length > 0){
          $.each(msg['mentors'],function(idx,obj){
            var eleTr = document.createElement('tr');
            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = idx + 1;
            eleTr.appendChild(eleIndex);

            var eleName = document.createElement('td');
            eleName.innerHTML = obj.name;
            eleTr.appendChild(eleName);

            var eleAdminApproval = document.createElement('td');
            var adminApprovalInnerHTML = '';
            adminApprovalInnerHTML = '<input type="checkbox" value="" data-mentor_id="'+obj.id+'" onclick="changeMentorApproveStatus(this.value);"';
            if( 1 == obj.admin_approve){
              adminApprovalInnerHTML += 'checked = checked';
            }
            adminApprovalInnerHTML += '>';
            eleAdminApproval.innerHTML = adminApprovalInnerHTML;
            eleTr.appendChild(eleAdminApproval);

            var eleDelete = document.createElement('td');
            var deleteUrl = "{{url('admin/deleteMentor')}}";
            var csrfField = '{{ csrf_field() }}';
            var deleteMethod = '{{ method_field('DELETE') }}';
            var deleteImg = "{{asset('images/delete2.png')}}";
            deleteInnerHTML = '';
            deleteInnerHTML +='<a id="'+obj.id+'" onclick="confirmDelete(this);"><img src="'+deleteImg+'" width="30" height="30" title="Delete '+obj.name+'" /></a><form id="deleteMentor_'+obj.id+'" action="'+deleteUrl+'" method="POST" style="display: none;">'+csrfField+''+deleteMethod+'<input type="hidden" name="mentor_id" value="'+obj.id+'"></form>';
            eleDelete.innerHTML = deleteInnerHTML;
            eleTr.appendChild(eleDelete);

            body.appendChild(eleTr);
          });
        } else {
          body.innerHTML = 'No Mentors';
        }
        select = document.getElementById('skill');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Skill';
        select.appendChild(opt);
        var optAll = document.createElement('option');
        optAll.value = 'All';
        optAll.innerHTML = 'All';
        select.appendChild(optAll);
        if( 0 < msg['skills'].length){
          $.each(msg['skills'], function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
      });
    }

    function showMentorsBySkill(skillId){
      var areaId = $('#area').val();
      $.ajax({
        method:'POST',
        url: "{{url('admin/getMentorsByAreaIdBySkillId')}}",
        data: {area:areaId,skill:skillId}
      }).done(function( msg ) {
        body = document.getElementById('allMentors');
        body.innerHTML = '';
        if(msg['mentors'].length > 0){
          $.each(msg['mentors'],function(idx,obj){
            var eleTr = document.createElement('tr');
            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = idx + 1;
            eleTr.appendChild(eleIndex);

            var eleName = document.createElement('td');
            eleName.innerHTML = obj.name;
            eleTr.appendChild(eleName);

            var eleAdminApproval = document.createElement('td');
            var adminApprovalInnerHTML = '';
            adminApprovalInnerHTML = '<input type="checkbox" value="" data-mentor_id="'+obj.id+'" onclick="changeMentorApproveStatus(this.value);"';
            if( 1 == obj.admin_approve){
              adminApprovalInnerHTML += 'checked = checked';
            }
            adminApprovalInnerHTML += '>';
            eleAdminApproval.innerHTML = adminApprovalInnerHTML;
            eleTr.appendChild(eleAdminApproval);

            var eleDelete = document.createElement('td');
            var deleteUrl = "{{url('admin/deleteMentor')}}";
            var csrfField = '{{ csrf_field() }}';
            var deleteMethod = '{{ method_field('DELETE') }}';
            var deleteImg = "{{asset('images/delete2.png')}}";
            deleteInnerHTML = '';
            deleteInnerHTML +='<a id="'+obj.id+'" onclick="confirmDelete(this);"><img src="'+deleteImg+'" width="30" height="30" title="Delete '+obj.name+'" /></a><form id="deleteMentor_'+obj.id+'" action="'+deleteUrl+'" method="POST" style="display: none;">'+csrfField+''+deleteMethod+'<input type="hidden" name="mentor_id" value="'+obj.id+'"></form>';
            eleDelete.innerHTML = deleteInnerHTML;
            eleTr.appendChild(eleDelete);

            body.appendChild(eleTr);
          });
        } else {
          body.innerHTML = 'No Mentors';
        }
      });
    }
</script>
@stop