@extends('layouts.master')
@section('content')
	<div class="content">
     	<div class="container">

     		<div class="row">
	     		<div class="col-xs-12 col-sm-5 col-md-5 col-lg-5 start-page">
			    <h3 class="text_underline">Choose Your Category </h3>
					<form data-toggle="validator" class="form-signin well" method="get" id='startQuiz' name="startQuiz" action="{{ url('questions') }}">
						{{ csrf_field() }}

						<div class="form-group">
							<select  class="form-control" name="subcat" id="subcat" data-error="Please enter name field.">
								<option value="">Choose Subcategory</option>

							</select>
							<span class="help-block"></span>
						</div>

						<br/>
						<button id="start_btn" class="btn btn-success btn-block" type="submit">Start!!!</button>
						<!-- {{-- <button type="button"  onclick="newSession();" class="btn  btn-danger">Start</button>&emsp; --}} -->
					</form>

				</div>
     		</div>
     	</div>
    </div> <!-- /container -->


<script type="text/javascript">

	function getSubcategories(id)
	{
	 	$.ajax({
            method: "GET",
            url: "{{url('subcategories')}}"+"/"+id
        })
        .done(function( msg ) {
        	select = document.getElementById('subcat');
			$.each(msg, function(idx, obj) {
			    var opt = document.createElement('option');
			    opt.value = obj.id;
			    opt.innerHTML = obj.name;
			    select.appendChild(opt);
			});
        });
	}
	$(document).ready( function(){
		$("#startQuiz").validate({
			submitHandler : function(form) {
			    $('#start_btn').attr('disabled','disabled');
			    form.submit();
			},
			rules : {
				category : {
					required : true
				},
				subcat : {
					required : true
				}
			},
			messages : {
				category:{
	                required : "Please choose your category to start Quiz."
	           },
	           subcat : {
	           	   required : "Please choose your sub category to start Quiz."
	           }
			},
			errorPlacement : function(error, element)
			{
				$(element).closest('.form-group').find('.help-block').html(error.html());
			},
			highlight : function(element) {
				$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
			}
		});

	});
		function newSession(){
			window.open("{{ asset('questions')}}", "My Window", "height=800px,width=1000px");
		}

</script>
@endsection