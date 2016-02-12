var SendForgotPasswordEmail='/sendforgotpasswordemail';

var LoginModel=function() {
	var self=this;
	self.TempForgotModel=function(){
        this.RoleID = ko.observable(EncRoleID);
		this.Email = ko.observable().extend({required:{message:window.Required_Email}, email:true});
	};
	self.ForgotModel = ko.observable(new self.TempForgotModel());
	self.SendForgotPasswordEmail=function (data) {
		var error = ko.validation.group(data, {deep:true});
		if(error().length ==0){
			delete data.errors;
			AjaxCall(SendForgotPasswordEmail, ko.toJSON({ Data: data }), "post", "json", "application/json").done(function (response) {
				if(response.IsSuccess){
					
					SetMessageForPageLoad(response.Message);
					location.href = baseUrl+ "/login/"+data.RoleID();
				}
				else
				{
					ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
				}
			});
		}else{
			error.showAllMessages(true);
		}
	};
};
$(document).ready(function(){
	ko.applyBindings(new LoginModel());
	$("#main").show();
	$("#main").css("min-height", "200px");
});