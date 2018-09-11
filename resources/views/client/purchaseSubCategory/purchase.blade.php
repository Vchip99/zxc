@extends('client.dashboard')
  @section('module_title')
  <section class="content-header">
    <h1> Purchased SubCategory  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-shopping-cart"></i> Market Place </li>
      <li class="active"> Purchased SubCategory </li>
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
              @if(isset($purchasedSubCategories[$testSubCategory->id]))
                <div class="col-lg-6 col-md-6 col-sm-6 small-img">
                    <div class="vchip_product_itm text-left">
                      <a href="{{url('showPurchaseSubcategory')}}/{{ $testSubCategory->id }}" class="btn-link">
                        <figure title="{{$testSubCategory->name}}">
                          @if(!empty($purchasedSubCategories[$testSubCategory->id]->client_image))
                            <img src="{{ asset($purchasedSubCategories[$testSubCategory->id]->client_image) }}" alt="exam" class="img-responsive " />
                          @else
                            <img src="{{ asset($testSubCategory->image_path) }}" alt="exam" class="img-responsive " />
                          @endif
                        </figure>
                        <ul class="vchip_categories list-inline">
                          <li>{{$testSubCategory->name}}</li>
                        </ul>
                      </a>
                      <div class="categoery" style="padding-left: 18px;">
                        <span style="color: #e91e63;">Price: {{$testSubCategory->price}} Rs/year </span> OR &nbsp;
                        <span style="color: #e91e63;">Price: {{$testSubCategory->monthly_price}} Rs/month</span>
                        </br>
                        <a class="btn btn-primary" title="Edit" style="min-width: 100px;" data-toggle="modal" data-target="#updateSubCatModel_{{$testSubCategory->id}}">Edit</a>
                        <div class="modal fade" id="updateSubCatModel_{{$testSubCategory->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Update Sub Category</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="{{url('updatePayableSubCategory')}}" method="POST" enctype="multipart/form-data" id="submitForm">
                              {{ csrf_field() }}
                              <div class="modal-body">
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Sub Category Price</label>
                                    <div class="col-sm-8">
                                      <input type="text" class="form-control" name="subcat_price" value="{{$purchasedSubCategories[$testSubCategory->id]->client_user_price}}" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Sub Category Image</label>
                                    <div class="col-sm-8">
                                      <input type="file" class="form-control" name="subcat_image">
                                    </div>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                <input type="hidden" name="subcategory_id" value="{{$testSubCategory->id}}">
                                <input type="hidden" name="subcategory_name" value="{{$testSubCategory->name}}">
                                <input type="hidden" name="payable_subcategory_id" value="{{$purchasedSubCategories[$testSubCategory->id]->id}}">
                                <button type="submit" class="btn btn-primary">Update</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="vchip_product_content">
                        <p class="mrgn_20_top"><a href="{{url('showPurchaseSubcategory')}}/{{ $testSubCategory->id }}" class="btn-link">Details <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                        </p>
                      </div>
                    </div>
                </div>
              @endif
            @endforeach
            @if(0 == count($purchasedSubCategories))
              No Sub Category Purchased.
            @endif
          @else
            No Sub Category Purchased.
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