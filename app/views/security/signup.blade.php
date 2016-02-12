@extends('layouts.loginmaster')
@section('Title', 'Signup')

@section('content')

<?php echo Form::hidden('UserModel', json_encode($UserModel),$attributes = array('id'=>'UserModel')); ?>

<div id="signupbox" style="margin-top: 20px" class="mainbox col-md-8 col-md-offset-2 col-sm-12 col-sm-offset-2">
	
	<div class="panel panel-info" style="border: medium none #EF5924; border-radius: 4px !important;">
		<div style="padding-top: 15px; border: 1px solid #EF5924;" class="panel-body">
			
			<div class="row">
				<div class="col-md-12">
					<h3 style="margin-top: 0; color: #263849">Create Your Account</h3>
					<p> Sign up for your Mula Express Account  </p>
				</div>
			</div>
			
			<div style="display: none" id="login-alert" class="alert alert-danger col-sm-12"></div>
			<div class="form-group">
				<label for="firstname" class="col-md-3 control-label">Acoount Type</label>
				<div class="col-md-9">
					<select class="form-control" name="usertype" id="usertype"  data-bind="options: $root.usertypes,value:$root.UserModel().UserTypeID, event:{change:$root.usertypecheck}">                             </select>
					
				</div>
			</div>

            <form id="signupform" class="form-horizontal" role="form" data-bind="with:UserModel">
				
				<div id="signupalert" style="display: none" class="alert alert-danger">
					<p>Error:</p>
					<span></span>
				</div>
                <div class="companydetails" id="companydetails" style="display:none;">
                    <label style="margin-top:20px;">Company Details</label>
                    <div style="border:1px solid #CCC; padding: 20px">
                        <div class="form-group">
                            <label for="CompanyName" class="col-md-3 control-label">Company Name</label>
                            <div class="col-md-9"><input type="text" class="form-control" id="CompanyName" name="CompanyName" placeholder="Company Name" data-bind="value:CompanyName,decorateErrorElement:CompanyName"></div>
						</div>
                        <div class="form-group">
                            <label for="CompanyAddress" class="col-md-3 control-label">Company Address</label>
                            <div class="col-md-9">
							<input type="text" class="form-control" id="CompanyAddress" name="CompanyAddress" placeholder="Company Address" data-bind="value:CompanyAddress,decorateErrorElement:CompanyAddress"></div>
						</div>
                        <div class="form-group">
                            <label for="PhoneNo" class="col-md-3 control-label">Phone No</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" maxlength="50" name="PhoneNo" id="PhoneNo" placeholder="Phone no" data-bind="value:PhoneNo,decorateErrorElement:PhoneNo">
							</div>
						</div>
                        <div class="form-group">
                            <label for="EIN" class="col-md-3 control-label">EIN</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="EIN" placeholder="EIN" name="EIN" data-bind="value:EIN,decorateErrorElement:EIN">
							</div>
						</div>
                        <div class="form-group">
                            <label for="icode" class="col-md-3 control-label">Company State</label>
                            <div class="col-md-9">
                                <select data-bind="options: $root.CompanyStates, optionsValue: 'StateCode', optionsText: 'StateName',value: CompanyStateCode,optionsCaption:'Select',event: { change: $root.CompanyStateSelectionChanged },decorateErrorElement:CompanyStateCode" name="CompanyStateCode" class="form-control seatdropdown"></select>
							</div>
						</div>
                        <div class="form-group">
                            <label for="icode" class="col-md-3 control-label">Company City</label>
                            <div class="col-md-9">
								
                                <select data-bind="options: $root.CompanyCities, optionsValue: 'CityCode', optionsText: 'CityName',value: CompanyCityCode, optionsCaption:'Select',decorateErrorElement:CompanyCityCode" name="CompanyCityCode"  class="form-control seatdropdown"></select>
								
							</div>
						</div>
					</div>
				</div>
                <label style="margin-top:20px;">User Details</label>
                <div >
					<div style="border:1px solid #CCC; padding: 20px">
						<div id="profileimage">
							<div class="form-group">
								<label for="CompanyLogo" class="col-md-3 control-label">Profile Image</label>
								<div class="col-md-9">
									<input  type="file"  name="files" title="Upload file"  class="dz-upload btn btn-info floatLeft"  data-bind="ApplyFileUpload:$root.UploadFileUrl,onSuccessCallback:$root.FileUploadCompletedCallback,onErrorCallback:$root.FileUploadErrorCallback,acceptedFiles:'image',paraMeter:$root.uploadmultiple" tabindex="110" />
									<div class="fileInfo" data-bind="visible:$data.FileDetails().length>0, foreach:$data.FileDetails">
										<div class="row-block">
											<span data-bind="visible:FileGuidName()">File Name: </span>
											<a  tabindex = "111" target="_blank" data-bind=""><span data-bind="text:FileName"></span></a>
											<a  href="javascript:void(0)" tabindex = "112" class="pointer removefile" data-bind="visible:FileGuidName(),click:$root.RemoveUploadedFileCallback">
												<i class="text text-danger font-small glyphicon glyphicon-minus-sign"></i>Remove File
											</a>
											<span class="right error-color uploadMessage" data-bind="visible:!FileGuidName()">*Please upload a file</span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="firstname" class="col-md-3 control-label">First Name</label>
							<div class="col-md-9">
								<input type="text" class="form-control" maxlength="50"  name="FirstName" placeholder="First Name" data-bind="value:FirstName,decorateErrorElement:FirstName">
							</div>
						</div>
						<div class="form-group">
							<label for="lastname" class="col-md-3 control-label">Last Name</label>
							<div class="col-md-9">
								<input type="text" class="form-control" maxlength="50" name="LastName" placeholder="Last Name" data-bind="value:LastName,decorateErrorElement:LastName">
							</div>
						</div>
						<div class="form-group">
							<label for="email" class="col-md-3 control-label">Email</label>
							<div class="col-md-9">
								<input type="text" class="form-control" maxlength="50" name="Email" placeholder="Email Address" data-bind="value:Email,decorateErrorElement:Email">
							</div>
						</div>
						<div class="form-group">
							<label for="password" class="col-md-3 control-label">Password</label>
							<div class="col-md-9">
								<input type="password" class="form-control" maxlength="20" name="Password" placeholder="Password" data-bind="value:TempPassword,decorateErrorElement:TempPassword">
							</div>
						</div>
						<div class="form-group">
							<label for="password" class="col-md-3 control-label">Confirm Password</label>
							<div class="col-md-9">
								<input type="password" class="form-control" maxlength="20"  name="cnfpasswd" placeholder="Confirm Password" data-bind="value:ConfirmPassword,decorateErrorElement:ConfirmPassword">
							</div>
						</div>
						<div class="form-group">
							<label for="icode" class="col-md-3 control-label">Address 1</label>
							<div class="col-md-9">
								<input type="text" class="form-control" maxlength="100" name="Address1" placeholder="Address 1" data-bind="value:Address1,decorateErrorElement:Address1">
							</div>
						</div>
						<div class="form-group">
							<label for="icode" class="col-md-3 control-label">Address 2</label>
							<div class="col-md-9">
								<input type="text" class="form-control" maxlength="100" name="Address2" placeholder="Address 2" data-bind="value:Address2,decorateErrorElement:Address2">
							</div>
						</div>
						<div class="form-group">
							<label for="icode" class="col-md-3 control-label">Phone</label>
							<div class="col-md-9">
								<input type="text" class="form-control" maxlength="20" name="Phone" id="Phone" placeholder="Phone" data-bind="value:Phone,decorateErrorElement:Phone">
							</div>
						</div>
						<div class="form-group">
							<label for="icode" class="col-md-3 control-label">State</label>
							<div class="col-md-9">
								<select data-bind="options: $root.States, optionsValue: 'StateCode', optionsText: 'StateName',value: StateCode,optionsCaption:'Select',event: { change: $root.StateSelectionChanged },decorateErrorElement:StateCode" name="StateCode" class="form-control seatdropdown"></select>
							</div>
						</div>
						<div class="form-group">
							<label for="icode" class="col-md-3 control-label">City</label>
							<div class="col-md-9">
								
								<select data-bind="options: $root.Cities, optionsValue: 'CityCode', optionsText: 'CityName',value: CityCode, optionsCaption:'Select',decorateErrorElement:CityCode" name="CityCode"  class="form-control seatdropdown"></select>
								
							</div>
						</div>
						<div class="form-group">
							<label for="icode" class="col-md-3 control-label">Zip Code</label>
							<div class="col-md-9">
								<input type="text" class="form-control" maxlength="10" name="ZipCode" placeholder="Zip Code" data-bind="value:ZipCode,decorateErrorElement:ZipCode">
							</div>
						</div>
                        <div class="form-group" id="registermula" data-bind="visible:$data.UserTypeID()=='Individual'">
							<label for="icode" class="col-md-3 control-label">Register As Mula</label>
							<div class="col-md-9">
								<input type="checkbox" name="registermula" placeholder="registermula" data-bind="checked:registermula">
							</div>
						</div>
					</div>

					<div class="insurancedetails" id="insurancedetails" style="display: none;" data-bind="visible: registermula">
						<label style="margin-top:20px;">Mula Details</label>
						<div style="border:1px solid #CCC; padding: 20px">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label col-md-4">Avaibility From Time</label>
									<div class="col-md-3">
										<div class="input-group">
											<input type="text" class="form-control timepicker timepicker-no-seconds zeroIndex"  data-bind = "timePicker:AvaibilityFromTime ,timePickerOptions:{autoclose: true,defaultTime:'11:45 AM',minuteStep: 15,showSeconds: false,showMeridian: true},decorateErrorElement:AvaibilityFromTime" name="AvaibilityFromTime">
											<span class="input-group-btn timefocus">
												<button class="btn default" type="button"><i class="fa fa-clock-o"></i></button>
											</span>
										</div>
									</div>
									
									
									
									
									<label class="control-label col-md-2 ">To Time</label>
									<div class="col-md-3">
										<div class="input-group">
											<input type="text" class="form-control timepicker timepicker-no-seconds zeroIndex"  data-bind = "timePicker:AvaibilityToTime,timePickerOptions:{autoclose: true,defaultTime:false,minuteStep: 15,showSeconds: false,showMeridian: true},decorateErrorElement:AvaibilityToTime" name="AvaibilityToTime">
											<span class="input-group-btn timefocus">
												<button class="btn default" type="button"><i class="fa fa-clock-o"></i></button>
											</span>
										</div>
									</div>
								</div>
							</div>
							
							
							<div class="col-md-12">
								<label class="control-label col-md-4"></label>
								<div class="col-md-8">
									<div class="validationMsg"></div>
								</div>
							</div>
							
							
							
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label col-md-4">How far willing to travel?</label>
									<div class="col-md-8">
										<select data-bind="options: $root.Miles, optionsValue: 'MileID', optionsText: 'Miles',value: MileID,optionsCaption:'Select',decorateErrorElement:MileID" class="form-control" name="MileID"></select>
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class="col-md-12" style="display: block;">
									<label class="control-label col-md-4">Transportation Mode</label>
									<div class="col-md-8">
										<select multiple="multiple" id="ms1" style="display: none;"  
										data-bind="options: $root.TransportationModes, optionsValue: 'TransportationTypeID', optionsText: 'TransportationType',multipleSelect:UserTransportationTypes,multipleSelectOptions:{'width': '100%','filter': true,placeholder:'Select Transportation Modes'},decorateErrorElement:UserTransportationTypes" name="UserTransportationTypes">
										</select>
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-12" style="display: block;">
									<label class="control-label col-md-4">Days of operation</label>
									<div class="col-md-8">
										<select multiple="multiple" id="ms2" style="display: none;" 
										data-bind="options: $root.OperationDays, optionsValue: 'OperationDayID', optionsText: 'OperationDay',multipleSelect:UserOperationDays,multipleSelectOptions:{'width': '100%','filter': true,placeholder:'Select Operation Days'},decorateErrorElement:UserOperationDays" name="UserOperationDays">
											
										</select>
									</div>
								</div>
							</div>

                            <div class="form-group">
                                <div class="col-md-12" style="display: block;">
                                    <label class="control-label col-md-4">What are you capable to transport?</label>
                                    <div class="col-md-8">

                                        <select multiple="multiple" id="msCategories" style="display: none;" data-bind="customMultipleSelect:UserCategories,decorateErrorElement:UserCategories" name="UserCategories">
                                            <!-- ko foreach: $root.Services -->
                                            <optgroup  data-bind="attr:{'label':$data.ServiceName}" >
                                                <!-- ko foreach: $root.Categories -->
                                                <!-- ko if: $parent.ServiceID()==$data.ServiceID() -->
                                                <option  data-bind="value:$data.CategoryID,text:$data.CategoryName"></option>
                                                <!-- /ko -->
                                                <!-- /ko -->
                                            </optgroup>
                                            <!-- /ko -->

                                        </select>

