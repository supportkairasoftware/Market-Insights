<!DOCTYPE html>
<html lang="en">
<head>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keywords" content="MI, Market Insight">
    <link rel="icon" href="/small.gif">
    <title>@yield('Title')</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo asset('/assets/css/bootstrap.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/bootstrap.min.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/font-awesome.min.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/ionicons.min.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/morris/morris.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/toaster/toaster.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/jvectormap/jquery-jvectormap-1.2.2.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/datepicker/datepicker3.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/daterangepicker/daterangepicker-bs3.css');?>" rel="stylesheet" type='text/css'/>
    <link href="<?php echo asset('/assets/css/iCheck/all.css');?>" rel="stylesheet" type='text/css'/>
    <link href='http://fonts.googleapis.com/css?family=Lato' rel="stylesheet" type='text/css'/>
    <link href="<?php echo asset('/assets/css/style.css');?>" rel="stylesheet" type='text/css'/>
    <link href="<?php echo asset('/assets/css/custom.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/iCheck/all.css');?>" rel="stylesheet" type='text/css'/>
    <link rel="stylesheet" href="<?php echo asset('/assets/css/sitecss/msgbox/msgGrowl.css');?>" />
    <link rel="stylesheet" href="<?php echo asset('/assets/css/sitecss/bootstrap/css/jquery.msgbox.css');?>" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="<?php echo asset('/assets/js/ie-emulation-modes-warning.js'); ?>"></script>

    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>

<body class="skin-black">
    <header class="header ">
        <a href="" class="logo login-header">
            Market Insights
        </a>
    </header>
    <div class="wrapper" >
        <div class="addcontentloader">
            <div class="contentloader">
                <div class="contentloaderimage">
                </div>
            </div>
        </div>
        <section class="content">
            @yield('content')
        </section>
    </div>
    <!--<div class="footer-main">
        Copyright &copy Director, 2015
    </div>-->


    <!-- Placed at the end of the document so the pages load faster -->
    <script>window.baseUrl = "<?php echo URL::to('/')?>"</script>
    <script src="<?php echo asset('/assets/js/jquery.js');?>"></script>

    <script src="<?php echo asset('/assets/js/jquery-1.11.2.min.js');?>"></script>
    <script src="<?php echo asset('/assets/js/bootstrap.min.js');?>"></script>
    <script src="<?php echo asset('/assets/js/ie10-viewport-bug-workaround.js');?>"></script>

    <script src="<?php echo asset('/assets/js/pagelibraries/knockout-2.1.0.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagelibraries/knockout.mapping.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagelibraries/knockout.validation.js');?>"></script>

    <script src="<?php echo asset('/assets/js/pagejs/msgbox/msgGrowl.js');?>"></script>
    <script src="<?php echo asset('/assets/js/toaster/toaster.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/jquery.cookie.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/common.js');?>"></script>
    <script src="<?php echo asset('/assets/js/BootstrapDialogJs/bootstrap-dialog.js');?>"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $(this).scrollTop(0);
            $(window).load(function () {
                $(".contentloader").fadeOut("slow");
                $("#main").show();
                //$('#footer').show();
            });
        });
        window.history.forward();
    </script>
    
    @yield('script')
</body>
</html>

