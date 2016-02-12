<?php
//use \Lang;
//use \Message;
use Infrastructure\Constants;
?>
@extends('layouts.sitemaster')
@section('Title')<?php print 'Payment History'; ?>
@stop
@section('CSS')
    <link href="<?php echo asset('/assets/css/bootstrap-datepicker.css');?>" rel="stylesheet" type='text/css'/>
@stop
@section('content')
    <main id="main" role="main" class="displayhide">
        <?php echo Form::hidden('PaymentModel', json_encode($PaymentModel),$attributes = array('id'=>'PaymentModel')); ?>
		<?php echo Form::hidden('Plan', @$_REQUEST['plan'],$attributes = array('id'=>'Plan')); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <header class="panel-heading">
                        <ul class="breadcrumb breadcrumb-subpages">
                            <li><a href="<?php echo URL::to('/dashboard'); ?>"><i class="fa fa-home"></i> Dashboard</a></li>
                            <li class="active">Payment History</li>
                        </ul>
                    </header>
                    <div class="panel-body">
                        <form class="form-inline" role="form" data-bind="with:$root.SearchModel">
                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                <input type="text" tabindex="1" class="form-control" id="searchuser" data-bind="value:$data.textKeyWord" placeholder="Search User ">
                            </div>
                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                {{--<select tabindex="3" class="form-control" style="width:100%"  data-bind="options: $root.RefNoListArray, value:ReferenceNo, optionsValue: 'ReferenceNo', optionsText: 'ReferenceNo', optionsCaption:'Select Reference No.'" name="ReferenceNo"></select>--}}
                                <input type="text" tabindex="2" class="form-control" id="searchuser" data-bind="value:$data.ReferenceNo" placeholder="Search ReferenceNo">
                            </div>
                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                <select  tabindex="3" class="form-control" style="width:100%"  data-bind="options: $root.PlanListArray, value:PlanName, optionsValue: 'PlanName', optionsText: 'PlanName', optionsCaption:'Select Plan Name'" name="SegmentID"></select>
                            </div>
                            <div class="form-group col-md-4 col-sm-4 col-xs-12 padd-top5">
                                <select class="form-control" tabindex="4" style="width:100%" data-bind="options: $root.ActiveArray, optionsValue: 'IsActive', optionsText: 'IsActive',value: IsActive , optionsCaption:'Select Status'" name="IsActive"></select>
                            </div>
                            <button type="reset"  tabindex="6" value="Clear Filters" class="btn btn-warning btn-clear"  data-bind="click:$root.ClearSearch">Clear</button>
                            <button type="submit" tabindex="5" class="btn btn-info btn-search" id="filterBtn" class="btn btn-default" data-bind="click:$root.ApplyFilter">Search</button>
                        </form>
                    </div>
                    <div class="panel-body table-responsive ">
                        <table class="table table-bordered table-responsive-margin table-hover">
                            <thead data-bind="visible:$root.UserPaymentListArray().length > 0">
                            <tr data-bind="with:pager">
                                <th style="width: 50px" class="text-align-center">Sr. No</th>
                                <th style="" class="text-align-center">User Name</th>
                                <th style="width: 100px" class="text-align-center">Mobile</th>
                                <th style="" class="text-align-center">Email</th>
                                <th style="" class="text-align-center">Address</th>
                                <th style="" class="text-align-center">Plan</th>
                                <th style="width: 70px" class="text-align-center">Amount</th>
                                <th style="width: 70px" class="text-align-center">Sub. Amount</th>
                                <th style="" class="text-align-center">Ref No.</th>
                                <th style="width: 78px" class="text-align-center">Start Date</th>
                                <!--<th style="width: 78px" class="text-align-center">End Date</th>-->
                                <th data-bind="with: new sortModel()" class="sort">
                                            <span class="sortLink" data-bind="click:function(){$data.sort('EndDate');}">
                                                <span class="sortLink cursor">End Date
                                                    <span class="fa" data-bind="visible:$parent.currentSort()==$data, css:{'fa-long-arrow-down':$data.isDesending(), 'fa-long-arrow-up': !$data.isDesending()}" class="fa fa-long-arrow-up"></span>
                                                </span>
                                            </span>
                                </th>
                                <th style="width: 70px" class="text-align-center">Active</th>
                                <th style="width: 70px" class="text-align-center">Delete</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach:$root.UserPaymentListArray(),visible:$root.UserPaymentListArray().length > 0">
                            <tr>
                                <td class="text-align-center" data-bind="text:$data.Index"></td>
                                <td class="break-word" data-bind="text:$data.DisplayName"></td>
                                <td data-bind="text:$data.Mobile"></td>
                                <td class="break-word" data-bind="text:$data.Email"></td>
                                <td  class="break-word" data-bind="text:$data.Address"></td>
                                <td data-bind="text:$data.PlanName"></td>
                                <td data-bind="text:$data.Amount"></td>
                                <td data-bind="text:$data.SubscriptionAmount"></td>
                                <td class="break-word" data-bind="text:$data.ReferenceNo"></td>
                                <td data-bind="text:$data.StartDate"></td>
                                <td data-bind="text:$data.EndDate"></td>
                                <td data-bind="text:$data.IsActive"></td>
                                <td><div class="hidden-phone text-align-center">
                                    <a class="btn btn-default btn-xs" title="Delete Payment" data-bind="click:$root.DeleteUserPayment"><i class="fa fa-times fa-delete-color"></i></a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="displayhide" id="nodata">
                            <div class="col-md-12 alert alert-warning ">
                                <i class="icon-large icon-warning-sign"></i>&nbsp;&nbsp;&nbsp;{{ trans('messages.NoRecord')}}
                            </div>
                        </div>
                        <div class="table-foot">
                            <div class="" data-bind="with:pager, visible: $data.UserPaymentListArray().length > 0">
                                <ul class="pagination pagination-sm no-margin pull-right" data-bind="if:allPages().length > 0">
                                    <li data-bind="css:{'disabled':currentPage()== 1} "><a title="Previous" data-bind="click: previousPage"><span class="arrow"></span> Prev</a></li>
                                    <!-- ko foreach: $data.pagesToShow() -->
                                    <li class="active" data-bind="css: { active: $data.pageNumber == $parent.currentPage() }">
                                        <a data-bind="attr: {title:$data.pageNumber},text: $data.pageNumber, click: $parent.gotoPage,attr:{disabled:$data.pageNumber === $parent.currentPage()}"></a>
                                    </li>
                                    <!-- /ko -->
                                    <li data-bind="css:{'disabled':currentPage() == allPages().length}">
                                        <a title="Next" data-bind="click: nextPage">Next <span class="arrow"></span></a></li>

                                </ul>
                            </div>
                        </div>
                    </div><!-- /.panel-body -->
                </div><!-- /.panel -->

            </div>
        </div>
    </main>
@stop
@section('script')
    <script>
        var AllRecords  = '<?php echo Constants::$AllRecords;?>';
        function selectScript(val) {
            $("#searchScript").val(val);
            $("#suggesstion-box").hide();
            DM.SearchModel().Script(val);
        }
    </script>
    <script type="text/javascript">
        $('#paymenthistory').addClass('active');
        $('#payment').addClass('active');
    </script>

    <script src="<?php echo asset('/assets/js/pagejs/pager.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/admin/paymentlist.js');?>"></script>

@stop