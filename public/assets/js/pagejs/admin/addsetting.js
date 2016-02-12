var SaveSettingUrl = '/savesetting';
var GetDashboardURL = '/dashboard';
var DM;

var ViewModel=function(responseData) {

    var self=this;
    self.IsEditMode = ko.observable(false);

    self.Setting=function(){
        var d =this;
        d.SettingID = ko.observable(0);
        d.SMSUrl = ko.observable();
        d.SMSUserName = ko.observable();
        d.SMSPassword = ko.observable();
        d.SMSTemplates = ko.observable();
        d.AccountName = ko.observable();
        d.AccountNumber = ko.observable();
        d.BranchName = ko.observable();
        d.IFSCCode = ko.observable();
        d.SenderID=ko.observable().extend({ required: true, maxLength: 6,minLength:6 });;

    };
    self.SettingModel = ko.observable(new self.Setting());

    self.Save = function(data)
    {
        var error = ko.validation.group(data, {deep:true});
       if(error().length == 0){
        //if(1==1){
            delete data.errors;
            AjaxCall(SaveSettingUrl,ko.toJSON({ Data : data }) ,"POST" , "json" , "application/json").done(function(response){
                if(response.IsSuccess){
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + GetDashboardURL;
                }
                else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }else{
            error.showAllMessages(true);
        }
    };

    self.Cancel = function(data) {location.assign(baseUrl + GetDashboardURL);};

    self.PageLoad = function (data) {
        self.DataModel = ko.observable();
        if(data){
            ko.mapping.fromJS(data,{},self.SettingModel);
        }
    };
    self.PageLoad(responseData);
};

$(document).ready(function() {
    var data = $.parseJSON($("#SettingModel").val());
    DM = new ViewModel(data);
    ko.applyBindings(DM);
});
