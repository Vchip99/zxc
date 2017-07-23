@extends('admin.master')
@section('module_title')
<style type="text/css">
.img-vsm{width: 12px;
    height: 12px;
    margin-top: 15px;
    margin-left: 7px;}
.abc{padding: 10px;}
</style>
  <section class="content-header">
    <h1> Manage College Info </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Add College Info </li>
      <li class="active"> Manage College Info </li>
    </ol>
  </section>

@stop
@section('admin_content')
  &nbsp;

  <div class="container admin_div">
  <div class="abc">
  @if(isset($college->id))
    <form action="{{url('admin/updateCollege')}}" method="POST" id="createCollege">
    {{ method_field('PUT') }}
    <input type="hidden" id="college_id" name="college_id" value="{{$college->id}}">
  @else
    <form action="{{url('admin/createCollege')}}" method="POST" id="createCollege">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('college')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="college">College Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="college" name="college" value="{{($college)?$college->name:null}}" required="true" placeholder="College Name">
      </div>
      @if($errors->has('college')) <p class="help-block">{{ $errors->first('college') }}</p> @endif
    </div>
    @if(!empty($college->id) && count($college->departments) > 0)
    <div class="form-group row ">
      <label class="col-sm-2 col-form-label" for="department">Departments:</label>
      <div class="col-sm-3" id='departments'>
        @foreach($college->departments as $index => $department)
          @if(0 == $index)
            <input type="text" class="form-control" name="department_{{$department->id}}" value="{{$department->name}}" required="true" placeholder="Department Name"><br/>

          @else
            <div class="input-group" id="{{$department->id}}">
              <span class="input-group-btn ">
                <input type="text" class="form-control" placeholder="Department Name" name="department_{{$department->id}}" value="{{$department->name}}" required="true" />
                <img class="img-vsm" src="{{ asset('images/delete3.png')}}" onclick="removeElement('departments','{{$department->id}}')" />
              </span>
            </div><br/>
          @endif
        @endforeach
      </div>
      <input type="hidden" id="delete_depts" name="delete_depts" value=""/>
    </div>
    @else
      <div class="form-group row @if ($errors->has('department_1')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="department">Departments:</label>
        <div class="col-sm-3" id='departments'>
          <input type="text" class="form-control" name="department_1" value="" placeholder="Department Name" required="true"><br>
        </div>
        @if($errors->has('department_1')) <p class="help-block">{{ $errors->first('department_1') }}</p> @endif
      </div>
    @endif

    <div class="form-group row">
      <!-- <div class="offset-sm-2 col-sm-3"> -->
        <button id="submitCollege" class="btn btn-primary">Submit</button>
        <button id="addDept" class="btn btn-primary">Add Department</button>
      <!-- </div> -->
    </div>
    </form>
    </div>
  </div>
<script type="text/javascript">
  $('#addDept').click(function(){
    var departmrnts = document.getElementById('departments');
    var numberOfChildren = departmrnts.getElementsByTagName('input').length + 1;
    var departmrntId = document.getElementById('college_id');

    var eleDiv = document.createElement('div');
    eleDiv.className = 'input-group';
    eleDiv.setAttribute("id", "id_" + numberOfChildren);

    var eleSpan = document.createElement('span');
    eleSpan.className = 'input-group-btn';

    var eleInput = document.createElement("INPUT");
    eleInput.setAttribute("type", "text");
    eleInput.setAttribute('class', 'form-control');
    eleInput.setAttribute("placeholder", "Department Name");

    eleInput.setAttribute("required", 'true');

    var eleImg = document.createElement("img");
    deleteImg = "{{asset('images/delete3.png')}}";
    eleImg.setAttribute("src", deleteImg);
    eleImg.className ="img-vsm";

    if(departmrntId){
      var randomNumber = Math.floor(Math.random() * 1000) - 100;
      eleInput.setAttribute("Name", "department_" + randomNumber);
      eleDiv.setAttribute("id", "id_" + randomNumber);
      eleImg.setAttribute("onclick", "removeElement('departments','id_" + randomNumber + "')");
    } else {
      eleInput.setAttribute("Name", "department_" + numberOfChildren);
      eleDiv.setAttribute("id", "id_" + numberOfChildren);
      eleImg.setAttribute("onclick", "removeElement('departments','" + numberOfChildren + "')");
    }

    eleSpan.appendChild(eleInput);
    eleSpan.appendChild(eleImg);


    eleDiv.appendChild(eleSpan);
    document.getElementById("departments").appendChild(eleDiv);
    var eleBr = document.createElement('Br');
    document.getElementById("departments").appendChild(eleBr);
  });

  function removeElement(parentDiv, childDiv){
    if (document.getElementById(childDiv)){
      var child = document.getElementById(childDiv);
      child.nextElementSibling.remove();
      var parent = document.getElementById(parentDiv);
      parent.removeChild(child);
      document.getElementById("delete_depts").value += childDiv + ',';
    }
  }
  $('#submitCollege').click(function(){
    document.getElementById("createCollege").submit();
  });
</script>
@stop