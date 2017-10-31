@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Placement Process</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-link"></i> Placement </li>
      <li class="active"> Manage Placement Process</li>
    </ol>
  </section>
@stop
@section('admin_content')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  &nbsp;
  <div class="container admin_div">
  @if(isset($placementProcess->id))
    <form id="submit_form" action="{{url('admin/updatePlacementProcess')}}" method="POST" enctype="multipart/form-data">
      {{method_field('PUT')}}
      <input type="hidden" id="placement_process_id" name="placement_process_id" value="{{$placementProcess->id}}">
  @else
      <form id="submit_form" action="{{url('admin/createPlacementProcess')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('area')) has-error @endif">
    <label class="col-sm-2 col-form-label">Placement Area:</label>
    <div class="col-sm-3">
      @if(isset($placementProcess->id) && count($placementAreas) > 0)
        @if(count($placementAreas) > 0)
            @foreach($placementAreas as $placementArea)
              @if( $placementProcess->placement_area_id == $placementArea->id)
                <input type="text" class="form-control" name="area_text" value="{{$placementArea->name}}" readonly="true">
                <input type="hidden" name="area" value="{{$placementArea->id}}">
              @endif
            @endforeach
          @endif
      @else
      <select class="form-control" name="area" required title="Placement Area" onClick="selectCompany(this);">
          <option value="">Select Placement Area</option>
          @if(count($placementAreas) > 0)
            @foreach($placementAreas as $placementArea)
              @if( $placementProcess->placement_area_id == $placementArea->id)
                <option value="{{$placementArea->id}}" selected="true">{{$placementArea->name}}</option>
              @else
                <option value="{{$placementArea->id}}">{{$placementArea->name}}</option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('area')) <p class="help-block">{{ $errors->first('area') }}</p> @endif
      @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('company')) has-error @endif">
    <label for="company" class="col-sm-2 col-form-label">Placement Company:</label>
    <div class="col-sm-3">
      @if(isset($placementProcess->id) && count($placementCompanies) > 0)
        @if(count($placementCompanies) > 0)
          @foreach($placementCompanies as $placementCompany)
            @if( $placementProcess->placement_company_id == $placementCompany->id)
             <input type="text" class="form-control" name="company_text" value="{{$placementCompany->name}}" readonly="true">
              <input type="hidden" name="company" value="{{$placementCompany->id}}">
            @endif
          @endforeach
        @endif
      @else
        <select class="form-control" id="company" name="company" required title="Placement Company" onClick="checkRecords(this);">
            <option value="">Select Placement Company</option>
            @if(count($placementCompanies) > 0)
              @foreach($placementCompanies as $placementCompany)
                @if( $placementProcess->placement_company_id == $placementCompany->id)
                  <option value="{{$placementCompany->id}}" selected="true">{{$placementCompany->name}}</option>
                @else
                  <option value="{{$placementCompany->id}}">{{$placementCompany->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
          @if($errors->has('company')) <p class="help-block">{{ $errors->first('company') }}</p> @endif
      @endif
    </div>
  </div>
  <div class="form-group row has-error hide" id="message_error">
    <label class="col-sm-2 col-form-label">Warning:</label>
    <div class="col-sm-10">
     <p> Placement Process is exists for above criteria. Please select another company.
    </div>
  </div>
  <div class="form-group row @if ($errors->has('selection_process')) has-error @endif">
    <label for="selection_process" class="col-sm-2 col-form-label">Selection Process:</label>
    <div class="col-sm-10">
      @if(isset($placementProcess))
        <textarea class="form-control" id="selection_process" name="selection_process" required="true">{{$placementProcess->selection_process}}</textarea>
      @else
        <textarea class="form-control" id="selection_process" name="selection_process" required="true">
        </textarea>
      @endif
       <script type="text/javascript">
          CKEDITOR.replace( 'selection_process' );
          CKEDITOR.config.width="100%";
          CKEDITOR.config.height="auto";
          CKEDITOR.on('dialogDefinition', function (ev) {

              var dialogName = ev.data.name,
                  dialogDefinition = ev.data.definition;

              if (dialogName == 'image') {
                  var onOk = dialogDefinition.onOk;

                  dialogDefinition.onOk = function (e) {
                      var width = this.getContentElement('info', 'txtWidth');
                      width.setValue('100%');//Set Default Width

                      var height = this.getContentElement('info', 'txtHeight');
                      height.setValue('400');////Set Default height

                      onOk && onOk.apply(this, e);
                  };
              }
          });
        </script>
      @if($errors->has('about_company')) <p class="help-block">{{ $errors->first('about_company') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('academic_criteria')) has-error @endif">
    <label for="academic_criteria" class="col-sm-2 col-form-label">Academic Criteria:</label>
    <div class="col-sm-10">
      @if(isset($placementProcess))
        <textarea class="form-control" id="academic_criteria" name="academic_criteria" required="true">{{$placementProcess->academic_criteria}}</textarea>
      @else
        <textarea class="form-control" id="academic_criteria" name="academic_criteria" required="true">
        </textarea>
      @endif
       <script type="text/javascript">
          CKEDITOR.replace( 'academic_criteria' );
          CKEDITOR.config.width="100%";
          CKEDITOR.config.height="auto";
          CKEDITOR.on('dialogDefinition', function (ev) {

              var dialogName = ev.data.name,
                  dialogDefinition = ev.data.definition;

              if (dialogName == 'image') {
                  var onOk = dialogDefinition.onOk;

                  dialogDefinition.onOk = function (e) {
                      var width = this.getContentElement('info', 'txtWidth');
                      width.setValue('100%');//Set Default Width

                      var height = this.getContentElement('info', 'txtHeight');
                      height.setValue('400');////Set Default height

                      onOk && onOk.apply(this, e);
                  };
              }
          });
        </script>
      @if($errors->has('about_company')) <p class="help-block">{{ $errors->first('about_company') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('aptitude_syllabus')) has-error @endif">
    <label for="aptitude_syllabus" class="col-sm-2 col-form-label">Aptitude Syllabus:</label>
    <div class="col-sm-10">
      @if(isset($placementProcess))
        <textarea class="form-control" id="aptitude_syllabus" name="aptitude_syllabus" required="true">{{$placementProcess->aptitude_syllabus}}</textarea>
      @else
        <textarea class="form-control" id="aptitude_syllabus" name="aptitude_syllabus" required="true">
        </textarea>
      @endif
       <script type="text/javascript">
          CKEDITOR.replace( 'aptitude_syllabus' );
          CKEDITOR.config.width="100%";
          CKEDITOR.config.height="auto";
          CKEDITOR.on('dialogDefinition', function (ev) {

              var dialogName = ev.data.name,
                  dialogDefinition = ev.data.definition;

              if (dialogName == 'image') {
                  var onOk = dialogDefinition.onOk;

                  dialogDefinition.onOk = function (e) {
                      var width = this.getContentElement('info', 'txtWidth');
                      width.setValue('100%');//Set Default Width

                      var height = this.getContentElement('info', 'txtHeight');
                      height.setValue('400');////Set Default height

                      onOk && onOk.apply(this, e);
                  };
              }
          });
        </script>
      @if($errors->has('about_company')) <p class="help-block">{{ $errors->first('about_company') }}</p> @endif
    </div>
  </div>
    <div class="form-group row @if ($errors->has('hr_questions')) has-error @endif">
    <label for="hr_questions" class="col-sm-2 col-form-label">Hr Questions:</label>
    <div class="col-sm-10">
      @if(isset($placementProcess))
        <textarea class="form-control" id="hr_questions" name="hr_questions" required="true">{{$placementProcess->hr_questions}}</textarea>
      @else
        <textarea class="form-control" id="hr_questions" name="hr_questions" required="true">
        </textarea>
      @endif
       <script type="text/javascript">
          CKEDITOR.replace( 'hr_questions' );
          CKEDITOR.config.width="100%";
          CKEDITOR.config.height="auto";
          CKEDITOR.on('dialogDefinition', function (ev) {

              var dialogName = ev.data.name,
                  dialogDefinition = ev.data.definition;

              if (dialogName == 'image') {
                  var onOk = dialogDefinition.onOk;

                  dialogDefinition.onOk = function (e) {
                      var width = this.getContentElement('info', 'txtWidth');
                      width.setValue('100%');//Set Default Width

                      var height = this.getContentElement('info', 'txtHeight');
                      height.setValue('400');////Set Default height

                      onOk && onOk.apply(this, e);
                  };
              }
          });
        </script>
      @if($errors->has('about_company')) <p class="help-block">{{ $errors->first('about_company') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('job_link')) has-error @endif">
    <label for="job_link" class="col-sm-2 col-form-label">Job Link:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" id="job_link" name="job_link" value="{{$placementProcess->job_link}}" required="true">
      @if($errors->has('job_link')) <p class="help-block">{{ $errors->first('job_link') }}</p> @endif
    </div>
  </div>
  <div class="form-group row">
    <label for="job_link" class="col-sm-2 col-form-label">Pattern of Written Exam:</label>
    <table class="">
      <thead class="">
        <tr>
          <th class="col-sm-3">Testing Area</th>
          <th class="col-sm-3">No. of Questions</th>
          <th class="col-sm-3">Duration</th>
          <th class="">Add</th>
          <th class="">Delete</th>
        </tr>
      </thead>
      <tbody id="pattern">
        @if(count($examPatterns) > 0)
          @foreach($examPatterns as $examPattern)
            <tr id="tr_{{$examPattern->id}}">
              <td class="col-sm-3"><input type="text" class="form-control" name="area_{{$examPattern->id}}" value="{{$examPattern->testing_area}}" required="true"></td>
              <td class="col-sm-3"><input type="text" class="form-control" name="question_{{$examPattern->id}}" value="{{$examPattern->no_of_question}}" required="true"></td>
              <td class="col-sm-3"><input type="text" class="form-control" name="duration_{{$examPattern->id}}" value="{{$examPattern->duration}}" required="true"></td>
              <td class=""><button onClick="addExamPattern();"> <i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>
              <td class=""><button onClick="removeElement('pattern',{{$examPattern->id}});"> <i class="fa fa-minus-circle" aria-hidden="true"></i></button></td>
            </tr>
          @endforeach
        @else
          <tr id="tr_1">
            <td class="col-sm-3"><input type="text" class="form-control" name="area_1" value="" required="true"></td>
            <td class="col-sm-3"><input type="text" class="form-control" name="question_1" value="" required="true"></td>
            <td class="col-sm-3"><input type="text" class="form-control" name="duration_1" value="" required="true"></td>
            <td class=""><button onClick="addExamPattern();"> <i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>

            @if(isset($placementProcess))
              <td class=""><input type="hidden" name="new_1" value="new"></td>
            @else
              <td class=""></td>
            @endif
          </tr>
        @endif
      </tbody>
    </table>
      <input type="hidden" id="pattern_count" name="pattern_count" value="{{$examPatternCounts}}">
  </div>
  <div class="form-group row" id="submit">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button id="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</form>
<script type="text/javascript">
  function addExamPattern(){
    event.preventDefault();
    var pattern = document.getElementById('pattern');
    var count = parseInt(document.getElementById('pattern_count').value) + 1;

    var eleBr = document.createElement('Br');
    eleBr.setAttribute("id", "br_" + count);
    pattern.appendChild(eleBr);

    var firstTr = document.createElement('tr');
    firstTr.setAttribute("id", "tr_" + count);

    var firstTd = document.createElement('td');
    firstTd.className = 'col-sm-3';

    var firstInput = document.createElement('input');
    firstInput.className = 'form-control';
    firstInput.setAttribute("id", "area_" + count);
    firstInput.setAttribute("name", "area_" + count);
    firstInput.setAttribute("type", "text");
    firstTd.appendChild(firstInput);
    firstTr.appendChild(firstTd);

    var secondTd = document.createElement('td');
    secondTd.className = 'col-sm-3';

    var secondInput = document.createElement('input');
    secondInput.className = 'form-control';
    secondInput.setAttribute("id", "question_" + count);
    secondInput.setAttribute("name", "question_" + count);
    secondInput.setAttribute("type", "text");
    secondTd.appendChild(secondInput);
    firstTr.appendChild(secondTd);

    var thirdTd = document.createElement('td');
    thirdTd.className = 'col-sm-3';

    var thirdInput = document.createElement('input');
    thirdInput.className = 'form-control';
    thirdInput.setAttribute("id", "duration_" + count);
    thirdInput.setAttribute("name", "duration_" + count);
    thirdInput.setAttribute("type", "text");
    thirdTd.appendChild(thirdInput);
    firstTr.appendChild(thirdTd);

    var fourthTd = document.createElement('td');

    var fourthButton = document.createElement('button');
    fourthButton.setAttribute("onClick", "addExamPattern();");
    fourthButton.innerHTML = '<i class="fa fa-plus-circle" aria-hidden="true"></i>';
    fourthTd.appendChild(fourthButton);
    firstTr.appendChild(fourthTd);

    var fifthTd = document.createElement('td');

    var fifthButton = document.createElement('button');
    fifthButton.setAttribute("onClick", "removeElement('pattern',"+count+");");
    fifthButton.innerHTML = '<i class="fa fa-minus-circle" aria-hidden="true"></i>';
    fifthTd.appendChild(fifthButton);
    if(document.getElementById('placement_process_id')){
      var processId = document.getElementById('placement_process_id').value;
      if(processId > 0){
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

    pattern.appendChild(firstTr);

    document.getElementById('pattern_count').value = count;
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

  $('#submit').click(function(){
    document.getElementById("submit_form").submit();
  });

  function selectCompany(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('admin/getPlacementCompaniesByArea')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('company');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Placement Company';
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
    document.getElementById('message_error').classList.add('hide');
    document.getElementById('submit').classList.remove('hide');
  }

  function checkRecords(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('admin/checkPlacementCompanyProcesss')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        if('true' == msg){
          document.getElementById('message_error').classList.remove('hide');
          document.getElementById('submit').classList.add('hide');

        } else {
          document.getElementById('message_error').classList.add('hide');
          document.getElementById('submit').classList.remove('hide');
        }
      });
    }
  }
</script>
@stop