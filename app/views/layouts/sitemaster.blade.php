<?php
use Infrastructure\Common;
use \Infrastructure\Constants;
use \ViewModels\SessionHelper;
?>
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
    <link rel="icon" href="/favicon.png">

    <title>@yield('Title')</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo asset('/assets/css/bootstrap.css');?>" rel="stylesheet" type='text/css'>
	<link href="<?php echo asset('/assets/css/bootstrap.min.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/font-awesome.min.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/ionicons.min.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/bootstrap-datepicker.css');?>" rel="stylesheet" type='text/css'>
	<link href="<?php echo asset('/assets/css/morris/morris.css');?>" rel="stylesheet" type='text/css'>
	<link href="<?php echo asset('/assets/css/toaster/toaster.css');?>" rel="stylesheet" type='text/css'>
    <link href="<?php echo asset('/assets/css/sitecss/msgbox/msgGrowl.css');?>" rel="stylesheet" type='text/css'>
	<link href="<?php echo asset('/assets/css/jvectormap/jquery-jvectormap-1.2.2.css');?>" rel="stylesheet" type='text/css'>

    <link href="<?php echo asset('/assets/css/iCheck/all.css');?>" rel="stylesheet" type='text/css'/>
    <link href='http://fonts.googleapis.com/css?family=Lato' rel="stylesheet" type='text/css'/>
    <link href="<?php echo asset('/assets/css/style.css');?>" rel="stylesheet" type='text/css'/>
    <link href="<?php echo asset('/assets/css/custom.css');?>" rel="stylesheet" type='text/css'/>
    <link href="<?php echo asset('/assets/css/custom-responsive.css');?>" rel="stylesheet" type='text/css'/>
    <link href="<?php echo asset('/assets/css/iCheck/all.css');?>" rel="stylesheet" type='text/css'/>
    <link href="<?php echo asset('/assets/css/jquery.datetimepicker.css');?>" rel="stylesheet" type='text/css'/>
    @yield('CSS')
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="<?php echo asset('/assets/js/ie-emulation-modes-warning.js'); ?>"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

</head>

<body class="skin-black">
<div class="windowloader">
    <div class="loaderimage">
    </div>
