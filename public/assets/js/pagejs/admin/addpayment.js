var SavePaymentUrl = '/savepayment';
var GetPaymentDashboardURL = '/userpaymentlist';
var DM;

var ViewModel=function(responseData) {

    var self=this;
    self.IsEditMode = ko.observable(false);
    self.UserListArray = ko.observableArray();
    self.PlanListArray = ko.observableArray();

    self.Payment = function(){
        var d =this;
        d.PaymentHistoryID = ko.observable(0);
        d.UserID = ko.observable();//.extend({required:{message:window.Required_UserName}});
        d.PlanID = ko.observable().extend({required:{message:window.Required_PlanID}});
        d.StartDate = ko.observable().extend({required:{message:window.Required_StartDate}});
        d.SubscriptionAmount = ko.observable().extend({required:{message:window.Required_SubscriptionAmount}});
        d.PlanID.subscribe(function (val) {
            var PlanAmount = ko.utils.arrayFirst(self.PlanListArray(), function(item) {
                return item.PlanID()==val;
            });
            if(PlanAmount.SubscriptionAmount() != undefined && PlanAmount.SubscriptionAmount() != null)
                d.SubscriptionAmount(PlanAmount.SubscriptionAmount());

        });
    };
    self.PaymentModel = ko.observable(new self.Payment());


    self.Save = function(data)
    {
        var error = ko.validation.group(data, {deep:true});
        if(error().length == 0){
            //if(1==1){
            delete data.errors;
            var serverData = ko.toJS(self.PaymentModel());
            serverData.StartDate = moment(serverData.StartDate).format('YYYY-MM-DD');
            AjaxCall(SavePaymentUrl,ko.toJSON({ Data : serverData }) ,"POST" , "json" , "application/json").done(function(response){
                if(response.IsSuccess){
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + GetPaymentDashboardURL;
                }
                else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }else{
            error.showAllMessages(true);
        }
    };

    self.Cancel = function(data) {location.assign(baseUrl + GetPaymentDashboardURL);};

    self.PageLoad = function (data) {
        self.DataModel = ko.observable();
        if(data){
            ko.mapping.fromJS(data,{},self.PaymentModel);
            ko.mapping.fromJS( data.UserListArray,{},self.UserListArray);
            ko.mapping.fromJS( data.PlanListArray,{},self.PlanListArray);
        }
    };
    self.PageLoad(responseData);
};

$(document).ready(function() {
    var data = $.parseJSON($("#PaymentModel").val());
    DM = new ViewModel(data);
    ko.applyBindings(DM);
});
