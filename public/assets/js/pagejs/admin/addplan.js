var SavePlanUrl = '/saveplan';
var GetPlanListURL = '/planlist';
var DM;

var ViewModel=function(responseData) {

    var self=this;
    self.IsEditMode = ko.observable(false);

    self.Plan=function(){
        var d =this;
        d.PlanID = ko.observable(0);
        d.PlanName = ko.observable().extend({required:{message:window.Required_PlanName}});
        d.Amount = ko.observable().extend({required:{message:window.Required_Amount}});
        d.Discount = ko.observable();
        d.NoOfDays = ko.observable().extend({required:{message:window.Required_NoOfDays}});
        d.IsTrial = ko.observable();
        d.EncryptPlanID = ko.observable();
    };
    self.PlanModel = ko.observable(new self.Plan());

    self.SavePlan = function(data)
    {
        var error = ko.validation.group(data, {deep:true});
       if(error().length == 0){
        //if(1==1){
            delete data.errors;
            AjaxCall(SavePlanUrl,ko.toJSON({ Data : data }) ,"POST" , "json" , "application/json").done(function(response){
                if(response.IsSuccess){
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + GetPlanListURL;
                }
                else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }else{
            error.showAllMessages(true);
        }
    };

    self.Cancel = function(data)
    {
        location.assign(baseUrl + GetPlanListURL);
    };

    self.PageLoad = function (data) {
        self.DataModel = ko.observable();
        if(data){
            ko.mapping.fromJS(data,{},self.PlanModel);
        }
    };
    self.PageLoad(responseData);
};

$(document).ready(function() {
    var data = $.parseJSON($("#PlanModel").val());
    DM = new ViewModel(data);
    ko.applyBindings(DM);
});