</div>
    <!-- Header Section -->
    <header class="header">
        <a href="<?php echo URL::to('/').'/dashboard'; ?>" class="logo">
            Market Insights
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="navbar-right">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i>
                            <span>{{ @Auth::User()->FirstName }} <i class="caret"></i></span>
                        </a>
                        <ul class="dropdown-menu dropdown-custom dropdown-menu-right">
                            <li class="dropdown-header text-center">Account</li>
                            <li>
                                <a href="<?php echo URL::to('/edituser');?>">
                                    <i class="fa fa-user fa-fw pull-right"></i>
                                    Profile
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?php echo URL::to('/').'/adminlogout'; ?>"><i class="fa fa-ban fa-fw pull-right"></i> Logout</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Header Section End-->

    <div class="wrapper row-offcanvas row-offcanvas-left">
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="left-side sidebar-offcanvas">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                         <img   src="<?php echo asset(!empty(Auth::User()->UserImageUrl)?Constants::$Path_ProfileImages.Auth::User()->UserID.'/'.Auth::User()->UserImageUrl:'/assets/img/no-user.jpg');?>" class="img-circle" alt="User Image" />
                    </div>
                    <div class="pull-left info">
                        <p>Hi, {{ @Auth::User()->FirstName; }}</p>

                        <!--<a href="#"><i class="fa fa-circle text-success"></i> Online</a>-->
                    </div>
                </div>

                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu">
                    <li id="dashboard">
                        <a href="<?php echo URL::to('/dashboard'); ?>">
                            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li id="userlist">
                        <a href="<?php echo URL::to('/userlist'); ?>">
                            <i class="fa fa-user"></i> <span>Users List</span>
                        </a>
                    </li>

                    <li class="treeview" id="groups">
                        <a href="#">
                            <i class="fa fa-users"></i> <span>Groups</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li id="addgroup"><a href="<?php echo URL::to('/addgroup'); ?>"><i class="fa fa-plus"></i> <span>Add Group</span></a></li>
                            <li id="grouplist"><a href="<?php echo URL::to('/grouplist'); ?>"><i class="fa fa-list"></i> <span>Group List</span></a></li>
                            <li id="usergroup"><a href="<?php echo URL::to('/usergrouplist'); ?>"><i class="fa fa-users"></i> <span>Users Group List</span></a></li>
                        </ul>
                    </li>

                    <li class="treeview" id="plan">
                        <a href="#">
                            <i class="fa fa-location-arrow"></i> <span>Plans</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li id="addplan"><a href="<?php echo URL::to('/addplan'); ?>"><i class="fa fa-plus"></i> <span>Add Plan</span></a></li>
                            <li id="planlist"><a href="<?php echo URL::to('/planlist'); ?>"><i class="fa fa-list"></i> <span>Plans List</span></a></li>
                        </ul>
                    </li>

                    <li class="treeview" id="payment">
                        <a href="#">
                            <i class="fa fa-inr"></i> <span>Payment</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li id="addpayment"><a href="<?php echo URL::to('/addpayment'); ?>"><i class="fa fa-plus"></i> <span>Add Payment</span></a></li>
                            <li id="paymenthistory"><a href="<?php echo URL::to('/userpaymentlist'); ?>"><i class="fa fa-list"></i> <span>Payment History</span></a></li>
                        </ul>
                    </li>

                    <li class="treeview" id="fundamental">
                        <a href="#">
                            <i class="fa fa-file-o"></i> <span>Technical Reports</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li id="addfundamental"><a href="<?php echo URL::to('/addfundamental'); ?>"><i class="fa fa-plus"></i> <span>Add Technical Report</span></a></li>
                            <li id="fundamentallist"><a href="<?php echo URL::to('/fundamentallist'); ?>"><i class="fa fa-list"></i> <span>Technical Report List</span></a></li>
                        </ul>
                    </li>

                    <li class="treeview" id="analyst">
                        <a href="#">
                            <i class="fa fa-files-o"></i> <span>Analyst View</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li id="addanalyst"><a href="<?php echo URL::to('/addanalyst'); ?>"><i class="fa fa-plus"></i> <span>Add Analyst View</span></a></li>
                            <li id="analystlist"><a href="<?php echo URL::to('/analystlist'); ?>"><i class="fa fa-list"></i> <span>Analyst View List</span></a></li>
                        </ul>
                    </li>
                    <li class="treeview" id="script">
                        <a href="#">
                            <i class="fa fa-files-o"></i> <span>Script</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li id="addscript"><a href="<?php echo URL::to('/addscript'); ?>"><i class="fa fa-plus"></i> <span>Add Script</span></a></li>
                            <li id="scriptlist"><a href="<?php echo URL::to('/scriptlist'); ?>"><i class="fa fa-list"></i> <span>Script List</span></a></li>
                        </ul>
                    </li>
                    <li id="calllist">
                        <a href="<?php echo URL::to('/calllist'); ?>">
                            <i class="fa fa-phone"></i> <span>Calls</span>
                        </a>
                    </li>
                    <li id="sms">
                        <a href="<?php echo URL::to('/smslist'); ?>">
                            <i class="fa fa-mobile"></i> <span>SMS</span>
                        </a>
                    </li>
                    <li id="notification">
                        <a href="<?php echo URL::to('/notificationlist'); ?>">
                            <i class="fa fa-envelope-o"></i> <span>Notifications</span>
                        </a>
                    </li>
                    <li id="userdevice">
                        <a href="<?php echo URL::to('/userdevicelist'); ?>">
                            <i class="fa fa-mobile"></i> <span>User Devices</span>
                        </a>
                    </li>
                    <!--<li id="setting">
                        <a href="<?php echo URL::to('/addsetting/eUNwaWNSWmJ5dDF0UElTc3dFYWZiQT09'); ?>">
                            <i class="fa fa-cogs"></i> <span>Settings</span>
                        </a>
                    </li>-->


                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>
        <!-- /.right-side  Section Start -->
        <aside class="right-side">
            <!-- Main content -->
            <div class="addcontentloader">
                <div class="contentloader">
                    <div class="contentloaderimage">
                    </div>
                </div>
            </div>
            <section class="content">
                @yield('content')
            </section>
            <!-- /.content -->
            <!-- Footer section-->
            <!--<div class="footer-main">
                Copyright &copy Director, 2014
            </div>-->
            <!-- Footer End -->
        </aside>
        <!-- /.right-side End-->

    </div><!-- ./wrapper -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script>window.baseUrl = "<?php echo URL::to('/')?>"</script>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>

	<script src="<?php echo asset('/assets/js/jquery.min.js');?>"></script>
    <script src="<?php echo asset('/assets/js/jquery-1.11.2.min.js');?>"></script>
    <script src="<?php echo asset('/assets/js/jquery-ui-1.10.3.min.js');?>"></script>
	<script src="<?php echo asset('/assets/js/ie10-viewport-bug-workaround.js');?>"></script>
    <script src="<?php echo asset('/assets/js/bootstrap.min.js');?>"></script>
    <script src="<?php echo asset('/assets/js/bootstrap-datepicker.js');?>"></script>
    <script src="<?php echo asset('/assets/js/plugins/iCheck/icheck.min.js');?>"></script>
    <script src="<?php echo asset('/assets/js/plugins/fullcalendar/fullcalendar.js');?>"></script>


    <script src="<?php echo asset('/assets/js/pagelibraries/jquery.history.js');?>"></script>
	<script src="<?php echo asset('/assets/js/pagelibraries/knockout-2.1.0.js');?>"></script>
	<script src="<?php echo asset('/assets/js/pagelibraries/knockout.mapping.js');?>"></script>
	<script src="<?php echo asset('/assets/js/pagelibraries/knockout.validation.js');?>"></script>
	<script src="<?php echo asset('/assets/js/toaster/toaster.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/jquery.cookie.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/msgbox/msgGrowl.js');?>"></script>
	<script src="<?php echo asset('/assets/js/pagejs/common.js');?>"></script>
    <script src="<?php echo asset('/assets/js/BootstrapDialogJs/bootstrap-dialog.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/moment.js');?>"></script>
    <script src="<?php echo asset('/assets/js/jquery.fileupload/jquery.ui.widget.js');?>"></script>
    <script src="<?php echo asset('/assets/js/jquery.fileupload/jquery.fileupload.js');?>"></script>
    <script src="<?php echo asset('/assets/js/Director/app.js');?>"></script>
    <script type="text/javascript">
        window.confirmdialogtitle = "{{ trans('messages.Confirmdialogtitle')}}";
        window.confirmdialogmessage = "{{ trans('messages.Confirmdialogmessage')}}";
        window.Blankmsg = "{{ trans('messages.Blankmsg')}}";
        window.ConfirmDialogSomethingWrong ="{{ trans('messages.ConfirmDialogSomethingWrong')}}";
        window.Required_Redirect ="{{ trans('messages.Redirect')}}";

        $('input').on('ifChecked', function(event) {
            // var element = $(this).parent().find('input:checkbox:first');
            // element.parent().parent().parent().addClass('highlight');
            $(this).parents('li').addClass("task-done");
            console.log('ok');
        });
        $('input').on('ifUnchecked', function(event) {
            // var element = $(this).parent().find('input:checkbox:first');
            // element.parent().parent().parent().removeClass('highlight');
            $(this).parents('li').removeClass("task-done");
            console.log('not');
        });

    </script>
    <script>
        $('#noti-box').slimScroll({
            height: '400px',
            size: '5px',
            BorderRadius: '5px'
        });

        $('input[type="checkbox"].flat-grey, input[type="radio"].flat-grey').iCheck({
            checkboxClass: 'icheckbox_flat-grey',
            radioClass: 'iradio_flat-grey'
        });
        $(document).ready(function () {
            /* Sets the minimum height of the wrapper div to ensure the footer reaches the bottom */
            function setWrapperMinHeight() {
                $('.sidebar').css('minHeight', window.innerHeight  - $('footer-main').height());
                $('.content').css('minHeight', window.innerHeight  - $('footer-main').height());
            }

            /* Make sure the main div gets resized on ready */
            setWrapperMinHeight();

            /* Make sure the wrapper div gets resized whenever the screen gets resized */
            window.onresize = function() {
                setWrapperMinHeight();
            }
        });
        $(document).ready(function () {
            $(this).scrollTop(0);
            $(window).load(function () {
                $(".contentloader").fadeOut("slow");
                $("#main").show();
                //$('#footer').show();
            });
        });
        //window.history.forward();
    </script>
   @yield('script')
</body>
</html>

