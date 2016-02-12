@extends('layouts.loginmaster')
@section('Title', ' Unauthorized ')
@section('content')

     <!-- Begin page content -->

     <main id="main" role="main" style="display:none;">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="error-container">
						<h1>Oops!</h1>
						<h2> Authorization Error</h2>
						<div class="error-details">
							Sorry, You are trying to access a web page without proper authorization.
						</div>
					</div> <!-- /.error-container -->
				</div> <!-- /.span12 -->
			</div> <!-- /.row -->
		</div>
		</main>
     <center><button class="btn btn-default"><a href="<?php echo URL::to('/'); ?>">Click here to Login</a></button></center>
	 <!-- End page content -->


@stop
@section('script')
<script type="text/javascript">
	$(document).ready(function () {
		$("#main").show();
	});
</script>
@stop