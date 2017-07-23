@extends('layouts.master')
@section('content')
	<div class="content">
     	<div class="container">
			<div class='row'>
				<div class="col-md-8 col-sm-8 col-xs-12">
					<h1>My Account:</h1><br>
					<form action="{{ url('account') }}" id="account-form" method="post" class="form-horizontal myaccount" role="form">
						{{ csrf_field() }}
						<div class="form-group">
							<span for="inputEmail3" class="col-sm-4 control-span">Name</span>
							<div class="col-sm-8">
								<p> {{ $userName }} </p>
							</div>
						</div>
						<div class="form-group">
							<span for="inputPassword3" class="col-sm-4 control-span">Email</span>
							<div class="col-sm-8">
								<p> {{ $userEmail }} </p>
							</div>
						</div>
						<hr>
						<div class="form-group">
							<span for="inputPassword3" class="col-sm-4 control-span">Current Password</span>
							<div class="col-sm-8">
								<input name="old_password" id="old_password" type="password" class="form-control" autocomplete="false">
								<span class="help-block"></span>
							</div>
						</div>

						<div class="form-group">
							<span for="inputPassword3" class="col-sm-4 control-span"> New Password</span>
							<div class="col-sm-8">
								<input name="password" id="password" type="text" class="form-control">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<span for="inputPassword3" class="col-sm-4 control-span"> Confirm Password</span>
							<div class="col-sm-8">
								<input name="confirm_password" id="confirm_password" type="text" class="form-control">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-4 col-sm-8">
								<button type="submit" class="btn btn-default">Change Password</button>
							</div>
						</div>
					</form>
			</div>
			</div>
     	</div>
    </div> <!-- /container -->
{{-- <script src="js/jquery.validate.min.js"></script> --}}
{{-- <script src="{{ asset('/js/account.js') }}"></script>   --}}
<script type="text/javascript">
	$(document).ready(function(){

	$("#account-form").validate({
		submitHandler : function(e) {
		    $(form).submit();
		},
		rules : {
			old_password : {
				required : true,
				remote   : {
						url: "{{ url('checkEmail')}}",
						type: "get",
						data: {
							password: function() {
								return $( "#old_password" ).val();
							}						}
				}
			},
			password : {
				required : true
			},
			confirm_password : {
				required : true,
				equalTo: "#password"
			}
		},
		messages : {
			old_password : {
				required : "Please enter current password",
				remote : "Please enter correct current password"
			},
			password : {
				required : "Please enter password"
			},
			confirm_password : {
				required : "Please enter confirm password",
				equalTo: "Password and confirm password doesn't match"
			}
		},
		errorPlacement : function(error, element) {
			$(element).closest('div').find('.help-block').html(error.html());
		},
		highlight : function(element) {
			$(element).closest('div').removeClass('has-success').addClass('has-error');
		},
		unhighlight: function(element, errorClass, validClass) {
			 $(element).closest('div').removeClass('has-error').addClass('has-success');
			 $(element).closest('div').find('.help-block').html('');
		}
	});


});


</script>

@endsection