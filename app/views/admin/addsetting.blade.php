<?php
use Infrastructure\Common;
use Infrastructure\Constants;
//use \Lang;
//use \Message;
?>
@extends('layouts.sitemaster')
@section('Title','Settings')
@stop
@section('content')
<main id="main" role="main" style="display: none;">
    <?php echo Form::hidden('SettingModel', json_encode($SettingModel),$attributes = array('id'=>'SettingModel'));?>
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <form class="form-horizontal" role="form" method="POST" data-bind="with:$root.SettingModel()">
                    <header class="panel-heading">
                        <ul class="breadcrumb breadcrumb-subpages">
                            <li><a href=""><i class="fa fa-cogs"></i>&nbsp;&nbsp; Settings</a></li>
                        </ul>
                    </header>
                    <div class="panel-body">
                        <h4 class="padding-40p">SMS Details</h4><br>
                        <!--<div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">SMS Url</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" id="inputError"  name="SMSUrl" data-bind="value:$data.SMSUrl" placeholder="SMS Url">
                            </div>
                        </div>-->
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">SMS UserName</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" id="inputError"  name="SMSUserName" data-bind="value:$data.SMSUserName" placeholder="SMS UserName">
                            </div>
                        </div>
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">SMS Password</label>
                            <div class="col-lg-4">
                                <input type="password" class="form-control" id="inputError"  name="SMSPassword" data-bind="value:$data.SMSPassword">
                            </div>
                        </div>
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">Sender ID</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" id="inputError"  name="SenderID" data-bind="value:$data.SenderID"  style="text-transform:uppercase">
                            </div>
                        </div>
                        <!--<div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">SMS Templates</label>
                            <div class="col-lg-4">
                                <textarea class="form-control" id="inputError"  name="SMSPassword" data-bind="value:$data.SMSTemplates" placeholder=""></textarea>
                            </div>
                        </div>-->
                        <br/>
                        <h4 class="padding-40p">Bank Details</h4><br>

                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">Account Name</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" id="inputError"  name="AccountName" data-bind="value:$data.AccountName" placeholder="Bank Account Name">
                            </div>
                        </div>
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">Account Number</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" id="inputError" maxlength="15" onkeypress="javascript:return isNumber (event)" name="AccountNumber" data-bind="value:$data.AccountNumber" placeholder="Account Number">
                            </div>
                        </div>
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">Branch Name</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" id="inputError"  name="BranchName" data-bind="value:$data.BranchName" placeholder="Branch Name">
                            </div>
                        </div>
                        <div class="form-group input-col">
                            <label class="col-sm-2 control-label col-lg-2" for="inputError">IFSC Code</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" id="inputError"  name="IFSCCode" data-bind="value:$data.IFSCCode" placeholder="IFSCCode">
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
    <script src="<?php echo asset('/assets/js/pagejs/admin/addsetting.js');?>"></script>
    <script type="text/javascript">
        $('#setting').addClass('active');
    </script>
@stop
