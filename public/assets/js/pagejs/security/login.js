var AuthenticateUserURL='/adminauthenticate';
var LoginModel=function() {
    var self=this;
    self.Login=function(){
        var L = this;
        L.Email = ko.observable().extend({required:{message:window.Required_Email,email:true}});
        L.Password = ko.observable().extend({required:{message:window.Required_Password }});
        L.IsSocial = ko.observable(0);
        L.RoleID = ko.observable(1);
    };

    self.Usersobj = ko.observable(new self.Login());
    self.AuthenticateUser = function (data) {


        var error = ko.validation.group(data, {deep:true});
        if(error().length ==0){
            delete data.errors;

            AjaxCall(AuthenticateUserURL, ko.toJSON({ Data: data }), "post", "json", "application/json").done(function (response) {
                if(response.IsSuccess){
                    SetMessageForPageLoad(response.Message);
                    location.href = response.Data.redirectURL;
                }
                else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }else{
            error.showAllMessages(true);
        }
    };
};
$(document).ready(function(){
    var SessionExpired  = $('#SessionExpired').val();
    if(SessionExpired != ''){
        ShowAlertMessage(SessionExpired,'error',window.ConfirmDialogSomethingWrong);
        $('#SessionExpired').val('');
    }
    ko.applyBindings(new LoginModel());
    ko.applyBindings(new LoginModel());
    $("#main").show();
});