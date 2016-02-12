@extends('layouts.loginmaster')
@section('Title', 'Login')

@section('content')
    <div class="row">
        <div class="col-lg-5" id="inner">
            <section class="panel">
                <header class="panel-heading">
                    Sign In
                </header>
                <input type="hidden" value="<?php echo Session::get('SessionExpired'); ?>" id="SessionExpired">
                <div class="panel-body">
                    <form class="form-horizontal" role="form" data-bind="with:$data.Usersobj()" method="POST">
                        <div class="form-group input-col">
                            <label for="" class="col-lg-3 col-sm-3 control-label text-align-left">Email</label>
                            <div class="col-lg-9">
                                <input id="inputEmail1" type="email" class="form-control" name="Email" data-bind="value:$data.Email,decorateErrorElement:Email" maxlength="50" placeholder="Email">
                               <!-- <p class="help-block">Your Mobile Number.</p> -->
                            </div>
                        </div>
                        <div class="form-group input-col">
                            <label for="inputPassword1" class="col-lg-3 col-sm-3 control-label text-align-left">Password</label>
                            <div class="col-lg-9">
                                <input type="password" class="form-control" id="Password" maxlength="20" name="Password" data-bind="value:$data.Password,decorateErrorElement:Password" placeholder="Password">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-3 col-lg-9">
                                <button type="submit" class="btn btn-info" data-bind="click:$root.AuthenticateUser">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@stop

@section('script')
<script src="<?php echo asset('/assets/js/pagejs/security/login.js');?>"></script>

 <script type="text/javascript">
	window.Required_Email ="{{ trans('messages.PropertyRequired',array('attribute'=>'Email'))}}";
    window.Required_Password ="{{ trans('messages.PropertyRequired',array('attribute'=>'Password'))}}";
</script>
@stop
