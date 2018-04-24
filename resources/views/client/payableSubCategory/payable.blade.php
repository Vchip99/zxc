@extends('client.dashboard')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Payable SubCategory  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-shopping-cart"></i> Market Place </li>
      <li class="active"> Payable SubCategory </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <link href="{{ asset('css/box.css')}}" rel="stylesheet"/>
  <style type="text/css">
    .vchip_product_item{
      background:#FFF;
      padding: 20px;
      -webkit-box-shadow:0 0 5px 0 rgba(130,130,130,0.4);
      -moz-box-shadow:0 0 5px 0 rgba(130,130,130,0.4);
      box-shadow:0 0 5px 0 rgba(130,130,130,0.4);
      margin-bottom:40px;
      text-align:left
    }
    .vchip_product_item:hover{

      box-shadow: 0 8px 17px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);

    }
    .vchip_product_content{padding:10px 20px}
  </style>
  <div class="container ">
    <div class="row">
      @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif
      @if(Session::has('message'))
        <div class="alert alert-success" id="message">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ Session::get('message') }}
        </div>
      @endif
      <div class="col-sm-9">
        <div class="row" id="testSubCategories">
          @if(count($testSubCategories) > 0)
            @foreach($testSubCategories as $testSubCategory)
              @if(false == isset($purchasedSubCategories[$testSubCategory->id]))
                <div class="col-lg-6 col-md-6 col-sm-6 small-img">
                    <div class="vchip_product_itm text-left">
                      <a href="{{url('showPayableSubcategory')}}/{{ $testSubCategory->id }}" class="btn-link">
                        <figure title="{{$testSubCategory->name}}">
                          <img src="{{ asset($testSubCategory->image_path) }}" alt="exam" class="img-responsive " />
                        </figure>
                        <ul class="vchip_categories list-inline">
                          <li>{{$testSubCategory->name}}</li>
                        </ul>
                      </a>
                      <div class="categoery" style="padding-left: 18px;">
                        <span style="color: #e91e63;">Price: {{$testSubCategory->price}} Rs/year </span> OR &nbsp;
                        <span style="color: #e91e63;">Price: {{$testSubCategory->monthly_price}} Rs/month</span>
                        </br>
                            <a class="btn btn-primary" title="Pay Now" style="min-width: 100px;" data-toggle="modal" data-target="#purchaseSubCatModel_{{$testSubCategory->id}}">Pay Now</a>
                            <!-- Modal -->
                            <div class="modal fade" id="purchaseSubCatModel_{{$testSubCategory->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Purchase Sub Category</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <form action="{{url('purchasePayableSubCategory')}}" method="POST" enctype="multipart/form-data" id="submitForm">
                                  {{ csrf_field() }}
                                  <div class="modal-body">
                                      <div class="form-group row">
                                        <label for="category" class="col-sm-4 col-form-label">Category</label>
                                        <div class="col-sm-8">
                                          <select class="form-control" id="category" name="category" title="Category" required>
                                            <option value="">Select Category</option>
                                            @foreach($testCategories as $testCategory)
                                              <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </div>
                                      <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Sub Category Price</label>
                                        <div class="col-sm-8">
                                          <input type="text" class="form-control" name="subcat_price" value="" required>
                                        </div>
                                      </div>
                                      <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Select Plan</label>
                                        <div class="col-sm-8">
                                          <input type="radio" name="plan_{{$testSubCategory->id}}" value="{{$testSubCategory->monthly_price}}" checked="true"  data-id="{{$testSubCategory->id}}" onclick="changeTotal(this)" required> Monthly:Rs {{$testSubCategory->monthly_price}}
                                          <input type="radio" name="plan_{{$testSubCategory->id}}" value="{{$testSubCategory->price}}" data-id="{{$testSubCategory->id}}" onclick="changeTotal(this)" required> Yearly:Rs {{$testSubCategory->price}}
                                        </div>
                                      </div>
                                      <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Select Month/Year</label>
                                        <div class="col-sm-8">
                                          <input type="number" id="duration_{{$testSubCategory->id}}" name="duration" value="1" min="1" data-id="{{$testSubCategory->id}}" onchange="showTotal(this);" required>
                                        </div>
                                      </div>
                                      <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Total</label>
                                        <div class="col-sm-8">
                                          <input type="text" name="total" id="total_{{$testSubCategory->id}}" value="{{$testSubCategory->monthly_price}}" required>
                                        </div>
                                      </div>
                                  </div>
                                  <div class="modal-footer">
                                    <input type="hidden" name="subcategory_id" value="{{$testSubCategory->id}}">
                                    <button type="submit" class="btn btn-primary">Pay</button>
                                  </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                      </div>
                      <div class="vchip_product_content">
                        <p class="mrgn_20_top"><a href="{{url('showPayableSubcategory')}}/{{ $testSubCategory->id }}" class="btn-link">Details <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                        </p>
                      </div>
                    </div>
                </div>
              @endif
            @endforeach
            @if(count($purchasedSubCategories) == count($testSubCategories))
              No Payable Sub Category available.
            @endif
          @else
            No Payable Sub Category available.
          @endif
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    function showTotal(ele){
      var duration = $(ele).val();
      var subcat = $(ele).data('id');
      var price = $('input[name="plan_'+subcat+'"]:checked').val();
      document.getElementById('total_'+subcat).value = price * duration;
    }
    function changeTotal(ele){
      var price = $(ele).val();
      var subcat = $(ele).data('id');
      var duration = document.getElementById('duration_'+subcat).value;
      document.getElementById('total_'+subcat).value = price * duration;
    }
  </script>
@stop