@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> My Offline Test Results </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test</li>
      <li class="active">My Offline Test Results </li>
    </ol>
  </section>
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div class="">
      <div class="container">
        <div class="row">
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
               RESULT
              </div>
              <div class="panel-body">
                <table  class="" id="dataTables-example">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Subject</th>
                      <th>Paper</th>
                      <th>Marks</th>
                    </tr>
                  </thead>
                  <tbody  id="test-result" >
                    @if(count($marks) > 0)
                      @foreach($marks as $index => $mark)
                        <tr class="">
                          <td>{{$index + 1}}</td>
                          <td>{{$collegeSubjectNames[$mark->college_subject_id]}}</td>
                          <td>{{$collegeOfflinePaperNames[$mark->college_offline_paper_id]}}</td>
                          <td class="center">{{$mark->marks}} / {{$mark->total_marks}}</td>
                        </tr>
                      @endforeach
                    @elseif(0 == count($marks))
                      <tr class="">
                        <td colspan="4">No result.</td>
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