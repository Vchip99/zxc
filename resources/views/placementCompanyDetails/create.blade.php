@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Placement Company Details</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-gift"></i> Placement </li>
      <li class="active"> Manage Placement Company Details</li>
    </ol>
  </section>
@stop
@section('admin_content')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  &nbsp;
  <div class="container admin_div">
  @if(isset($companyDetail->id))
    <form action="{{url('admin/updatePlacementCompanyDetails')}}" method="POST" enctype="multipart/form-data">
      {{method_field('PUT')}}
      <input type="hidden" name="company_details_id" value="{{$companyDetail->id}}">
  @else
      <form action="{{url('admin/createPlacementCompanyDetails')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('area')) has-error @endif">
    <label class="col-sm-2 col-form-label">Placement Area:</label>
    <div class="col-sm-3">
          @if( isset($companyDetail->id) && count($placementAreas) > 0)
            @foreach($placementAreas as $placementArea)
              @if( $companyDetail->placement_area_id == $placementArea->id)
                <input type="text" class="form-control" name="area_text" value="{{$placementArea->name}}" readonly="true">
                <input type="hidden" name="area" value="{{$placementArea->id}}">
              @endif
            @endforeach
          @else
            <select class="form-control" name="area" required title="Placement Area" onClick="selectCompany(this);">
              <option value="">Select Placement Area</option>
              @if(count($placementAreas) > 0)
                @foreach($placementAreas as $placementArea)
                  @if( $companyDetail->placement_area_id == $placementArea->id)
                    <option value="{{$placementArea->id}}" selected="true">{{$placementArea->name}}</option>
                  @else
                    <option value="{{$placementArea->id}}">{{$placementArea->name}}</option>
                  @endif
                @endforeach
              @endif
              </select>
          @endif
        @if($errors->has('area')) <p class="help-block">{{ $errors->first('area') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('company')) has-error @endif">
    <label for="company" class="col-sm-2 col-form-label">Placement Company:</label>
    <div class="col-sm-3">
      @if( isset($companyDetail->id) && count($placementCompanies) > 0)
        @if(count($placementCompanies) > 0)
          @foreach($placementCompanies as $placementCompany)
            @if( $companyDetail->placement_company_id == $placementCompany->id)
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
              @if( $companyDetail->placement_company_id == $placementCompany->id)
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
     <p> Company Details is exists for above criteria. Please select another company.
    </div>
  </div>
  <div class="form-group row @if ($errors->has('about_company')) has-error @endif">
    <label for="about_company" class="col-sm-2 col-form-label">About Company:</label>
    <div class="col-sm-10">
      @if(isset($companyDetail))
        <textarea class="form-control" id="about_company" name="about_company" required="true">{{$companyDetail->about_company}}</textarea>
      @else
        <textarea class="form-control" id="about_company" name="about_company" required="true">
        </textarea>
      @endif
       <script type="text/javascript">
          CKEDITOR.replace( 'about_company' );
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
  <div class="form-group row @if ($errors->has('industry_type')) has-error @endif">
    <label for="industry_type" class="col-sm-2 col-form-label">Industry Type:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" id="industry_type" name="industry_type" value="{{$companyDetail->industry_type}}" required="true">
      @if($errors->has('industry_type')) <p class="help-block">{{ $errors->first('industry_type') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('founded_year')) has-error @endif">
    <label for="founded_year" class="col-sm-2 col-form-label">Founded Year:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" id="founded_year" name="founded_year" value="{{$companyDetail->founded_year}}" required="true">
      @if($errors->has('founded_year')) <p class="help-block">{{ $errors->first('founded_year') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('founder_name')) has-error @endif">
    <label for="founder_name" class="col-sm-2 col-form-label">Founder Name:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" id="founder_name" name="founder_name" value="{{$companyDetail->founder_name}}" required="true">
      @if($errors->has('founder_name')) <p class="help-block">{{ $errors->first('founder_name') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('headquarters')) has-error @endif">
    <label for="headquarters" class="col-sm-2 col-form-label">Headquarters:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" id="headquarters" name="headquarters" value="{{$companyDetail->headquarters}}" required="true">
      @if($errors->has('headquarters')) <p class="help-block">{{ $errors->first('headquarters') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('ceo')) has-error @endif">
    <label for="ceo" class="col-sm-2 col-form-label">CEO/MD:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" id="ceo" name="ceo" value="{{$companyDetail->ceo}}" required="true">
      @if($errors->has('ceo')) <p class="help-block">{{ $errors->first('ceo') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('website')) has-error @endif">
    <label for="website" class="col-sm-2 col-form-label">Official Website:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" id="website" name="website" value="{{$companyDetail->website}}" required="true">
      @if($errors->has('website')) <p class="help-block">{{ $errors->first('website') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('mock_test_link')) has-error @endif">
    <label for="mock_test_link" class="col-sm-2 col-form-label">Mock Test Link:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" id="mock_test_link" name="mock_test_link" value="{{$companyDetail->mock_test_link}}" required="true">
      @if($errors->has('mock_test_link')) <p class="help-block">{{ $errors->first('mock_test_link') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('products')) has-error @endif">
    <label for="products" class="col-sm-2 col-form-label">Products:</label>
    <div class="col-sm-3">
      @if(isset($companyDetail))
        <textarea class="form-control" id="products" name="products" value="{{$companyDetail->products}}" required="true">{{$companyDetail->products}}</textarea>
      @else
        <textarea class="form-control" id="products" name="products" value="{{$companyDetail->products}}" required="true">
        </textarea>
      @endif
      <p>ex. a,b,c</p>
      @if($errors->has('products')) <p class="help-block">{{ $errors->first('products') }}</p> @endif
    </div>
  </div>
  <div class="form-group row"  id="submit">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</form>
<script type="text/javascript">
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
          url: "{{url('admin/checkCompanyDetails')}}",
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