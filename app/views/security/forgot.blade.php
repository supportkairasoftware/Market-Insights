@extends('layouts.loginmaster')
@section('Title', 'Forgot Password')

@section('content')
	<?php echo Form::hidden('ForgotModel', json_encode($ForgotModel),$attributes = array('id'=>'ForgotModel')); ?>
	<div id="signupbox" style="margin-top: 50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">Reset Password</div>
                    <!--<div style="float: right; font-size: 85%; position: relative; top: -10px"><a id="signinlink" href="#" onclick="$('#signupbox').hide(); $('#loginbox').show()">Sign In</a></div>-->
                </div>
                <div class="panel-body">
                    <form id="signupform" class="form-horizontal" role="form" data-bind="with:ForgotModel">

                        <div id="signupalert" style="display: none" class="alert alert-danger">
                            <p>Error:</p>
                            <span></span>
                        </div>

                        <div class="form-group">
                            <label for="firstname" class="col-md-4 control-label">Email</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="email" placeholder="Email" data-bind="value:Email,decorateErrorElement:Email" maxlength="50" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-md-4 control-label">New Password</label>
                            <div class="col-md-8">
                                <input type="password" class="form-control" name="password" placeholder="New Password"   maxlength="20" data-bind="value:Password,decorateErrorElement:Password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-md-4 control-label">Confirm Password</label>
                            <div class="col-md-8">
                                <input type="password" class="form-control" name="confirmPasssword" placeholder="Confirm Password"  maxlength="20" data-bind="value:ConfirmPassword,decorateErrorElement:ConfirmPassword">
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
 <script src="<?php echo asset('/assets/js/pagejs/security/forgot.js');?>"></script>


 <script type="text/javascript">
	window.Required_Email ="{{ trans('messages.PropertyRequired',array('attribute'=>'Email'))}}";
	window.Required_EmailModify ="{{ trans('messages.EmailModify')}}";
	window.Required_Password ="{{ trans('messages.PropertyRequired',array('attribute'=>'Password'))}}";
	window.Required_PasswordDoesNotMatch ="{{ trans('messages.PasswordDoesNotMatch')}}";

    
   </script>
@stop
