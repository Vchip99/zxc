@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Online Payments </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Payments</li>
      <li class="active"> My Online Payments </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
<div class="content-wrapper v-container tab-content" >
    <div class="">
        <div class="container">
          <div class="row">
            <div class="col-lg-12" id="all-result">
              <div class="panel panel-info">
                  <div class="panel-heading text-center">
                    Payments
                  </div>
                  <div class="panel-body">
                    <table  class="" id="dataTables-example">
                      <thead>
                        <tr>
                          <th>Sr. No.</th>
                          <th>Name</th>
                          <th>Type</th>
                          <th>Date</th>
                          <th>Payment</th>
                          <th>Receipt</th>
                        </tr>
                      </thead>
                      <tbody >
                        @php
                          $total = 0;
                        @endphp
                        @if(count($userPurchasedCourses) > 0)
                          @foreach($userPurchasedCourses as $index => $userPurchasedCourse)
                            <tr style="overflow: auto;">
                              <td>{{$index + 1}}</td>
                              <td>{{$userPurchasedCourse->course->name}}</td>
                              <td>Course</td>
                              <td>{{date('Y-m-d',strtotime($userPurchasedCourse->updated_at))}}</td>
                              <td>{{$userPurchasedCourse->price}}</td>
                              <td><a href="{{ url('onlineReceipt')}}/Course/{{$userPurchasedCourse->id}}" target="_blank">Receipt</a></td>
                            </tr>
                            @php
                              $total += $userPurchasedCourse->price;
                            @endphp
                          @endforeach
                        @endif
                        @if(count($userPurchasedSubCategories) > 0)
                          @foreach($userPurchasedSubCategories as $index => $userPurchasedSubCategory)
                            <tr style="overflow: auto;">
                              <td>{{$index + 1}}</td>
                              <td>{{$userPurchasedSubCategory->testSubCategory->name}}</td>
                              <td>SubCategory</td>
                              <td>{{date('Y-m-d',strtotime($userPurchasedSubCategory->updated_at))}}</td>
                              <td>{{$userPurchasedSubCategory->price}}</td>
                              <td><a href="{{ url('onlineReceipt')}}/SubCategory/{{$userPurchasedSubCategory->id}}" target="_blank">Receipt</a></td>
                            </tr>
                            @php
                              $total += $userPurchasedSubCategory->price;
                            @endphp
                          @endforeach
                        @endif
                        @if($total > 0)
                          <tr>
                            <td colspan="3"></td>
                            <td>Total</td>
                            <td colspan="2">{{$total}}</td>
                          </tr>
                        @else
                          <tr>
                            <td colspan="6">No Payments.</td>
                          </tr>
                        @endif
                      </tbody>
                    </table>
                  </div>
              </div>
            </div>
          </div>
        </div>
    </div>
</div>
@stop