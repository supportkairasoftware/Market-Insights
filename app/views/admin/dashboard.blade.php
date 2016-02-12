<?php
//use \Lang;
//use \Message;

?>
@extends('layouts.sitemaster')
@section('Title')<?php print 'Dashboard'; ?>
@stop

@section('content')
<style>
.sm-st-info ul {
	list-style: none;
    white-space: nowrap;
    padding: 0;
    height: 58px;
    overflow: hidden;	
    margin : 0 !important
}

.sm-st-info ul li{
	display: none;
}

.sm-st-info ul li.active{
	display: block;
}

.pager-dashboard{
	font-size: 20px;
    width: 14px;
    position: absolute;
    top: 0;
    right: 15px;
    margin-top: 20px;
    height: 60px;	
    margin-right: 20px;
}
span.yearinfo{
	font-size: 12px !important;
	font-weight: normal !important;
}
.prev-year{
	position: absolute;
    top: 0px;
    cursor: pointer;
}

.next-year{
	position: absolute;
    bottom: 0px;
    cursor: pointer;
}
</style>
<main id="main" role="main" style="display: none;">

    <div class="row">
        <div class="col-md-12">
            <!--breadcrumbs start -->
            <ul class="breadcrumb">
                <li class="active" id="clr-black">Dashboard</li>
            </ul>
            <!--breadcrumbs end -->
        </div>
    </div>
    <?php
    	$remainbalance = file_get_contents('http://smsc.a4add.com/api/smscredit.aspx?username=naresh123&password=naresh123');
    	$remainbalancemvaayo = file_get_contents('http://api.mvaayoo.com/mvaayooapi/APIUtil?user=girishkhemani09@gmail.com:giri1991&type=0 ');
    	$remainbalancemvaayo = explode(',Credit balance is',$remainbalancemvaayo)[1];
    ?>
    <div class="row">
        <div class="col-md-12">
            <!--breadcrumbs start -->
            <!--<div class="alert <?php echo $remainbalance>1000 ?'alert-info':'alert-danger'; ?>" id="clr-black">Your SMS Balance (a4add) : <?php echo $remainbalance ; ?></div>-->
	        <div class="alert <?php echo $remainbalance>1000 ?'alert-info':'alert-danger'; ?>" id="clr-black">Your SMS Balance (mvaayoo) : <?php echo $remainbalancemvaayo ; ?></div>
            <!--breadcrumbs end -->
        </div>
    </div>
    <div class="row">
	    <div class="col-md-3">
	        <div class="sm-st clearfix">
                <a href="<?php echo URL::to('/userlist'); ?>"><span class="sm-st-icon st-red"><i class="fa fa-users"></i></span></a>
	            <div class="sm-st-info">
	                <span data-bind="text:$root.TotalUsers"></span>
	                Total Users
	            </div>
	        </div>
	    </div>
	    <div class="col-md-3">
	        <div class="sm-st clearfix">
                <a href="<?php echo URL::to('/userpaymentlist?plan=2%20Months'); ?>"><span class="sm-st-icon st-green"><i class="fa fa-check-circle"></i></span></a>
	            <div class="sm-st-info">
	                <span data-bind="text:TotalPaidUsers"></span>
	                Paid Users
	            </div>
	        </div>
	    </div>
	    <div class="col-md-3">
	        <div class="sm-st clearfix">
                <a href="<?php echo URL::to('/userpaymentlist?plan=Trial'); ?>"><span class="sm-st-icon st-violet"><i class="fa fa-clock-o"></i></span></a>
	            <div class="sm-st-info">
	                <span data-bind="text:TotalTrialUsers"></span>
	                Trial Users
	            </div>
	        </div>
	    </div>
	    <div class="col-md-3">
	        <div class="sm-st clearfix">
                <a href="<?php echo URL::to('/userpaymentlist'); ?>"> <span class="sm-st-icon st-blue"><i class="fa fa-inr"></i></span></a>
	            <div class="sm-st-info">
	                <!--<span data-bind="text:TotalEarning"></span>-->
	                <script>
	                	function goPrev (elem){
	                		$(elem).closest('div.sm-st-info').find('ul li.active').removeClass('active').prev('li').addClass('active');
	                		if($('div.sm-st-info').find('ul li.active').index()==0){
								$('.prev-year').hide();
							}
								
							$('.next-year').show();
						}
						
						function goNext(elem){
							$(elem).closest('div.sm-st-info').find('ul li.active').removeClass('active').next('li').addClass('active');
							if($('div.sm-st-info').find('ul li.active').index()==($('div.sm-st-info').find('ul li').length-1)){
								$('.next-year').hide();
							}
							$('.prev-year').show();
						}
	                </script>
	                <ul data-bind="foreach:$root.TotalEarning()">
					  <li data-bind="css:{'active':$index()==$root.TotalEarning().length-1}"><span data-bind="text:$data.Amount">0</span><span class="yearinfo" data-bind="text:$data.Year+' Earning'"></span></li>
					</ul>
	                <div class="pager-dashboard">
	                	<a class="prev-year" data-bind="visible:$root.TotalEarning() && $root.TotalEarning().length>1" onclick="goPrev(this)" ><i class="fa fa-angle-up"></i></a>
	                	<a class="next-year" style="display: none" onclick="goNext(this)" ><i class="fa fa-angle-down"></i></a>
	                </div>
				</div>
	        </div>
	    </div>
    </div>
    <div class="row">
        <div class="col-md-6">
        	<div class="sm-st clearfix">
		        <h4 class="breadcrumb dashboardtable-header">Last 10 Registered User</h4>
		        <div class="table-responsive">
				  <table class="table table-hover">
				    <!-- On rows -->
					<thead data-bind="visible:$root.LastTenUser().length > 0">
						<tr>
							<th>First Name</th>
							<th>Last Name</th>
							<th>City</th>
							<th>Mobile</th>
						</tr>
					</thead>

					<tbody data-bind="foreach:$root.LastTenUser(),visible:$root.LastTenUser().length > 0">

                        <tr  data-bind="click:$root.UserEditMode" class="cursor">
                          <td data-bind="text:FirstName"></td>
						  <td data-bind="text:LastName"></td>
						  <td data-bind="text:City"></td>
						  <td data-bind="text:Mobile"></td>
						</tr>
					</tbody>
				  </table>
				</div>
			</div>
	    </div>
	    <div class="col-md-6">
	        <div class="sm-st clearfix">
		        <h4 class="breadcrumb dashboardtable-header">Last 10 Payment Details</h4>
		        <div class="table-responsive">
				  <table class="table table-hover">
				    <!-- On rows -->
					<thead data-bind="visible:$root.LastTenPayment().length > 0">
						<tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>City</th>
                            <th>Mobile</th>
                            <th>Amount</th>
						</tr>
					</thead>
					<tbody data-bind="foreach:$root.LastTenPayment(),visible:$root.LastTenPayment().length > 0">
						<tr data-bind="click:$root.PaymentHisotyPage" class="cursor">
                            <td data-bind="text:FirstName"></td>
                            <td data-bind="text:LastName"></td>
                            <td data-bind="text:City"></td>
                            <td data-bind="text:Mobile"></td>
                            <td data-bind="text:SubscriptionAmount"></td>
						</tr>
					</tbody>
				  </table>
				</div>
			</div>
	    </div>
    </div>

    <!-- Main row -->

</main>
@stop
@section('script')
    <script src="<?php echo asset('/assets/js/pagejs/admin/dashboard.js');?>"></script>
    <script type="text/javascript">
        $('#dashboard').addClass('active');
    </script>
@stop