<!--
                                            <select multiple="multiple" id="ms3" style="display: none;"
                                            data-bind="options: $root.Categories, optionsValue: 'CategoryID', optionsText: 'CategoryName',multipleSelect:UserCategories,multipleSelectOptions:{'width': '100%','filter': true,placeholder:'Select Capable Carieer'},decorateErrorElement:UserCategories" name="UserCategories">
                                        </select>-->

                                    </div>
                                </div>
                            </div>
							
							
							
							
							<div class="form-group">
								<div class="col-md-12" style="display: block;">
									<label class="control-label col-md-4">Insurance Type</label>
									<div class="col-md-8">
										<select multiple="multiple" id="ms2" style="display: none;"
										data-bind="options: $root.InsuranceTypes, optionsValue: 'InsuranceTypeID', optionsText: 'InsuranceType',multipleSelect:InsuranceTypeID,multipleSelectOptions:{'width': '100%','filter': true,'single':true,placeholder:'Select Insurance Type'},decorateErrorElement:InsuranceTypeID" name="InsuranceTypeID">
										</select>
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label col-md-4">Insurance Start Date</label>
									<div class="col-md-3">
										<div class="input-group">
											<input type="text" class="form-control datepicker zeroIndex" name="insuranceStartDateJobStartDate"  placeholder="Start Date" data-bind="datepicker:InsuranceStartDate,datepickerOptions:{autoclose: true},decorateErrorElement:InsuranceStartDate" class="form-control" />
											<span class="input-group-btn datefocus">
												<button tabindex="-1" type="button" class="btn default">
													<i class="fa fa-calendar"></i>
												</button>
											</span>
											
										</div>
									</div>
									
									
									
									
									<label class="control-label col-md-2 ">End Date</label>
									<div class="col-md-3">
										<div class="input-group">
											<input type="text" class="form-control datepicker zeroIndex" name="insuranceEndDate"  placeholder="End Date" data-bind="datepicker:InsuranceEndDate,datepickerOptions:{autoclose: true},decorateErrorElement:InsuranceEndDate" class="form-control" />
											<span class="input-group-btn datefocus">
												<button tabindex="-1" type="button" class="btn default">
													<i class="fa fa-calendar"></i>
												</button>
											</span>
											
										</div>
									</div>
								</div>
							</div>
							
							<div class="col-md-12">
								<label class="control-label col-md-4"></label>
								<div class="col-md-8">
									<div class="validationDateMsg"></div>
								</div>
							</div>
							
							
							
							
							
							<div class="form-group">
								<div class="col-md-12" style="display: block;">
									<label class="control-label col-md-4">Insurance Number</label>
									<div class="col-md-8">
										<input type="text" class="form-control" maxlength="20" name="InsuranceNumber" placeholder="Insurance Number" data-bind="value:InsuranceNumber">
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class="col-md-12" style="display: block;">
									<label class="control-label col-md-4">Insurance Company</label>
									<div class="col-md-8">
										<input type="text" class="form-control" maxlength="20" name="InsuranceCompany" placeholder="Insurance Company" data-bind="value:InsuranceCompany">
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class="col-md-12" style="display: block;">
									<label class="control-label col-md-4">Insurance Amount</label>
									<div class="col-md-8">
										<input type="text" class="form-control" maxlength="20" name="InsuranceAmount" placeholder="Insurance Amount" data-bind="value:InsuranceAmount,decorateErrorElement:InsuranceAmount">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<!-- Button -->
						<div class="col-md-offset-9 col-md-9" style="margin-top:20px;">
							<button id="btn-signup" type="button" class="btn btn-info" data-bind="click:$root.SaveUser">Sign Up</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@stop


