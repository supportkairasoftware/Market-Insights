@extends('layouts.sitemaster')
@section('Title', 'Reset Password')

@section('content')

             <div id="signupbox" style="margin-top: 20px;" class="mainbox col-md-6 col-md-offset-3 col-sm-12 col-sm-offset-2">
            <div class="panel panel-info" style="border: medium none #EF5924; border-radius: 4px !important;">
        
               
                 <div style="padding-top: 15px; border: 1px solid #EF5924;" class="panel-body">

                    <div class="row">
                        <div class="col-md-12">
                            <h3 style="margin-top: 0; color: #263849">Reset Password</h3>
                            <p>Reset Your Account Password: </p>
                        </div>
                    </div>

                    <div style="display: none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                    <form id="resetform" class="form-horizontal" role="form" data-bind="with:ResetModel">

						<div id="signupalert" style="display: none" class="alert alert-danger">
                            <p>Error:</p>
                            <span></span>
                        </div>

                        <div class="form-group">
                            <label for="firstname" class="col-md-4 control-label">Email</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" maxlength="50"  name="Email" placeholder="Email" data-bind="value:Email,decorateErrorElement:Email">
                            </div>
                        </div>
						<div class="form-group">
                            <label for="lastname" class="col-md-4 control-label">Old Password</label>
                            <div class="col-md-8">
                                <input type="password" class="form-control" maxlength="20"  name="OldPassword" placeholder="Old Password" data-bind="value:OldPassword,decorateErrorElement:OldPassword">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-md-4 control-label">New Password</label>
                            <div class="col-md-8">
                                <input type="password" class="form-control" maxlength="20"  name="Password" placeholder="New Password" data-bind="value:Password,decorateErrorElement:Password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-md-4 control-label">Confirm Password</label>
                            <div class="col-md-8">
                                <input type="password" class="form-control" maxlength="20"  name="ConfirmPasssword" placeholder="Confirm Password" data-bind="value:ConfirmPassword,decorateErrorElement:ConfirmPassword">
                            </div>
                        </div>
                        <div class="form-group">
                            <!-- Button -->
                            <div class="col-md-offset-4 col-md-8">
                                <button id="btn-signup" type="button" class="btn btn-info" data-bind="click: $root.ResetPassword">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@stop


@section('script')
<script src="<?php echo asset('/assets/js/pagejs/security/resetpassword.js');?>"></script>

<script type="text/javascript">

	window.Required_Email ="{{ trans('messages.PropertyRequired',array('attribute'=>'Email'))}}";
	window.Required_Password ="{{ trans('messages.PropertyRequired',array('attribute'=>'Password'))}}";
	window.Required_OldPassword ="{{ trans('messages.PropertyRequired',array('attribute'=>'Old Password'))}}";
	window.Required_PasswordDoesNotMatch ="{{ trans('messages.PasswordDoesNotMatch')}}";
    
   </script>
@stop
