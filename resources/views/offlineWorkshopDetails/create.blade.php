  @extends('admin.master')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Workshop Details </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-space-shuttle"></i> Offline Workshop </li>
      <li class="active"> Manage Workshop Details </li>
    </ol>
  </section>
@stop
@section('admin_content')
    <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  <div class="container admin_div">
  @if(isset($workshopDetail->id))
    <form action="{{url('admin/updateOfflineWorkshopDetails')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" id="workshop_id" name="workshop_id" value="{{$workshopDetail->id}}">
  @else
    <form action="{{url('admin/createOfflineWorkshopDetails')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label">Category Name:</label>
      <div class="col-sm-3">
        @if(count($workshopCategories) > 0 && isset($workshopDetail->id))
          @foreach($workshopCategories as $workshopCategory)
            @if( isset($workshopDetail->id) && $workshopDetail->offline_workshop_category_id == $workshopCategory->id)
              <input type="text" class="form-control" name="category_text" value="{{$workshopCategory->name}}" readonly="true">
              <input type="hidden" name="category" id="category" value="{{$workshopCategory->id}}">
            @endif
          @endforeach
        @else
          <select id="category" class="form-control" name="category" title="Category" required >
          <option value="">Select Category</option>
            @foreach($workshopCategories as $workshopCategory)
              @if( isset($workshopDetail->id) && $workshopDetail->offline_workshop_category_id == $workshopCategory->id)
                <option value="{{$workshopCategory->id}}" selected="true">{{$workshopCategory->name}}</option>
              @else
                <option value="{{$workshopCategory->id}}">{{$workshopCategory->name}}</option>
              @endif
            @endforeach
          </select>
        @endif
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
      </div>
    </div>
     <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Workshop Name:</label>
      <div class="col-sm-3">
          <input type="text" class="form-control" name="workshop" id="workshop" value="{{$workshopDetail->name}}" required="true">
        @if($errors->has('workshop')) <p class="help-block">{{ $errors->first('workshop') }}</p> @endif
        <span class="hide" id="workshopError" style="color: white;">Given workshop name is already exist with selected category.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('about')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">About Workshop:</label>
      <div class="col-sm-10">
        <textarea name="about" cols="60" rows="4" id="about" placeholder="About Workshop" required>
          @if(isset($workshopDetail->id))
            {!! $workshopDetail->about !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'about', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('about')) <p class="help-block">{{ $errors->first('about') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('about_image')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="about_image">About Workshop Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="about_image" id="about_image" >
        @if($errors->has('about_image')) <p class="has-error">{{ $errors->first('about_image') }}</p> @endif
        @if(isset($workshopDetail->about_image))
          <b><span>Existing Image: {!! basename($workshopDetail->about_image) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('benefits')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Workshop Benefits:</label>
      <div class="col-sm-10">
        <textarea name="benefits" cols="60" rows="4" id="benefits" placeholder="Workshop Benefits" required>
          @if(isset($workshopDetail->id))
            {!! $workshopDetail->benefits !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'benefits', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('benefits')) <p class="help-block">{{ $errors->first('benefits') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('benefits_image')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="benefits_image">Workshop Benefits Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="benefits_image" id="benefits_image" >
        @if($errors->has('benefits_image')) <p class="has-error">{{ $errors->first('benefits_image') }}</p> @endif
        @if(isset($workshopDetail->benefits_image))
          <b><span>Existing Image: {!! basename($workshopDetail->benefits_image) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('duration')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Duration:</label>
      <div class="col-sm-3">
        @if(isset($workshopDetail->id))
          <input type="text" class="form-control" name="duration" value="{{$workshopDetail->duration}}" required="true">
        @else
          <input type="text" class="form-control" name="duration" value="" placeholder="Duration" required="true">
        @endif
        @if($errors->has('duration')) <p class="help-block">{{ $errors->first('duration') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('topics')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Topics:</label>
      <div class="col-sm-10">
        <textarea name="topics" cols="60" rows="4" id="topics" required>
          @if(isset($workshopDetail->id))
            {!! $workshopDetail->topics !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'topics', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('topics')) <p class="help-block">{{ $errors->first('topics') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('projects')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Projects:</label>
      <div class="col-sm-10">
        <textarea name="projects" cols="60" rows="4" id="projects" required>
          @if(isset($workshopDetail->id))
            {!! $workshopDetail->projects !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'projects', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('projects')) <p class="help-block">{{ $errors->first('projects') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('prerequisite')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Prerequisite:</label>
      <div class="col-sm-10">
        <textarea name="prerequisite" cols="60" rows="4" id="prerequisite" required>
          @if(isset($workshopDetail->id))
            {!! $workshopDetail->prerequisite !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'prerequisite', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('prerequisite')) <p class="help-block">{{ $errors->first('prerequisite') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
    <label for="job_link" class="col-sm-2 col-form-label">Tool Kit:</label>
    <table class="">
      <thead class="">
        <tr>
          <th class="col-sm-3">Component Name</th>
          <th class="col-sm-3">Quantity</th>
          <th class="">Add</th>
          <th class="">Delete</th>
        </tr>
      </thead>
      <tbody id="pattern">
        @if(count($components) > 0)
          @foreach($components as $component)
            <tr id="tr_{{$component->id}}">
              <td class="col-sm-3"><input type="text" class="form-control" name="component_{{$component->id}}" value="{{$component->name}}" required="true"></td>
              <td class="col-sm-3"><input type="text" class="form-control" name="quantity_{{$component->id}}" value="{{$component->quantity}}" required="true"></td>
              <td class=""><button onClick="addComponent();"> <i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>
              <td class=""><button onClick="removeElement('pattern',{{$component->id}});"> <i class="fa fa-minus-circle" aria-hidden="true"></i></button></td>
            </tr>
          @endforeach
        @else
          <tr id="tr_1">
            <td class="col-sm-3"><input type="text" class="form-control" name="component_1" value="" required="true"></td>
            <td class="col-sm-3"><input type="text" class="form-control" name="quantity_1" value="" required="true"></td>
            <td class=""><button onClick="addComponent();"> <i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>
            @if(isset($workshopDetail))
              <td class=""><input type="hidden" name="new_1" value="new"></td>
            @else
              <td class=""></td>
            @endif
          </tr>
        @endif
      </tbody>
    </table>
      <input type="hidden" id="component_count" name="component_count" value="{{(count($components)==0)?1:count($components)}}">
  </div>
    <div class="form-group row @if ($errors->has('attendees')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Who Could Attend:</label>
      <div class="col-sm-10">
        <textarea name="attendees" cols="60" rows="4" id="attendees" required>
          @if(isset($workshopDetail->id))
            {!! $workshopDetail->attendees !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'attendees', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('attendees')) <p class="help-block">{{ $errors->first('attendees') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('learn_reason')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Why Learn From Vhip Technology:</label>
      <div class="col-sm-10">
        <textarea name="learn_reason" cols="60" rows="4" id="learn_reason" required>
          @if(isset($workshopDetail->id))
            {!! $workshopDetail->learn_reason !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'learn_reason', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('learn_reason')) <p class="help-block">{{ $errors->first('learn_reason') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="button" class="btn btn-primary" onclick="searchWorkshop();">Submit</button>
        </div>
      </div>
  </div>
</form>

<script type="text/javascript">
  function addComponent(){
    event.preventDefault();
    var pattern = document.getElementById('pattern');
    var count = parseInt(document.getElementById('component_count').value) + 1;

    var eleBr = document.createElement('Br');
    eleBr.setAttribute("id", "br_" + count);
    pattern.appendChild(eleBr);

    var firstTr = document.createElement('tr');
    firstTr.setAttribute("id", "tr_" + count);

    var firstTd = document.createElement('td');
    firstTd.className = 'col-sm-3';

    var firstInput = document.createElement('input');
    firstInput.className = 'form-control';
    firstInput.setAttribute("id", "component_" + count);
    firstInput.setAttribute("name", "component_" + count);
    firstInput.setAttribute("type", "text");
    firstTd.appendChild(firstInput);
    firstTr.appendChild(firstTd);

    var secondTd = document.createElement('td');
    secondTd.className = 'col-sm-3';

    var secondInput = document.createElement('input');
    secondInput.className = 'form-control';
    secondInput.setAttribute("id", "quantity_" + count);
    secondInput.setAttribute("name", "quantity_" + count);
    secondInput.setAttribute("type", "text");
    secondTd.appendChild(secondInput);
    firstTr.appendChild(secondTd);

    var fourthTd = document.createElement('td');

    var fourthButton = document.createElement('button');
    fourthButton.setAttribute("onClick", "addComponent();");
    fourthButton.innerHTML = '<i class="fa fa-plus-circle" aria-hidden="true"></i>';
    fourthTd.appendChild(fourthButton);
    firstTr.appendChild(fourthTd);

    var fifthTd = document.createElement('td');

    var fifthButton = document.createElement('button');
    fifthButton.setAttribute("onClick", "removeElement('pattern',"+count+");");
    fifthButton.innerHTML = '<i class="fa fa-minus-circle" aria-hidden="true"></i>';
    fifthTd.appendChild(fifthButton);
    if(document.getElementById('workshop_id')){
      var processId = document.getElementById('workshop_id').value;
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

    document.getElementById('component_count').value = count;
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
  function searchWorkshop(){
    var category = document.getElementById('category').value;
    var workshop = document.getElementById('workshop').value;
    if(document.getElementById('workshop_id')){
      var workshopId = document.getElementById('workshop_id').value;
    } else {
      var workshopId = 0;
    }
    if(category && workshop){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isOfflineWorkshopExist')}}",
        data:{category:category,workshop:workshop,workshop_id:workshopId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('workshopError').classList.remove('hide');
          document.getElementById('workshopError').classList.add('has-error');
        } else {
          document.getElementById('workshopError').classList.add('hide');
          document.getElementById('workshopError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else if(!category){
      alert('please select category.');
    } else {
      alert('please enter name.');
    }
  }
</script>
@stop