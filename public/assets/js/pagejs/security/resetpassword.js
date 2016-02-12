var ResetPasswordURL = '/resetpassword';

var ViewModel=function() {
	var self=this;
	self.TempResetModel=function(){
		var u =this;
        this.OldPassword =ko.observable().extend({required:{message:window.Required_OldPassword}});
		this.Password =ko.observable().extend({required:{message:window.Required_Password}});
		this.ConfirmPassword = ko.observable().extend({required:{message:window.Required_ConfirmPassword},validation: {
	         validator: function (val, Password) {
				 if(Password==undefined || Password=="" || val==undefined)
					 return true;

					 return val.trim() == Password.trim();
			},
			message: window.Required_PasswordDoesNotMatch,
			params: u.Password 
		}});
	 };
	
	self.ResetPassword = function (data) {
		var error = ko.validation.group(data, {deep:true});
		
		error.subscribe(function(newerrors){
			console.log(newerrors);
		});
		 if(error().length ==0){
			delete data.errors;
			AjaxCall(ResetPasswordURL, ko.toJSON({ Data: data }), "post", "json", "application/json").done(function (response) {
				if(response.IsSuccess){
					
					SetMessageForPageLoad(response.Message);
					location.href = baseUrl+ "/login";
					}else{
						ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
				}
				
			});
		}else{
			error.showAllMessages(true);
		}
	};

	self.ResetModel = ko.observable(new self.TempResetModel());
};
			
						
var model;
$(document).ready(function() {
    model = new ViewModel();
    ko.applyBindings(model);
    $("#main").show();
});

