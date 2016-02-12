var SaveGroupUrl = '/savegroup';
var GetGroupListURL = '/grouplist';
var DM;

var ViewModel=function(responseData) {

    var self=this;
    self.IsEditMode = ko.observable(false);

    self.Group=function(){
        var d =this;
        d.GroupID = ko.observable(0);
        d.GroupName = ko.observable().extend({required:{message:window.Required_GroupName}});
        d.EncryptGroupID = ko.observable();
    };
    self.GroupModel = ko.observable(new self.Group());

    self.SaveGroup = function(data)
    {
        var error = ko.validation.group(data, {deep:true});
        if(error().length == 0){
            delete data.errors;
            AjaxCall(SaveGroupUrl,ko.toJSON({ Data : data }) ,"POST" , "json" , "application/json").done(function(response){
                if(response.IsSuccess){
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + GetGroupListURL;
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
        location.assign(baseUrl + GetGroupListURL);
    };
    self.PageLoad = function (data) {
        self.DataModel = ko.observable();
        if(data){
            ko.mapping.fromJS(data,{},self.GroupModel);
        }
    };
    self.PageLoad(responseData);
};

$(document).ready(function() {
    var data = $.parseJSON($("#GroupModel").val());
    DM = new ViewModel(data);
    ko.applyBindings(DM);
});
