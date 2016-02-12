@extends('layouts.loginmaster')
@section('Title', 'Login')

@section('content')
	
	<div id="loginbox" style="margin-top: 50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-info">
               

                <div style="padding: 25px" class="panel-body">
                 
					@if($IsVerified)
					<span>Account verification process is successful. Please login <a href="<?php echo URL::to('/').'/login'; ?>">here</a></span>
					@else
					<span>Account verification failed. Please contact administration for more details.</span>
					@endif

                  
                </div>
            </div>
        </div>

       


@stop


