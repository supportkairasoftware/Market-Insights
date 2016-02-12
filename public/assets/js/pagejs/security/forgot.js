var ForgotPasswordURL = '/setforgotpassword';

var ViewModel=function(responseData) {
	var self=this;
	self.TempForgotModel=function(){
		var u =this;
		
		this.TempEmail = ko.observable();
		this.Email = ko.observable().extend({required:{message:window.Required_Email},email:true,validation: {
	         validator: function (val, TempEmail) {
				 if(TempEmail==undefined || TempEmail=="" || val==undefined)
					 return true;

                 return val.trim() == TempEmail.trim();
			},
			message:window.Required_EmailModify,
			params: u.TempEmail 
		}});

		this.Password =ko.observable().extend({required:{message:window.Required_Passwordvalidation},pattern:{message:window.Required_Password ,params:/^(?=(.*\d){1})(?=.*[a-zA-Z])[0-9a-zA-Z\W]{8,15}$/}});
		this.ConfirmPassword = ko.observable().extend({required:{message:window.Required_ConfirmPassword},validation: {
	         validator: function (val, Password) {
				 if(Password==undefined || Password=="" || val==undefined)
					 return true;

                 return val.trim() == Password.trim();
			},
			message: window.Required_PasswordDoesNotMatch,
			params: u.Password 
		}});
		this.DecryptedValue=ko.observable();
	 };
	
	self.ResetPassword = function (data) {
		
		var error = ko.validation.group(data, {deep:true});
		error.subscribe(function(newerrors){
			console.log(newerrors);
		});	

		if(error().length ==0){
			delete data.errors;
            
			AjaxCall(ForgotPasswordURL, ko.toJSON({ Data: data }), "post", "json", "application/json").done(function (response) {
				if(response.IsSuccess){
					
					self.ForgotModel(new self.TempForgotModel());
					SetMessageForPageLoad(response.Message);
					location.href = baseUrl+ "/login/"+response.Data;
					
				}else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
				}
				
			});
		}else{
			error.showAllMessages(true);
		}
	};

	 self.PageLoad = function (data) {
         self.ForgotModel = ko.mapping.fromJS(data,{},ko.observable(new self.TempForgotModel()));
	 };

    self.PageLoad(responseData);
};
			
						
var model;
$(document).ready(function() {
    var data = $.parseJSON($("#ForgotModel").val());
    model = new ViewModel(data);
    ko.applyBindings(model);
    $("#main").show();
});

