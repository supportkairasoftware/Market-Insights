var GetDashboardURL ='/getdashboard';
var cookieFLName = 'cookieFLName';
var cookieSubmitBtnName = 'cookiePaymentHistoryBtn';
var DM;
var ViewModel=function() {
    var self=this;

    self.TotalUsers = ko.observable();
    self.TotalEarning = ko.observableArray();
    self.TotalPaidUsers = ko.observable();
    self.TotalTrialUsers = ko.observable();
    self.LastTenUser = ko.observableArray();
    self.LastTenPayment = ko.observableArray();

self.UserEditMode = function(data) {
    location.href = baseUrl+ '/edituser/'+ data.EncryptUserID();
};
self.PaymentHisotyPage = function(data) {
    var Username = data.FirstName() + ' ' + data.LastName();
    SetMessageForPageLoad(Username,cookieFLName);
    SetMessageForPageLoad('paymenthistorybtn',cookieSubmitBtnName);
    location.href = baseUrl+ '/userpaymentlist';
};
    self.PageLoad = function () {

        var data = {};
        AjaxCall(GetDashboardURL, ko.toJSON({ Data: data }), "post", "json", "application/json").done(function (response) {
            if (response.IsSuccess) {
                self.TotalUsers(response.Data.TotalUsers);
                self.TotalEarning(response.Data.TotalEarning);
                self.TotalPaidUsers(response.Data.TotalPaidUsers);
                self.TotalTrialUsers(response.Data.TotalTrialUsers);
                ko.mapping.fromJS(response.Data.LastTenUser,'', self.LastTenUser);
                ko.mapping.fromJS(response.Data.LastTenPayment,'', self.LastTenPayment);
            }
        });
    };

    self.PageLoad();
};
$(document).ready(function() {
    DM = new ViewModel();
    ko.applyBindings(DM);
    $("#main").show();
});
