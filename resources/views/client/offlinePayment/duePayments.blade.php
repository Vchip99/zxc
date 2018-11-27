@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Due Payments  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Offline Payment </li>
      <li class="active"> Due Payments </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
  <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
  <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  &nbsp;
  <div class="container ">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
  @if(count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif
      <div class="row" >
          <div class="form-group">
              <div class="col-md-3">
                  <div style="margin-bottom: 10px">
                      <select class="form-control" name="batch" id="batch" onClick="setDate();">
                          <option value="">Select Batch</option>
                          @if(count($batches) > 0)
                              @foreach($batches as $batch)
                                  <option value="{{$batch->id}}">{{$batch->name}}</option>
                              @endforeach
                          @endif
                      </select>
                  </div>
              </div>
              <div class="col-md-3">
                  <div style="margin-bottom: 10px">
                      <input type="text"  class="form-control" name="due_date" id="due_date" placeholder="Select Date" >
                  </div>
              </div>
          </div>
      </div>
      <div class="row">
        <div class="col-lg-12" id="all-result">
          <div class="panel panel-info">
            <div class="panel-heading text-center">
              <span class="">Due Students</span>
            </div>
            <div class="panel-body">
              <table  class="" id="">
                <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Batch</th>
                    <th>Total Paid Amount</th>
                    <th>Last Comment</th>
                  </tr>
                </thead>
                <tbody id="dueUsers" class="">
                  @if(count($dueUsers) > 0)
                    @php
                      $index = 1;
                    @endphp
                    @foreach($dueUsers as $batchId => $users)
                      @if(count($users) > 0)
                        @foreach($users as $user)
                          <tr style="overflow: auto;">
                            <td>{{$index++}}</td>
                            <td>{{$user['user']}}</td>
                            <td>{{$user['batch']}}</td>
                            <td>{{$user['amount']}}</td>
                            <td>{{$user['comment']}}</td>
                          </tr>
                        @endforeach
                      @endif
                    @endforeach
                  @else
                    <tr>
                      <td colspan="5">Today, There is no due for students.</td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
<script type="text/javascript">
  function showDueStudents(){
    var batchId = document.getElementById('batch').value;
    var dueDate = document.getElementById('due_date').value;
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    if(batchId && dueDate){
      $.ajax({
          method:'POST',
          url: "{{url('getDueStudentsByBatchIdByDueDate')}}",
          data:{_token:currentToken,batch_id:batchId,due_date:dueDate}
      }).done(function( dueUsers ) {
         var body = document.getElementById('dueUsers');
         body.innerHTML = '';
        if(Object.keys(dueUsers).length){
          var index = 1;
          $.each(dueUsers,function(batchId, users){
            $.each(users,function(userId, user){
              var eleTr = document.createElement('tr');
              var eleIndex = document.createElement('td');
              eleIndex.innerHTML = index++;
              eleTr.appendChild(eleIndex);

              var eleName = document.createElement('td');
              eleName.innerHTML = user.user;
              eleTr.appendChild(eleName);

              var eleBatch = document.createElement('td');
              eleBatch.innerHTML = user.batch;
              eleTr.appendChild(eleBatch);

              var eleAmount = document.createElement('td');
              eleAmount.innerHTML = user.amount;
              eleTr.appendChild(eleAmount);

              var eleComment = document.createElement('td');
              eleComment.innerHTML = user.comment;
              eleTr.appendChild(eleComment);

              body.appendChild(eleTr);
            });

          });
        } else {
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.setAttribute('colspan',5);
          eleIndex.innerHTML = 'No Result.';
          eleTr.appendChild(eleIndex);
          body.appendChild(eleTr);
        }
      });
    } else if(!batchId){
      alert('Select Batch.');
    } else if(!dueDate){
      alert('Select Due Date.');
    }
  }
  function setDate(student){
    document.getElementById('due_date').value = '';
  }
  var currentDate = "{{ date('Y-m-d')}}";
  $(function () {
    $('#due_date').datetimepicker({
      defaultDate: currentDate,
      format: 'YYYY-MM-DD'
    }).on('dp.change', function (ev) {
      showDueStudents();
    });
  });
</script>
@stop