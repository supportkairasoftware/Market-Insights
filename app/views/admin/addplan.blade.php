<?php
use Infrastructure\Common;
use Infrastructure\Constants;
//use \Lang;
//use \Message;
?>
@extends('layouts.sitemaster')
@section('Title')
    <?php isset($PlanModel->PlanID) ? print 'Edit Plan' : print 'Add Plan' ; ?>
@stop
@section('content')
<main id="main" role="main" style="display: none;">
    <?php echo Form::hidden('PlanModel', json_encode($PlanModel),$attributes = array('id'=>'PlanModel'));?>
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <form class="form-horizontal" role="form" method="POST" data-bind="with:$root.PlanModel()">
                    <header class="panel-heading">
                        <ul class="breadcrumb breadcrumb-subpages">
                            <li><a href="<?php echo URL::to('/planlist'); ?>"><i class="fa fa-location-arrow"></i> Plan List</a></li>
                            <li class="active" data-bind="text:$data.PlanID()?'Edit Plan':'Add Plan'"></li>
                        </ul>
                    </header>
                    <div class="panel-body">
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">Plan Name</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" id="inputError" maxlength="100" name="PlanName" data-bind="value:$data.PlanName,decorateErrorElement:PlanName" placeholder="Plan Name">
                            </div>
                        </div>
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">Amount</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" id="inputError" maxlength="11" onkeypress="javascript:return isNumber (event)" name="Amount" data-bind="value:$data.Amount,decorateErrorElement:Amount" placeholder="Amount">
                            </div>
                        </div>
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">Discount (%)</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" id="inputError" maxlength="5" onkeypress="javascript:return isNumber (event)" name="Discount" data-bind="value:$data.Discount" placeholder="Discount (%)">
                            </div>
                        </div>
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">No Of Days</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" id="inputError" maxlength="3" onkeypress="javascript:return isNumber (event)" name="NoOfDays" data-bind="value:$data.NoOfDays,decorateErrorElement:NoOfDays" placeholder="No Of Days">
                            </div>
                        </div>
                        {{--<div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">IsTrial</label>
                            <div class="col-lg-4">
                                <input type="checkbox" name="IsTrial" data-bind="checked:$data.IsTrial">
                            </div>
                        </div>--}}
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button type="submit" class="btn btn-success" data-bind="click:$root.SavePlan">Save</button>
                                <button type="button" class="btn btn-default" data-bind="click:$root.Cancel" >Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div><!--row1-->
</main>
@stop
@section('script')
    <script src="<?php echo asset('/assets/js/pagejs/admin/addplan.js');?>"></script>
    <script type="text/javascript">
        window.Required_PlanName ="{{ trans('messages.PropertyRequired',array('attribute'=>'Plan Name'))}}";
        window.Required_Amount ="{{ trans('messages.PropertyRequired',array('attribute'=>'Amount'))}}";
        window.Required_NoOfDays ="{{ trans('messages.PropertyRequired',array('attribute'=>'No Of Days'))}}";
        $('#addplan').addClass('active');
        $('#plan').addClass('active');
    </script>
@stop
