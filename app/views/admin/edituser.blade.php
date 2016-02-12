<?php
use Infrastructure\Common;
use Infrastructure\Constants;
//use \Lang;
//use \Message;
$flage=0;
if($UserModel->UserID == Auth::user()->UserID){
    $flage =1;
}else{
    $flage=0;
}
?>
@extends('layouts.sitemaster')
@section('Title')
    <?php if(isset($UserModel->UserID)){if($flage){print 'Edit Profile';}else{ print 'Edit User';}} ?>
@stop
<script type="text/javascript">
    var imagebase='<?php echo asset('assets/profilepic'); ?>/';
</script>
@section('content')
    <main id="main" role="main">
        <?php echo Form::hidden('UserModel', json_encode($UserModel),$attributes = array('id'=>'UserModel'));?>
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <form class="form-horizontal" role="form" method="POST" data-bind="with:$root.UserModel()">
                        <header class="panel-heading">
                            <ul class="breadcrumb breadcrumb-subpages">
                                <li><i class="fa fa-users">&nbsp;</i><?php if($flage){echo " My Profile";}else{echo "Edit User";}?></li>
                            </ul>
                        </header>
                        <div class="panel-body">
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">First Name</label>
                                <div class="col-lg-4">
                                    <input type="text" tabindex="1" class="form-control" id="inputError" maxlength="50" name="FirstName" data-bind="value:$data.FirstName,decorateErrorElement:FirstName" placeholder="First Name">
                                </div>
                            </div>
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Last Name</label>
                                <div class="col-lg-4">
                                    <input type="text" tabindex="2" class="form-control" id="inputError" maxlength="50" name="LastName" data-bind="value:$data.LastName,decorateErrorElement:LastName" placeholder="Last Name">
                                </div>
                            </div>
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Mobile</label>
                                <div class="col-lg-4">
                                    <input type="text" tabindex="3" class="form-control" id=""  maxlength="10" name="Mobile" onkeypress="javascript:return isNumber (event)" data-bind="value:$data.Mobile,decorateErrorElement:Mobile" placeholder="Mobile  ">
                                </div>
                            </div>
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Email</label>
                                <div class="col-lg-4">
                                    <input type="email" tabindex="4" class="form-control" id="inputError"   maxlength="200" name="Email" data-bind="value:$data.Email,decorateErrorElement:Email" placeholder="Email">
                                </div>
                            </div>

                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Password</label>
                                <div class="col-lg-2">
                                    <input type="password" tabindex="5" value="1234567890" class="form-control" id="inputError"  maxlength="10" name="OldPassword"   placeholder="">
                                </div>
                                <div class="col-lg-2">
                                    <input type="checkbox" tabindex="6" data-bind="checked: ChangePassword"> Change Password
                                </div>
                            </div>
                            <div  data-bind="if: ChangePassword">
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">New Password</label>
                                <div class="col-lg-4">
                                    <input type="password" tabindex="7" class="form-control" id="inputError" maxlength="50" name="TempPassword" data-bind="value:$data.TempPassword,decorateErrorElement:TempPassword" placeholder="New Password" value="">
                                </div>
                            </div>
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Confirm Password</label>
                                <div class="col-lg-4">
                                    <input type="password" tabindex="8" class="form-control" id="inputError" maxlength="50" name="ConfirmPassword" data-bind="value:$data.ConfirmPassword,decorateErrorElement:ConfirmPassword" placeholder="Confirm Password">
                                </div>
                            </div>
                            </div>
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">City</label>
                                <div class="col-lg-4">
                                    <input type="text" tabindex="9" class="form-control" id="inputError" maxlength="50" name="City" data-bind="value:$data.City,decorateErrorElement:City" placeholder="City">
                                </div>
                            </div>
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">State</label>
                                <div class="col-lg-4">
                                    <input type="text" tabindex="10" class="form-control" id="inputError" maxlength="50" name="State" data-bind="value:$data.State,decorateErrorElement:State" placeholder="State">
                                </div>
                            </div>
                            <!-- ko if:$data.UserID()>1 -->
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Update Role</label>
                                <div class="col-lg-4">
                                    <select class="form-control" tabindex="11"  tabindex="4" data-bind="options: $root.RoleListArray, optionsValue: 'RoleID', optionsText: 'RoleName',value: RoleID , optionsCaption:'Select Role Name',decorateErrorElement: RoleID" name="RoleID"></select>
                                </div>
                            </div>
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">IsVerified</label>
                                <div class="col-lg-4">
                                    <input type="checkbox" tabindex="10" class="" id="inputError" name="IsVerified" data-bind="checked:$data.IsVerified" placeholder="Is Verified">
                                </div>
                            </div>
                            <!-- /ko -->
                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <button type="submit" tabindex="12" class="btn btn-success" data-bind="click:$root.SaveUser">Save</button>
                                    <button type="button" tabindex="13" class="btn btn-default" data-bind="click:$root.cancel">Cancel</button>
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
    <script src="<?php echo asset('/assets/js/pagejs/admin/edituser.js');?>"></script>
    <script type="text/javascript">
        window.Required_FirstName ="{{ trans('messages.PropertyRequired',array('attribute'=>'First Name'))}}";
        window.Required_RoleID ="{{ trans('messages.PropertyRequired',array('attribute'=>'Select Role'))}}";
        window.Required_LastName ="{{ trans('messages.PropertyRequired',array('attribute'=>'Last Name'))}}";
        window.Required_Mobile ="{{ trans('messages.PropertyRequired',array('attribute'=>'Mobile'))}}";
        window.Required_Email ="{{ trans('messages.PropertyRequired',array('attribute'=>'Email'))}}";
        window.Required_City ="{{ trans('messages.PropertyRequired',array('attribute'=>'City'))}}";
        window.Required_State ="{{ trans('messages.PropertyRequired',array('attribute'=>'State'))}}";
        window.Required_Password ="{{ trans('messages.PropertyRequired',array('attribute'=>'New Password'))}}";
        window.Required_ConfirmPassword ="{{ trans('messages.PropertyRequired',array('attribute'=>'Confirm Password'))}}";
        window.Required_PasswordDoesNotMatch ="{{ trans('messages.PasswordDoesNotMatch')}}";
    </script>
@stop
