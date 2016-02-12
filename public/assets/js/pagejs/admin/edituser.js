var SaveUserUrl = '/saveuser';
var GetUserListURL = '/userlist';
var GetDashboardURL = '/dashboard';
var DM;

var ViewModel=function(responseData) {

    var self=this;
    self.IsEditMode = ko.observable(true);
    self.RealImagePath=ko.observable(imagebase + 'Samplephoto.png');
    self.RoleListArray = ko.observableArray();
    self.User=function(){
        var d =this;
        d.UserID = ko.observable(0);
        d.FirstName = ko.observable().extend({required:{message:window.Required_FirstName}});
        d.LastName = ko.observable().extend({required:{message:window.Required_LastName}});
      /*  d.City = ko.observable().extend({required:{message:window.Required_City}});
        d.State = ko.observable().extend({required:{message:window.Required_State}});*/
        d.City = ko.observable();
        d.State = ko.observable();
        d.Mobile = ko.observable().extend({required:{message:window.Required_Mobile}});
        d.Email = ko.observable().extend({required:{message:window.Required_Email},email:true});
        d.EncryptUserID = ko.observable();
        d.IsVerified = ko.observable();
        d.ChangePassword = ko.observable(false);
        d.RoleID = ko.observable().extend({required:{message:window.Required_RoleID}})

        d.TempPassword =ko.observable().extend({required:{message:window.Required_Password,onlyIf: function () {
            return d.ChangePassword();
        }}});
        d.ConfirmPassword = ko.observable().extend({
            required:{message:window.Required_ConfirmPassword,
                onlyIf: function () {
                    return d.ChangePassword();
                }},
            validation: {
            validator: function (val, TempPassword) {
                if(TempPassword==undefined || TempPassword=="" || val==undefined)
                    return true;

                return val.trim() == TempPassword.trim();
            },
            message: 'Password doesn\'t match',
            params: d.TempPassword,
            onlyIf: function () {
                return d.ChangePassword();
            }

        }});

    };
    self.UserModel = ko.observable(new self.User());

    self.SaveUser = function(data)
    {
        var error = ko.validation.group(data, {deep:true});
        if(error().length == 0){
            delete data.errors;
            AjaxCall(SaveUserUrl,ko.toJSON({ Data : data }) ,"POST" , "json" , "application/json").done(function(response){
                if(response.IsSuccess){
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + GetUserListURL;
                }
                else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }else{
            error.showAllMessages(true);
        }
    };
    self.cancel = function(data)
    {
        //location.assign(baseUrl + GetUserListURL);
        window.history.go(-1);
    };


    self.PageLoad = function (data) {
        self.DataModel = ko.observable();
        if(data){
            ko.mapping.fromJS(data,{},self.UserModel);
            ko.mapping.fromJS( data.RoleListArray,{},self.RoleListArray);
        }
    };
    self.PageLoad(responseData);
};

$(document).ready(function() {
    var data = $.parseJSON($("#UserModel").val());
    DM = new ViewModel(data);
    ko.applyBindings(DM);
});