@section('script')
<script src="<?php echo asset('/assets/js/pagejs/security/signup.js');?>"></script>
<script src="<?php echo asset('/assets/js/pagejs/jquery.maskedinput.js');?>"></script>

<script type="text/javascript">
	jQuery(function($){
		$("#Phone").mask("9 (999) 999-9999",{placeholder:"x (xxx) xxx-xxxx"});
		$("#EIN").mask("99-99999999",{placeholder:"xx-xxxxxxxx"});
		$("#PhoneNo").mask("9 (999) 999-9999",{placeholder:"x (xxx) xxx-xxxx"});
	});
	
	window.Required_FirstName ="{{ trans('messages.PropertyRequired',array('attribute'=>'First Name'))}}";
	window.Required_LastName ="{{ trans('messages.PropertyRequired',array('attribute'=>'Last Name'))}}";
	window.Required_Email ="{{ trans('messages.PropertyRequired',array('attribute'=>'Email'))}}";
window.Required_Address1 ="{{ trans('messages.PropertyRequired',array('attribute'=>'Address'))}}";
window.Required_Phone ="{{ trans('messages.PropertyRequired',array('attribute'=>'Phone no'))}}";
window.Required_StateCode ="{{ trans('messages.PropertyRequired',array('attribute'=>'State'))}}";
window.Required_CityCode ="{{ trans('messages.PropertyRequired',array('attribute'=>'City'))}}";
window.Required_ZipCode ="{{ trans('messages.PropertyRequired',array('attribute'=>'Zip Code'))}}";
window.Required_Password ="{{ trans('messages.PropertyRequired',array('attribute'=>'Password'))}}";
window.Required_PasswordDoesNotMatch ="{{ trans('messages.PasswordDoesNotMatch')}}";
window.Required_Redirect ="{{ trans('messages.Redirect')}}";
window.Required_CompanyLogo ="{{ trans('messages.PropertyRequired',array('attribute'=>'Company Logo'))}}";
window.Required_CompanyName ="{{ trans('messages.PropertyRequired',array('attribute'=>'Company Name'))}}";
window.Required_CompanyAddress ="{{ trans('messages.PropertyRequired',array('attribute'=>'Company Address'))}}";
window.Required_PhoneNo ="{{ trans('messages.PropertyRequired',array('attribute'=>'Phone no'))}}";
window.Required_EIN ="{{ trans('messages.PropertyRequired',array('attribute'=>'EIN'))}}";
window.Required_CompanyStateCode ="{{ trans('messages.PropertyRequired',array('attribute'=>'Company State'))}}";
window.Required_CompanyCityCode="{{ trans('messages.PropertyRequired',array('attribute'=>'Company City'))}}";
window.Required_ProfileImage="{{ trans('messages.PropertyRequired',array('attribute'=>'Profile Image'))}}";
window.Required_UserOperationDays ="{{ trans('messages.PropertyRequired',array('attribute'=>'Operation Days'))}}";
window.Required_UserCategories ="{{ trans('messages.PropertyRequired',array('attribute'=>'Carrier Categories'))}}";
window.Required_UserTransportationTypes ="{{ trans('messages.PropertyRequired',array('attribute'=>'Transportation Types'))}}";
window.Required_AvaibilityFromTime ="{{ trans('messages.PropertyRequired',array('attribute'=>'Avaibility From Time'))}}";
window.Required_AvaibilityToTime ="{{ trans('messages.PropertyRequired',array('attribute'=>'Avaibility To Time'))}}";
window.Required_Mile ="{{ trans('messages.PropertyRequired',array('attribute'=>'Mile'))}}";
window.Required_InsuranceType ="{{ trans('messages.PropertyRequired',array('attribute'=>'Insurance Type'))}}";
window.Required_Redirect ="{{ trans('messages.Redirect')}}";
window.AvailableToTimeGreaterThanFromTime ="{{ trans('messages.AvailableToTimeGreaterThanFromTime')}}";
window.InsuranceToDateGreaterThanFromDate ="{{ trans('messages.InsuranceToDateGreaterThanFromDate')}}";

</script>
@stop