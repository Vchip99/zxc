@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |V-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"/>
@stop
@section('header-js')
  @include('layouts.home-js')
  <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('content')
	@include('header.header_menu')
  <section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single">
      <div class="vchip-background-img">
        <figure>
          <img src="{{asset('images/course.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip courses" />
        </figure>
      </div>
      <div class="vchip-background-content">
          <h2 class="animated bounceInLeft">Digital Education</h2>
        </div>
    </div>
  </section>
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
<!-- Start course section -->
<section id="sidemenuindex" class="v_container">
  <div class="container ">
    <div class="row">

    	<div class="col-md-5">
    	<div id="form">
	    	<form action="{{url('doAdvertisementPayment')}}" method="POST" enctype="multipart/form-data">
	    		{{ csrf_field() }}
	    		<div class="form-group row">
			      	<label for="course" class="col-sm-3 col-form-label">Show Ad on page</label>
			      	<div class="col-sm-9">
			    		<select class="form-control" name="selected_page" id="selected_page" required="true" onChange="showCalendar(this);">
			    			<option value="" style="font-weight: bold;">Select Page</option>
			    			@if(count($advertisementPages) > 0)
			    				@foreach($advertisementPages as $advertisementPage)
			    					@if($advertisementPage['parent_page'] > 0)
			    					<option value="{{$advertisementPage['id']}}" @if($advertisementPage['id'] == $selectedPage) selected="true" @endif >
			    							{!! $advertisementPage['name'] !!}
			    					</option>
			    					@else
			    					<option value="{{$advertisementPage['id']}}" @if($advertisementPage['id'] == $selectedPage) selected="true" @endif style="font-weight: bold;">
			    							{!! $advertisementPage['name'] !!}
			    					</option>
			    					@endif
			    				@endforeach
			    			@endif
			    		</select>
			      	</div>
			    </div>
		    	<div class="form-group row">
			      	<label for="course" class="col-sm-3 col-form-label">Institute</label>
			      	<div class="col-sm-9">
			          <input type="text" class="form-control" name="name" value="" required="true" placeholder="Max 20 characters only">
			      	</div>
			    </div>
			    <div class="form-group row">
			      	<label for="course" class="col-sm-3 col-form-label">Tag line</label>
			      	<div class="col-sm-9">
			          <input type="text" class="form-control" name="tag_line" value="" required="true"  placeholder="Max 50 characters only">
			      	</div>
			    </div>
			    <div class="form-group row">
			      	<label for="course" class="col-sm-3 col-form-label">Website/Url</label>
			      	<div class="col-sm-9">
			          <input type="text" class="form-control" name="website_url" value="" required="true">
			      	</div>
			    </div>
			    <div class="form-group row">
			      	<label for="course" class="col-sm-3 col-form-label">Email:</label>
			      	<div class="col-sm-9">
			          <input type="email" class="form-control" name="email" value="" required="true">
			      	</div>
			    </div>
			    <div class="form-group row">
			      	<label for="course" class="col-sm-3 col-form-label">Phone:</label>
			      	<div class="col-sm-9">
			          <input type="text" class="form-control" name="phone" value="" required="true">
			      	</div>
			    </div>
			    <div class="form-group row">
			      	<label for="course" class="col-sm-3 col-form-label">Logo/Image</label>
			      	<div class="col-sm-9">
			          <input type="file" class="form-control" name="logo" value="" required="true">
			      	</div>
			    </div>
			    <div class="form-group row">
			      	<label for="course" class="col-sm-3 col-form-label">Start Date</label>
			      	<div class="col-sm-9">
			          <input type="text" class="form-control" name="start_date" id="start_date" value="" required="true">
			      	</div>
			      	<script>
						$( "#start_date" ).datetimepicker({format: 'YYYY-MM-DD'}).on('dp.hide', function(e){ checkStartDate(e.currentTarget.value); });
					</script>
					<div class="hide" style="color: red" id="start_date_error">please select another date. already reached max limit( 3 ads per Page) for above selected page.</div>
			    </div>
			    <div class="form-group row">
			      	<label for="course" class="col-sm-3 col-form-label">End Date</label>
			      	<div class="col-sm-9">
			          <input type="text" class="form-control" name="end_date" id="end_date" value="" required="true">
			      	</div>
			      	<script>
						$( "#end_date" ).datetimepicker({format: 'YYYY-MM-DD'}).on('dp.hide', function(e){ checkDateSlot(e.currentTarget.value); });
					</script>
			    </div>
				<div class="hide" style="color: red" id="date_error">
					<div id="date_table">
					</div>
					<p style="color: red;"> Max limit for ads per page is 3. please check calendar and select another dates for above selected page.</p>
				</div>
				<div class="form-group row">
			      	<label for="course" class="col-sm-3 col-form-label">Total Price:</label>
			      	<div class="col-sm-9">
			          Rs. <span id="price"></span>
			      	</div>
			    </div>
		    	<div class="form-group row" >
			      	<div class="offset-sm-2 col-sm-3" title="Submit">
			        	<button type="submit" class="btn btn-primary" id="submit" disabled="true">Make Payment</button>
			      	</div>
		    	</div>
		    </form>
		    <form action="{{url('createAd')}}" method="GET" id="selectedPageForm">
		    	<input type="hidden" id="hidden_selected_page" name="page" value="{{ $selectedPage }}" />
		    </form>
	  	</div>
	  	</div>
	  	<div class="col-md-7">
	  		<div id="mycalendar">
	  			{!! $calendar->calendar() !!}
    		</div>
	  	</div>
    </div>
  </div>
