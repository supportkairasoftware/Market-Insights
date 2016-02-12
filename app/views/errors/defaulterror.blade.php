@extends('layouts.sitemaster')
@section('Title', $HeaderMessage)
@stop
@section('content')

     <!-- Begin page content -->
	<main id="main" role="main" style="display:block;">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="error-container">
                                <h1>Oops!</h1>
                                <h2><?php echo $ErrorCodeWithMessage;?></h2>
                                <div class="error-details">
                                    <?php echo $ErrorMessage ;?>
                                </div>
                            </div> <!-- /.error-container -->
                        </div> <!-- /.span12 -->
                    </div> <!-- /.row -->
                </div>

    </main>
     <!-- End page content -->

@stop
@section('script')
 <script type="text/javascript">
 $(document).ready(function () {
	 $("#main").show();
	});
</script>
@stop