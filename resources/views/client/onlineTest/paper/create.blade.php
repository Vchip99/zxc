@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Paper </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Test </li>
      <li class="active"> Manage Paper </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
    <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  &nbsp;
  <div class="container admin_div">
  @if(isset($paper->id))
    <form action="{{url('updateOnlineTestSubjectPaper')}}" method="POST">
    {{method_field('PUT')}}
    <input type="hidden" name="paper_id" id="paper_id" value="{{$paper->id}}">
  @else
    <form action="{{url('createOnlineTestSubjectPaper')}}" method="POST">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('category')) has-error @endif">
    <label class="col-sm-2 col-form-label">Category Name:</label>
    <div class="col-sm-3">
      <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
          <option value="">Select Category</option>
          @if(count($testCategories) > 0))
            @foreach($testCategories as $testCategory)
              @if( isset($paper->id) && $paper->category_id == $testCategory->id)
                <option value="{{$testCategory->id}}" selected="true">{{$testCategory->name}}</option>
              @else
                <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
              @endif
            @endforeach
          @endif
      </select>
      @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
    <label class="col-sm-2 col-form-label">Sub Category Name:</label>
    <div class="col-sm-3">
      <select id="subcategory" class="form-control" name="subcategory" onChange="selectSubject(this);" required title="Sub Category">
        <option value="">Select Sub Category</option>
        @if(count($testSubCategories) > 0 && isset($paper->id))
          @foreach($testSubCategories as $testSubCategory)
            @if($paper->sub_category_id == $testSubCategory->id)
                <option value="{{$testSubCategory->id}}" selected="true">{{$testSubCategory->name}}</option>
              @else
                <option value="{{$testSubCategory->id}}">{{$testSubCategory->name}}</option>
            @endif
          @endforeach
        @endif
      </select>
      @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('subject')) has-error @endif">
    <label class="col-sm-2 col-form-label">Subject Name:</label>
    <div class="col-sm-3">
      <select id="subject" class="form-control" name="subject" required title="Subject">
        <option value="">Select Subject</option>
          @if(count($testSubjects) > 0 && isset($paper->id))
          @foreach($testSubjects as $testSubject)
            @if($paper->subject_id == $testSubject->id)
                <option value="{{$testSubject->id}}" selected="true">{{$testSubject->name}}</option>
              @else
                <option value="{{$testSubject->id}}">{{$testSubject->name}}</option>
            @endif
          @endforeach
        @endif
      </select>
      @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('name')) has-error @endif">
    <label for="name" class="col-sm-2 col-form-label">Paper Name:</label>
    <div class="col-sm-3">
      @if(isset($paper->id))
        <input type="text" class="form-control" name="name" value="{{$paper->name}}" required="true">
      @else
        <input type="text" class="form-control" name="name" value="" placeholder="paper name" required="true">
      @endif
      @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('is_free')) has-error @endif">
    <label for="course" class="col-sm-2 col-form-label">Free Paper:</label>
    <div class="col-sm-3">
        @if(isset($paper->id))
        <label class="radio-inline"><input type="radio" name="is_free" value="1" onClick="showUnAuthorisedUser(this);" @if(1 == $paper->is_free) checked="true" @endif> Yes</label>
        <label class="radio-inline"><input type="radio" name="is_free" value="0" onClick="showUnAuthorisedUser(this);" @if(0 == $paper->is_free) checked="true" @endif> No</label>
        @else
          <label class="radio-inline"><input type="radio" name="is_free" value="1"  onClick="showUnAuthorisedUser(this);" > Yes</label>
          <label class="radio-inline"><input type="radio" name="is_free" value="0" checked  onClick="showUnAuthorisedUser(this);" > No</label>
        @endif
      @if($errors->has('is_free')) <p class="help-block">{{ $errors->first('is_free') }}</p> @endif
    </div>
  </div>
  <div class="form-group row allowed_unauthorised_user hide @if ($errors->has('allowed_unauthorised_user')) has-error @endif">
    <label for="course" class="col-sm-2 col-form-label">Allowed Non Login User:</label>
    <div class="col-sm-3">
        @if(isset($paper->id))
        <label class="radio-inline"><input type="radio" name="allowed_unauthorised_user" value="1" @if(1 == $paper->allowed_unauthorised_user) checked="true" @endif> Yes</label>
        <label class="radio-inline"><input type="radio" name="allowed_unauthorised_user" value="0" @if(0 == $paper->allowed_unauthorised_user) checked="true" @endif> No</label>
        @else
          <label class="radio-inline"><input type="radio" name="allowed_unauthorised_user" value="1"> Yes</label>
          <label class="radio-inline"><input type="radio" name="allowed_unauthorised_user" value="0" checked> No</label>
        @endif
      @if($errors->has('allowed_unauthorised_user')) <p class="help-block">{{ $errors->first('allowed_unauthorised_user') }}</p> @endif
    </div>
  </div>
  <div class="form-group row">
    <label for="date_to_active" class="col-sm-2 col-form-label">Date To Active:</label>
    <div class="col-sm-3">
      <input type="text"  class="form-control" name="date_to_active" id="date_to_active" @if(isset($paper->id)) value="{{$paper->date_to_active}}" @endif required="true" placeholder="date" required="true">
    </div>
    <script type="text/javascript">
        $(function () {
            var currentDate = "{{ date('Y-m-d')}}";
            $('#date_to_active').datetimepicker({defaultDate: currentDate,format: 'YYYY-MM-DD'});
        });
    </script>
  </div>
  <div class="form-group row">
    <label for="date_to_active" class="col-sm-2 col-form-label">Date To Inactive:</label>
    <div class="col-sm-3">
      <input type="text"  class="form-control" name="date_to_inactive" id="date_to_inactive" @if(isset($paper->id)) value="{{$paper->date_to_inactive}}" @endif required="true" placeholder="date" required="true">
    </div>
    <script type="text/javascript">
        $(function () {
            $('#date_to_inactive').datetimepicker({defaultDate: "2050-01-01",format: 'YYYY-MM-DD'});
        });
    </script>
  </div>
  <div class="form-group row @if ($errors->has('time_out_by')) has-error @endif ">
    <label for="paper" class="col-sm-2 col-form-label">Total Time Out By:</label>
    <div class="col-sm-3">
      @if(isset($paper->id))
        <label class="radio-inline"><input type="radio" name="time_out_by" value="1" onClick="selectedSession(this);" @if(1 == $paper->time_out_by) checked="true" @endif> Paper Wise</label>
        <label class="radio-inline"><input type="radio" name="time_out_by" value="0" onClick="selectedSession(this);" @if(0 == $paper->time_out_by) checked="true" @endif> Session Wise</label>
      @else
        <label class="radio-inline"><input type="radio" name="time_out_by" value="1" onClick="selectedSession(this);" checked> Paper Wise</label>
        <label class="radio-inline"><input type="radio" name="time_out_by" value="0" onClick="selectedSession(this);"> Session Wise</label>
      @endif
      @if($errors->has('time_out_by')) <p class="help-block">{{ $errors->first('time_out_by') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('time')) has-error @endif paper_duration">
    <label for="name" class="col-sm-2 col-form-label">Paper Time:</label>
    <div class="col-sm-3">
      @if(isset($paper->id))
        <input type="text" class="form-control" name="time" id="time" value="{{$paper->time}}" required="true">
      @else
        <input type="text" class="form-control" name="time" id="time" value="" >
      @endif
      @if($errors->has('time')) <p class="help-block">{{ $errors->first('time') }}</p> @endif
    </div>
  </div>
  <div class="form-group row">
    <label for="" class="col-sm-2 col-form-label">Session And Its Time:</label>
    <table class="">
      <thead class="">
        <tr>
          <th class="col-sm-4">Session Name</th>
          <th class="col-sm-4 duration">Duration(In Sec.)</th>
          <th class="col-sm">Add</th>
          <th class="col-sm">Delete</th>
        </tr>
      </thead>
      <tbody id="all_session">
        @if(count($allSessions) > 0)
          @foreach($allSessions as $session)
            <tr id="tr_{{$session->id}}">
              <td class="col-sm-3"><input type="text" class="form-control" name="session_{{$session->id}}" value="{{$session->name}}" required="true"></td>
              <td class="col-sm-3 duration">
                <input type="text" class="form-control" name="duration_{{$session->id}}" value="{{$session->duration}}">
              </td>
              <td class=""><button type="button" onClick="addSessions();"> <i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>
              <td class=""><button onClick="removeElement('all_session',{{$session->id}});"> <i class="fa fa-minus-circle" aria-hidden="true"></i></button></td>
            </tr>
          @endforeach
        @else
          <tr id="tr_1">
            <td class="col-sm-3"><input type="text" class="form-control" name="session_1" value="" required="true"></td>
            <td class="col-sm-3 duration"><input type="text" class="form-control " name="duration_1" value=""></td>
            <td class=""><button type="button" onClick="addSessions();"> <i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>
            @if(isset($paper))
              <td class=""><input type="hidden" name="new_1" value="new"></td>
            @else
              <td class=""></td>
            @endif
          </tr>
        @endif
      </tbody>
    </table>
      <input type="hidden" id="all_session_count" name="all_session_count" value="1">
  </div>
  <div class="form-group row @if ($errors->has('show_calculator')) has-error @endif">
    <label for="paper" class="col-sm-2 col-form-label">Show Calculator:</label>
    <div class="col-sm-3">
      @if(isset($paper->id))
        <label class="radio-inline"><input type="radio" name="show_calculator" value="1" @if(1 == $paper->show_calculator) checked="true" @endif> Yes</label>
        <label class="radio-inline"><input type="radio" name="show_calculator" value="0" @if(0 == $paper->show_calculator) checked="true" @endif> No</label>
      @else
        <label class="radio-inline"><input type="radio" name="show_calculator" value="1" checked> Yes</label>
        <label class="radio-inline"><input type="radio" name="show_calculator" value="0"> No</label>
      @endif
      @if($errors->has('show_calculator')) <p class="help-block">{{ $errors->first('show_calculator') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('show_solution')) has-error @endif">
    <label for="paper" class="col-sm-2 col-form-label">Show Solution:</label>
    <div class="col-sm-3">
      @if(isset($paper->id))
        <label class="radio-inline"><input type="radio" name="show_solution" value="1" @if(1 == $paper->show_solution) checked="true" @endif> Yes</label>
        <label class="radio-inline"><input type="radio" name="show_solution" value="0" @if(0 == $paper->show_solution) checked="true" @endif> No</label>
      @else
        <label class="radio-inline"><input type="radio" name="show_solution" value="1" checked> Yes</label>
        <label class="radio-inline"><input type="radio" name="show_solution" value="0"> No</label>
      @endif
      @if($errors->has('show_solution')) <p class="help-block">{{ $errors->first('show_solution') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('option_count')) has-error @endif">
    <label for="paper" class="col-sm-2 col-form-label">Option Count:</label>
    <div class="col-sm-3">
      @if(isset($paper->id))
        <label class="radio-inline"><input type="radio" name="option_count" value="4" @if(4 == $paper->option_count) checked="true" @endif> 4</label>
        <label class="radio-inline"><input type="radio" name="option_count" value="5" @if(5 == $paper->option_count) checked="true" @endif> 5</label>
      @else
        <label class="radio-inline"><input type="radio" name="option_count" value="4" checked> 4</label>
        <label class="radio-inline"><input type="radio" name="option_count" value="5"> 5</label>
      @endif
    @if($errors->has('option_count')) <p class="help-block">{{ $errors->first('option_count') }}</p> @endif
    </div>
  </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</form>
<input type="hidden" id="selected_time_out" name="selected_time_out" value="{{$paper->time_out_by}}">
<script type="text/javascript">
  function addSessions(){
    event.preventDefault();
    var allSession = document.getElementById('all_session');
    var count = parseInt(document.getElementById('all_session_count').value) + 1;

    var eleBr = document.createElement('Br');
    eleBr.setAttribute("id", "br_" + count);
    allSession.appendChild(eleBr);

    var firstTr = document.createElement('tr');
    firstTr.setAttribute("id", "tr_" + count);

    var firstTd = document.createElement('td');
    firstTd.className = 'col-sm-3';

    var firstInput = document.createElement('input');
    firstInput.className = 'form-control';
    firstInput.setAttribute("id", "session_" + count);
    firstInput.setAttribute("name", "session_" + count);
    firstInput.setAttribute("type", "text");
    firstTd.appendChild(firstInput);
    firstTr.appendChild(firstTd);

    var secondTd = document.createElement('td');
    if(1 == document.getElementById('selected_time_out').value){
      secondTd.className = 'col-sm-3 duration hide';
    } else {
      secondTd.className = 'col-sm-3 duration';
    }

    var secondInput = document.createElement('input');
    secondInput.className = 'form-control';
    secondInput.setAttribute("id", "duration_" + count);
    secondInput.setAttribute("name", "duration_" + count);
    secondInput.setAttribute("type", "text");

    secondTd.appendChild(secondInput);
    firstTr.appendChild(secondTd);

    var thirdTd = document.createElement('td');

    var thirdButton = document.createElement('button');
    thirdButton.setAttribute("onClick", "addSessions();");
    thirdButton.innerHTML = '<i class="fa fa-plus-circle" aria-hidden="true"></i>';
    thirdTd.appendChild(thirdButton);
    firstTr.appendChild(thirdTd);

    var fifthTd = document.createElement('td');

    var fifthButton = document.createElement('button');
    fifthButton.setAttribute("onClick", "removeElement('all_session',"+count+");");
    fifthButton.innerHTML = '<i class="fa fa-minus-circle" aria-hidden="true"></i>';
    fifthTd.appendChild(fifthButton);
    if(document.getElementById('paper_id')){
      var paperId = document.getElementById('paper_id').value;
      if(paperId > 0){
        var hiddenInput = document.createElement('input');
        hiddenInput.className = 'form-control';
        hiddenInput.setAttribute("name", "new_" + count);
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("value", "new");
        fifthTd.appendChild(hiddenInput);
        firstTr.appendChild(fifthTd);
      }
    }
      firstTr.appendChild(fifthTd);

    allSession.appendChild(firstTr);

    document.getElementById('all_session_count').value = count;
  }

  function removeElement(parent,childId){
      event.preventDefault();
      var childTr = document.getElementById('tr_'+childId);
      var childBr = document.getElementById('br_'+childId);
      var parent = document.getElementById(parent);
      if(childTr){
        parent.removeChild(childTr);
      }
      if(childBr){
        parent.removeChild(childBr);
      }
  }

  function selectedSession(ele){
    document.getElementById('selected_time_out').value = $(ele).val();
    if(1 == $(ele).val()){
      $('.duration').addClass('hide');
      $('.paper_duration').removeClass('hide');
      $.each($('.duration > input'), function(idx, obj){ $(obj).prop('required', false); });
      $('#time').prop('required', true);
    } else {
      $('.duration').removeClass('hide');
      $('.paper_duration').addClass('hide');
      $.each($('.duration > input'), function(idx, obj){ $(obj).prop('required', true); });
      $('#time').prop('required', false);
    }
  }

  function showUnAuthorisedUser(ele){
    if(1 == $(ele).val()){
      $('.allowed_unauthorised_user').removeClass('hide');
    } else {
      $('.allowed_unauthorised_user').addClass('hide');
    }
  }

  function selectSubcategory(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
              method: "POST",
              url: "{{url('getOnlineTestSubCategories')}}",
              data: {id:id}
          })
          .done(function( msg ) {
            select = document.getElementById('subcategory');
            select.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '';
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
    } else {
      select = document.getElementById('subcategory');
      select.innerHTML = '';
      var opt = document.createElement('option');
      opt.value = '';
      opt.innerHTML = 'Select Sub Category';
      select.appendChild(opt);
    }
  }

  function selectSubject(ele){
    subcatId = parseInt($(ele).val());
    catId = parseInt(document.getElementById('category').value);
    if( 0 < catId && 0 < subcatId ){
      $.ajax({
              method: "POST",
              url: "{{url('getOnlineSubjectsByCatIdBySubcatId')}}",
              data: {catId:catId, subcatId:subcatId}
          })
          .done(function( msg ) {
            selectSub = document.getElementById('subject');
            selectSub.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '';
            opt.innerHTML = 'Select Subject';
            selectSub.appendChild(opt);
            if( 0 < msg.length){
              $.each(msg, function(idx, obj) {
                  var opt = document.createElement('option');
                  opt.value = obj.id;
                  opt.innerHTML = obj.name;
                  selectSub.appendChild(opt);
              });
            }
          });
    } else {
      selectSub = document.getElementById('subject');
      selectSub.innerHTML = '';
      var opt = document.createElement('option');
      opt.value = '';
      opt.innerHTML = 'Select Subject';
      selectSub.appendChild(opt);
    }
  }

  $( document ).ready(function() {
    var selectedOption = $("input:radio[name=time_out_by]:checked").val()
    if(1 == selectedOption){
      $('.duration').addClass('hide');
      $('.paper_duration').removeClass('hide');
      $.each($('.duration > input'), function(idx, obj){ $(obj).prop('required', false); });
      $('#time').prop('required', true);
      document.getElementById('selected_time_out').value = 1;
    } else {
      $('.duration').removeClass('hide');
      $('.paper_duration').addClass('hide');
      $.each($('.duration > input'), function(idx, obj){ $(obj).prop('required', true); });
      $('#time').prop('required', false);
      document.getElementById('selected_time_out').value = 0;
    }

    var isFreeTest = $("input:radio[name=is_free]:checked").val()
    if(1 == isFreeTest){
      $('.allowed_unauthorised_user').removeClass('hide');
    } else {
      $('.allowed_unauthorised_user').addClass('hide');
    }
  });
</script>
@stop