</section>

@stop
@section('footer')
	@include('footer.footer')
	{!! $calendar->script() !!}
  <script type="text/javascript" src="{{ asset('js/togleForFilterBy.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/read_info.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>
  <script type="text/javascript">

	function checkStartDate(date){
  		var selectedPage =  document.getElementById('selected_page').value;
  		var myDate = new Date(date);
  		myDate.setHours(0,0,0,0);
        var today = new Date();
        today.setHours(0,0,0,0);
        if ( myDate >= today ) {
	      	$.ajax({
	         	method: "POST",
	          	url: "{{url('checkStartDate')}}",
	          	data: {date:date,selected_page:selectedPage}
	      	})
	      	.done(function( count ) {
	      		if( 3 <= count){
	      			$('#start_date_error').removeClass('hide');
	      		} else {
	      			$('#start_date_error').addClass('hide');
	      		}
	      	});
        } else {
        	document.getElementById('start_date').value = '';
        	$('#start_date_error').addClass('hide');
        	alert('start date should be equal to or greter than today.');
        }
  	}

  	function checkDateSlot(endDate){
	  	var startDate =  document.getElementById('start_date').value;
	  	var selectedPage =  document.getElementById('selected_page').value;
        var spdate = new Date();
		var sdd = spdate.getDate();
		if(spdate.getMonth() > 9){
			var smm = spdate.getMonth() + 1;
		} else {
			var smm = '0'+(spdate.getMonth() + 1);
		}
		var syyyy = spdate.getFullYear();
		var today = syyyy+'-'+smm+'-'+sdd;

	  	if(endDate < startDate){
	  		document.getElementById('end_date').value = '';
	  		alert('end date is always greter than start date.');
	  	} else if(startDate < today) {
	  		document.getElementById('start_date').value = '';
        	$('#start_date_error').addClass('hide');
	  		alert('start date should be equal to or greter than today.');
	  	} else {
	      $.ajax({
	          method: "POST",
	          url: "{{url('checkDateSlot')}}",
	          data: {start_date:startDate,end_date:endDate,selected_page:selectedPage}
	      })
	      .done(function( output ) {
	      	if( false == output['status']){
	      		$('#date_error').removeClass('hide');
	      		var tableDiv = document.getElementById('date_table');
	      			tableDiv.innerHTML = 'there is no space for ads in between '+output['start_date']+' and ' +output['end_date']+'.';
	      		document.getElementById('submit').setAttribute('disabled', true);
	      	} else {
	      		if(output['price']){
	      			document.getElementById('price').innerHTML = output['price'];
	      		}
	      		$('#date_error').addClass('hide');
	      		document.getElementById('submit').removeAttribute('disabled');
	      	}
	      });
		}
  	}

  	function showCalendar(ele){
	    var selectedPage = parseInt($(ele).val());
	    document.getElementById('hidden_selected_page').value = selectedPage;
	    document.getElementById('selectedPageForm').submit();
  	}
  </script>
@stop