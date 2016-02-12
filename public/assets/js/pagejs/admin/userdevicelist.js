var UserDeviceListUrl ='/getuserdevicelist';
var DeleteUserDeviceURL ='/deleteuserdevice';
var UserDeviceList= '/userdevicelist';
var DM;

var ViewModel=function() {
    var self=this;
    self.pager = new Pager();
    self.UserDeviceListArray=ko.observableArray();
    self.pager.isSearch(false);

    self.SearchCategories=function(){
        var sc = this;
        sc.textKeyWord = ko.observable();
    };

    self.ClearSearch = function(){
        self.SearchModel(new self.SearchCategories());
        self.pager.isSearch(false);
        self.pager.iPageSize(docDefaultPageSize);
        self.PageLoad();
    };

    self.ApplyFilter = function(data){
        self.pager.isSearch(true);
        self.pager.search();
    };

    self.SearchModel = ko.observable(   new self.SearchCategories());

    self.DeleteUserDevice = function (data) {
        ShowConfirm(" This User Device : "+" ' "+ data.DeviceID()+" '?", function () {
            AjaxCall(DeleteUserDeviceURL, ko.toJSON({ Data : data.UserDeviceID()}), "post", "json", "application/json").done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + UserDeviceList;
                }else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }, 'Delete Group');
    };

    self.PageLoad = function () {
        var param = {
            PageIndex: self.pager.currentPage()
        };
        if(self.pager.isSearch()) {
            param.SearchParams = ko.toJS(self.SearchModel());
        }
        param.PageSize = self.pager.selectedPageSize();
        AjaxCall(UserDeviceListUrl, ko.toJSON({ Data: param }), "post", "json", "application/json").done(function (response) {
            if (response.IsSuccess) {
                ko.mapping.fromJS(response.Data.UserDeviceListArray,'', self.UserDeviceListArray);
                self.pager.currentPage(response.Data.CurrentPage);
                self.pager.iPageSize(response.Data.ItemsPerPage);
                self.pager.iTotalDisplayRecords(response.Data.ItemsPerPage);
                self.pager.iTotalRecords(response.Data.TotalItems);
                if(self.UserDeviceListArray().length<=0)
                    $('#nodata').show();
                else
                    $('#nodata').hide();
            }
        });
    };

    self.pager.getDataCallback = self.PageLoad;
    self.pager.selectedPageSize(docDefaultPageSize);
};
$(document).ready(function() {
    DM = new ViewModel();
    ko.applyBindings(DM);
    $("#main").show();
});
