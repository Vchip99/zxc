<footer style="background: black;">
	<div class="container" style="padding-top: 20px;">
		<div class="row">
			<div class="col-sm-4">
				<h2 style="text-decoration: underline;">ABOUT</h2>
				<p><a href="/">Home</a></p>
				<p><a href="{{ url('mentors') }}">Mentors</a></p>
				<p><a href="{{ url('faq') }}">FAQ</a></p>
			</div>
			<div class="col-sm-4">
				<h2 style="text-decoration: underline;">MENTOR</h2>
				<p><a href="{{ url('mentors') }}">All Mentors</a></p>
				<p><a href="#">Mentor Login</a></p>
				<p><a href="#">Mentor Sign-up</a></p>
			</div>
			<div class="col-sm-4">
				<h2 style="text-decoration: underline;">TERMS</h2>
				<p><a href="#">Terms and Conditions</a></p>
				<p><a href="#"> Private Policy</a></p>
			</div>
		</div>
	</div>

    <hr>
    <div style="text-align: center;">
        <a href="https://vchiedu.com">©2018 Vchip Technology, All Rights Reserved. Design by: Vchip Technology</a>
    </div>
<script type="text/javascript">
  	$(document).ready(function(){
        setTimeout(function() {
          $('.alert-success').fadeOut('fast');
        }, 10000); // <-- time in milliseconds
    });
</script>
</footer>