<?php
use Infrastructure\Common;
use Infrastructure\Constants;
//use \Lang;
//use \Message;
?>
@extends('layouts.sitemaster')
@section('Title','Add Payment')
@stop
@section('content')
<main id="main" role="main" style="display: none;">
    <?php echo Form::hidden('PaymentModel', json_encode($PaymentModel),$attributes = array('id'=>'PaymentModel'));?>
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <form class="form-horizontal" role="form" method="POST" data-bind="with:$root.PaymentModel()">
                    <header class="panel-heading">
                        <ul class="breadcrumb breadcrumb-subpages">
                            <li><a href="<?php echo URL::to('/userpaymentlist'); ?>"><i class="fa fa-home"></i> Payment History</a></li>
                            <li class="active">Add Payment</li>
                        </ul>
                    </header>
                    <div class="panel-body">

                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">Select User</label>
                            <div class="col-lg-4">
                                {{--<select name="UserID"  data-bind="value:UserID,options:$root.UserListArray(),optionsCaption:'Select User',optionsValue:'UserID',optionsText:'DisplayName',decorateErrorElement:UserID" class="form-control"></select>--}}
                                <input id="demo5" type="text" class="col-md-12 form-control" placeholder="Search User..." autocomplete="off" />
                                <span class="validationMessage UserID" style="display:none">User is required</span>
                            </div>
                        </div>
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">Select Plan</label>
                            <div class="col-lg-4">
                                <select name="UserID"  data-bind="value:PlanID,options:$root.PlanListArray(),optionsCaption:'Select Plan',optionsValue:'PlanID',optionsText:'PlanName',decorateErrorElement:PlanID" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">Select StartDate</label>
                            <div class="col-lg-4">
                                <input  id="date" name="startDate"  type="text" class="form-control" placeholder="Start Date" data-bind="datepicker:StartDate,decorateErrorElement:StartDate">
                            </div>
                        </div>
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">Received Subscription Amount</label>
                            <div class="col-lg-4">
                                <input  id="subscription" onkeypress="javascript:return isNumber (event)"  maxlength="15" name="SubscriptionAmount"  type="text" class="form-control" placeholder="Subscription Amount" data-bind="value:SubscriptionAmount,decorateErrorElement:SubscriptionAmount">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button type="submit" class="btn btn-success" data-bind="click:$root.Save">Save</button>
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
    <script src="<?php echo asset('/assets/js/pagejs/admin/addpayment.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/bootstrap-typeahead.js');?>"></script>
    <script type="text/javascript">
        //window.Required_UserName ="{{ trans('messages.PropertyRequired',array('attribute'=>'User Name'))}}";
        window.Required_PlanID ="{{ trans('messages.PropertyRequired',array('attribute'=>'Select Plan'))}}";
        window.Required_StartDate ="{{ trans('messages.PropertyRequired',array('attribute'=>'Start Date'))}}";
        window.Required_SubscriptionAmount ="{{ trans('messages.PropertyRequired',array('attribute'=>'Subscription Amount'))}}";
        $('#addpayment').addClass('active');
        $('#payment').addClass('active');
    </script>
    <script type="text/javascript">
        $(function() {
            $('#demo5').typeahead({
                ajax: {
                    url: baseUrl+'/userlisturl',
                    method: 'post',
                },
                onSelect:function(item){
                    window.DM.PaymentModel().UserID(item.value);
                },
                scrollBar:true,
                hint: true,
                highlight: true,
                minLength: 1
            });
        });
    </script>
@stop
